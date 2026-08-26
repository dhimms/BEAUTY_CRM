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

    public function blastMessage(array $customerIds, string $channel, string $message): int
    {
        $count = 0;
        foreach ($customerIds as $id) {
            $customer = Customer::find($id);
            if (!$customer) continue;

            if ($channel === 'email' && !empty($customer->email)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\BlastMessageMail($message));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim blast email ke {$customer->email}: " . $e->getMessage());
                }
            } elseif ($channel === 'whatsapp' && !empty($customer->phone)) {
                try {
                    $token = env('FONNTE_TOKEN');
                    if ($token) {
                        \Illuminate\Support\Facades\Http::withHeaders([
                            'Authorization' => $token
                        ])->post('https://api.fonnte.com/send', [
                            'target' => $customer->phone,
                            'message' => $message,
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Fonnte Token is missing, cannot send WA blast to {$customer->phone}");
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim blast WA ke {$customer->phone}: " . $e->getMessage());
                }
            }

            // Catat ke dalam activity history
            Activity::create([
                'user_id' => auth()->id(),
                'activitable_type' => Customer::class,
                'activitable_id' => $id,
                'type' => $channel === 'whatsapp' ? 'whatsapp' : 'email',
                'description' => "Kirim Blast " . ucfirst($channel) . ": " . $message,
                'status' => 'completed',
                'activity_date' => now(),
                'notes' => $message
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
}
