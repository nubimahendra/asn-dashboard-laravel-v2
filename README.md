<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

</p>

## Technology Stack

**Backend**
*   **Framework:** Laravel 12.0
*   **Language:** PHP ^8.2
*   **Database:** MySQL (implied by typical Laravel usage & previous conversations)

**Frontend**
*   **Styling:** Tailwind CSS 4.0 (via CDN & Vite)
*   **Charting:** ApexCharts (via CDN)
*   **Build Tool:** Vite 7.0
*   **Font:** Google Fonts (Inter)

**Key Features**
*   **Dashboard:** Custom statistical dashboard with charts.
*   **Dark Mode:** Built-in dark/light mode toggle with persistence.
*   **Sidebar Filter:** Custom searchable dropdown for filtering data by Unit Kerja (OPD).
*   **Responsive:** optimized for desktop and mobile views.

## Running Locally

To run this project on your local machine without Docker:

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Steps
1.  **Clone the repository**
    ```bash
    git clone https://github.com/nubimahendra/asn-dashboard-laravel.git
    cd asn-dashboard-laravel
    ```
2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```
3.  **Setup Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database credentials in the `.env` file.*
4.  **Run Migrations**
    ```bash
    php artisan migrate
    ```
5.  **Install & Build Frontend Assets**
    ```bash
    npm install
    npm run dev
    ```
6.  **Serve the Application**
    ```bash
    php artisan serve
    ```
    Visit `http://localhost:8000`

## Deployment on Ubuntu VPS

This guide assumes a fresh Ubuntu 22.04/24.04 server with Nginx, MySQL, PHP 8.2, and Composer installed (LEMP Stack).

1.  **Clone the Repository**
    ```bash
    cd /var/www
    git clone https://github.com/nubimahendra/asn-dashboard-laravel.git
    cd asn-dashboard-laravel
    ```

2.  **Set Permissions**
    ```bash
    sudo chown -R www-data:www-data /var/www/asn-dashboard-laravel
    sudo chmod -R 775 storage bootstrap/cache
    ```

3.  **Install Dependencies**
    ```bash
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    ```

4.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Edit `.env` and set `APP_ENV=production`, `APP_DEBUG=false`, and database credentials.*

5.  **Database Migration**
    ```bash
    php artisan migrate --force
    ```

6.  **Nginx Configuration**
    Create a new config file: `/etc/nginx/sites-available/asn-dashboard`
    ```nginx
    server {
        listen 80;
        server_name your-domain.com;
        root /var/www/asn-dashboard-laravel/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
    ```

7.  **Enable Site & Restart Nginx**
    ```bash
    sudo ln -s /etc/nginx/sites-available/asn-dashboard /etc/nginx/sites-enabled/
    sudo nginx -t
    sudo systemctl restart nginx
    ```

## Deployment with Docker

This project is fully containerized using Docker, allowing it to be deployed easily on any environment.

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Getting Started

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd asn-dashboard
    ```

2.  **Setup environment:**
    ```bash
    cp .env.example .env
    ```

3.  **Start the application:**
    ```bash
    docker-compose up -d --build
    ```

4.  **Install dependencies and run migrations:**
    ```bash
    docker-compose exec app composer install
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan migrate
    ```

5.  **Access the application:**
    Open [http://localhost:8080](http://localhost:8080) in your browser.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
