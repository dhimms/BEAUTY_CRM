# 📘 Buku Pintar: Masterclass Back-End Role CS (Customer Service)

Dokumen ini adalah rangkuman komprehensif dari materi *coaching* khusus fitur **Customer Service (CS)**. Catatan ini dirancang sangat detail, lengkap dengan potongan kode dan penjelasan baris per baris beserta alurnya.

---

## 🎯 TAHAP 1: Route & Middleware (Sistem Keamanan Akses)

**Tujuan:** Memahami bagaimana Laravel mengatur lalu lintas URL dan melindungi halaman agar hanya bisa diakses oleh Role tertentu.

### 1. Titik Awal: `routes/web.php`
Semua Request (klik dari user) pertama kali akan masuk ke file ini.

```php
// File: routes/web.php
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match (true) {
        $user->isAdmin() => redirect()->route('admin.dashboard'),
        $user->isSales() => redirect()->route('sales.dashboard'),
        $user->isCS() => redirect()->route('cs.dashboard'),
        $user->isManager() => redirect()->route('manager.dashboard'),
        default => redirect()->route('login'),
    };
})->name('dashboard');

// Memanggil file rute terpisah agar rapi
require __DIR__ . '/admin.php';
require __DIR__ . '/sales.php';
require __DIR__ . '/cs.php';
require __DIR__ . '/manager.php';
```
**Penjelasan Kode:**
- `match (true)`: Fitur PHP modern untuk mengecek kondisi secara elegan. Jika user yang berhasil login adalah CS (`isCS()`), maka dia akan dilempar (`redirect`) secara otomatis ke halaman dashboard khusus CS.
- `require __DIR__ . '/cs.php'`: Teknik memecah file rute. Daripada menulis ribuan baris menumpuk di `web.php`, kita pisahkan semua URL khusus CS ke dalam file mandiri `cs.php`.

### 2. Sang Penjaga Pintu: `routes/cs.php`
Di sinilah letak keamanan utama untuk panel Customer Service.

```php
// File: routes/cs.php
Route::middleware(['role:Customer Service'])->prefix('cs')->name('cs.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    // ...
});
```
**Penjelasan Kode:**
- `middleware(['role:Customer Service'])`: Ini adalah "Satpam". Middleware akan mencegat siapapun yang mencoba mengakses daftar URL di dalam blok grup ini. Jika jabatan user yang sedang login *bukan* "Customer Service" (misal: Sales), sistem langsung menampilkan halaman error `403 Forbidden`.
- `prefix('cs')`: Menghemat pengetikan kode. Semua URL di dalam blok ini otomatis akan ditambahkan awalan `/cs`, sehingga rutenya menjadi `/cs/dashboard`, `/cs/customers`, dsb.

---

## 🎯 TAHAP 2: Manajemen Customer & Follow-up

**Tujuan:** Memahami arsitektur kode yang bersih (Service Pattern) dan pengambilan data database yang optimal (Eager Loading).

### 1. Dependency Injection di Controller
Controller bertindak seperti **Resepsionis**. Ia hanya bertugas menerima pesanan dari tamu (*Request HTTP*) dan mengembalikan hidangan (*Return View Blade*). Resepsionis **dilarang keras** memasak (*melakukan Query Database* secara langsung).

```php
// File: app/Http/Controllers/CS/CustomerController.php
class CustomerController extends Controller
{
    // Ini disebut Dependency Injection (Menyuntikkan Service ke Controller)
    public function __construct(private CustomerService $customerService) {}

    // controller mengarahkan customerservice untuk menampilkan data customer sesuai ID nya
    public function show(Customer $customer)
    {
        // Controller menyuruh Koki (CustomerService) mengambil data detail
        $customer = $this->customerService->getCustomerDetail($customer->id);
        
        return view('cs.customers.show', compact('customer', 'csUsers'));
    }
}
```

### 2. Eager Loading (Mengatasi N+1 Problem)
Di dalam Dapur (Service), Koki (`CustomerService`) menyiapkan pesanan data dengan sangat cepat menggunakan nampan besar.

```php
// File: app/Services/CustomerService.php
public function getCustomerDetail(int $id): Customer
{
    return Customer::with([
        'csUser',
        'lead',
        'serviceTickets' => fn($q) => $q->with('assignedUser')->latest(),
        'activities' => fn($q) => $q->with('user')->latest(),
    ])->findOrFail($id);
}
```
**Penjelasan Kode:**
- `Customer::with([...])`: Ini adalah teknik **Eager Loading**. Tanpa ini (N+1 Problem), Laravel akan melakukan query ke database secara berulang-ulang untuk setiap tiket dan aktivitas. Dengan `with()`, sistem menarik data Profil Customer, Riwayat Tiket, Nama CS, dan Log Aktivitas **sekaligus dalam satu waktu**. Loading website menjadi jauh lebih ringan dan instan!
- `fn($q) => $q->with('user')`: Fungsi dalam *closure* ini berguna untuk melakukan "Nested Eager Loading". Saat mengambil list aktivitas, kita *juga* mengambil data relasi `user` (Tabel Staf) agar foto profil dan nama karyawan yang membuat catatan aktivitas tersebut bisa langsung ditampilkan.

### 3. Logika Menghitung Overdue Follow-up
Bagaimana sistem tahu ada janji menelepon pelanggan yang sudah lewat tenggat waktu?

```php
// File: app/Services/CustomerService.php
// metode ini digunakan untuk customer service untuk mengetahui jadwal follow up
public function getFollowUps(array $filters = []): array
{
    $baseQuery = Activity::with(['user', 'activitable'])
        ->whereNotNull('follow_up_date');

    $overdue = (clone $baseQuery)
        ->where('follow_up_status', 'pending')
        ->whereDate('follow_up_date', '<', Carbon::today())
        ->orderBy('follow_up_date')
        ->get();
    
    // ... return compact('pending', 'overdue', 'completed');
}
```
**Penjelasan Kode:**
- `where('follow_up_status', 'pending')`: Memfilter hanya tugas-tugas (*task*) yang belum selesai dikerjakan oleh CS.
- `whereDate('follow_up_date', '<', Carbon::today())`: Mencari catatan di mana tanggal janjinya **lebih kecil (sudah lewat)** dari tanggal hari ini (`Carbon::today()`). Jika tanggal jadwal lebih kecil dari hari ini, maka otomatis ia masuk ke kategori kotak **OVERDUE** (menunggak).

---

## 🎯 TAHAP 3: Activity Logging Polymorphic (Panel CS)

**Tujuan:** Memahami teknik ajaib mencatat log interaksi secara universal, tanpa membuat banyak tabel mubazir (seperti `customer_activities`, `lead_activities`, dsb).

### 1. Konsep Model Polymorphic (Satu Tabel Untuk Semua)
Kita hanya punya **1 tabel** bernama `activities`. Tabel ini dibekali dua "Stiker Label": `activitable_type` dan `activitable_id`.

```php
// File: app/Models/Activity.php
class Activity extends Model
{
    protected $fillable = [
        'activitable_type', // Stiker Kategori: Contoh isi 'App\Models\Customer'
        'activitable_id',   // Stiker ID Unik: Contoh isi '12' (Customer ID 12)
        'type',             // misal: 'call', 'whatsapp'
        // ...
    ];

    // activitable digunakan untuk polymophic relationship
    // karena dapat berhubungan dengan model lain seperti customer, lead, dll
    public function activitable()
    {
        return $this->morphTo();
    }
}
```
**Penjelasan Kode:**
- `morphTo()`: Jika kita memanggil perintah `$aktivitas->activitable`, Laravel akan melirik ke kolom stiker `activitable_type`. Jika stikernya bertuliskan `App\Models\Customer`, maka fungsi ini secara pintar merubah dirinya menjadi relasi Customer dan mengambil data Profil Budi (ID 12).
- **Aturan Emas Database:** Karena kolom `activitable_id` itu isinya "campur-campur" (bisa ID Customer, bisa ID Lead, bisa ID Tiket), maka secara arsitektur ia **TIDAK BOLEH** dipasangi gembok *Foreign Key Constraints*. Jika digembok khusus ke tabel *customers*, maka database akan meledak saat kita mencoba mengisi ID milik tabel *leads*.

### 2. Alur Eksekusi (Dari Layar Web ke Database)
Berikut adalah urutan kejadian (*lifecycle*) ketika CS memencet tombol **"Simpan Catatan"** di halaman web:

#### Langkah A: Pos Satpam (Form Request Validation)
Formulir HTML di layar diam-diam mengirim 2 stiker tersembunyi (`type=hidden`). Sebelum mencapai logika program, data akan diperiksa Satpam.
```php
// File: app/Http/Requests/CS/StoreActivityRequest.php
public function rules(): array
{
    return [
        'activitable_type' => 'required|in:customer',
        'activitable_id' => 'required|integer',
        'type' => 'required|in:call,whatsapp,email,note,meeting',
    ];
}
```
Satpam memastikan bahwa: "Hei, pastikan stiker kategorinya ada dan bentuk ID-nya adalah angka!". Jika gagal, request ditendang kembali ke browser dengan tulisan merah (Error).

#### Langkah B: Resepsionis (ActivityController)
Data yang terjamin bersih dan lolos validasi kini masuk ke Controller.
```php
// File: app/Http/Controllers/CS/ActivityController.php
// controller untuk mengelola aktivitas customer service
public function store(StoreActivityRequest $request)
{
    // Resepsionis melempar data yg divalidasi agar dimasak oleh Koki
    $this->customerService->logActivity($request->validated());

    // Memerintahkan web untuk memuat ulang (refresh) halaman
    return redirect()->back()->with('success', 'Aktivitas berhasil dicatat.');
}
```

#### Langkah C: Koki Dapur (CustomerService)
```php
// File: app/Services/CustomerService.php
// logactivity digunakan untuk mencatat aktivitas yang dilakukan oleh customer service
public function logActivity(array $data): Activity
{
    // Mengubah tulisan biasa 'customer' menjadi nama Class sesungguhnya
    $typeMap = [
        'customer' => Customer::class,
    ];

    // Menuliskan datanya secara permanen ke dalam tabel 'activities'
    return Activity::create([
        'user_id' => auth()->id(), // Mencatat Siapa (CS mana) yang sedang login
        'activitable_type' => $typeMap[$data['activitable_type']] ?? Customer::class,
        'activitable_id' => $data['activitable_id'],
        'type' => $data['type'],
        'subject' => $data['subject'] ?? null,
        // ...
    ]);
}
```
**Kesimpulan Akhir:** Data aktivitas sukses tersimpan ke database. Berkat Controller yang menjalankan *redirect*, browser web akan di-refresh, memicu fungsi *Eager Loading* lagi, dan catatan telepon CS yang baru saja diinput langsung ter-render dengan mulus di dalam *Timeline Customer*! 

---
> *Pelajari siklus ini berulang-ulang sampai kamu bisa membayangkan alurnya hanya dengan memejamkan mata. Jika sudah menguasai alur ini, kamu telah menjadi programmer Back-End Laravel sejati!* 🚀
