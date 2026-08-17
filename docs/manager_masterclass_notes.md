# 📘 Buku Pintar: Masterclass Back-End Role Manager

Dokumen ini adalah rangkuman komprehensif dari materi *coaching* khusus fitur **Manager**. Sebagai Manager, tugas utama aplikasi adalah menyajikan rangkuman angka (*Helicopter View*), visualisasi metrik, dan melacak kinerja tim. 

Catatan ini dirancang sangat detail, lengkap dengan potongan kode dan penjelasan baris per baris beserta alurnya.

---

## 🎯 TAHAP 4: Dashboard & Team Performance

**Tujuan:** Memahami bagaimana sistem memfilter data berdasarkan waktu secara dinamis, melakukan perhitungan agregat (Total), dan menyusun Papan Peringkat (Leaderboard).

### 1. Resepsionis Manager (DashboardController)
Ketika Manager membuka halaman dashboard dan memilih filter waktu (misal: "Bulan Ini"), permintaan akan ditangkap di sini.

```php
// File: app/Http/Controllers/Manager/DashboardController.php
public function index(\Illuminate\Http\Request $request)
{
    // 1. Menangkap filter tanggal dari URL web (default: 'all')
    $period = $request->get('period', 'all');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    
    // 2. Mendelegasikan perhitungan matematika yang rumit ke Koki (ReportService)
    $data = $this->reportService->getManagerDashboard($period, $startDate, $endDate);
    
    // 3. Mengembalikan hasil hitungan ke layar (View)
    $data['period'] = $period;
    return view('manager.dashboard.index', $data);
}
```

### 2. Senjata Rahasia Koki: Filter Waktu (applyPeriodFilter)
Agar kita tidak perlu menulis ulang logika filter tanggal untuk setiap kotak metrik (Total Lead, Total Member, dll), kita membuat satu fungsi khusus pembantu.

```php
// File: app/Services/ReportService.php
private function applyPeriodFilter($query, string $column, string $period, ...)
{
    // Fitur 'match' PHP 8. Mengecek apa isi filter yang dipilih Manager.
    return match ($period) {
        // Jika filter "Hari Ini"
        'today' => $query->whereDate($column, Carbon::today()),
        
        // Jika filter "Minggu Ini"
        'this_week' => $query->whereBetween($column, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
        
        // Jika filter "Bulan Ini"
        'this_month' => $query->whereYear($column, Carbon::now()->year)->whereMonth($column, Carbon::now()->month),
        
        // Jika tidak ada filter yang dipilih, jangan batasi tanggalnya.
        default => $query,
    };
}
```

### 3. Menghitung Metrik Kartu KPI (Agregat)
Sistem menggunakan `count()` untuk menghitung baris data di database.

```php
// File: app/Services/ReportService.php
public function getManagerDashboard(string $period = 'all', ...)
{
    // MENGHITUNG TOTAL LEADS
    // $this->applyPeriodFilter(...) akan otomatis menambahkan rumus tanggal ke dalam query Lead.
    // Hasil akhirnya adalah sebuah Angka Total (misal: 150 Lead).
    $totalLeads = $this->applyPeriodFilter(Lead::query(), 'created_at', $period, ...)->count();
    
    // MENGHITUNG TOTAL KEMENANGAN (WON)
    // Deal::won() adalah jalan pintas (Scope) untuk mencari transaksi berstatus "sukses".
    $wonDeals = $this->applyPeriodFilter(Deal::won(), 'closed_at', $period, ...)->count();

    // ...
}
```

### 4. Logika Leaderboard & Bahaya *Division by Zero*
Bagaimana sistem menyusun peringkat Sales terbaik bulan ini?

```php
// File: app/Services/ReportService.php
public function getTeamLeaderboard(...)
{
    $salesUsers = User::role('Sales')->where('is_active', true)->get();

    return $salesUsers->map(function ($user) use ($period, ...) {
        $wonDeals = $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->won(), ...)->count();
        $lostDeals = $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->lost(), ...)->count();
        
        // LOGIKA PENCEGAH ERROR (Persentase Kemenangan)
        // Kita tidak boleh membagi angka dengan 0 (Misal: 0/0). Itu akan membuat server meledak/crash.
        // Oleh karena itu kita cek dulu apakah (won + lost) jumlahnya LEBIH DARI 0.
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1) // Jika ada data, hitung rumusnya
            : 0; // Jika datanya kosong (0), biarkan nilai win_rate jadi 0%

        return [
            'name' => $user->name,
            'won' => $wonDeals,
            'win_rate' => $winRate,
        ];
    })->sortByDesc('won')->values(); // URUTKAN data dari peraih 'won' (kemenangan) tertinggi!
}
```

---

## 🎯 TAHAP 5: Pipeline Kanban & Forecast Member

**Tujuan:** Memahami visualisasi papan Kanban dan memprediksi masa depan (*Forecasting*) menggunakan hitungan probabilitas (bukan halu!).

### 1. Papan Kanban
Mengubah data tabel yang membosankan menjadi kolom-kolom proses (*Pipeline Stages*).

```php
// File: app/Services/ReportService.php
public function getPipelineData(): array
{
    // 1. Ambil daftar kolom (Misal: Prospecting, Proposal, Closing)
    $stages = PipelineStage::ordered()->get();
    $pipeline = [];

    foreach ($stages as $stage) {
        // 2. Ambil data pelanggan yang "tersangkut" (masih berstatus open) di kolom ini saja
        $deals = Deal::with(['lead', 'assignedUser'])
            ->where('pipeline_stage_id', $stage->id)
            ->where('status', 'open')
            ->orderBy('expected_close_date')
            ->get();

        // 3. Masukkan datanya ke dalam format kotak array
        $pipeline[] = [
            'name' => $stage->name,
            'probability' => $stage->probability, // Peluang (misal 50%)
            'deals' => $deals,
            'count' => $deals->count(), // Ada berapa orang di dalam kolom ini
        ];
    }
    return $pipeline;
}
```

### 2. Mesin Peramal Masa Depan (Forecasting)
**Aturan Baru Bisnis:** Sistem sudah tidak lagi memprediksi total *Uang* (Revenue), melainkan memprediksi total **Jumlah Orang** (Member Baru). 

Bagaimana mencegah Manager berasumsi palsu (terlalu optimis)? Kita gunakan `Weighted Value` (Bobot Probabilitas).
Misal: 1 calon pelanggan (Deal) di tahap Proposal hanya memiliki probabilitas 50%. Maka, ia tidak dihitung sebagai 1 orang penuh, melainkan hanya `1 * 50% = 0.5 orang`.

```php
// File: app/Models/Deal.php
public function getWeightedValueAttribute(): float
{
    $probability = $this->pipelineStage?->probability ?? 0;
    // Bobot untuk 1 calon member (1 Deal = 1 Orang).
    // Hasilnya adalah pecahan/desimal (misal 50/100 = 0.5)
    return $probability / 100;
}
```

Di dalam mesin laporannya:
```php
// File: app/Services/ReportService.php
public function getForecastData(): array
{
    // ... Looping berulang selama 12 bulan (masa lalu s.d. masa depan)
    
    // --- MASA LALU (Data Aktual) ---
    // Hitung jumlah ORANG (count) yang sudah resmi menang di bulan tsb.
    $actual = Deal::won()
        ->whereYear('closed_at', $date->year)
        ->whereMonth('closed_at', $date->month)
        ->count();

    // --- MASA DEPAN (Data Proyeksi) ---
    // Hitung calon target (orang) berdasarkan Bobot Probabilitas (weighted_value)
    $projected = Deal::open()
        ->whereYear('expected_close_date', $date->year)
        ->whereMonth('expected_close_date', $date->month)
        ->get()
        ->sum(fn($deal) => $deal->weighted_value); // Jika ada 2 org @50%, totalnya jadi = 1 Orang.
        
    // ...
}
```

---

## 🎯 TAHAP 6: Report Center (Pola Arsitektur yang Bersih)

**Tujuan:** Menjaga kebersihan file *Controller* dan mendemonstrasikan keahlian tingkat lanjut meracik query SQL.

### 1. Service Class Pattern (Koki di Balik Layar)
Bayangkan jika semua logika rumit di Tahap 4 & 5 di atas ditulis di dalam file `ReportController`. File itu pasti akan panjangnya ribuan baris!
Sebaliknya, inilah yang terjadi di aplikasi kita yang elegan:

```php
// File: app/Http/Controllers/Manager/ReportController.php
class ReportController extends Controller
{
    // Dependency Injection: Controller (Resepsionis) disuntikkan asisten Koki (ReportService)
    public function __construct(private ReportService $reportService) {}

    public function revenue()
    {
        // Controller cukup memberikan instruksi 1 baris, biarkan Koki yang bekerja keras
        // Catatan: Walaupun fungsinya bernama "revenue" (uang), namun di dalamnya sudah menghitung "Member".
        $revenueData = $this->reportService->getMemberAcquisitionReport(12);
        
        return view('manager.reports.revenue', compact('revenueData'));
    }
}
```

### 2. Analisis Kenapa Pelanggan Batal (SQL JOIN)
Mencari alasan paling umum kenapa transaksi gagal. Ini menggunakan manipulasi *Database/SQL* tingkat mahir.

```php
// File: app/Services/ReportService.php
public function getLostReasons(): array
{
    $totalLost = Deal::lost()->count(); // Hitung total kegagalan

    $reasons = Deal::lost()
        ->whereNotNull('lost_reason_id')
        
        // 1. GABUNGKAN (JOIN): Gabungkan tabel Deal dengan tabel lost_reasons 
        // untuk mengambil nama alasannya (Misal: "Fasilitas Kurang")
        ->join('lost_reasons', 'deals.lost_reason_id', '=', 'lost_reasons.id')
        
        // 2. KELOMPOKKAN (GROUP BY): Kelompokkan berdasarkan alasan yang sama (Mirip fungsi Pivot di Excel)
        ->selectRaw('lost_reasons.name, COUNT(*) as count')
        ->groupBy('lost_reasons.name')
        ->orderByDesc('count') // 3. URUTKAN: Dari yang paling sering muncul
        ->get()
        
        // 4. HITUNG PERSENTASE (Jika 30 org beralasan sama dari total 100 batal = 30%)
        ->map(fn($item) => [
            'name' => $item->name,
            'count' => $item->count,
            'percentage' => $totalLost > 0 ? round(($item->count / $totalLost) * 100, 1) : 0,
        ]);

    return ['reasons' => $reasons, 'total' => $totalLost];
}
```

---

## 🎯 TAHAP 7: Export Excel & Audit Log (CCTV Aplikasi)

**Tujuan:** Memberikan kemudahan pembuatan pelaporan fisik (Excel) dan memastikan keamanan data melalui jejak digital (Audit).

### 1. Rahasia Export Excel (Kontrak Interfaces)
Aplikasi menggunakan library pihak ketiga `Maatwebsite\Excel`. Agar kode rapi, library ini memaksa kita menandatangani **Kontrak Kerja (Interfaces)**.

```php
// File: app/Exports/RevenueExport.php
// "implements" berarti menandatangani kontrak. 
// Kontrak mewajibkan kita membuat fungsi array(), headings(), dan styles()
class RevenueExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct(ReportService $reportService) {
        $this->data = $reportService->getMemberAcquisitionReport(12); // Panggil Koki
    }

    // FUNGSI WAJIB KONTRAK 1: Menyusun isi baris data (FromArray)
    public function array(): array {
        return array_map(function ($month) {
            return [
                $month['month'],          // Kolom A (Misal: Jan 2026)
                $month['deals_count'],    // Kolom B (Misal: 45)
            ];
        }, $this->data['monthly']);
    }

    // FUNGSI WAJIB KONTRAK 2: Menyusun Judul Kolom (WithHeadings)
    public function headings(): array {
        return ['Bulan', 'Member Baru'];
    }

    // FUNGSI WAJIB KONTRAK 3: Menghias Excel (WithStyles)
    public function styles(Worksheet $sheet): array {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]], // Jadikan tulisan baris ke-1 (Header) tebal!
        ];
    }
}
```
Untuk mengunduh, *Controller* cukup mengeksekusi satu baris pendek:
`Excel::download(new RevenueExport($this->reportService), "laporan.xlsx")`

### 2. Audit Log (CCTV Rahasia)
Sistem memiliki rekaman jejak digital (Audit Trail). Setiap kali data disimpan/dihapus oleh karyawan, sistem otomatis merekam: Siapa, Apa, di Mana, dan Kapan hal itu dilakukan.
Manager bisa mengakses riwayat CCTV ini di *AuditLogController*.

```php
// File: app/Http/Controllers/Manager/AuditLogController.php
class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Manager menggunakan Filter di web (Mencari berdasarkan Nama Karyawan atau Modul tertentu)
        // Request dilempar ke ReportService untuk dicari dari database.
        $logs = $this->reportService->getAuditLogs(
            $request->only(['action', 'user_id', 'module'])
        );
        
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('manager.audit-logs.index', compact('logs', 'users'));
    }
}
```
*(Dengan fitur ini, tidak ada karyawan yang bisa menghindari tanggung jawab jika ada data yang tak sengaja terhapus!)*

---
> 💡 **Kata-kata dari Coach:** 
> Arsitektur yang kamu lihat di Role Manager ini (*Agregasi Data, Forecast Probabilitas, Service Pattern, SQL Manipulation, Exporting*) adalah materi yang diuji pada programmer Senior Backend. Pastikan kamu selalu membaca kembali catatan ini untuk menyegarkan ingatanmu ya! 🚀
