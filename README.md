# ASN Dashboard v2

A comprehensive dashboard application for managing State Civil Apparatus (ASN) data, featuring advanced statistics, bulk data import, robust synchronization engines, complex financial reporting for KORPRI contributions, and SLKS proposal management. Built with Laravel 12 and Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

## 🚀 Key Features & Modules

### 1. MASN (Manajemen Aparatur Sipil Negara)
- **Interactive Dashboard**: Visual statistics, employee composition, generational breakdowns, and Top 10 OPDs.
- **Employee Master Data**: Comprehensive database of all active and inactive employees with profile pages.
- **Advanced Sync Engine (CSV Import)**: Upload large CSV exports from SIDAWAI. The system stages the data and performs a "Diff" check to categorize records as `new`, `changed`, or `unchanged`.
- **Memory-Safe Background Processing**: Utilizes Laravel Queues with optimized chunking (`chunkById`) to prevent Out-Of-Memory (OOM) errors during massive data imports.
- **Real-time Progress Tracking**: Interactive UI showing sync progress, processed counts, and anomaly detection.
- **Monthly Snapshot & History**: Archive employee data snapshots per month with Excel/PDF export.
- **User Management**: Admin-only CRUD for managing users and module permissions.

### 2. MARI (Manajemen Analisis & Rekapitulasi Info)
- **Dashboard Analitik**: Ringkasan statistik iuran dan data pegawai.
- **Rincian Iuran KORPRI**: Hybrid dual-parameter calculation system. Calculates contributions based on **Eselon** for structural positions and **Golongan** for non-structural staff (PNS/PPPK).
- **Rekon & Override**: Manual override capabilities for individual KORPRI contributions to handle edge cases, tracked with audit logs.
- **Eselon Mapping**: Auto-generate and manage jabatan-to-eselon mappings.
- **Kelas Jabatan**: Manage jabatan-to-kelas mappings via Perbup rules, with bulk import support.
- **Tarif Management**: UI to configure dynamic contribution rates based on Golongan or Eselon levels.
- **Invoice Generator**: Configurable invoice generation with header/footer settings.

### 3. MESRA (Manajemen Evaluasi & Sinkronisasi Referensi ASN)
- **Surat Masuk**: Full CRUD for incoming correspondence tracking with print support.
- **Pengajuan Cerai**: Record and track divorce applications with employee lookup and Excel export.
- **Chatbot / Helpdesk**: Dual-mode (Bot FAQ + Human Admin) chat system with session management, NIP verification, and admin panel.

### 4. SIPUT (Sistem Pengusulan) — 🆕 NEW
- **Dashboard Statistik**: Overview of SLKS proposals by status (draft, diajukan, disetujui) with breakdown by employee type (PNS, PPPK, PPPK Purnawaktu).
- **Usul SLKS**: Form pengusulan Surat Keterangan Lulus Kinerja with NIP-based employee search, auto-fill data, and SLKS history lookup.
- **Manage Usulan**: Table view for managing all active proposals with edit/delete capabilities.
- **Laporan**: Print-ready report view for all active proposals.

### Global Features
- **Dark/Light Mode**: Global theme toggle for visual ergonomics.
- **Multi-Module Access Control**: Role-based (admin/user) with per-module permission (JSON array).
- **Hub Navigation**: Central module selector based on user permissions.
- **App Settings**: Runtime-configurable key-value settings (e.g., invoice headers).

---

## 💻 Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel | 12.x |
| PHP | PHP | ≥ 8.2 |
| Frontend | Blade + Tailwind CSS | 4.0 |
| JS Framework | Alpine.js | 3.x |
| Charts | ApexCharts | CDN |
| Autocomplete | Tom Select | 2.5 |
| Build Tool | Vite | 7.x |
| Database | MySQL | 8.0+ |
| Queue | Laravel Queue | database driver |
| Excel | Maatwebsite/Excel | 3.1 |
| PDF | barryvdh/laravel-dompdf | 3.1 |
| Realtime | Livewire | 4.1 |
| Auth | Laravel Sanctum | 4.0 (session-based) |
| Containerization | Docker + Nginx | Optional |

---

## ⚙️ Installation & Setup (Local)

### Prerequisites
- PHP >= 8.2 (with extensions: `pdo_mysql`, `mbstring`, `xml`, `ctype`, `json`, `zip`, `dom`, `gd`, `bcmath`, `pcntl`)
- Composer
- Node.js >= 18 & NPM
- MySQL 8.0+ Database

### Option A: Quick Setup (Recommended)

```bash
# Clone
git clone https://github.com/nubimahendra/asn-dashboard-laravel-v2.git
cd asn-dashboard-laravel-v2

# One-command setup (install deps, generate key, migrate, build frontend)
composer setup
```

### Option B: Manual Setup

```bash
# 1. Clone the Repository
git clone https://github.com/nubimahendra/asn-dashboard-laravel-v2.git
cd asn-dashboard-laravel-v2

# 2. Install Dependencies
composer install
npm install

# 3. Environment Configuration
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_dashboard_asn
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database      # Required for CSV sync imports
```

```bash
# 4. Database Setup
php artisan migrate --seed

# 5. Link Storage
php artisan storage:link
```

### Running Locally

#### Option 1: Concurrent Mode (Recommended)

Runs all services at once (server + queue worker + log tail + vite):

```bash
composer dev
```

#### Option 2: Manual (Multiple Terminals)

**Terminal 1** — Laravel Server:
```bash
php artisan serve
```

**Terminal 2** — Queue Worker (required for CSV imports):
```bash
php artisan queue:work --timeout=600 --sleep=3 --tries=3
```

**Terminal 3** — Vite Dev Server:
```bash
npm run dev
```

### Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@asn.go.id` | `password` |
| User | *(see UserSeeder)* | `password` |

---

## 🐳 Docker Setup (Optional)

```bash
# Build and start all services (app + nginx + mysql + redis)
docker-compose up -d --build

# Run migrations inside container
docker-compose exec app php artisan migrate --seed

# Access at http://localhost:8080
```

Docker stack includes:
- **app**: PHP 8.3-FPM Alpine with all required extensions
- **web**: Nginx Alpine (port 8080)
- **db**: MySQL 8.0 with persistent volume
- **redis**: Redis Alpine

---

## 🚀 Panduan Deploy / Instalasi di VPS (Production)

Untuk melakukan instalasi aplikasi di VPS berbasis Linux (Ubuntu/Debian), ikuti panduan komprehensif berikut:

### 1. Persiapan Server

Pastikan server Anda memiliki:
- **Web Server:** Nginx atau Apache
- **PHP:** Versi 8.2+ beserta ekstensi (`pdo_mysql`, `mbstring`, `xml`, `ctype`, `json`, `zip`, `dom`, `gd`, `bcmath`, `pcntl`)
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
APP_NAME="ASN Dashboard"
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
SESSION_DRIVER=database
CACHE_STORE=database

LOG_CHANNEL=daily                     # Rotasi log harian, lebih aman di production
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

### 8. Konfigurasi Nginx

Buat file konfigurasi Nginx:
```bash
sudo nano /etc/nginx/sites-available/asn-dashboard
```

```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/asn-dashboard/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 50M;  # Penting untuk upload CSV besar
}
```

Aktifkan site dan restart:
```bash
sudo ln -s /etc/nginx/sites-available/asn-dashboard /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 9. Optimasi Laravel Cache

Jalankan perintah ini di akhir setup untuk mempercepat kinerja dan mengatasi *cache* lama:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

### 10. Setup Supervisor (Untuk Background Process & Sync)

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

### 11. SSL (HTTPS) — Recommended

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com
```

---

## 🔄 Update / Redeploy di VPS

Ketika ada update dari repository, jalankan langkah berikut di VPS:

```bash
cd /var/www/asn-dashboard

# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install

# 3. Run migrations
php artisan migrate --force

# 4. Rebuild frontend
npm run build

# 5. Clear & rebuild cache
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 6. Restart queue worker
sudo supervisorctl restart asn-dashboard-worker:*
```

---

## 📁 Project Structure

```
asn-dashboard-laravel-v2/
├── app/
│   ├── Console/Commands/       # 6 Artisan commands
│   ├── Exports/                # Excel/PDF exports (2 classes)
│   ├── Helpers/                # GolonganHelper
│   ├── Http/
│   │   ├── Controllers/        # 24 controllers
│   │   └── Middleware/         # CheckModuleAccess, IsAdmin
│   ├── Imports/                # CSV/Excel imports (3 classes)
│   ├── Jobs/                   # ProcessPegawaiImport
│   ├── Models/                 # 45 Eloquent models
│   ├── Providers/              # AppServiceProvider
│   ├── Services/               # 10 service classes
│   └── View/Components/        # SearchableSelect
├── config/                     # Laravel config (with sidawai DB connection)
├── database/
│   ├── migrations/             # 63 migrations
│   └── seeders/                # 9 seeders
├── docker/                     # Docker config (entrypoint + nginx)
├── resources/views/            # Blade views (5 layouts, 9 view dirs)
├── routes/
│   ├── web.php                 # Main routes (168 lines, 4 modules)
│   └── api.php                 # Chat API routes
├── Dockerfile                  # Multi-stage build
├── docker-compose.yml          # Full stack: app + nginx + mysql + redis
├── skills.md                   # 🧠 Knowledge base for AI/developers
└── laravel-architecture.md     # Architecture documentation
```

---

## 📚 Documentation

- **[skills.md](skills.md)** — Comprehensive knowledge base for developers and AI agents. Read before developing.
- **[laravel-architecture.md](laravel-architecture.md)** — Detailed architecture documentation with database schemas and coding patterns.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
