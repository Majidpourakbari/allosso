# راهنمای Deploy AlloSSO روی CloudPanel

## پیش‌نیازها

1. دسترسی SSH به سرور
2. دسترسی به CloudPanel
3. دیتابیس MySQL ایجاد شده
4. دامنه یا subdomain تنظیم شده

## اطلاعات دیتابیس

- **Host:** 127.0.0.1
- **Port:** 3306
- **Database:** mainsso
- **Username:** main
- **Password:** Dehghan_339*

---

## مراحل Deploy

### 1. اتصال به سرور

```bash
ssh root@allolancer-8gb-nbg1
cd /home/allo-sso/htdocs/www.allo-sso.com
```

### 2. آپلود فایل‌های پروژه

اگر فایل‌ها را از طریق Git clone می‌کنید:

```bash
# اگر Git repository دارید
git clone your-repository-url .
```

یا اگر فایل‌ها را از طریق FTP/SFTP آپلود کرده‌اید، مطمئن شوید همه فایل‌ها در مسیر `/home/allo-sso/htdocs/www.allo-sso.com` هستند.

### 3. نصب Dependencies

```bash
# نصب Composer dependencies
composer install --optimize-autoloader --no-dev

# نصب NPM dependencies (اگر نیاز باشد)
npm install
npm run build
```

### 4. تنظیم فایل .env

```bash
# کپی فایل .env.example
cp .env.example .env

# ویرایش فایل .env
nano .env
```

محتویات `.env`:

```env
APP_NAME=AlloSSO
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://allo-sso.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mainsso
DB_USERNAME=main
DB_PASSWORD=Dehghan_339*

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

ALLO_SSO_API_KEY=your-secret-api-key-here-generate-a-strong-one

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. تولید Application Key

```bash
php artisan key:generate
```

### 6. اجرای Migration ها

```bash
php artisan migrate --force
```

### 7. تنظیم Permissions

```bash
# تنظیم مجوزهای فایل‌ها و پوشه‌ها
chown -R allo-sso:allo-sso /home/allo-sso/htdocs/www.allo-sso.com
chmod -R 755 /home/allo-sso/htdocs/www.allo-sso.com
chmod -R 775 /home/allo-sso/htdocs/www.allo-sso.com/storage
chmod -R 775 /home/allo-sso/htdocs/www.allo-sso.com/bootstrap/cache
```

### 8. تنظیم CloudPanel

#### در CloudPanel:

1. **Site Settings:**
   - Document Root: `/home/allo-sso/htdocs/www.allo-sso.com/public`
   - PHP Version: 8.2 یا بالاتر

2. **PHP Settings:**
   - `memory_limit`: 256M یا بیشتر
   - `upload_max_filesize`: 10M
   - `post_max_size`: 10M
   - `max_execution_time`: 300

3. **Extensions مورد نیاز:**
   - `gd` (برای CAPTCHA)
   - `mbstring`
   - `openssl`
   - `pdo_mysql`
   - `fileinfo`

### 9. تنظیم Nginx (اگر نیاز به تنظیمات خاص دارید)

در CloudPanel، می‌توانید تنظیمات Nginx را ویرایش کنید:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### 10. Cache و Optimization

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear old cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 11. تنظیم Cron Job (اگر نیاز باشد)

در CloudPanel، به بخش Cron Jobs بروید و این job را اضافه کنید:

```bash
* * * * * cd /home/allo-sso/htdocs/www.allo-sso.com && php artisan schedule:run >> /dev/null 2>&1
```

### 12. تست API

```bash
# تست API endpoint
curl -X GET "https://allo-sso.com/api/v1/check-allohash?allohash=test&api_key=your-api-key" \
  -H "X-API-Key: your-api-key"
```

---

## بررسی‌های نهایی

### 1. بررسی فایل‌ها
```bash
ls -la /home/allo-sso/htdocs/www.allo-sso.com
```

### 2. بررسی Permissions
```bash
ls -la /home/allo-sso/htdocs/www.allo-sso.com/storage
ls -la /home/allo-sso/htdocs/www.allo-sso.com/bootstrap/cache
```

### 3. بررسی Log ها
```bash
tail -f /home/allo-sso/htdocs/www.allo-sso.com/storage/logs/laravel.log
```

### 4. تست اتصال دیتابیس
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## مشکلات رایج و راه‌حل

### خطای 500 Internal Server Error
```bash
# بررسی log ها
tail -f storage/logs/laravel.log

# بررسی permissions
chmod -R 775 storage bootstrap/cache
```

### خطای Database Connection
- بررسی اطلاعات دیتابیس در `.env`
- بررسی دسترسی کاربر دیتابیس
- تست اتصال: `mysql -u main -p mainsso`

### خطای Permission Denied
```bash
chown -R allo-sso:allo-sso /home/allo-sso/htdocs/www.allo-sso.com
chmod -R 755 /home/allo-sso/htdocs/www.allo-sso.com
```

### CAPTCHA کار نمی‌کند
- بررسی نصب extension `gd`: `php -m | grep gd`
- نصب: `apt-get install php8.2-gd` (بسته به نسخه PHP)

---

## به‌روزرسانی پروژه

```bash
cd /home/allo-sso/htdocs/www.allo-sso.com

# Pull آخرین تغییرات
git pull origin main

# نصب dependencies جدید
composer install --optimize-autoloader --no-dev

# اجرای migration های جدید
php artisan migrate --force

# Clear و rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Backup

### Backup دیتابیس
```bash
mysqldump -u main -p mainsso > backup_$(date +%Y%m%d).sql
```

### Backup فایل‌ها
```bash
tar -czf backup_files_$(date +%Y%m%d).tar.gz /home/allo-sso/htdocs/www.allo-sso.com
```

---

## امنیت

1. **API Key قوی:** یک API key قوی و طولانی در `.env` تنظیم کنید
2. **APP_DEBUG=false:** در production حتماً false باشد
3. **HTTPS:** از SSL certificate استفاده کنید
4. **Firewall:** فقط پورت‌های لازم را باز کنید

---

**نکته:** بعد از deploy، حتماً API را تست کنید و مطمئن شوید همه چیز درست کار می‌کند.

