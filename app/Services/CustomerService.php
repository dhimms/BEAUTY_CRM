<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerService
{
    // ─── Dashboard ────────────────────────────────────

    public function getDashboardData(): array
    {
        $today = Carbon::today();

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();

        $todayFollowUps = Activity::with(['user', 'activitable'])
            ->whereNotNull('follow_up_date')
            ->where('follow_up_status', 'pending')
            ->whereDate('follow_up_date', $today)
            ->orderBy('follow_up_date')
            ->get();

        $overdueFollowUps = Activity::with(['user', 'activitable'])
            ->overdueFollowUps()
            ->get();

        $upcomingFollowUps = Activity::with(['user', 'activitable'])
            ->whereNotNull('follow_up_date')
            ->where('follow_up_status', 'pending')
            ->whereDate('follow_up_date', '>', $today)
            ->orderBy('follow_up_date')
            ->take(10) // Limit to 10 upcoming for the dashboard
            ->get();

        $newCustomersThisMonth = Customer::whereNotNull('lead_id')
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->count();

        // Aktivitas kontak yang dilakukan hari ini (termasuk blast pesan)
        $contactedToday = Activity::with(['user', 'activitable'])
            ->where('activitable_type', Customer::class)
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        // List customer untuk modal Buat Follow-up
        $customers = Customer::orderBy('name')->get(['id', 'name']);

        // List CS users untuk modal Tambah Customer
        $csUsers = $this->getCsUsers();

        return compact(
            'totalCustomers',
            'activeCustomers',
            'todayFollowUps',
            'overdueFollowUps',
            'upcomingFollowUps',
            'newCustomersThisMonth',
            'contactedToday',
            'customers',
            'csUsers'
        );
    }

    // ─── Customers ────────────────────────────────────

    public function getCustomers(array $filters = []): LengthAwarePaginator
    {
        return Customer::with('csUser')
            ->select('customers.*')
            ->selectRaw("IFNULL((SELECT SUM(value) FROM deals WHERE deals.lead_id = customers.lead_id AND deals.status = 'won'), 0) as total_spend")
            ->selectRaw("(SELECT MAX(created_at) FROM activities WHERE activities.activitable_id = customers.id AND activities.activitable_type = ?) as last_contacted_at", [Customer::class])
            ->search($filters['search'] ?? null)
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['user_id'] ?? null, fn($q, $u) => $q->where('user_id', $u))
            ->when($filters['min_spend'] ?? null, fn($q, $v) => $q->minSpend($v))
            ->when($filters['deal_keyword'] ?? null, fn($q, $v) => $q->hasDealName($v))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function createCustomer(array $data): Customer
    {
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        return Customer::create($data);
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $customer->update($data);
        return $customer->fresh();
    }

    public function blastMessage(array $customerIds, string $channel, string $message, $image = null): int
    {
        $count = 0;

        // Untuk WhatsApp: konversi HTML rich text ke format teks / WhatsApp Markdown (*bold*, _italic_, ~strike~, dll)
        $plainText = $this->formatMessageForWhatsApp($message);

        // Simpan gambar sementara ke local disk jika ada
        // Path relatif terhadap storage/app/public/
        $storedPath = null;
        if ($image) {
            $storedPath = $image->store('uploads/blast', 'public');
        }

        foreach ($customerIds as $id) {
            $customer = Customer::find($id);
            if (!$customer) continue;

            if ($channel === 'email' && !empty($customer->email)) {
                try {
                    // Kirim email — gambar di-embed sebagai inline attachment (CID)
                    // Tidak perlu URL publik, langsung embed ke dalam email
                    \Illuminate\Support\Facades\Mail::to($customer->email)
                        ->send(new \App\Mail\BlastMessageMail($message, $storedPath));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim blast email ke {$customer->email}: " . $e->getMessage());
                }
            } elseif ($channel === 'whatsapp' && !empty($customer->phone)) {
                try {
                    $token = config('services.fonnte.token') ?: env('FONNTE_TOKEN');
                    if ($token) {
                        // Gunakan cURL + CURLFile sesuai dokumentasi resmi Fonnte
                        $fields = [
                            'target'  => $customer->phone,
                            'message' => $plainText,
                        ];

                        if ($storedPath) {
                            $absolutePath = storage_path('app/public/' . $storedPath);
                            // CURLFile adalah cara resmi Fonnte untuk upload file
                            $fields['file'] = new \CURLFile(
                                $absolutePath,
                                mime_content_type($absolutePath),
                                basename($absolutePath)
                            );
                        }

                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL            => 'https://api.fonnte.com/send',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST           => true,
                            CURLOPT_POSTFIELDS     => $fields,
                            CURLOPT_HTTPHEADER     => ['Authorization: ' . $token],
                        ]);
                        $response = curl_exec($curl);
                        $curlError = curl_error($curl);
                        curl_close($curl);

                        if ($curlError) {
                            \Illuminate\Support\Facades\Log::error("cURL error blast WA ke {$customer->phone}: " . $curlError);
                        } else {
                            \Illuminate\Support\Facades\Log::info("Fonnte response untuk {$customer->phone}: " . $response);
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Fonnte Token is missing, cannot send WA blast to {$customer->phone}");
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim blast WA ke {$customer->phone}: " . $e->getMessage());
                }
            }

            // Catat ke dalam activity history
            Activity::create([
                'user_id'          => auth()->id(),
                'activitable_type' => Customer::class,
                'activitable_id'   => $id,
                'type'             => $channel === 'whatsapp' ? 'whatsapp' : 'email',
                'description'      => "Kirim Blast " . ucfirst($channel) . ": " . $plainText,
                'status'           => 'completed',
                'activity_date'    => now(),
                'notes'            => $plainText . ($storedPath ? " [+gambar]" : ''),
            ]);
            $count++;
        }
        return $count;
    }

    // digunakan untuk mengatasi masalah performa N+1
    // karena jika tidak menggunakan eager loading maka akan terjadi N+1 query
    public function getCustomerDetail(int $id): Customer
    {
        return Customer::with([
            'csUser',
            'lead',
            'activities' => fn($q) => $q->with('user')->latest(),
        ])->findOrFail($id);
    }

    // ─── Follow-ups ───────────────────────────────────
    // metode ini digunakan untuk customer service untuk mengetahui jadwal follow up
    // mengambil data follow up dari database berdasarkan tanggal follow up
    public function getFollowUps(array $filters = []): array
    {
        $baseQuery = Activity::with(['user', 'activitable'])
            ->whereNotNull('follow_up_date');

        
        // variabel $pending dan $overdue digunakan untuk sistem cs mengetahui siapa customer yg kelewatan jadwal follow up nya    
        $pending = (clone $baseQuery)
            ->where('follow_up_status', 'pending')
            ->when($filters['from'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '>=', $d))
            ->when($filters['to'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '<=', $d))
            ->orderBy('follow_up_date')
            ->get();

        $overdue = (clone $baseQuery)
            ->where('follow_up_status', 'pending')
            ->whereDate('follow_up_date', '<', Carbon::today())
            ->orderBy('follow_up_date')
            ->get();

        $completed = (clone $baseQuery)
            ->where('follow_up_status', 'done')
            ->when($filters['from'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '>=', $d))
            ->when($filters['to'] ?? null, fn($q, $d) => $q->whereDate('follow_up_date', '<=', $d))
            ->latest('updated_at')
            ->take(20)
            ->get();

        return compact('pending', 'overdue', 'completed');
    }

    public function createFollowUp(array $data): Activity
    {
        return Activity::create([
            'user_id' => auth()->id(),
            'activitable_type' => Customer::class,
            'activitable_id' => $data['customer_id'],
            'type' => $data['follow_up_type'] ?? 'call',
            'subject' => $data['subject'] ?? 'Follow-up',
            'follow_up_date' => $data['follow_up_date'],
            'follow_up_type' => $data['follow_up_type'] ?? 'call',
            'follow_up_notes' => $data['follow_up_notes'] ?? null,
            'follow_up_status' => 'pending',
            'activity_date' => now(),
        ]);
    }

    public function completeFollowUp(Activity $activity): Activity
    {
        $activity->update([
            'follow_up_status' => 'done',
            'result' => 'connected',
        ]);

        return $activity->fresh();
    }

    // ─── Activities ───────────────────────────────────
    // logactivity digunakan untuk mencatat aktivitas yang dilakukan oleh customer service
    // untuk mengetahui aktivitas apa saja yang dilakukan oleh customer service 
    public function logActivity(array $data): Activity
    {
        $typeMap = [
            'customer' => Customer::class,
        ];

        return Activity::create([
            'user_id' => auth()->id(),
            'activitable_type' => $typeMap[$data['activitable_type']] ?? Customer::class,
            'activitable_id' => $data['activitable_id'],
            'type' => $data['type'],
            'subject' => $data['subject'] ?? null,
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'] ?? null,
            'result' => $data['result'] ?? null,
            'activity_date' => $data['activity_date'] ?? now(),
        ]);
    }

    // ─── Helpers ──────────────────────────────────────

    public function getCsUsers(): Collection
    {
        return User::role('Customer Service')->where('is_active', true)->get();
    }

    /**
     * Mengubah format HTML (dari Rich Text Editor) menjadi teks dengan format WhatsApp markdown yang valid.
     */
    protected function formatMessageForWhatsApp(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        // 1. Tangani ordered list (<ol><li>...</li></ol>) agar bernomor 1., 2., 3., dst.
        $html = preg_replace_callback('/<ol\b[^>]*>(.*?)<\/ol>/is', function ($matches) {
            $count = 1;
            return preg_replace_callback('/<li\b[^>]*>(.*?)<\/li>/is', function ($li) use (&$count) {
                return ($count++) . '. ' . trim($li[1]) . "\n";
            }, $matches[1]);
        }, $html);

        // 2. Tangani unordered list (<ul><li>...</li></ul>) atau list bullet
        $html = preg_replace_callback('/<li\b[^>]*>(.*?)<\/li>/is', function ($li) {
            return '• ' . trim($li[1]) . "\n";
        }, $html);

        // 3. Tangani Heading (<h1>..<h6>) -> teks tebal
        $html = preg_replace_callback('/<h[1-6]\b[^>]*>(.*?)<\/h[1-6]>/is', function ($m) {
            $content = trim(strip_tags($m[1]));
            return $content !== '' ? "\n*" . $content . "*\n" : '';
        }, $html);

        // 4. Tangani Bold (<b>, <strong>)
        // Whitespace di dalam tag dipindahkan ke luar tanda bintang agar WA memformatnya sebagai BOLD
        $html = preg_replace_callback('/<(b|strong)\b[^>]*>(.*?)<\/\1>/is', function ($m) {
            $raw = $m[2];
            preg_match('/^(\s*)(.*?)(\s*)$/us', $raw, $parts);
            $leading = $parts[1] ?? '';
            $trimmed = $parts[2] ?? '';
            $trailing = $parts[3] ?? '';
            return $trimmed !== '' ? "{$leading}*{$trimmed}*{$trailing}" : $raw;
        }, $html);

        // 5. Tangani Italic (<i>, <em>)
        $html = preg_replace_callback('/<(i|em)\b[^>]*>(.*?)<\/\1>/is', function ($m) {
            $raw = $m[2];
            preg_match('/^(\s*)(.*?)(\s*)$/us', $raw, $parts);
            $leading = $parts[1] ?? '';
            $trimmed = $parts[2] ?? '';
            $trailing = $parts[3] ?? '';
            return $trimmed !== '' ? "{$leading}_{$trimmed}_{$trailing}" : $raw;
        }, $html);

        // 6. Tangani Strikethrough (<s>, <strike>, <del>)
        $html = preg_replace_callback('/<(s|strike|del)\b[^>]*>(.*?)<\/\1>/is', function ($m) {
            $raw = $m[2];
            preg_match('/^(\s*)(.*?)(\s*)$/us', $raw, $parts);
            $leading = $parts[1] ?? '';
            $trimmed = $parts[2] ?? '';
            $trailing = $parts[3] ?? '';
            return $trimmed !== '' ? "{$leading}~{$trimmed}~{$trailing}" : $raw;
        }, $html);

        // 7. Tangani Monospace / Code (<code>)
        $html = preg_replace_callback('/<code\b[^>]*>(.*?)<\/code>/is', function ($m) {
            $trimmed = trim($m[1]);
            return $trimmed !== '' ? "```{$trimmed}```" : '';
        }, $html);

        // 8. Tangani paragraf kosong dan baris baru
        $html = preg_replace('/<p>\s*<br\s*\/?>\s*<\/p>/i', "\n\n", $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n", $html);
        $html = preg_replace('/<p\b[^>]*>/i', '', $html);

        // 9. Hapus tag HTML yang tersisa
        $text = strip_tags($html);

        // 10. Decode entity HTML
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);

        // 11. Normalisasi jarak baris baru
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }
}
