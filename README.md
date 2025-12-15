# Warung Padi

**Nomor Kelompok:** Kelompok L  
**Judul Project:** Warung Padi

## Anggota Kelompok
1. **Sahrul Adicandra Effendy** (164231013)
2. **Gilardo Diaz Saragih** (164231055)
3. **Faiz Iqbal I’tishom** (164231059)
4. **Glennesha Putri Anandaprasa** (164231086)

---

## Deskripsi Project
**Warung Padi** adalah platform berbasis web yang dirancang untuk mengelola rantai pasok beras secara terintegrasi. Sistem ini menghubungkan berbagai pemangku kepentingan dalam ekosistem pertanian padi, mulai dari Petani, Pengepul, Distributor, hingga Pasar.

**Fitur Utama:**
- **Manajemen User & Role:** Mendukung peran spesifik seperti Petani, Pengepul, Distributor, dan Pasar dengan dashboard yang disesuaikan.
- **Manajemen Inventaris:** Pencatatan stok beras, tipe beras, dan kualitas.
- **Transaksi & Negosiasi:** Fitur untuk melakukan penawaran harga dan transaksi jual-beli antar pengguna.
- **E-Wallet & Top-Up:** Sistem pembayaran digital terintegrasi untuk memudahkan transaksi.
- **Pelaporan & Analitik:** Dashboard interaktif (menggunakan integrasi Pentaho/OLAP) untuk memantau metrik harian, tren penjualan, dan performa bisnis.
- **Cetak Laporan:** Ekspor laporan transaksi ke format PDF.

---

## List Libraries

**Backend (Laravel):**
- `laravel/framework`: ^12.0 (Core Framework)
- `barryvdh/laravel-dompdf`: ^3.1 (Pembuatan laporan PDF)
- `laravel/tinker`: ^2.10 (REPL)
- `laravel/breeze`: ^2.3 (Authentication scaffolding)

**Frontend:**
- `tailwindcss`: ^4.0 (Utility-first CSS framework)
- `alpinejs`: ^3.4 (Ringan JavaScript framework untuk interaktivitas UI)
- `axios`: ^1.11 (HTTP Client)
- `vite`: ^7.0 (Frontend Tooling)

---

## Tata Cara Penggunaan Code

Berikut adalah langkah-langkah untuk menjalankan project ini di lingkungan lokal (Local Environment):

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Database

### Instalasi & Menjalankan Aplikasi

1. **Clone Repository**
   Pastikan Anda telah mengambil kode terbaru dari repository.

2. **Install Dependencies Backend**
   Jalankan perintah berikut untuk mengunduh library PHP yang dibutuhkan:
   ```bash
   composer install
   ```

3. **Install Dependencies Frontend**
   Jalankan perintah berikut untuk mengunduh library JavaScript dan aset:
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   - Salin file `.env.example` menjadi `.env`.
   - Atur konfigurasi database di file `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
   - Generate app key:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database**
   Jalankan migrasi untuk membuat tabel-tabel yang diperlukan:
   ```bash
   php artisan migrate
   ```
   *(Opsional: Jika tersedia seeder, jalankan `php artisan db:seed` untuk data dummy)*

6. **Menjalankan Aplikasi (Development)**
   Buka dua terminal terpisah:
   
   **Terminal 1 (Laravel Server):**
   ```bash
   php artisan serve
   ```
   
   **Terminal 2 (Vite Server - untuk aset frontend):**
   ```bash
   npm run dev
   ```

7. **Akses Website**
   Buka browser dan kunjungi `http://localhost:8000` (atau port yang ditampilkan di terminal).

---

**Catatan Tambahan:**
Project ini juga mencakup file konfigurasi Pentaho (`pentaho/schema.xml`) untuk keperluan OLAP/Business Intelligence. Pastikan server Mondrian/Pentaho terkonfigurasi jika ingin menjalankan fitur analitik tingkat lanjut.
