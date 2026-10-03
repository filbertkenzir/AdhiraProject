# Dokumentasi & Alur Kerja Backend Sistem Kasir & Admin Print & Copy (Laravel 12 + PostgreSQL)

Dokumen ini menjelaskan secara rinci seluruh tahapan pembuatan, perubahan, serta alur kerja backend yang telah diimplementasikan pada proyek **Adhira Project (Print & Copy / Toko ATK)**.

---

## 📐 1. Ringkasan Sistem & Peran User (Roles)

Sistem berbasis **Laravel 12** dengan database **PostgreSQL** ini dirancang untuk toko cetak/fotocopy dan ATK dengan dua peran (*roles*):

1. **Admin (`role: admin`)**:
   - Berhak mengakses seluruh halaman aplikasi.
   - **Admin Dashboard (`/admin`)**: Menampilkan statistik real-time (*Total Penjualan Hari Ini*, *Total Pengunjung Hari Ini*, *Metode Terbanyak*), Grafik Penjualan 7 Hari Terakhir via Chart.js, dan Persentase Keperluan Pengunjung.
   - **Inventory (`/item` & `/method`)**: Menambah dan melihat daftar barang stok ATK serta metode layanan cetak/fotocopy.
   - **Halaman Kasir (`/`)**: Akses penuh untuk bertransaksi.

2. **Kasir (`role: kasir`)**:
   - **Halaman Kasir (`/` atau `/cashier`)**: Tempat melakukan transaksi (pilih layanan/barang, pilih kertas, jumlah lembar/pcs, metode pembayaran Cash/QRIS, hitung kembalian).
   - **Restriksi Hak Akses**: Dibatasi oleh middleware backend. Jika kasir mencoba mengakses `/admin`, `/item`, atau `/method`, sistem akan menolak akses dan mengarahkan kembali ke halaman kasir.

---

## 🔄 2. Diagram Alur Kerja (Workflow Diagram)

```mermaid
flowchart TD
    A[User Membuka Web] --> B{Status Login?}
    B -- Belum Login / Guest --1> C[Akses Halaman Kasir]
    B -- Logged In Kasir --2> C
    B -- Logged In Admin --3> D[Akses Semua Halaman: Kasir, Inventory, Admin Dashboard]

    C --> E[Pilih Metode / Barang ATK & Jenis Kertas]
    E --> F[Tambah ke Pesanan & Hitung Subtotal]
    F --> G{Pilih Pembayaran}
    G -- Cash --4> H[Input Uang Bayar -> Hitung Kembalian]
    G -- QRIS --5> I[Tampil Modal QRIS Total Transaksi]
    H --> J[Kirim Request POST /transactions ke Backend]
    I --> J

    J --> K[Simpan Transaksi & Transaction Items di PostgreSQL]
    K --> L[Potong Stok Barang Fisik jika ada Pembelian ATK]
    L --> M[Response Transaksi Sukses]

    D --> N[Admin Buka /admin]
    N --> O[Backend Hitung Penjualan, Pengunjung, Trend 7 Hari, Persentase Layanan]
    O --> P[Render Dashboard Analytics]
```

---

## 🛠️ 3. Tahapan Pembuatan & Perubahan Backend

### Tahap 1: Konfigurasi Database PostgreSQL & Environment
- **Aktivasi Driver PHP**: Mengaktifkan modul `pdo_pgsql` dan `pgsql` pada konfigurasi PHP.
- **Membuat Database**: Database `adhira_db` dibuat di PostgreSQL server (Port `5432`).
- **File Configuration ([.env](file:///.env))**:
  ```env
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=adhira_db
  DB_USERNAME=postgres
  DB_PASSWORD=admin
  ```

---

### Tahap 2: Desain Skema Database & Migrasi (Migrations)

Dibuat 5 tabel utama untuk mendukung seluruh kebutuhan bisnis:

1. **`users`** ([`0001_01_01_000000_create_users_table.php`](file:///database/migrations/0001_01_01_000000_create_users_table.php))
   - Field: `id`, `name`, `username` (unique), `email` (unique), `password`, `role` (`admin` / `kasir`), `remember_token`, `timestamps`.
2. **`items`** ([`2026_10_03_000001_create_items_table.php`](file:///database/migrations/2026_10_03_000001_create_items_table.php))
   - Menyimpan stok barang ATK (misal: *Kertas F4*, *Kertas A4*).
   - Field: `id`, `name`, `stock`, `price`, `timestamps`.
3. **`methods`** ([`2026_10_03_000002_create_methods_table.php`](file:///database/migrations/2026_10_03_000002_create_methods_table.php))
   - Menyimpan tarif jasa cetak/fotocopy (misal: *Print Hitam Putih*, *Print Berwarna*).
   - Field: `id`, `name`, `price`, `timestamps`.
4. **`transactions`** ([`2026_10_03_000003_create_transactions_table.php`](file:///database/migrations/2026_10_03_000003_create_transactions_table.php))
   - Menyimpan header transaksi kasir.
   - Field: `id`, `transaction_code` (misal: `TRX-20261003-0001`), `user_id` (foreign key ke users), `total_amount`, `payment_method` (`cash` / `qris`), `paid_amount`, `change_amount`, `timestamps`.
5. **`transaction_items`** ([`2026_10_03_000004_create_transaction_items_table.php`](file:///database/migrations/2026_10_03_000004_create_transaction_items_table.php))
   - Menyimpan rincian setiap item/jasa yang dibeli dalam transaksi.
   - Field: `id`, `transaction_id`, `item_type` (`method` / `item`), `method_id`, `item_id`, `name`, `paper_type` (`F4`, `A4`, `A3`, `A5`, `-`), `qty`, `price`, `subtotal`, `timestamps`.

---

### Tahap 3: Model Eloquent & Relasi Data
- **[`User.php`](file:///app/Models/User.php)**: Menambahkan `username` & `role` pada `$fillable`, serta fungsi pembantu `isAdmin()` dan `isKasir()`.
- **[`Item.php`](file:///app/Models/Item.php)**: Model untuk tabel barang & stok.
- **[`Method.php`](file:///app/Models/Method.php)**: Model untuk tabel metode/jasa cetak.
- **[`Transaction.php`](file:///app/Models/Transaction.php)**: Memiliki relasi `belongsTo(User::class)` dan `hasMany(TransactionItem::class)`.
- **[`TransactionItem.php`](file:///app/Models/TransactionItem.php)**: Memiliki relasi `belongsTo(Transaction::class)`, `belongsTo(Method::class)`, dan `belongsTo(Item::class)`.

---

### Tahap 4: Seeder Data Awal ([`DatabaseSeeder.php`](file:///database/seeders/DatabaseSeeder.php))
Untuk memastikan sistem langsung memiliki data awal saat dijalankan:
- **Akun Default**:
  - Admin: Username `admin` | Email `admin@adhira.com` | Password `admin123` | Role `admin`
  - Kasir: Username `kasir` | Email `kasir@adhira.com` | Password `kasir123` | Role `kasir`
- **Data Metode**: *Print Hitam Putih* (Rp 500), *Print Berwarna* (Rp 1.500), *Fotocopy Hitam Putih* (Rp 300), *Fotocopy Berwarna* (Rp 1.200).
- **Data Barang**: *Kertas F4* (Stok: 120, Rp 500), *Kertas A4* (Stok: 8, Rp 450).
- **Sample Transaksi**: Otomatis generate riwayat transaksi 7 hari terakhir agar grafik penjualan & statistik pengunjung di Admin Dashboard langsung berisi data riil.

---

### Tahap 5: Autentikasi & Middleware Hak Akses
- **[`AuthController.php`](file:///app/Http/Controllers/AuthController.php)**:
  - `login()`: Menerima input email atau username + password. Memverifikasi kredensial via `Auth::attempt()`, menggenerasi session, dan mengembalikan response JSON/redirect.
  - `logout()`: Mengencerkan session dan memproses logout pengguna.
- **[`RoleMiddleware.php`](file:///app/Http/Middleware/RoleMiddleware.php)**:
  - Memeriksa role pengguna yang sedang login.
  - Jika pengguna role `kasir` mencoba membuka halaman khusus admin (`/admin`, `/item`, `/method`), middleware menghentikan akses dan mengarahkan pengguna kembali ke halaman kasir (`/`) dengan notifikasi error.
- **[`bootstrap/app.php`](file:///bootstrap/app.php)**:
  - Mendaftarkan alias middleware `'role' => RoleMiddleware::class`.

---

### Tahap 6: Kontroler Logika Bisnis (Controllers)

1. **[`CashierController.php`](file:///app/Http/Controllers/CashierController.php)**:
   - `index()`: Mengambil seluruh daftar `methods` dan `items` dari database PostgreSQL untuk mengisi dropdown pada halaman kasir.
   - `storeTransaction()`: Menerima keranjang pesanan (`cart`), metode pembayaran (`cash`/`qris`), dan uang bayar.
     - Menggunakan **Database Transaction** (`DB::transaction()`) untuk menjamin konsistensi data.
     - Menggenerasi kode transaksi unik `TRX-YYYYMMDD-XXXX`.
     - Menyimpan record `Transaction` dan `TransactionItem`.
     - Jika transaksi mencakup barang fisik (ATK), stok pada tabel `items` otomatis dipotong sejumlah `qty` yang dibeli (`$item->decrement('stock', $qty)`).

2. **[`AdminController.php`](file:///app/Http/Controllers/AdminController.php)**:
   - `index()`: Mengkalkulasi 5 data analitik utama:
     1. **Total Penjualan Hari Ini**: `SUM(total_amount)` untuk transaksi hari ini.
     2. **Total Pengunjung Hari Ini**: `COUNT(*)` jumlah transaksi hari ini.
     3. **Metode Terbanyak**: Query item/layanan yang paling banyak dipesan hari ini.
     4. **Grafik Penjualan (7 Hari Terakhir)**: Query agregasi total penjualan harian selama 7 hari ke belakang untuk diolah oleh Chart.js.
     5. **Keperluan Pengunjung**: Persentase kontribusi setiap layanan/barang berdasarkan total quantity pesanan.

3. **[`ItemController.php`](file:///app/Http/Controllers/ItemController.php)**:
   - `index()`: Memuat daftar barang dari tabel `items`.
   - `store()`: Validasi dan menyimpan barang baru ke tabel `items`.

4. **[`MethodController.php`](file:///app/Http/Controllers/MethodController.php)**:
   - `index()`: Memuat daftar metode dari tabel `methods`.
   - `store()`: Validasi dan menyimpan metode layanan baru ke tabel `methods`.

---

### Tahap 7: Routing ([`routes/web.php`](file:///routes/web.php))

```php
// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Kasir Routes (Dapat diakses oleh Kasir & Admin)
Route::get('/', [CashierController::class, 'index'])->name('cashier.index');
Route::get('/cashier', [CashierController::class, 'index']);
Route::post('/transactions', [CashierController::class, 'storeTransaction'])->name('transactions.store');

// Admin Routes (Dilindungi oleh middleware role:admin)
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/item', [ItemController::class, 'index'])->name('items.index');
    Route::post('/item', [ItemController::class, 'store'])->name('items.store');
    Route::get('/method', [MethodController::class, 'index'])->name('methods.index');
    Route::post('/method', [MethodController::class, 'store'])->name('methods.store');
});
```

---

### Tahap 8: Penyesuaian View (Tanpa Mengubah Tampilan Visual UI)

Tampilan frontend asli tetap 100% dipertahankan, hanya disesuaikan dengan data dinamis dari Laravel Blade & JavaScript AJAX:

1. **[`cashier.blade.php`](file:///resources/views/cashier.blade.php)**:
   - Dropdown `<select id="methodSelect">` kini memuat data dinamis dari `$methods` dan `$items`.
   - Fungsi `finishTransaction()` pada JavaScript mengirimkan payload keranjang via AJAX `POST /transactions` dengan token CSRF.
   - Form modal login terhubung ke `POST /login`.
   - Tombol logout dinamis di bagian bawah sidebar jika user telah login.

2. **[`admin.blade.php`](file:///resources/views/admin.blade.php)**:
   - Card statistik kini menampilkan data riil dari `$totalSalesToday`, `$totalVisitorsToday`, dan `$topMethodName`.
   - Injeksi data `@json($chartLabels)` dan `@json($chartData)` ke Chart.js untuk menampilkan grafik 7 hari terakhir secara akurat.
   - Progress bar persentase keperluan pengunjung diisi secara dinamis dari `$visitorNeeds`.

3. **[`items.blade.php`](file:///resources/views/items.blade.php)**:
   - Form penambahan barang terhubung ke `POST /item`.
   - Tabel `Daftar Barang` menampilkan data dari database (`$items`) dengan indikator warna stok otomatis (merah jika stok $\le 10$).

4. **[`method.blade.php`](file:///resources/views/method.blade.php)**:
   - Form penambahan metode terhubung ke `POST /method`.
   - Tabel `Daftar Metode` menampilkan data dari database (`$methods`) dengan format mata uang Rupiah.

---

## 🔑 4. Informasi Kredensial Login

| Peran (Role) | Email / Username | Password | Akses Halaman |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` / `admin@adhira.com` | `admin123` | Kasir (`/`), Inventory (`/item`, `/method`), Dashboard (`/admin`) |
| **Kasir** | `kasir` / `kasir@adhira.com` | `kasir123` | Kasir (`/`) |

---

## 🚀 5. Cara Memulai & Menguji Aplikasi

1. **Jalankan Migrasi & Seeder Database (jika belum)**:
   ```bash
   php artisan migrate:fresh --seed
   ```
2. **Jalankan Server Lokal Laravel**:
   ```bash
   php artisan serve
   ```
3. Buka browser pada alamat `http://localhost:8000`.
