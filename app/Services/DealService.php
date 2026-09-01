<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use Illuminate\Support\Facades\DB;

class DealService
{
    /**
     * Dipanggil oleh: DealController@store (Submit Form Buat Deal)
     * Penjelasan: Membuat data Deal baru di pipeline stage awal dan mengubah status Lead dari qualified menjadi 'converted'.
     */
    public function createFromLead(Lead $lead, array $data): Deal
    {
        return DB::transaction(function () use ($lead, $data) {
            // Mengambil stage pertama dari pipeline jika Sales belum memilih stage
            $firstStage = PipelineStage::ordered()->first();

            // Membuat Deal baru berdasarkan data Lead dan input dari Sales
            $deal = Deal::create([
                'lead_id'             => $lead->id,
                'name'                => $data['name'],
                'value'               => 0,
                'pipeline_stage_id'   => $data['pipeline_stage_id'] ?? $firstStage->id,
                'status'              => 'open',
                'expected_close_date' => $data['expected_close_date'] ?? null,
                'assigned_to'         => $lead->assigned_to,
                'created_by'          => auth()->id(),
            ]);

            // Mengubah status Lead menjadi converted karena sudah dibuat menjadi Deal
            $lead->update(['status' => 'converted']);

            return $deal;
        });
    }

    /**
     * Dipanggil oleh: DealController@moveStage (Tombol "Pindah Stage Next")
     * Penjelasan: Menggeser stage deal 1 tingkat ke depan dalam pipeline.
     */
    public function moveToNextStage(Deal $deal): Deal
    {
        // Mengambil urutan stage tempat Deal berada sekarang
        $currentOrder = $deal->pipelineStage->order;

        // Mencari stage berikutnya berdasarkan urutan pipeline
        $nextStage = PipelineStage::where('order', '>', $currentOrder)
            ->ordered()
            ->first();

        // Jika tidak ada stage berikutnya, berarti Deal sudah di stage terakhir
        if (!$nextStage) {
            throw new \Exception('Deal sudah berada di stage terakhir.');
        }

        // Mengubah Deal ke stage berikutnya
        $deal->update([
            'pipeline_stage_id' => $nextStage->id
        ]);

        // Mengambil kembali data Deal beserta stage terbarunya
        return $deal->fresh('pipelineStage');
    }

    /**
     * Dipanggil oleh: DealController@moveStage (Drag & Drop di Kanban Board `pipeline.blade.php`)
     * Penjelasan: Memindahkan stage deal langsung ke stageId tertentu hasil drag and drop.
     */
    public function moveToStage(Deal $deal, int $stageId): Deal
    {
        // Mencari stage berdasarkan ID yang dipilih
        $stage = PipelineStage::findOrFail($stageId);

        // Mengubah stage Deal ke stage yang dipilih
        $deal->update([
            'pipeline_stage_id' => $stage->id
        ]);

        // Mengambil kembali data Deal beserta stage terbaru
        return $deal->fresh('pipelineStage');
    }

    /**
     * Dipanggil oleh: DealController@close (Tombol "Mark as Won")
     * Penjelasan: Mengubah status deal menjadi 'won', mencatat closed_at, dan otomatis membuatkan data Customer baru dari Lead.
     */
    public function closeWon(Deal $deal, ?string $productName = null, ?float $value = null): Deal
    {
        return DB::transaction(function () use ($deal, $productName, $value) {

            // Mengubah status Deal menjadi WON dan mencatat waktu Deal ditutup
            $deal->update([
                'status'       => 'won',
                'closed_at'    => now(),
                'product_name' => $productName,
                'value'        => $value !== null ? $value : $deal->value,
            ]);

            // Mengambil Lead yang berhubungan dengan Deal
            $lead = $deal->lead;

            // Mengecek apakah Customer dari Lead tersebut sudah pernah dibuat
            $existingCustomer = Customer::where('lead_id', $lead->id)->first();

            // Jika Customer belum ada, maka buat Customer baru dari data Lead
            if (!$existingCustomer) {
                Customer::create([
                    'lead_id' => $lead->id,
                    'user_id' => null, // CS akan ditentukan nanti
                    'name'    => $lead->name,
                    'email'   => $lead->email,
                    'phone'   => $lead->phone,
                    'address' => $lead->address,
                    'status'  => 'active',
                    'notes'   => "Converted from Deal: {$deal->name}",
                ]);
            }

            return $deal;
        });
    }

    /**
     * Dipanggil oleh: DealController@close (Tombol "Mark as Lost")
     * Penjelasan: Mengubah status deal menjadi 'lost', mencatat closed_at, alasan lost (lost_reason_id), dan catatan kekalahan.
     */
    public function closeLost(
        Deal $deal,
        int $lostReasonId,
        ?string $lostNotes = null
    ): Deal {
        // Mengubah status Deal menjadi LOST dan menyimpan alasan kehilangan
        $deal->update([
            'status'         => 'lost',
            'closed_at'      => now(),
            'lost_reason_id' => $lostReasonId,
            'lost_notes'     => $lostNotes,
        ]);

        return $deal;
    }

    /**
     * Mengirim pesan blast ke Lead yang terhubung dengan Deal-deal yang dipilih.
     */
    public function blastMessage(array $dealIds, string $channel, string $message, $image = null): int
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

        foreach ($dealIds as $id) {
            $deal = Deal::with('lead')->find($id);
            if (!$deal || !$deal->lead) continue;
            
            $lead = $deal->lead;

            if ($channel === 'email' && !empty($lead->email)) {
                try {
                    // Kirim email — gambar di-embed sebagai inline attachment (CID)
                    // Tidak perlu URL publik, langsung embed ke dalam email
                    \Illuminate\Support\Facades\Mail::to($lead->email)
                        ->send(new \App\Mail\BlastMessageMail($message, $storedPath));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim blast email ke {$lead->email}: " . $e->getMessage());
                }
            } elseif ($channel === 'whatsapp' && !empty($lead->phone)) {
                try {
                    $token = config('services.fonnte.token') ?: env('FONNTE_TOKEN');
                    if ($token) {
                        // Gunakan cURL + CURLFile sesuai dokumentasi resmi Fonnte
                        $fields = [
                            'target'  => $lead->phone,
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
                            \Illuminate\Support\Facades\Log::error("cURL error blast WA ke {$lead->phone}: " . $curlError);
                        } else {
                            \Illuminate\Support\Facades\Log::info("Fonnte response untuk {$lead->phone}: " . $response);
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Fonnte Token is missing, cannot send WA blast to {$lead->phone}");
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim blast WA ke {$lead->phone}: " . $e->getMessage());
                }
            }

            // Catat ke dalam activity history untuk Lead tersebut
            \App\Models\Activity::create([
                'user_id'          => auth()->id(),
                'activitable_type' => \App\Models\Lead::class,
                'activitable_id'   => $lead->id,
                'type'             => $channel === 'whatsapp' ? 'whatsapp' : 'email',
                'description'      => "Kirim Blast via " . ucfirst($channel) . " (Terkait Deal: {$deal->name})",
                'status'           => 'completed',
                'activity_date'    => now(),
                'notes'            => $plainText . ($storedPath ? " [+gambar]" : ''),
            ]);
            $count++;
        }
        return $count;
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