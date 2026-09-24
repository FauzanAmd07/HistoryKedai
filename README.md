# ☕ HistoryKedai - Blueprint & Refactoring Guide (Native PHP to Laravel)

Dokumen ini berisi panduan acuan (*blueprint*) lengkap untuk memperbarui aplikasi **HistoryKedai** dari **Native PHP** menjadi **Laravel**, dengan peningkatan animasi JavaScript interaktif, serta mempertahankan skema warna asli aplikasi.

---

## 📌 1. Visi & Target Pembaruan

- **Framework Backend**: Migrasi dari Native PHP (`mysqli`) ke **Laravel 10 / 11** (MVC Architecture, Eloquent ORM, Migrations, & Blade/Inertia).
- **Interaktivitas & Animasi**: Penggunaan **JavaScript (GSAP / Alpine.js / SweetAlert2 / Vanilla JS)** untuk animasi micro-interaction yang modern dan *smooth*.
- **Desain & Warna**: Mempertahankan **Desain & Palet Warna Asli** (Customer: Rich Purple, Dashboard Admin/Kasir: Soft Blue).
- **Sistem Peran (RBAC)**: Pemisah hak akses untuk **Pemilik**, **Admin**, **Kasir**, dan **Pelanggan (Public Order)**.

---

## 🎨 2. Palet Warna (Design System Tokens)

Warna aplikasi dibagi menjadi dua modul sesuai dengan fungsionalitasnya:

### 🛍️ Modul Pelanggan (Customer Storefront & Checkout)
```css
:root {
    --color-primary: #6D28D9;      /* Ungu Utama (Buttons, Accents) */
    --color-secondary: #8B5CF6;    /* Ungu Secondary / Hover */
    --color-light-bg: #F9FAFB;     /* Background Utama */
    --color-surface: #FFFFFF;      /* Card & Modal Container */
    --color-dark-text: #111827;    /* Teks Utama */
    --color-muted-text: #6B7280;   /* Teks Sekunder */
    --shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
    --border-radius: 12px;
}
```

### 📊 Modul Dashboard (Pemilik, Admin, & Kasir)
```css
:root {
    --color-blue-primary: #3B82F6;   /* Biru Utama */
    --color-blue-secondary: #BFDBFE; /* Biru Muda / Highlight Menu Active */
    --color-blue-dark: #1E40AF;      /* Biru Gelap / Header Text */
    --color-bg: #F3F4F6;             /* Background Halaman */
    --color-surface: #FFFFFF;         /* Surface Card & Table */
    --color-text-dark: #1F2937;       /* Teks Gelap */
    --color-text-muted: #6B7280;      /* Teks Muted */
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --border-radius: 12px;
}
```

---

## 🗄️ 3. Skema Database & Migration Plan (Laravel Eloquent)

Berikut adalah pemetaan dari tabel Native PHP ke Laravel Migrations:

### A. Tabel `users` (Gabungan `Karyawan`)
```php
Schema::create('users', function (Blueprint $table) {
    $table->id('id_karyawan');
    $table->string('nama');
    $table->string('username')->unique();
    $table->string('password');
    $table->enum('jabatan', ['Pemilik', 'Admin', 'Kasir']);
    $table->enum('status_karyawan', ['Aktif', 'Tidak Aktif'])->default('Aktif');
    $table->rememberToken();
    $table->timestamps();
});
```

### B. Tabel `kategori`
```php
Schema::create('kategori', function (Blueprint $table) {
    $table->id('id_kategori');
    $table->string('nama_kategori');
    $table->enum('status_kategori', ['Tersedia', 'Diarsipkan'])->default('Tersedia');
    $table->timestamps();
});
```

### C. Tabel `menu`
```php
Schema::create('menu', function (Blueprint $table) {
    $table->id('id_menu');
    $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('cascade');
    $table->string('nama_menu');
    $table->decimal('harga', 10, 2);
    $table->string('gambar')->nullable();
    $table->enum('status_menu', ['Tersedia', 'Diarsipkan'])->default('Tersedia');
    $table->timestamps();
});
```

### D. Tabel `pelanggan`
```php
Schema::create('pelanggan', function (Blueprint $table) {
    $table->id('id_pelanggan');
    $table->string('nama');
    $table->timestamps();
});
```

### E. Tabel `transaksi`
```php
Schema::create('transaksi', function (Blueprint $table) {
    $table->id('id_transaksi');
    $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->onDelete('cascade');
    $table->foreignId('id_karyawan')->nullable()->constrained('users', 'id_karyawan')->onDelete('cascade');
    $table->dateTime('tanggal');
    $table->decimal('total_harga', 12, 2);
    $table->enum('status', ['Baru Masuk', 'Sedang Diproses', 'Selesai', 'Batal'])->default('Baru Masuk');
    $table->string('metode_bayar')->default('Tunai');
    $table->timestamps();
});
```

### F. Tabel `detail_transaksi`
```php
Schema::create('detail_transaksi', function (Blueprint $table) {
    $table->id('id_detail');
    $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi')->onDelete('cascade');
    $table->foreignId('id_menu')->constrained('menu', 'id_menu')->onDelete('cascade');
    $table->integer('jumlah');
    $table->decimal('subtotal', 12, 2);
    $table->timestamps();
});
```

---

## ✨ 4. Panduan Animasi & Interaktivitas JavaScript

Untuk memberikan pengalaman pengguna (*user experience*) yang lebih hidup dan interaktif, gunakan kombinasi library berikut:

| Modul | Animasi / Interaksi JS | Technology Stack |
| :--- | :--- | :--- |
| **Storefront (Katalog Menu)** | - Filter Kategori Tanpa Reload (*Smooth Fade In/Out*)<br>- Stagger Animation saat kartu menu muncul<br>- Counter badge keranjang dengan efek bounce | **GSAP** / **Alpine.js** |
| **Keranjang & Checkout Slide-Over** | - Sliding drawer dari sisi kanan layar<br>- Update subtotal & total otomatis (*real-time calculation*)<br>- Micro-ripple effect pada tombol checkout | **Alpine.js** / **Vanilla JS** |
| **Dashboard Statistics** | - Animated Counter-Up untuk Total Pendapatan & Pesanan<br>- Real-time notification badge pesanan baru | **CountUp.js** / **Laravel Echo / Pusher** |
| **Modals & Form Feedback** | - SweetAlert2 toast notification saat item ditambah<br>- Konfirmasi soft delete (diarsipkan) dengan dialog cantik | **SweetAlert2** |

### Contoh Implementasi JS Animasi (Alpine.js + GSAP):
```javascript
// Filter Menu Tanpa Reload dengan Animasi Stagger GSAP
function filterKategori(kategoriId) {
    gsap.to('.menu-card', {
        opacity: 0,
        y: 20,
        duration: 0.2,
        onComplete: () => {
            // Tampilkan item sesuai kategori
            document.querySelectorAll('.menu-card').forEach(card => {
                if (kategoriId === 'all' || card.dataset.kategori === kategoriId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            // Animasi Masuk Smooth
            gsap.to('.menu-card[style*="display: block"]', {
                opacity: 1,
                y: 0,
                duration: 0.4,
                stagger: 0.05
            });
        }
    });
}
```

---

## 🚀 5. Tahapan Langkah-Langkah Migrasi ke Laravel

### Langkah 1: Inisialisasi Project Laravel
```bash
composer create-project laravel/laravel history-kedai-laravel
cd history-kedai-laravel
```

### Langkah 2: Setup Autentikasi (Laravel Breeze / Custom Auth)
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### Langkah 3: Membuat Models & Migrations
```bash
php artisan make:model Kategori -m
php artisan make:model Menu -m
php artisan make:model Pelanggan -m
php artisan make:model Transaksi -m
php artisan make:model DetailTransaksi -m
```

### Langkah 4: Membuat Controllers
```bash
php artisan make:controller CustomerController
php artisan make:controller DashboardController
php artisan make:controller MenuController --resource
php artisan make:controller KategoriController --resource
php artisan make:controller KaryawanController --resource
php artisan make:controller TransaksiController
```

### Langkah 5: Pindahkan Asset & Setup Blade Views
- Pindahkan folder gambar dari project lama ke `storage/app/public/gambar` atau `public/gambar`.
- Konfigurasi variabel CSS warna pada `resources/css/app.css`.
- Buat Blade Layout untuk **Customer Layout** (`layouts/app.blade.php`) dan **Dashboard Layout** (`layouts/dashboard.blade.php`).

---

## 🔐 6. Akun Pengujian Seeder Default (`DatabaseSeeder.php`)

Saat menjalankan `php artisan db:seed`, pastikan akun default berikut dibuat:

```php
User::create([
    'nama' => 'Pemilik Kedai',
    'username' => 'pemilik',
    'password' => Hash::make('admin123'),
    'jabatan' => 'Pemilik',
    'status_karyawan' => 'Aktif',
]);

User::create([
    'nama' => 'Admin Kedai',
    'username' => 'admin',
    'password' => Hash::make('admin123'),
    'jabatan' => 'Admin',
    'status_karyawan' => 'Aktif',
]);

User::create([
    'nama' => 'Kasir Kedai',
    'username' => 'kasir',
    'password' => Hash::make('kasir123'),
    'jabatan' => 'Kasir',
    'status_karyawan' => 'Aktif',
]);
```

---

## 📝 Catatan Tambahan
File SQL versi Native PHP saat ini tetap dapat diakses di **[`history.sql`](file:///d:/UNENG%20JTIK/SEMESTER%203%20TEKOM/pemrograman%20web/HistoryKedai/HistoryKedai/history.sql)** sebagai referensi data awal.
