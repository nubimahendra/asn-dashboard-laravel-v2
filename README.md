# ASN Dashboard v2

A comprehensive dashboard application for managing State Civil Apparatus (ASN) data, featuring advanced statistics, bulk data import, robust synchronization engines, and complex financial reporting for KORPRI contributions. Built with Laravel 12 and Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 🚀 Key Features & Modules

### 1. MARI (Manajemen Analisis & Rekapitulasi Info)
- **Interactive Dashboard**: Visual statistics, employee composition, generational breakdowns, and Top 10 OPDs.
- **Rincian Iuran KORPRI**: Hybrid dual-parameter calculation system. Calculates contributions based on **Eselon** for structural positions and **Golongan** for non-structural staff (PNS/PPPK).
- **Rekon & Override**: Manual override capabilities for individual KORPRI contributions to handle edge cases, tracked with audit logs.
- **Dark/Light Mode**: Global theme toggle for visual ergonomics.

### 2. MASN (Manajemen Aparatur Sipil Negara)
- **Employee Master Data**: Comprehensive database of all active and inactive employees.
- **Access Control**: Secure access restricted to authorized personnel with specific module permissions.
- **Data Integrity**: Strict validation and separation between PNS and PPPK data to prevent ID collisions (utilizing `kedudukan_hukum_id`).

### 3. MESRA (Manajemen Evaluasi & Sinkronisasi Referensi ASN)
- **Advanced Sync Engine**: Upload large CSV exports from SIDAWAI. The system stages the data and performs a "Diff" check to categorize records as `new`, `changed`, or `unchanged`.
- **Memory-Safe Background Processing**: Utilizes Laravel Queues with optimized chunking (`chunkById`) to prevent Out-Of-Memory (OOM) errors during massive data imports.
- **Real-time Progress Tracking**: Interactive UI showing sync progress, processed counts, and anomaly detection.
- **Tarif Management**: UI to configure dynamic contribution rates based on Golongan or Eselon levels.

---

## 💻 Technology Stack

- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Blade Templates, Tailwind CSS 4.0, Alpine.js, ApexCharts
- **Database**: MySQL 8.0+
- **Build Tools**: Vite 7.0, NPM

---

## ⚙️ Installation & Setup (Local)

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Database

### Steps
1. **Clone the Repository**
   ```bash
   git clone https://github.com/nubimahendra/asn-dashboard-laravel-v2.git
   cd asn-dashboard-laravel-v2
   ```
2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure `.env` (Database, etc.). Set `QUEUE_CONNECTION=database` for imports.*
4. **Database Setup**
   ```bash
   php artisan migrate:fresh --seed
   ```
5. **Link Storage**
   ```bash
   php artisan storage:link
   ```
6. **Build Frontend**
   ```bash
   npm run dev
   ```
7. **Run Queues (Terminal 2)**
   ```bash
   php artisan queue:work
   ```

---

## 🚀 Panduan Deploy / Instalasi di VPS (Production)

Untuk melakukan instalasi aplikasi di VPS berbasis Linux (Ubuntu/Debian), ikuti panduan komprehensif berikut:

### 1. Persiapan Server
Pastikan server Anda memiliki:
- **Web Server:** Nginx atau Apache
- **PHP:** Versi 8.2+ beserta ekstensi (`pdo_mysql`, `mbstring`, `xml`, `ctype`, `json`, `zip`, `dom`, `gd`, dll)
- **Database:** MySQL 8.0+ atau MariaDB
- **Composer** & **Node.js (NPM)**
- **Supervisor** (Penting: untuk menjaga Queue Worker tetap hidup di background)

### 2. Upload / Clone Source Code
```bash
cd /var/www/
git clone https://github.com/nubimahendra/asn-dashboard-laravel-v2.git asn-dashboard
cd asn-dashboard
```

### 3. Install Dependencies
```bash
# Install PHP dependencies (tanpa package dev, optimasi autoloader)
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
```

### 4. Konfigurasi Environment & Zona Waktu
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com       # Ganti dengan domain VPS Anda (Penting untuk asset URL)

APP_TIMEZONE="Asia/Jakarta"

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_vps
DB_USERNAME=user_database
DB_PASSWORD=password_database

QUEUE_CONNECTION=database             # Wajib untuk sinkronisasi data background
```

### 5. Setup Database & Key
```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
```

### 6. Build Frontend (Sangat Penting)
Agar tampilan UI (Tailwind) dan JavaScript berfungsi normal di VPS, **wajib** melakukan *build* aset:
```bash
npm run build
```

> **⚠️ PERHATIAN PENTING (Masalah Sidebar Tidak Bisa Diklik):**
> Jika Anda menggunakan *Laravel Vite* dan menyalin seluruh file dari komputer lokal ke VPS secara menimpa, pastikan file bernama **`hot`** di dalam folder `public/` (yaitu `public/hot`) **DIHAPUS**. Jika file tersebut ada di VPS, Laravel akan mencari dev server lokal sehingga aset JavaScript tidak termuat.
> ```bash
> rm -f public/hot
> ```

### 7. Atur Hak Akses (Permissions)
Berikan hak akses (RW) pada folder `storage` dan `bootstrap/cache` untuk *user web server* (biasanya `www-data` atau `nginx`).
```bash
sudo chown -R www-data:www-data /var/www/asn-dashboard
sudo chmod -R 775 /var/www/asn-dashboard/storage
sudo chmod -R 775 /var/www/asn-dashboard/bootstrap/cache
```

### 8. Optimasi Laravel Cache
Jalankan perintah ini di akhir setup untuk mempercepat kinerja dan mengatasi *cache* lama:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

### 9. Setup Supervisor (Untuk Background Process & Sync)
Aplikasi ini memiliki *Sync Engine* yang memproses ribuan data. Agar proses ini stabil dan tidak mati saat Anda menutup terminal, gunakan Supervisor.

Buat file konfigurasi Supervisor:
```bash
sudo nano /etc/supervisor/conf.d/asn-dashboard-worker.conf
```
Isi dengan konfigurasi berikut (perhatikan penambahan `--memory` untuk mencegah OOM saat import besar):
```ini
[program:asn-dashboard-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/asn-dashboard/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --memory=512
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/asn-dashboard/storage/logs/worker.log
stopwaitsecs=3600
```
Update & Start Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start asn-dashboard-worker:*
```

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
