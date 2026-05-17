# 🍳 API SatoeRasa (Selera Nusantara Backend API)

[![Laravel Version](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL Version](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![PHP Version](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

API RESTful modern berbasis **Laravel** yang dirancang khusus sebagai penyedia data dan engine backend untuk aplikasi Android **Selera Nusantara**. API ini menangani otentikasi pengguna, manajemen resep masakan, pengelolaan kategori, sinkronisasi resep favorit, hingga integrasi data dashboard admin secara real-time.

---

## 🌟 Fitur Utama API

1. **🔐 Authentication & User Management:** Registrasi, Login, dan Logout menggunakan Laravel Sanctum yang aman.
2. **📖 Recipe RESTful API:** Pencarian resep secara cerdas, pembatasan resep populer, pencarian detail bahan (ingredients), serta langkah memasak (steps).
3. **📂 Category Management:** Manajemen kategori makanan nusantara terpusat.
4. **💖 Favorite Recipe Sync:** Penyimpanan resep favorit pengguna yang disinkronkan secara aman ke database server.
5. **📊 Admin Dashboard Integration:** Penghitungan jumlah resep, jumlah kategori, dan manajemen CRUD resep langsung terhubung ke dashboard admin Android.
6. **🌱 Database Auto-Seeder:** Pengisian otomatis database dengan resep-resep nusantara populer yang siap saji saat setup pertama kali.

---

## 🛠️ Spesifikasi Teknologi

* **Framework:** Laravel 10.x / 11.x
* **Bahasa Pemrograman:** PHP >= 8.1
* **Database:** MySQL / MariaDB (Direkomendasikan menggunakan **Laragon**)
* **Otentikasi:** Laravel Sanctum (Token-based Auth)

---

## 🚀 Panduan Setup & Instalasi (Localhost)

Ikuti langkah-langkah di bawah ini untuk menjalankan API SatoeRasa di komputer Anda atau komputer rekan tim Anda:

### 1. Prasyarat (Prerequisites)
Pastikan Anda sudah menginstal alat-alat berikut di komputer Anda:
* **Laragon** atau **XAMPP** (Web Server + MySQL)
* **Composer** (PHP Dependency Manager)
* **Git**

---

### 2. Langkah Demi Langkah Setup

#### Langkah A: Pull / Clone & Install Dependensi
Buka terminal di folder project `api_satoerasa` Anda, lalu jalankan perintah:
```bash
# Mengunduh library PHP yang diperlukan
composer install
```

#### Langkah B: Duplikasi Konfigurasi Environment (`.env`)
Salin berkas konfigurasi default `.env.example` menjadi `.env`:
```bash
# Di Windows Command Prompt / PowerShell:
copy .env.example .env

# Di Linux / Mac / Git Bash:
cp .env.example .env
```

#### Langkah C: Konfigurasi Database Lokal
Buka berkas `.env` yang baru dibuat menggunakan text editor Anda, lalu sesuaikan bagian koneksi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_satoerasa
DB_USERNAME=root
DB_PASSWORD=             # Kosongkan jika menggunakan Laragon bawaan default
```

#### Langkah D: Generate Application Key
Jalankan perintah ini untuk membuat kunci keamanan unik aplikasi Anda:
```bash
php artisan key:generate
```

#### Langkah E: Buat Database Baru
1. Buka **Laragon** atau **XAMPP**, lalu pastikan service **MySQL** dan **Apache** dalam posisi **Start/Running**.
2. Buka database manager (seperti **HeidiSQL** bawaan Laragon atau **phpMyAdmin**).
3. Buat database baru bernama: **`db_satoerasa`**.

#### Langkah F: Jalankan Migrasi & Database Seeder 🌟
Langkah krusial ini akan otomatis membuat semua struktur tabel database dan langsung mengisinya dengan data resep awal yang lengkap:
```bash
php artisan migrate --seed
```
*Catatan: Semua resep awal, kategori, serta akun admin pengujian otomatis masuk ke database lokal Anda setelah menjalankan perintah di atas.*

#### Langkah G: Hubungkan Storage Media (Sangat Penting!)
Agar gambar-gambar resep/kategori yang diunggah dapat diakses oleh aplikasi Android, Anda harus menghubungkan folder penyimpanan lokal ke folder public:
```bash
php artisan storage:link
```

#### Langkah H: Jalankan API Server lokal
Mulai server backend Anda dengan port default:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
> [!NOTE]
> Menambahkan `--host=0.0.0.0` sangat penting agar API server Anda tidak hanya menerima koneksi dari PC lokal (localhost), melainkan juga dari perangkat HP Fisik yang terhubung di jaringan Wi-Fi yang sama!

---

## 📲 Cara Menghubungkan Android App ke API

Agar aplikasi Android **Selera Nusantara** dapat berkomunikasi dengan API lokal Anda, sesuaikan alamat IP pada kode Android:

### A. Jika Menggunakan Emulator Android Studio:
Cukup gunakan IP loopback khusus emulator Android Studio:
* **Base URL:** `http://10.0.2.2:8000/api/`
* Buka file `RetrofitClient.kt` di Android Studio, ubah baris URL menjadi:
  ```kotlin
  private const val BASE_URL = "http://10.0.2.2:8000/api/"
  ```

### B. Jika Menggunakan HP Fisik (Real Device):
Jika Anda ingin menguji langsung di smartphone fisik Anda:
1. Hubungkan HP fisik dan PC Anda ke **satu Wi-Fi / Hotspot yang sama**.
2. Cari alamat IP local PC Anda. Buka CMD/Terminal PC Anda, lalu ketik `ipconfig`.
3. Cari **IPv4 Address** (contoh: `192.168.1.15`).
4. Buka file `RetrofitClient.kt` di Android Studio, lalu ubah URL menjadi IP PC Anda:
  ```kotlin
  private const val BASE_URL = "http://192.168.1.15:8000/api/" // Sesuaikan dengan IP komputer Anda!
  ```
5. Pastikan server Laravel di PC dijalankan dengan perintah: `php artisan serve --host=0.0.0.0 --port=8000`.
6. Run aplikasi langsung ke HP fisik Anda!

---

## 🧹 Cheat-Sheet Perintah Berguna (Developer Tools)

Jika terjadi eror atau Anda ingin mereset database ke kondisi awal pabrik:
* **Reset & Seed Ulang Database:**
  ```bash
  php artisan migrate:fresh --seed
  ```
* **Melihat Daftar Route API yang Tersedia:**
  ```bash
  php artisan route:list --path=api
  ```
* **Membersihkan Cache Laravel:**
  ```bash
  php artisan optimize:clear
  ```

---

## 👥 Kontributor & Tim Pengembang
* **Frontend Android App:** Tim Selera Nusantara Android
* **Backend API Engineer:** Tim SatoeRasa API

*Selamat Memasak Kode! Semoga Sukses dengan Tugas Akhir PAS / Portofolio Anda! 🍳🔥*
