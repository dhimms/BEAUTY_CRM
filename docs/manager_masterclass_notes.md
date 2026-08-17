# 📘 Buku Pintar: Masterclass Back-End Role Manager

Dokumen ini adalah rangkuman komprehensif dari materi *coaching* khusus fitur **Manager**. Sebagai Manager, tugas utama aplikasi adalah menyajikan rangkuman angka (*Helicopter View*), visualisasi metrik, dan melacak kinerja tim. 

Catatan ini dirancang sangat detail, lengkap dengan potongan kode dan penjelasan baris per baris beserta alurnya.

---

## 🎯 TAHAP 4: Dashboard & Team Performance (Filter Waktu & Agregat)

**Tujuan:** Memahami bagaimana sistem memfilter data berdasarkan waktu secara dinamis, melakukan perhitungan agregat (Total), dan menyusun Papan Peringkat (Leaderboard).

### 1. Resepsionis Manager (DashboardController)
Ketika Manager membuka halaman dashboard dan memilih filter waktu (misal: "Bulan Ini"), permintaan akan ditangkap di sini.

```php
// File: app/Http/Controllers/Manager/DashboardController.php
class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Menerima Filter Tanggal: Menangkap filter 'period' dari URL (Misal: ?period=this_month)
        // Jika tidak ada di URL, maka nilai default-nya adalah 'all' (Semua Waktu)
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // 2. Mendelegasikan Tugas: Resepsionis melempar data filter ke Koki (ReportService)
        $data = $this->reportService->getManagerDashboard($period, $startDate, $endDate);
        
        // 3. Mengembalikan Hasil: Mengirim data yang sudah dimasak ke tampilan layar (Blade)
        $data['period'] = $period;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        
        return view('manager.dashboard.index', $data);
    }
}
```

### 2. Senjata Rahasia Koki: Filter Waktu Otomatis
Agar kita tidak perlu menulis ulang logika filter tanggal untuk setiap kotak metrik (Total Lead, Total Member, dll), kita membuat satu fungsi khusus pembantu (`applyPeriodFilter`).

```php
// File: app/Services/ReportService.php (Fungsi Bantuan)
private function applyPeriodFilter($query, string $column, string $period, ...)
{
    // Fitur 'match' PHP 8. Mengecek apa isi filter yang dipilih Manager.
    return match ($period) {
        // Jika Manager memilih "Hari Ini", otomatis menempelkan instruksi SQL: 'where date = hari_ini'
        'today' => $query->whereDate($column, Carbon::today()),
        
        // Jika Manager memilih "Minggu Ini", otomatis mencari di antara hari Senin s/d Minggu
        'this_week' => $query->whereBetween($column, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
        
        // Jika Manager memilih "Bulan Ini", otomatis mencari Tahun dan Bulan yang sama dengan sekarang
        'this_month' => $query->whereYear($column, Carbon::now()->year)->whereMonth($column, Carbon::now()->month),
        
        // Jika default ('all'), kembalikan query mentahnya tanpa filter tanggal.
        default => $query,
    };
}
```

### 3. Menghitung Metrik Kartu KPI (Agregat)
Koki menggunakan senjata rahasia tadi untuk menghitung total angka secara efisien.

```php
// File: app/Services/ReportService.php
public function getManagerDashboard(string $period = 'all', ...)
{
    // MENGHITUNG TOTAL LEADS (Orang Baru)
    // - Lead::query() mengambil semua data Lead
    // - applyPeriodFilter akan menambahkan filter tanggal (Misal: Hanya bulan ini)
    // - count() akan menghitung total jumlah orangnya di database
    $totalLeads = $this->applyPeriodFilter(Lead::query(), 'created_at', $period, ...)->count();
    
    // MENGHITUNG TOTAL KEMENANGAN (WON)
    // - Deal::won() adalah jalan pintas (Local Scope) untuk memfilter transaksi yang hanya berstatus 'won' (sukses)
    // - count() menghitung jumlah keberhasilannya
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
    // 1. Ambil daftar semua user yang jabatannya adalah 'Sales'
    $salesUsers = User::role('Sales')->where('is_active', true)->get();

    // 2. Looping (Perulangan) untuk menghitung statistik masing-masing Sales
    return $salesUsers->map(function ($user) use ($period, ...) {
        
        // Hitung total kemenangan (won) dan kekalahan (lost) khusus untuk Sales ini ($user->id)
        $wonDeals = $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->won(), ...)->count();
        $lostDeals = $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->lost(), ...)->count();
        
        // 3. LOGIKA PENCEGAH ERROR (Persentase Kemenangan)
        // Rumus Persentase = (Menang / Total Transaksi) * 100
        // BAHAYA: Jika Sales baru belum punya data (won=0, lost=0), maka Total = 0.
        // Komputer TIDAK BOLEH membagi angka dengan 0 (Division by Zero). Itu akan membuat server meledak/crash.
        // Solusi: Kita cek dulu apakah (won + lost) jumlahnya LEBIH DARI 0!
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1) // Jika ada data > 0, jalankan rumus matematika
            : 0; // Jika belum ada data (0), langsung berikan nilai 0% agar aman.

        return [
            'name' => $user->name,
            'won' => $wonDeals, // Simpan total kemenangan
            'win_rate' => $winRate,
        ];
        
    // 4. Tahap Akhir: URUTKAN array data dari peraih 'won' (kemenangan) tertinggi ke terendah!
    })->sortByDesc('won')->values(); 
}
```

---

## 🎯 TAHAP 5: Pipeline Kanban & Forecast Member

**Tujuan:** Memahami visualisasi papan Kanban dan memprediksi masa depan (*Forecasting*) menggunakan hitungan probabilitas.

### 1. Papan Kanban
Mengubah data tabel yang membosankan menjadi kolom-kolom proses (*Pipeline Stages*).

```php
// File: app/Services/ReportService.php
// Fitur kanban board (Mengelompokkan transaksi berdasarkan tahapannya)
public function getPipelineData(): array
{
    // 1. Mengambil semua "Kolom Status" (PipelineStage) (Misal: Prospecting, Proposal, Closing)
    $stages = PipelineStage::ordered()->get();
    
    $pipeline = [];
    
    // 2. Lakukan perulangan untuk mengecek isi setiap kolom
    foreach ($stages as $stage) {
        
        // 3. Ambil Deal (transaksi) yang nyangkut di kolom status ini
        $deals = Deal::with(['lead', 'assignedUser'])
            ->where('pipeline_stage_id', $stage->id)
            ->where('status', 'open') // Hanya yang masih berstatus terbuka/negosiasi
            ->orderBy('expected_close_date')
            ->get()
            // 4. Ubah format datanya agar rapi dan mudah dibaca oleh tampilan Web
            ->map(fn($deal) => [
                'id' => $deal->id,
                'name' => $deal->name,
                // ...
            ]);

        // 5. Masukkan data transaksi yang sudah difilter tadi ke dalam "Kotak Kolom" (Array)
        $pipeline[] = [
            'name' => $stage->name,
            'probability' => $stage->probability, // Peluang menang (Misal: 'Closing' probabilitasnya 90%)
            'deals' => $deals, // Daftarnya
            'count' => $deals->count(), // Total ada berapa orang di kolom ini
        ];
    }
    
    return $pipeline;
}
```

### 2. Mesin Peramal Masa Depan (Forecasting)
**Aturan Baru Bisnis:** Sistem sudah tidak lagi memprediksi total *Uang* (Revenue), melainkan memprediksi total **Jumlah Orang** (Member Baru). 

Bagaimana mencegah Manager berasumsi palsu (terlalu optimis)? Kita gunakan `Weighted Value` (Bobot Probabilitas).

```php
// File: app/Models/Deal.php
// Fungsi untuk menghitung nilai bobot probabilitas dari 1 transaksi
public function getWeightedValueAttribute(): float
{
    // Ambil persentase kemungkinan menang dari tahapan saat ini (Misal: Tahap Proposal = 50)
    $probability = $this->pipelineStage?->probability ?? 0;
    
    // Bobot untuk 1 calon member (1 Deal = 1 Orang).
    // Hasilnya adalah pecahan/desimal (misal 50/100 = 0.5 Orang)
    return $probability / 100;
}
```

Di dalam mesin laporannya:
```php
// File: app/Services/ReportService.php
// Function untuk meramal member baru berdasarkan data yang masuk
public function getForecastData(): array
{
    // Looping 12 Bulan (5 bulan lalu, sekarang, 6 bulan ke depan)
    for ($i = 5; $i >= -6; $i--) {
        $date = $now->copy()->subMonths($i);

        // --- 1. DATA AKTUAL (Masa Lalu) ---
        // Hitung JUMLAH ORANG (count) yang SUDAH BERHASIL (won) gabung pada bulan tersebut.
        $actual = Deal::won()
            ->whereYear('closed_at', $date->year)
            ->whereMonth('closed_at', $date->month)
            ->count(); // Menggunakan count() karena kita menghitung orang, bukan uang.

        // --- 2. DATA PROYEKSI (Masa Depan) ---
        // Hitung prediksi member baru dari transaksi yg masih gantung (open) 
        // dan rencana closingnya adalah di bulan tersebut.
        $projected = Deal::open()
            ->whereYear('expected_close_date', $date->year)
            ->whereMonth('expected_close_date', $date->month)
            ->get()
            // Gunakan rumus matematika SUM untuk menjumlahkan semua Bobot Probabilitas (Weighted Value).
            // Contoh: Jika ada 2 pelanggan di tahap Proposal (@50%), maka Prediksi = 0.5 + 0.5 = 1 Orang.
            ->sum(fn($deal) => $deal->weighted_value); 
            
        // ...
}
```

---

## 🎯 TAHAP 6: Report Center (Pola Arsitektur yang Bersih)

**Tujuan:** Menjaga kebersihan file *Controller* (Service Pattern) dan mendemonstrasikan keahlian tingkat lanjut meracik query SQL.

### 1. Service Class Pattern (Koki di Balik Layar)
```php
// File: app/Http/Controllers/Manager/ReportController.php
class ReportController extends Controller
{
    // Ini disebut Dependency Injection. 
    // Kita menyuntikkan Service (Koki) ke dalam Controller (Resepsionis).
    // Tujuannya agar Controller tidak memuat perhitungan matematika/database yang berat.
    public function __construct(private ReportService $reportService) {}

    public function revenue()
    {
        // Meskipun nama fungsinya "revenue", kita sudah mengubah targetnya menjadi Member Baru.
        // Di sini Controller cukup melempar tugas ke Service untuk menghitung tren selama 12 bulan terakhir.
        $revenueData = $this->reportService->getMemberAcquisitionReport(12);
        
        // Hasil masakan Koki dikirim ke layar (Blade View)
        return view('manager.reports.revenue', compact('revenueData'));
    }
}
```

### 2. Analisis Kenapa Pelanggan Batal (SQL JOIN)
Mencari alasan paling umum kenapa transaksi gagal. Ini menggunakan manipulasi *Database/SQL* tingkat mahir.

```php
// File: app/Services/ReportService.php
// Tahap 6: Analisis Kegagalan (Kenapa Pelanggan Batal?)
public function getLostReasons(): array
{
    // Hitung total keseluruhan orang yang gagal (Misal: 100 orang)
    $totalLost = Deal::lost()->count(); 

    $reasons = Deal::lost()
        // Pastikan transaksi ini memang memiliki ID alasan batal
        ->whereNotNull('lost_reason_id')
        
        // 1. GABUNGKAN (JOIN): Gabungkan tabel Deal dengan tabel lost_reasons 
        // untuk mengambil nama alasannya dari database (Misal: ID 2 = "Kemahalan")
        ->join('lost_reasons', 'deals.lost_reason_id', '=', 'lost_reasons.id')
        
        // 2. KELOMPOKKAN (GROUP BY): Kelompokkan berdasarkan nama alasan (Mirip fitur Pivot Table di Excel)
        ->selectRaw('lost_reasons.name, COUNT(*) as count')
        ->groupBy('lost_reasons.name')
        
        // 3. URUTKAN: Dari alasan yang paling sering muncul (Highest count)
        ->orderByDesc('count') 
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
// Class ini menandatangani "Kontrak Kerja" (Interfaces) dari library Maatwebsite\Excel
// Kontrak ini mewajibkan class memiliki fungsi-fungsi khusus: array(), headings(), styles()
class RevenueExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct(ReportService $reportService) {
        // Saat diekspor, ambil data laporan 12 bulan terakhir
        $this->data = $reportService->getMemberAcquisitionReport(12); 
    }

    // FUNGSI WAJIB KONTRAK 1: KONTRAK ISI DATA (FromArray)
    // Fungsi ini wajib mengembalikan susunan data per baris di Excel
    public function array(): array {
        return array_map(function ($month) {
            return [
                $month['month'],          // Kolom A Excel (Misal: Jan 2026)
                $month['deals_count'],    // Kolom B Excel (Misal: 45)
            ];
        }, $this->data['monthly']); // Lakukan perulangan untuk setiap bulannya
    }

    // FUNGSI WAJIB KONTRAK 2: KONTRAK JUDUL KOLOM / HEADER (WithHeadings)
    // Baris pertama di Excel (A1, B1) akan diisi oleh tulisan ini
    public function headings(): array {
        return ['Bulan', 'Member Baru'];
    }

    // FUNGSI WAJIB KONTRAK 3: KONTRAK MENGHIAS EXCEL (WithStyles)
    // Digunakan untuk menebalkan (Bold) baris ke-1 (Header)
    public function styles(Worksheet $sheet): array {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]], 
        ];
    }
}
```

### 2. Audit Log (CCTV Rahasia)
Sistem memiliki rekaman jejak digital (Audit Trail). Setiap kali data disimpan/dihapus oleh karyawan, sistem otomatis merekam: Siapa, Apa, di Mana, dan Kapan hal itu dilakukan.

```php
// File: app/Http/Controllers/Manager/AuditLogController.php
class AuditLogController extends Controller
{
    // Tahap 7: Audit Log (CCTV Aplikasi)
    // Fitur ini digunakan Manager untuk melacak "Jejak Digital" (Siapa mengubah apa dan kapan)
    public function index(Request $request)
    {
        // Manager menggunakan Filter di web (Mencari berdasarkan Aksi / Nama Karyawan / Modul tertentu)
        // Request dilempar ke ReportService untuk dicari dari database.
        $logs = $this->reportService->getAuditLogs(
            $request->only(['action', 'user_id', 'module'])
        );
        
        // Mengambil daftar karyawan untuk ditampilkan di *dropdown* filter pencarian web
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('manager.audit-logs.index', compact('logs', 'users'));
    }
}
```

---
> 💡 **Kata-kata dari Coach:** 
> Arsitektur yang kamu lihat di Role Manager ini (*Agregasi Data, Forecast Probabilitas, Service Pattern, SQL Manipulation, Exporting*) adalah materi yang diuji pada programmer Senior Backend. Pastikan kamu selalu membaca kembali catatan ini untuk menyegarkan ingatanmu ya! 🚀
