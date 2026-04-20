# Konveksi hub - Professional Apparel Manufacturer System

![Konveksi Hub Banner](https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=2070&auto=format&fit=crop)

**Konveksi hub** adalah sistem manajemen konveksi modern yang dibangun menggunakan **Laravel 12**, **Filament v3**, dan **Tailwind CSS**. Sistem ini dirancang untuk memfasilitasi pemesanan seragam dan apparel premium secara efisien, mulai dari katalog produk hingga manajemen stok dan transaksi.

## 🚀 Fitur Utama

### 🌐 Frontend (Customer Facing)
- **Landing Page Premium**: Antarmuka modern dengan desain responsif menggunakan Alpine.js.
- **Katalog Produk**: Penjelajahan produk dengan filter kategori dan pengurutan harga.
- **Cek Stok Real-time**: Halaman publik untuk memantau ketersediaan stok produk (warna & ukuran).
- **Sistem Keranjang & Checkout**: Proses pemesanan yang mudah dengan instruksi pembayaran manual.
- **Konfirmasi WhatsApp**: Integrasi langsung ke WhatsApp Admin untuk bukti pembayaran dan konsultasi.

### 🔐 Backend (Admin Panel)
- **Dashboard Statistik**: Overview performa penjualan dan stok menggunakan Filament Widgets.
- **Manajemen Produk & Varian**: Pengelolaan produk kompleks dengan banyak varian warna dan ukuran.
- **Manajemen Transaksi**: Kontrol penuh atas status pesanan (Pending, Processing, Shipped, dsb).
- **Inventory Overview**: Pemantauan stok gudang secara menyeluruh.
- **Import/Export Excel**: Kemudahan pengisian stok dalam jumlah besar menggunakan file Excel.

## 🛠️ Tech Stack

- **Framework**: Laravel 12
- **Admin Panel**: Filament v3
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Database**: MySQL / SQLite
- **Package Penting**:
    - `maatwebsite/excel`: Untuk manajemen data stok via Excel.
    - `awcodes/filament-table-repeater`: UI yang disempurnakan untuk input varian.

## 📦 Instalasi

### 1. Clone Repositori
```bash
git clone https://github.com/username/konveksihub.git
cd konveksihub
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan atur konfigurasi database Anda.
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi & Database Setup
```bash
php artisan migrate
php artisan db:seed # Opsional untuk data testing
```

### 5. Build Aset Frontend
```bash
npm run build
```

### 6. Jalankan Server
```bash
php artisan serve
```

## ☁️ Deployment Notes (Shared Hosting)

Sistem ini telah dioptimasi untuk berjalan di lingkungan *shared hosting* yang memiliki keterbatasan fungsi PHP (`exec`, `symlink`):

1. **Storage Fix**: Gunakan route `/admin/fix-storage` untuk menginisialisasi folder fisik `public/storage` jika fungsi `symlink()` dinonaktifkan.
2. **Filesystem**: Konfigurasi `public` disk diarahkan langsung ke `public_path('storage')` di `config/filesystems.php`.
3. **Database Migration**: Gunakan route `/migrate-database` jika akses SSH tidak tersedia.

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---
*Developed with ❤️ for Konveksi hub.*
