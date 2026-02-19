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

## 📂 Project Structure

- `app/Http/Controllers/PegawaiImportController.php` - Handles file upload and history logic.
- `app/Http/Controllers/IuranKorpriController.php` - Logic for KORPRI contribution reports.
- `app/Jobs/ProcessPegawaiImport.php` - Background job for processing import files.
- `resources/views/pegawai/import/index.blade.php` - Frontend for Import History.
- `resources/views/admin/iuran-korpri/index.blade.php` - Frontend for KORPRI Reports.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
