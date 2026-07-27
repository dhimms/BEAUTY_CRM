<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/TailwindCSS-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="TailwindCSS 4">
  <img src="https://img.shields.io/badge/Vite-7.0-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white" alt="Alpine.js 3">
</p>

<h1 align="center">💄 Beauty CRM</h1>

<p align="center">
  <strong>Sistem CRM (Customer Relationship Management) khusus untuk industri kecantikan (Beauty Industry).</strong><br>
  Dibangun dengan Laravel 12, Tailwind CSS 4, Alpine.js, dan Vite 7.
</p>

<p align="center">
  <a href="#-tentang-projek">Tentang</a> •
  <a href="#-fitur-utama">Fitur</a> •
  <a href="#-tech-stack">Tech Stack</a> •
  <a href="#-persyaratan-sistem">Persyaratan</a> •
  <a href="#-panduan-instalasi">Instalasi</a> •
  <a href="#-akun-default">Akun Default</a> •
  <a href="#-struktur-projek">Struktur Projek</a>
</p>

---

## 📖 Tentang Projek

**Beauty CRM** adalah sistem manajemen hubungan pelanggan (CRM) yang dirancang khusus untuk bisnis di industri kecantikan seperti salon, klinik kecantikan, beauty studio, dan spa. Sistem ini membantu tim Sales, Customer Service, dan Manager dalam mengelola leads, deals, pelanggan, tiket layanan, dan menganalisis performa bisnis melalui dashboard yang komprehensif.

### Mengapa Beauty CRM?

- 🎯 **Dirancang khusus** untuk alur kerja bisnis kecantikan
- 👥 **Multi-role system** — Admin, Sales, Customer Service, dan Manager memiliki dashboard & akses masing-masing
- 📊 **Pipeline Management** — Kelola deal dari prospecting hingga closing dengan drag & drop
- 📈 **Laporan & Analisis** — Performa sales, revenue, analisis pipeline, dan banyak lagi
- 🔔 **Notifikasi Real-time** — Pemberitahuan otomatis saat lead di-assign atau deal berhasil closing
- 📥 **Import/Export Excel** — Import lead secara massal dan export laporan ke Excel
- 🔍 **Audit Trail** — Setiap perubahan data tercatat untuk keperluan audit

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Otorisasi
- Login berbasis role dengan [Spatie Laravel Permission](https://github.com/spatie/laravel-permission)
- 4 Role: **Admin**, **Sales**, **Customer Service**, **Manager**
- Middleware perlindungan route per role
- Pengecekan status user aktif/non-aktif
- 12 jenis permission granular

### 👨‍💼 Panel Admin
- **Dashboard** — Statistik keseluruhan (total leads, deals, revenue, conversion rate)
- **User Management** — CRUD user, assign role, aktifkan/nonaktifkan user
- **Lead Management** — CRUD lead, assign ke sales, filter & pencarian
- **Deal Management** — Lihat & kelola semua deal
- **Customer Management** — Lihat data seluruh pelanggan
- **Pengaturan Pipeline** — Kelola tahapan pipeline (drag & drop reorder)
- **Lead Sources** — Kelola sumber lead (Website, WhatsApp, Email, Referral, Social Media, Walk-in)
- **Lost Reasons** — Kelola alasan deal gagal
- **Import/Export** — Import lead via Excel, export data, download template
- **Audit Logs** — Lacak semua perubahan data dalam sistem
- **Settings** — Konfigurasi aplikasi

### 💼 Panel Sales
- **Dashboard** — Statistik personal (leads, deals, target, aktivitas hari ini)
- **Lead Management** — Kelola lead yang di-assign, buat lead baru
- **Lead Qualification** — Kualifikasi lead (Potensial, Tidak Potensial, Tidak Cocok)
- **Lead Conversion** — Konversi lead menjadi deal/customer
- **Deal Pipeline** — Visualisasi pipeline dengan tahapan deal
- **Deal Management** — Buat, update, pindahkan stage, close (Won/Lost)
- **Activity Tracking** — Catat aktivitas (Telepon, WhatsApp, Email, Meeting, Catatan)
- **Follow-up Management** — Jadwalkan & kelola follow-up dengan pelanggan
- **Customer List** — Lihat data pelanggan yang terkait

### 🎧 Panel Customer Service (CS)
- **Dashboard** — Overview tiket layanan & follow-up hari ini
- **Customer Management** — CRUD data pelanggan, lacak riwayat interaksi
- **Service Tickets** — Buat & kelola tiket layanan (Open, In Progress, Resolved, Closed)
- **Kategori Tiket** — Complaint, Question, Request, Feedback, Technical Issue
- **Prioritas Tiket** — Low, Medium, High, Urgent
- **Follow-up Scheduling** — Jadwalkan follow-up pelanggan
- **Activity Logging** — Catat aktivitas interaksi dengan pelanggan

### 📊 Panel Manager
- **Dashboard** — Overview performa tim dan bisnis
- **Pipeline Overview** — Visualisasi seluruh pipeline deal tim
- **Reports Center** — Laporan komprehensif:
  - 📈 Sales Performance Report
  - 💰 Revenue Report
  - ❌ Lost Reasons Analysis
  - 🔗 Lead Sources Analysis
  - 🔄 Pipeline Analysis
  - 👥 Team Activity Report
- **Export Report** — Export laporan ke Excel
- **Team Performance** — Monitor performa individual tim sales
- **Sales Forecast** — Prediksi pendapatan berdasarkan pipeline
- **Audit Logs** — Akses log audit

### 🔔 Sistem Notifikasi
- Notifikasi otomatis saat lead di-assign ke sales
- Notifikasi saat deal berhasil di-close (Won)
- Mark as read functionality

### 📦 Import/Export
- Import lead massal via file Excel (.xlsx, .csv)
- Download template import
- Export data lead
- Export laporan (Sales Performance, Revenue)

---

## 🛠 Tech Stack

| Kategori | Teknologi | Versi |
|----------|-----------|-------|
| **Backend Framework** | Laravel | 12.x |
| **Bahasa** | PHP | 8.2+ |
| **Frontend CSS** | Tailwind CSS | 4.0 |
| **JavaScript Framework** | Alpine.js | 3.x |
| **Build Tool** | Vite | 7.x |
| **Chart Library** | Chart.js | 4.x |
| **Drag & Drop** | SortableJS | 1.x |
| **DataTables** | Yajra DataTables | 12.x |
| **Permission** | Spatie Laravel Permission | 6.x |
| **Image Processing** | Intervention Image | 3.x |
| **Excel Import/Export** | Maatwebsite Excel | 3.x |
| **Database** | MySQL / SQLite | - |
| **Font** | Cormorant Garamond, DM Sans | - |

---

## 📋 Persyaratan Sistem

Sebelum menginstal Beauty CRM, pastikan sistem Anda memenuhi persyaratan berikut:

| Persyaratan | Versi Minimum |
|-------------|---------------|
| **PHP** | 8.2 atau lebih tinggi |
| **Composer** | 2.x |
| **Node.js** | 18.x atau lebih tinggi |
| **npm** | 9.x atau lebih tinggi |
| **MySQL** | 8.0 (atau SQLite untuk development) |
| **Git** | 2.x |

### Ekstensi PHP yang Dibutuhkan
- `php-mbstring`
- `php-xml`
- `php-zip`
- `php-gd` (untuk Intervention Image)
- `php-mysql` (jika menggunakan MySQL)
- `php-sqlite3` (jika menggunakan SQLite)
- `php-bcmath`

> **💡 Tip:** Jika menggunakan **XAMPP**, sebagian besar ekstensi PHP sudah terinstal secara default. Pastikan untuk mengaktifkan ekstensi yang dibutuhkan di file `php.ini`.

---

## 🚀 Panduan Instalasi

### Metode 1: Instalasi Cepat (Recommended)

```bash
# 1. Clone repository
git clone https://github.com/username/beauty-crm.git
cd beauty-crm

# 2. Jalankan setup otomatis
composer setup
```

Perintah `composer setup` akan secara otomatis menjalankan:
- `composer install` — Install dependency PHP
- Copy `.env.example` → `.env`
- `php artisan key:generate` — Generate application key
- `php artisan migrate --force` — Jalankan migrasi database
- `npm install` — Install dependency Node.js
- `npm run build` — Build asset frontend

---

### Metode 2: Instalasi Manual (Step-by-Step)

#### Langkah 1 — Clone Repository

```bash
git clone https://github.com/username/beauty-crm.git
cd beauty-crm
```

#### Langkah 2 — Install Dependency PHP

```bash
composer install
```

#### Langkah 3 — Konfigurasi Environment

```bash
# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Langkah 4 — Konfigurasi Database

Buka file `.env` dan sesuaikan konfigurasi database:

**Opsi A — MySQL (Recommended untuk Production):**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beauty_crm
DB_USERNAME=root
DB_PASSWORD=
```

> ⚠️ Buat database `beauty_crm` terlebih dahulu di MySQL:
> ```sql
> CREATE DATABASE beauty_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> ```

**Opsi B — SQLite (Untuk Development Cepat):**

```env
DB_CONNECTION=sqlite
# Hapus atau komentari DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

SQLite akan otomatis menggunakan file `database/database.sqlite`.

#### Langkah 5 — Jalankan Migrasi & Seeder

```bash
# Jalankan migrasi untuk membuat tabel
php artisan migrate

# Jalankan seeder untuk data awal (roles, permissions, pipeline stages, lead sources, user demo)
php artisan db:seed
```

> **📌 Penting:** Perintah `db:seed` akan membuat:
> - 4 Role (Admin, Sales, Customer Service, Manager)
> - 12 Permission
> - 4 Pipeline Stage (Prospecting → Proposal → Negotiation → Closing)
> - 6 Lead Source (Website, WhatsApp, Email, Referral, Social Media, Walk-in)
> - Lost Reasons
> - 4 User demo (lihat bagian [Akun Default](#-akun-default))

#### Langkah 6 — Install Dependency Frontend

```bash
npm install
```

#### Langkah 7 — Build Asset Frontend

```bash
# Untuk development (dengan hot reload)
npm run dev

# Untuk production
npm run build
```

#### Langkah 8 — Buat Storage Link

```bash
php artisan storage:link
```

#### Langkah 9 — Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

---

### Metode 3: Development Mode (Recommended untuk Development)

Gunakan perintah ini untuk menjalankan semua service development secara bersamaan:

```bash
composer dev
```

Perintah ini akan menjalankan secara paralel:
- 🌐 **Laravel Server** (`php artisan serve`)
- ⚡ **Queue Worker** (`php artisan queue:listen`)
- 📋 **Log Viewer** (`php artisan pail`)
- 🔥 **Vite Dev Server** (`npm run dev`)

> Semua service akan ditampilkan dengan warna yang berbeda di terminal untuk memudahkan monitoring.

---

## 🔑 Akun Default

Setelah menjalankan `php artisan db:seed`, Anda dapat login menggunakan akun-akun berikut:

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Admin** | `admin@beautycrm.com` | `password` | Full akses ke seluruh fitur |
| **Sales** | `sales@beautycrm.com` | `password` | Lead, Deal, Pipeline, Activity |
| **Customer Service** | `cs@beautycrm.com` | `password` | Customer, Ticket, Follow-up |
| **Manager** | `manager@beautycrm.com` | `password` | Reports, Pipeline, Team, Forecast |

> ⚠️ **PENTING:** Segera ubah password default setelah deployment ke production!

---

## 📂 Struktur Projek

```
beauty-crm/
├── app/
│   ├── Exports/                    # Export classes (Excel)
│   │   ├── LeadExport.php
│   │   ├── RevenueExport.php
│   │   └── SalesPerformanceExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Controller panel Admin
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── LeadController.php
│   │   │   │   ├── DealController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── LeadSourceController.php
│   │   │   │   ├── PipelineStageController.php
│   │   │   │   ├── LostReasonController.php
│   │   │   │   ├── AuditLogController.php
│   │   │   │   ├── ImportExportController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── Auth/               # Controller autentikasi
│   │   │   │   └── LoginController.php
│   │   │   ├── CS/                 # Controller panel Customer Service
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── ServiceTicketController.php
│   │   │   │   ├── FollowUpController.php
│   │   │   │   └── ActivityController.php
│   │   │   ├── Manager/            # Controller panel Manager
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── PipelineController.php
│   │   │   │   ├── TeamPerformanceController.php
│   │   │   │   ├── ForecastController.php
│   │   │   │   └── AuditLogController.php
│   │   │   ├── Sales/              # Controller panel Sales
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LeadController.php
│   │   │   │   ├── DealController.php
│   │   │   │   ├── ActivityController.php
│   │   │   │   └── CustomerController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── CheckActiveUser.php # Cek user aktif
│   │   │   └── CheckRole.php       # Cek role user
│   │   └── Requests/               # Form request validation
│   ├── Imports/
│   │   └── LeadImport.php          # Import lead dari Excel
│   ├── Models/
│   │   ├── Activity.php            # Model aktivitas (polymorphic)
│   │   ├── AuditLog.php            # Model audit trail
│   │   ├── Customer.php            # Model pelanggan
│   │   ├── Deal.php                # Model deal/transaksi
│   │   ├── Lead.php                # Model lead/prospek
│   │   ├── LeadSource.php          # Model sumber lead
│   │   ├── LostReason.php          # Model alasan deal gagal
│   │   ├── PipelineStage.php       # Model tahapan pipeline
│   │   ├── ServiceTicket.php       # Model tiket layanan
│   │   └── User.php                # Model user
│   ├── Notifications/
│   │   ├── DealWonNotification.php
│   │   └── LeadAssignedNotification.php
│   ├── Observers/
│   │   ├── AuditObserver.php       # Observer untuk audit trail
│   │   └── AuditServiceProvider.php
│   ├── Providers/
│   └── Services/
│       ├── CustomerService.php     # Business logic pelanggan
│       ├── DealService.php         # Business logic deal
│       ├── ImportExportService.php  # Business logic import/export
│       └── ReportService.php       # Business logic laporan
├── config/
│   └── beauty-crm.php             # Konfigurasi CRM (statuses, types, dll)
├── database/
│   ├── migrations/                # File migrasi database
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php          # Seeder roles & permissions
│       ├── AdminUserSeeder.php     # Seeder user demo
│       ├── LeadSourceSeeder.php    # Seeder sumber lead
│       ├── PipelineStageSeeder.php # Seeder tahapan pipeline
│       └── LostReasonSeeder.php    # Seeder alasan deal gagal
├── resources/
│   ├── css/
│   │   └── app.css                # Stylesheet utama
│   ├── js/
│   │   └── app.js                 # JavaScript utama
│   └── views/
│       ├── admin/                 # View panel Admin
│       ├── auth/                  # View autentikasi
│       ├── components/            # Blade components
│       ├── cs/                    # View panel Customer Service
│       ├── layouts/               # Layout utama
│       ├── manager/               # View panel Manager
│       ├── profile/               # View profil user
│       └── sales/                 # View panel Sales
├── routes/
│   ├── web.php                    # Route utama & autentikasi
│   ├── admin.php                  # Route panel Admin
│   ├── sales.php                  # Route panel Sales
│   ├── cs.php                     # Route panel Customer Service
│   └── manager.php                # Route panel Manager
├── composer.json
├── package.json
├── tailwind.config.js
└── vite.config.js
```

---

## 🗄️ Skema Database

### Entity Relationship

```
Users ──┬──< Leads (assigned_to)
        ├──< Deals (assigned_to)
        ├──< Activities (user_id)
        ├──< Customers (user_id)
        ├──< ServiceTickets (assigned_to)
        └──< AuditLogs (user_id)

Leads ──┬──< Deals (lead_id)
        ├──1 Customer (lead_id)
        ├──< Activities (polymorphic)
        └──> LeadSource (lead_source_id)

Deals ──┬──< Activities (polymorphic)
        ├──> PipelineStage (pipeline_stage_id)
        └──> LostReason (lost_reason_id)

Customers ──┬──< ServiceTickets (customer_id)
            └──< Activities (polymorphic)

ServiceTickets ──< Activities (polymorphic)
```

### Tabel Utama

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna sistem (name, email, phone, avatar, role) |
| `leads` | Data lead/prospek (name, email, phone, source, status, qualification) |
| `deals` | Data deal/transaksi (value, stage, status, expected close date) |
| `customers` | Data pelanggan (name, email, phone, tags, status) |
| `activities` | Log aktivitas (polymorphic: lead/deal/customer/ticket) |
| `service_tickets` | Tiket layanan pelanggan (ticket number, category, priority, status) |
| `pipeline_stages` | Tahapan pipeline (name, color, order, probability) |
| `lead_sources` | Sumber lead (name, icon, color) |
| `lost_reasons` | Alasan deal gagal (name) |
| `audit_logs` | Log audit perubahan data |
| `roles` | Definisi role (Spatie Permission) |
| `permissions` | Definisi permission (Spatie Permission) |

---

## ⚙️ Konfigurasi

### Environment Variables

Variabel penting di file `.env`:

```env
# Aplikasi
APP_NAME="Beauty CRM"
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

# Database
DB_CONNECTION=mysql
DB_DATABASE=beauty_crm
DB_USERNAME=root
DB_PASSWORD=

# Queue (untuk notifikasi & background jobs)
QUEUE_CONNECTION=database

# Session
SESSION_DRIVER=database

# Cache
CACHE_STORE=database

# Mail (opsional, untuk notifikasi email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@beautycrm.com
MAIL_FROM_NAME="Beauty CRM"
```

### Konfigurasi CRM

File `config/beauty-crm.php` berisi konfigurasi khusus aplikasi:

- **Lead Statuses** — New, Contacted, Qualified, Converted, Closed
- **Lead Qualifications** — Potensial, Tidak Potensial, Tidak Cocok, Win, Lost
- **Deal Statuses** — Open, Won, Lost
- **Activity Types** — Telepon, WhatsApp, Email, Meeting, Catatan, Lainnya
- **Ticket Statuses** — Open, In Progress, Resolved, Closed
- **Ticket Priorities** — Low, Medium, High, Urgent
- **Ticket Categories** — Complaint, Question, Request, Feedback, Technical Issue

---

## 🧪 Menjalankan Tests

```bash
# Jalankan semua test
composer test

# Atau langsung dengan artisan
php artisan test

# Test dengan coverage
php artisan test --coverage
```

---

## 📝 Perintah Artisan Berguna

```bash
# Jalankan migrasi
php artisan migrate

# Reset & jalankan ulang migrasi + seeder
php artisan migrate:fresh --seed

# Buat user admin baru via tinker
php artisan tinker

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Jalankan queue worker
php artisan queue:work

# Lihat daftar routes
php artisan route:list
```

---

## 🚀 Deployment ke Production

### Checklist Deployment

1. **Set environment ke production:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Ubah password default semua akun demo**

3. **Konfigurasi database production (MySQL)**

4. **Build asset frontend:**
   ```bash
   npm run build
   ```

5. **Optimize Laravel:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

6. **Set permission folder:**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

7. **Setup cron job untuk scheduler (opsional):**
   ```bash
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

8. **Jalankan queue worker sebagai service:**
   ```bash
   php artisan queue:work --daemon
   ```

---

## ❓ Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "The Mix manifest does not exist"
```bash
npm run build
```

### Error: "SQLSTATE - Table not found"
```bash
php artisan migrate:fresh --seed
```

### Error: "Permission denied" pada storage
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows (jalankan sebagai Administrator)
# Pastikan folder storage dan bootstrap/cache memiliki izin write
```

### Error: "Vite manifest not found"
```bash
# Pastikan Vite dev server berjalan (development)
npm run dev

# Atau build untuk production
npm run build
```

### Cache Issues
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload
```

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan ikuti langkah berikut:

1. **Fork** repository ini
2. Buat **branch** fitur baru (`git checkout -b feature/fitur-baru`)
3. **Commit** perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. **Push** ke branch (`git push origin feature/fitur-baru`)
5. Buat **Pull Request**

### Konvensi Commit Message

```
feat: menambahkan fitur baru
fix: memperbaiki bug
docs: memperbarui dokumentasi
style: perubahan formatting (tanpa perubahan logika)
refactor: refactoring kode
test: menambahkan test
chore: update dependency
```

---

## 📄 Lisensi

Projek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---

## 🙏 Credits

Dibangun dengan teknologi open-source:

- [Laravel](https://laravel.com) — PHP Framework
- [Tailwind CSS](https://tailwindcss.com) — Utility-first CSS Framework
- [Alpine.js](https://alpinejs.dev) — Lightweight JavaScript Framework
- [Vite](https://vitejs.dev) — Next Generation Frontend Build Tool
- [Chart.js](https://www.chartjs.org) — JavaScript Charting Library
- [SortableJS](https://sortablejs.github.io/Sortable/) — Drag & Drop Library
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) — Role & Permission Management
- [Maatwebsite Excel](https://laravel-excel.com) — Excel Import/Export
- [Yajra DataTables](https://yajrabox.com/docs/laravel-datatables) — Server-side DataTables
- [Intervention Image](https://image.intervention.io) — Image Processing

---

<p align="center">
  Dibuat dengan ❤️ untuk industri kecantikan Indonesia
</p>
