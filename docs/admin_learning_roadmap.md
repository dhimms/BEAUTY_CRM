# 🎓 Coaching Plan: Belajar Fitur CS & Manager Beauty CRM (Maximized)

> Coach: Antigravity | Murid: Alvinn | Proyek: Beauty CRM
> Setiap kali kamu mau lanjut belajar, bilang "Coach, lanjut ke [Tahap X]" dan aku akan membedah kode bareng kamu.

---

## 📍 Status Belajarmu Sekarang

| Materi | Status |
|---|---|
| PHP Dasar (Variabel, Array, Function, Conditional) | ✅ Selesai |
| MVC + Laravel Setup (Routing, Controller, Blade) | ✅ Selesai |
| Database (Migration, Seeder, Eloquent, CRUD) | ✅ Selesai |
| Authentication (Login, Register, Middleware, Role) | ✅ Selesai |
| **Fitur CS & Manager** | ⏳ Mulai Sekarang |

---

## 🗺️ Peta Belajar CS & Manager (7 Tahap Terlengkap)

```
[Tahap 1] → [Tahap 2] → [Tahap 3] → [Tahap 4] → [Tahap 5] → [Tahap 6] → [Tahap 7]
 Route &    Customer &  Aktivitas   Dashboard   Pipeline &  Report &   Export &
Middleware   Follow-up   Logging    Team Perf.   Forecast   Analysis   Audit Log
 (Keduanya)    (CS)        (CS)     (Manager)   (Manager)   (Manager)  (Manager)
```

---

## TAHAP 1 — Memahami Gerbang CS & Manager (Route & Middleware)
> 🎯 Tujuan: Paham pemisahan rute untuk Role Customer Service dan Manager

### File yang dibedah:
- `routes/cs.php` & `routes/manager.php` → Route grup khusus
- `routes/web.php` → Bagaimana file routing di-load
- `app/Models/User.php` → Method `isCS()` dan `isManager()`

### Konsep yang akan kamu pelajari:
- Memisahkan file routing berdasarkan role.
- Middleware otorisasi Spatie (`role:Customer Service|Manager`).

---

## TAHAP 2 — Manajemen Customer & Follow-up (Panel CS)
> 🎯 Tujuan: Memahami tugas utama CS dalam mengelola database pelanggan dan penjadwalan

### File yang dibedah:
- `app/Http/Controllers/CS/CustomerController.php`
- `app/Http/Controllers/CS/FollowUpController.php`

### Konsep yang akan kamu pelajari:
- Eager loading relasi yang kompleks.
- Form submission untuk memanipulasi data Customer (termasuk tagihan/info spesifik).
- Penjadwalan Follow-up: Sistem pengingat CS berdasarkan tanggal jatuh tempo.

---

## TAHAP 3 — Activity Logging (Polymorphic) (Panel CS)
> 🎯 Tujuan: Memahami cara sistem mencatat "Catatan, Telepon, Email, WhatsApp" ke satu tempat

### File yang dibedah:
- `app/Models/Activity.php`
- `app/Http/Controllers/CS/ActivityController.php`

### Konsep yang akan kamu pelajari:
- **Polymorphic Relationship**: Konsep database tingkat lanjut di mana tabel `activities` bisa diikat ke `Customer`, `Lead`, atau entitas lainnya tanpa harus membuat banyak tabel.
- Menampilkan *Timeline* aktivitas interaksi di halaman Customer.

---

## TAHAP 4 — Dashboard & Team Performance (Panel Manager)
> 🎯 Tujuan: Mengerti cara Manager memantau performa harian dari tim

### File yang dibedah:
- `app/Http/Controllers/Manager/DashboardController.php`
- `app/Http/Controllers/Manager/TeamPerformanceController.php`

### Konsep yang akan kamu pelajari:
- Eloquent Aggregates (`count()`, `sum()`) dengan rentang tanggal khusus (`whereMonth`, `whereYear`).
- Leaderboard logic: Bagaimana meranking Salesperson berdasarkan pencapaian target **Member Baru** (bukan lagi target nominal uang/revenue).

---

## TAHAP 5 — Pipeline Kanban & Member Forecast (Panel Manager)
> 🎯 Tujuan: Memahami cara Manager melihat keseluruhan transaksi (Kanban) & Prediksi target

### File yang dibedah:
- `app/Http/Controllers/Manager/PipelineController.php`
- `app/Http/Controllers/Manager/ForecastController.php`

### Konsep yang akan kamu pelajari:
- **Kanban Board Logic**: Memecah data `Deals` berdasarkan `PipelineStage` secara dinamis.
- **Forecasting**: Menghitung probabilitas penambahan member di masa depan berdasarkan tahapan pipeline saat ini.

---

## TAHAP 6 — Report Center & Pipeline Analysis (Panel Manager)
> 🎯 Tujuan: Paham pola "Service Class" untuk memisahkan logika matematika/statistik pelaporan

### File yang dibedah:
- `app/Services/ReportService.php` (Pusat logika perhitungan laporan)
- `app/Http/Controllers/Manager/ReportController.php`

### Konsep yang akan kamu pelajari:
- **Service Class Pattern**: Jangan membebani Controller! Pindahkan kalkulasi berat ke service.
- *Dependency Injection* di Controller.
- Membuat Chart Data (Visualisasi tren *Member Acquisition* selama berbulan-bulan, menggantikan laporan *Revenue* lama).

---

## TAHAP 7 — Export Laporan Excel & Audit Log (Panel Manager)
> 🎯 Tujuan: Belajar membuat fitur unduh laporan (`.xlsx`) & melacak jejak perubahan (Audit)

### File yang dibedah:
- `app/Exports/SalesPerformanceExport.php` & `RevenueExport.php` (Kini diekspor sebagai Laporan Member)
- `app/Http/Controllers/Manager/AuditLogController.php`

### Konsep yang akan kamu pelajari:
- Mapping data query Eloquent menjadi kolom dan baris di Excel.
- **Audit Trails**: Bagaimana Manager melihat siapa mengubah apa, dan kapan (Log tracking).

---

## 📊 Tracker Progress Kamu

| Tahap | Topik | Status |
|---|---|---| 
| Tahap 1 | Route & Middleware CS & Manager | ✅ Selesai |
| Tahap 2 | Customer & Follow-up (CS) | ✅ Selesai |
| Tahap 3 | Activity Logging Polymorphic (CS) | ⬜ Belum |
| Tahap 4 | Dashboard & Team Perf (Manager) | ⬜ Belum |
| Tahap 5 | Pipeline Kanban & Forecast (Manager) | ⬜ Belum | 
| Tahap 6 | Report & Pipeline Analysis (Manager) | ⬜ Belum | 
| Tahap 7 | Export Laporan Excel & Audit Log (Manager) | ⬜ Belum |   

---

## 💬 Cara Pakai Coaching Ini

Setiap kali kamu mau belajar, kirim pesan ke aku dengan format:
> **"Coach, aku mau mulai Tahap [X]"** → aku akan bedah kodenya bareng kamu step by step
> **"Coach, aku stuck di [bagian ini]"** → aku akan jelaskan spesifik bagian yang bikin bingung
> **"Coach, update progress, aku sudah selesai Tahap [X]"** → aku akan update tracker ini

---

> 💡 **Tips dari Coach:** Pelajari *Polymorphic Relationship* (Tahap 3) dan *Service Class* (Tahap 6) dengan sungguh-sungguh. Itu adalah dua "jurus rahasia" yang akan mengangkat skill back-end Laravel-mu ke tingkat mahir!
