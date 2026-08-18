# 🎓 Coaching Plan: Belajar Fitur Admin Beauty CRM

> Coach: Antigravity | Murid: Alvinn | Proyek: Beauty CRM
> Setiap kali kamu mau lanjut belajar, bilang "Coach, lanjut ke [Tahap X]" dan aku akan membedah kode bareng kamu.

---

## 📍 Status Belajarmu Sekarang

| Materi | Status |
|---|---|
| PHP Dasar (Variabel, Array, Function, Conditional) | ✅ Selesai |
| MVC + Laravel Setup (Routing, Controller, Blade) | ✅ Selesai |
| Database (Migration, Seeder, Eloquent, CRUD) | ✅ Selesai |
| CRUD Lanjutan (Update, Delete, Form, Validation) | ✅ Selesai |
| Authentication (Login, Register, Middleware, Role) | ✅ Selesai |
| File Upload + Relationship + Pagination | ✅ Selesai |
| **Fitur Admin** | ⏳ Mulai Sekarang |

---

## 🗺️ Peta Belajar Admin (6 Tahap)

```
[Tahap 1] → [Tahap 2] → [Tahap 3] → [Tahap 4] → [Tahap 5] → [Tahap 6]
  Route &      CRUD       CRUD +       User         Dashboard    Import/
 Middleware    Basic     Business     Mgmt +       & Statistik   Export
              (Model)     Logic       Spatie
```

---

## TAHAP 1 — Memahami Gerbang Admin (Route & Middleware)
> 🎯 Tujuan: Paham kenapa halaman admin tidak bisa diakses sembarangan

### File yang dibedah:
- [`routes/web.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/routes/web.php) → Lihat bagaimana route di-grup dengan `middleware(['auth', 'active.user'])`
- [`routes/admin.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/routes/admin.php) → Lihat middleware `role:Admin` membungkus semua route admin
- [`app/Models/User.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Models/User.php) → Fokus pada method `isAdmin()` dan trait `HasRoles`

### Konsep yang akan kamu pelajari:
- Middleware chaining (`auth` + `active.user` + `role:Admin`)
- Spatie Permission: apa itu role dan bagaimana ia dicek
- Route prefix & name prefix (`admin.*`)
- Route Resource (shortcut untuk 7 CRUD route sekaligus)

### Tanda kamu sudah paham ✅
- Bisa jelaskan kenapa user Sales tidak bisa akses URL `/admin/users`
- Bisa jelaskan apa bedanya `Route::resource` vs `Route::get`

---

## TAHAP 2 — CRUD Admin Paling Sederhana (Lost Reason)
> 🎯 Tujuan: Lihat bahwa CRUD admin = CRUD biasa, hanya tempatnya berbeda

### File yang dibedah:
- [`app/Models/LostReason.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Models/LostReason.php) → Model simpel dengan 1 relasi
- [`app/Http/Requests/Admin/LostReasonRequest.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Requests/Admin/LostReasonRequest.php) → Form Request (validasi yang dipindah ke class tersendiri)
- [`app/Http/Controllers/Admin/LostReasonController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/LostReasonController.php) → Controller CRUD lengkap

### Konsep yang akan kamu pelajari:
- **Form Request Class** — cara memisahkan validasi dari controller agar lebih rapi
- `withCount('deals')` — cara menghitung relasi di query tanpa `foreach`
- Guard delete: `if ($lostReason->deals()->exists())` — bisnis logika sebelum hapus data

### Tanda kamu sudah paham ✅
- Bisa jelaskan perbedaan validasi di controller vs Form Request class
- Bisa baca kenapa `LostReason` tidak bisa dihapus jika masih ada Deal yang pakai

---

## TAHAP 3 — CRUD + Toggle Status (Lead Source & Pipeline Stage)
> 🎯 Tujuan: Paham CRUD yang memiliki fitur tambahan di luar standar

### File yang dibedah:
- [`app/Http/Controllers/Admin/LeadSourceController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/LeadSourceController.php) → Fokus pada method `toggle()`
- [`app/Http/Controllers/Admin/PipelineStageController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/PipelineStageController.php) → Fokus pada method `reorder()`
- [`routes/admin.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/routes/admin.php) line 27 & 31 → Route `patch` di luar resource

### Konsep yang akan kamu pelajari:
- **JSON Response** — `response()->json()` untuk komunikasi dengan JavaScript (AJAX)
- **Non-resource route** — cara tambah route custom di luar 7 route resource standar
- `$request->boolean()` — cara aman ambil nilai checkbox dari form
- Array update massal dengan `foreach` pada reorder

### Tanda kamu sudah paham ✅
- Bisa jelaskan kapan controller harus return JSON vs return redirect
- Bisa baca alur kerja saat user klik tombol toggle aktif/nonaktif di halaman

---

## TAHAP 4 — User Management + Spatie Permission (Paling Penting!)
> 🎯 Tujuan: Paham cara admin kelola user beserta role-nya

### File yang dibedah:
- [`app/Http/Controllers/Admin/UserController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/UserController.php) → Controller paling kompleks, baca dari atas ke bawah
- [`app/Http/Requests/Admin/UserRequest.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Requests/Admin/UserRequest.php) → Validasi user termasuk password opsional saat edit

### Konsep yang akan kamu pelajari:
- `->when()` — cara filter query secara kondisional (search, filter role, status)
- `->with('roles')` — eager loading relasi untuk mencegah N+1 query problem
- `Hash::make()` — cara hash password dengan benar
- `Storage::disk('public')->delete()` — hapus file lama saat upload avatar baru
- `$user->assignRole()` vs `$user->syncRoles()` — bedanya assign pertama kali vs update role
- **Business rule di controller**: Manager tidak bisa diedit/dihapus

### Tanda kamu sudah paham ✅
- Bisa jelaskan kenapa `syncRoles` dipakai di `update()`, bukan `assignRole()`
- Bisa jelaskan N+1 problem dan kenapa `->with('roles')` penting
- Bisa tracing alur: User klik "Hapus" → controller cek apakah itu dirinya sendiri → cek apakah Manager → baru dihapus

---

## TAHAP 5 — Dashboard & Statistik (Paling Seru!)
> 🎯 Tujuan: Paham cara admin melihat ringkasan data seluruh sistem

### File yang dibedah:
- [`app/Http/Controllers/Admin/DashboardController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/DashboardController.php) → Baca bagian per bagian (KPI → Chart Data → Top Sales)

### Konsep yang akan kamu pelajari:
- `whereMonth()` + `whereYear()` — filter data berdasarkan bulan dan tahun
- `count()` vs `sum()` — menghitung jumlah record vs menjumlahkan nilai kolom
- Kalkulasi trend persentase dengan arrow function `fn($curr, $prev) => ...`
- `DB::raw()` — menulis query SQL mentah di dalam Eloquent
- `->withCount()` dengan kondisi (constrained eager loading)
- `->withSum()` — menjumlahkan nilai relasi sekaligus saat query

### Tanda kamu sudah paham ✅
- Bisa jelaskan cara kerja perhitungan KPI trend (naik/turun vs bulan lalu)
- Bisa baca query `$topSales` dan jelaskan apa yang dihasilkan

---

## TAHAP 6 — Audit Log & Import/Export (Fitur Lanjutan)
> 🎯 Tujuan: Paham dua fitur admin tingkat lanjut

### File yang dibedah:
- [`app/Http/Controllers/Admin/AuditLogController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/AuditLogController.php) ← **Ini yang lagi kamu buka sekarang!**
- [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Models/AuditLog.php) → Lihat scope filter yang dipanggil di controller
- [`app/Http/Controllers/Admin/ImportExportController.php`](file:///c:/xampp/htdocs/beauty_crm/main_crm/BEAUTY_CRM/app/Http/Controllers/Admin/ImportExportController.php) → Pola Service Class

### Konsep yang akan kamu pelajari:
- **Local Query Scope** — `filterAction()`, `filterUser()`, `filterModule()` di model
- `->withQueryString()` — cara pertahankan parameter filter di URL saat paginasi
- **Service Class** — cara memisahkan logika bisnis kompleks (import/export) ke class tersendiri
- `mimes:xlsx,xls,csv` — validasi tipe file upload secara spesifik

### Tanda kamu sudah paham ✅
- Bisa baca `AuditLogController` dan jelaskan setiap baris query-nya
- Bisa jelaskan kenapa Import/Export logic ditaruh di `ImportExportService`, bukan langsung di controller

---

## 📊 Tracker Progress Kamu

| Tahap | Topik | Status |
|---|---|---| 
| Tahap 1 | Route & Middleware Admin | ✅ Selesai |
| Tahap 2 | CRUD Basic (LostReason) | ✅ Selesai |
| Tahap 3 | CRUD + Toggle (LeadSource, Pipeline) | ✅ Selesai | 
| Tahap 4 | User Management + Spatie | ✅ Selesai | 
| Tahap 5 | Dashboard & Statistik | ✅ Selesai |  
| Tahap 6 | Audit Log + Import/Export | ⏳ Sedang Proses |   

--- 

## 💬 Cara Pakai Coaching Ini

Setiap kali kamu mau belajar, kirim pesan ke aku dengan format:
> **"Coach, aku mau mulai Tahap [X]"** → aku akan bedah kodenya bareng kamu step by step
> **"Coach, aku stuck di [bagian ini]"** → aku akan jelaskan spesifik bagian yang bikin bingung
> **"Coach, update progress, aku sudah selesai Tahap [X]"** → aku akan update tracker ini

---

> 💡 **Tips dari Coach:** Jangan langsung lompat ke Tahap 5 atau 6. Tahap 1–3 membangun fondasi yang bikin Tahap 4 dan 5 jadi gampang. Urutan ini penting!
