# ASN Dashboard v2

A comprehensive dashboard application for managing State Civil Apparatus (ASN) data, featuring advanced statistics, bulk data import, and financial reporting for KORPRI contributions. Built with Laravel 12 and Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 🚀 Key Features

### 📊 Interactive Dashboard
- **Visual Statistics**: Interactive charts displaying employee composition (Pie Chart) and Top 10 OPDs (Bar Chart).
- **Real-time Counters**: Instant overview of total employees, active status, and other key metrics.
- **Dark Mode**: Fully supported dark mode theme for better visual ergonomics.

### 📥 Advanced Import System
- **Bulk Data Processing**: Support for uploading large Excel/CSV files for employee data.
- **Background Processing**: Uses Laravel Queues to handle imports asynchronously without blocking the UI.
- **Progress Tracking**: Real-time progress bars and status updates for active imports.
- **Import History**: Paginated history of all past imports with detailed error reporting and status logs.

### 💰 Iuran KORPRI Management
- **Automated Calculation**: Calculates contributions automatically based on employee "Golongan" (Rank).
- **Configurable Rates**: Admin can easily update contribution rates (Tarif) directly from the frontend.
- **Detailed Reporting**:
  - Global summary of total contributions.
  - Per-OPD breakdown with pagination (10 items per page).
  - Categorization of employees into "Golongan" and "Non-Golongan" (e.g., PPPK).
- **Standalone Module**: Dedicated sidebar menu for quick access to financial reports.

### 🛠️ Data & User Management
- **Sidebar Filters**: Global filter to view data specific to a Unit Kerja (OPD).
- **User Roles**: Role-based access control (Admin/User) for secure data management.
- **Searchable Tables**: Fast search functionality across data tables.

---

## 💻 Technology Stack

- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Blade Templates, Tailwind CSS 4.0, Alpine.js (via Livewire/Blade), ApexCharts
- **Database**: MySQL 8.0+
- **Build Tools**: Vite 7.0, NPM

---

## ⚙️ Installation & Setup

Follow these steps to set up the project locally:

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Database

### 1. Clone the Repository
```bash
git clone https://github.com/nubimahendra/asn-dashboard-laravel-v2.git
cd asn-dashboard-laravel-v2
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```
*Open `.env` and configure your database settings (DB_DATABASE, DB_USERNAME, etc.).*

### 4. Database Setup
Run migrations and seeders to populate initial data (User roles, Golongan, Iuran Rates):
```bash
php artisan migrate --seed
```

### 5. Link Storage
Create a symbolic link for public file storage (required for file uploads):
```bash
php artisan storage:link
```

### 6. Build Frontend
```bash
npm run dev
# Or for production: npm run build
```

---

## ▶️ Running the Application

To run the full application, you need to run the web server and the queue worker (for imports) simultaneously.

### Terminal 1: Web Server
```bash
php artisan serve
```
Access the app at `http://localhost:8000`.

### Terminal 2: Queue Worker (Crucial for Import System)
This processes the background data import jobs.
```bash
php artisan queue:work
```

---

## 🚀 Panduan Deploy / Instalasi di VPS (Production)

Untuk melakukan instalasi aplikasi di VPS (Virtual Private Server) berbasis Linux (Ubuntu/Debian), ikuti langkah-langkah berikut:

### 1. Persiapan Server
Pastikan server Anda sudah terinstal:
- **Web Server:** Nginx atau Apache
- **PHP:** Versi 8.2 atau lebih baru beserta ekstensi yang dibutuhkan (pdo_mysql, mbstring, xml, ctype, json, zip, dom, dll)
- **Database:** MySQL 8.0+ atau MariaDB
- **Composer** & **Node.js (NPM)**
- **Supervisor** (untuk menjalankan Queue Worker di background)

### 2. Upload / Clone Source Code
Anda bisa melakukan `git clone` atau meng-upload file ZIP dari project ke direktori web server (misalnya: `/var/www/asn-dashboard`).

```bash
cd /var/www/
git clone https://github.com/nubimahendra/asn-dashboard-laravel-v2.git asn-dashboard
cd asn-dashboard
```

### 3. Install Dependencies
```bash
# Install PHP dependencies (tanpa package dev)
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
```

### 4. Konfigurasi Environment & Zona Waktu
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi berikut di file `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com       # Ganti dengan domain VPS Anda (Penting agar UI/Sidebar berfungsi)

APP_TIMEZONE="Asia/Jakarta"           # Sesuaikan zona waktu (opsional)

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_vps
DB_USERNAME=user_database
DB_PASSWORD=password_database

QUEUE_CONNECTION=database             # Wajib untuk fitur import background
```

### 5. Setup Database & Key
```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
```

### 6. Build Frontend (Sangat Penting)
Agar tampilan UI, CSS (Tailwind), dan JavaScript (termasuk fitur klik pada Sidebar) berfungsi normal di VPS, **wajib** melakukan *build* aset untuk *production*:
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
Jalankan perintah ini di akhir setup untuk mempercepat kinerja dan mengatasi *cache* yang nyangkut:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

### 9. Setup Supervisor (Untuk Background Process)
Aplikasi ini membutuhkan *Queue Worker* agar fitur Export/Import Excel berjalan. Buat file konfigurasi Supervisor:
```bash
sudo nano /etc/supervisor/conf.d/asn-dashboard-worker.conf
```
Isi dengan:
```ini
[program:asn-dashboard-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/asn-dashboard/artisan queue:work --sleep=3 --tries=3 --max-time=3600
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

## 📂 Project Structure

- `app/Http/Controllers/PegawaiImportController.php` - Handles file upload and history logic.
- `app/Http/Controllers/IuranKorpriController.php` - Logic for KORPRI contribution reports.
- `app/Jobs/ProcessPegawaiImport.php` - Background job for processing import files.
- `resources/views/pegawai/import/index.blade.php` - Frontend for Import History.
- `resources/views/admin/iuran-korpri/index.blade.php` - Frontend for KORPRI Reports.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
