# Product Catalog - Backend API

Backend API untuk Platform Marketplace Product Catalog yang dibangun dengan Laravel 11.

## 📋 Daftar Isi

-   [Prasyarat](#prasyarat)
-   [Instalasi](#instalasi)
-   [Konfigurasi](#konfigurasi)
-   [Menjalankan Aplikasi](#menjalankan-aplikasi)
-   [Testing API](#testing-api)
-   [Struktur Project](#struktur-project)
-   [Fitur Utama](#fitur-utama)
-   [Troubleshooting](#troubleshooting)

## 🛠️ Prasyarat

Pastikan komputer Anda memiliki tools berikut dengan versi minimal yang ditentukan:

### 1. PHP

```bash
php --version
```

**Versi yang diperlukan: PHP 8.2 atau lebih tinggi**

Download dari: https://www.php.net/downloads

### 2. Composer

```bash
composer --version
```

**Versi yang diperlukan: Composer 2.0 atau lebih tinggi**

Download dari: https://getcomposer.org/download/

### 3. PostgreSQL

```bash
psql --version
```

**Versi yang diperlukan: PostgreSQL 12 atau lebih tinggi**

Download dari: https://www.postgresql.org/download/

### 4. Git

```bash
git --version
```

**Versi yang diperlukan: Git 2.20 atau lebih tinggi**

Download dari: https://git-scm.com/downloads

### 5. Node.js (Optional, untuk frontend development)

```bash
node --version
npm --version
```

**Versi yang diperlukan: Node.js 16 atau lebih tinggi, npm 7 atau lebih tinggi**

Download dari: https://nodejs.org/

## 📥 Instalasi

### Step 1: Clone Repository

```bash
git clone https://github.com/mohamadsolkhannawawi/backend-product-catalog.git
cd backend-product-catalog
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Setup Environment File

Copy file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

## ⚙️ Konfigurasi

### 1. Konfigurasi Database

Edit file `.env` dan atur parameter database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=product_catalog
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

**Langkah-langkah:**

1. Buka pgAdmin atau terminal PostgreSQL
2. Buat database baru:

```sql
CREATE DATABASE product_catalog;
```

3. Update `.env` dengan username dan password PostgreSQL Anda

### 2. Konfigurasi Email (Optional)

Untuk fitur email notifications, update di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@productcatalog.com
```

### 3. Konfigurasi Frontend URL

```env
FRONTEND_URL=http://localhost:5173
APP_URL=http://localhost:8000
```

## 🚀 Menjalankan Aplikasi

### Step 1: Jalankan Database Migration

```bash
php artisan migrate
```

### Step 2: Seed Database (Optional - untuk data dummy)

```bash
php artisan db:seed
```

### Step 3: Generate Storage Symbolic Link

```bash
php artisan storage:link
```

### Step 4: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
```

### Step 5: Jalankan Development Server

```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`


## 📁 Struktur Project

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # API Controllers
│   │   ├── Middleware/         # Custom Middleware
│   │   └── Requests/           # Form Validation
│   ├── Models/                 # Eloquent Models
│   ├── Notifications/          # Email Notifications
│   └── Services/               # Business Logic
├── database/
│   ├── migrations/             # Database Migrations
│   ├── seeders/                # Database Seeders
│   └── factories/              # Model Factories
├── routes/
│   ├── api.php                 # API Routes
│   ├── web.php                 # Web Routes
│   └── channels.php            # Broadcasting Channels
├── resources/
│   ├── views/                  # Blade Templates
│   └── lang/                   # Language Files
├── storage/                    # Storage (logs, uploads)
├── tests/                      # Unit & Feature Tests
├── config/                     # Configuration Files
├── .env.example                # Environment Template
├── artisan                     # Laravel CLI
└── composer.json               # PHP Dependencies
```

## ✨ Fitur Utama

### Authentication

-   Register pengguna baru
-   Login dengan email & password
-   Token-based authentication (Sanctum)
-   Logout

### Product Management (Seller)

-   Create, Read, Update, Delete produk
-   Upload multiple images
-   Kategori produk
-   Activate/Deactivate produk
-   Manage stok

### Reviews & Ratings

-   Pelanggan dapat memberikan rating & review
-   Rating berdasarkan provinsi pemberi review
-   Notifikasi email ke seller

### Dashboard

**Seller Dashboard:**

-   Overview penjualan
-   Grafik stok per produk
-   Grafik rating per produk
-   Grafik lokasi pemberi rating
-   Generate laporan PDF (Stok, Rating, Restock)

**Admin Dashboard:**

-   Grafik produk per kategori
-   Grafik toko per provinsi
-   Statistik seller (aktif/tidak aktif)
-   Total pemberi rating
-   Manage seller status
-   Generate laporan PDF

### Reports

-   Laporan stok produk
-   Laporan rating produk
-   Laporan restock
-   Laporan seller
-   Laporan produk tertinggi

## 🐛 Troubleshooting

### Error: "Class not found"

```bash
composer dump-autoload
```

### Error: "SQLSTATE[08006]"

-   Pastikan PostgreSQL sudah berjalan
-   Cek konfigurasi database di `.env`
-   Test koneksi: `php artisan tinker` → `DB::connection()->getPDO()`

### Error: "Storage disk not found"

```bash
php artisan storage:link
```

### Error: "Key not generated"

```bash
php artisan key:generate
```

### Port 8000 sudah terpakai

```bash
# Jalankan di port berbeda
php artisan serve --port=8001
```

### Database belum ter-migrate

```bash
php artisan migrate:fresh --seed
```


## 📄 Lisensi

Project ini dilindungi oleh lisensi MIT.

---

**Happy Coding! 🚀**
