# Panduan Deployment AvaraDesa

## Spesifikasi Minimum

- PHP 8.3+
- MySQL 8.0 / MariaDB 10.6+
- Redis 6+ (wajib untuk queue + cache)
- Composer 2.x
- Node.js 20+ (hanya untuk build frontend)
- RAM: 1 GB minimum
- Storage: 500 MB + data penduduk

## 1. Clone & Install

```bash
git clone <repo-url> avaradesa
cd avaradesa

cp .env.example .env
# Sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD di .env

composer install --no-dev --optimize-autoloader

npm ci && npm run build

php artisan key:generate
php artisan storage:link
```

## 2. Database

```bash
# Buat database MySQL/MariaDB terlebih dahulu
mysql -u root -p -e "CREATE DATABASE avaradesa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate --seed
```

## 3. Tuning 1 GB RAM

### PHP-FPM (`/etc/php/8.3/fpm/pool.d/www.conf`)

```ini
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.max_requests = 200
```

### MariaDB (`/etc/mysql/mariadb.conf.d/50-server.cnf`)

```ini
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_method = O_DIRECT
max_connections = 50
query_cache_size = 0
query_cache_type = 0
tmp_table_size = 32M
max_heap_table_size = 32M
```

### Redis (`/etc/redis/redis.conf`)

```
maxmemory 200mb
maxmemory-policy allkeys-lru
```

### PHP OpCache (`/etc/php/8.3/cli/conf.d/10-opcache.ini`)

```ini
opcache.memory_consumption = 64
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
```

## 4. Queue Worker (Supervisor — Redis)

```ini
[program:avaradesa-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/avaradesa/artisan queue:work redis --queue=default --sleep=3 --tries=3 --max-time=3600 --memory=128
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/dev/stdout
```

> **Penting:** Gunakan `queue:work redis` (eksplisit ke Redis), bukan `queue:work` saja. Jika ada perubahan konfigurasi AI di database, restart worker:
> ```bash
> sudo supervisorctl restart avaradesa-worker:*
> ```

## 5. Crontab

```bash
* * * * * cd /path/to/avaradesa && php artisan schedule:run >> /dev/null 2>&1
```

## 6. Optimasi Laravel

```bash
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> **Peringatan:** Jangan jalankan `php artisan config:cache` jika Anda menggunakan pengaturan AI dinamis dari database (`PengaturanDesa`). Config cache akan membekukan konfigurasi AI dan menyebabkan bot Telegram tidak berfungsi. Cukup gunakan `php artisan optimize` tanpa `--config` atau jalankan `php artisan config:clear` setelah mengubah API Key AI di panel admin.

## 7. First Run Setup

Kunjungi `/admin` dan login dengan akun yang dibuat via seeder:

- **Username:** `kepala_desa`
- **Password:** `password123`

> **Penting:** Ganti password segera setelah login pertama.

## 8. Telegram Bot

Set token bot di `.env`:

```
TELEGRAM_BOT_TOKEN=your_bot_token_here
```

Set webhook:

```bash
curl -X POST "https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://domain-anda/api/v1/telegram/webhook"
```

## 9. Verifikasi Deployment

```bash
php artisan about        # Cek konfigurasi
php artisan queue:status # Pastikan queue berjalan
curl https://domain-anda/up  # Harus return 200
```

## Troubleshooting

**PDF tidak muncul / error DomPDF:**
```bash
composer dump-autoload
php artisan view:clear
php artisan config:clear
```

**Vite manifest error (500 pada halaman publik):**
```bash
npm ci && npm run build
```

**Storage link error (foto tidak tampil):**
```bash
php artisan storage:link
```
