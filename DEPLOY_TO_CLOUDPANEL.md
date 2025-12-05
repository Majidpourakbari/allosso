# راهنمای Deployment به CloudPanel

این راهنما نحوه deploy و به‌روزرسانی پروژه AlloSSO از گیت به سرور CloudPanel را توضیح می‌دهد.

## اطلاعات سرور

- **Root Directory:** `/home/allo-sso/htdocs/www.allo-sso.com`
- **Document Root:** `/home/allo-sso/htdocs/www.allo-sso.com/public`
- **Domain:** `www.allo-sso.com`
- **Git Repository:** `https://github.com/Majidpourakbari/allosso.git`

---

## روش 1: استفاده از اسکریپت Deployment (پیشنهادی)

### مرحله 1: اتصال به سرور

```bash
ssh root@allolancer-8gb-nbg1
# یا
ssh allo-sso@allolancer-8gb-nbg1
```

### مرحله 2: Clone کردن پروژه (فقط برای اولین بار)

```bash
cd /home/allo-sso/htdocs
git clone https://github.com/Majidpourakbari/allosso.git www.allo-sso.com
cd www.allo-sso.com
```

### مرحله 3: تنظیم Git (فقط برای اولین بار)

```bash
cd /home/allo-sso/htdocs/www.allo-sso.com

# تنظیم remote URL
git remote set-url origin https://github.com/Majidpourakbari/allosso.git

# یا اگر از SSH استفاده می‌کنید:
# git remote set-url origin git@github.com:Majidpourakbari/allosso.git
```

### مرحله 4: تنظیم فایل .env (فقط برای اولین بار)

```bash
cd /home/allo-sso/htdocs/www.allo-sso.com

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
APP_URL=https://www.allo-sso.com

LOG_CHANNEL=stack
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

ALLO_SSO_API_KEY=your-secret-api-key-here
```

### مرحله 5: تولید Application Key (فقط برای اولین بار)

```bash
php artisan key:generate
```

### مرحله 6: اجرای اسکریپت Deployment

```bash
cd /home/allo-sso/htdocs/www.allo-sso.com

# اجرای اسکریپت
bash deploy.sh
```

یا اگر فایل executable است:

```bash
./deploy.sh
```

---

## روش 2: Deployment دستی

### مرحله 1: اتصال به سرور

```bash
ssh root@allolancer-8gb-nbg1
cd /home/allo-sso/htdocs/www.allo-sso.com
```

### مرحله 2: Pull کردن از گیت

```bash
git fetch origin
git reset --hard origin/master
git clean -fd
```

### مرحله 3: نصب Dependencies

```bash
# نصب Composer packages
composer install --optimize-autoloader --no-dev

# نصب NPM packages (در صورت نیاز)
npm ci --production
npm run build
```

### مرحله 4: Clear و Rebuild Cache

```bash
# پاک کردن cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache کردن
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### مرحله 5: اجرای Migrations

```bash
php artisan migrate --force
```

### مرحله 6: تنظیم Permissions

```bash
chown -R allo-sso:allo-sso /home/allo-sso/htdocs/www.allo-sso.com
chmod -R 755 /home/allo-sso/htdocs/www.allo-sso.com
chmod -R 775 /home/allo-sso/htdocs/www.allo-sso.com/storage
chmod -R 775 /home/allo-sso/htdocs/www.allo-sso.com/bootstrap/cache
```

---

## تنظیمات CloudPanel

### 1. Document Root

در CloudPanel:
- **Sites** → **www.allo-sso.com** → **Settings**
- **Document Root:** `/home/allo-sso/htdocs/www.allo-sso.com/public`

### 2. PHP Version

- **PHP Version:** 8.2 یا بالاتر
- **Extensions مورد نیاز:**
  - `gd` (برای CAPTCHA)
  - `mbstring`
  - `openssl`
  - `pdo_mysql`
  - `fileinfo`
  - `zip`
  - `curl`

### 3. PHP Settings

- `memory_limit`: 256M
- `upload_max_filesize`: 10M
- `post_max_size`: 10M
- `max_execution_time`: 300

---

## به‌روزرسانی خودکار (Git Hook)

می‌توانید یک Git hook تنظیم کنید تا به صورت خودکار deploy شود:

### ایجاد Post-Receive Hook

```bash
# در سرور
cd /home/allo-sso/htdocs/www.allo-sso.com
mkdir -p .git/hooks

cat > .git/hooks/post-receive << 'EOF'
#!/bin/bash
cd /home/allo-sso/htdocs/www.allo-sso.com
git reset --hard origin/master
bash deploy.sh
EOF

chmod +x .git/hooks/post-receive
```

---

## استفاده از Personal Access Token

اگر از HTTPS استفاده می‌کنید و نیاز به authentication دارید:

```bash
# تنظیم credential helper
git config --global credential.helper store

# یا استفاده از token در URL (موقت)
git remote set-url origin https://YOUR_TOKEN@github.com/Majidpourakbari/allosso.git
```

---

## بررسی‌های بعد از Deployment

### 1. بررسی دسترسی سایت

```bash
curl -I https://www.allo-sso.com
```

### 2. بررسی Log ها

```bash
tail -f /home/allo-sso/htdocs/www.allo-sso.com/storage/logs/laravel.log
```

### 3. بررسی Permissions

```bash
ls -la /home/allo-sso/htdocs/www.allo-sso.com/storage
ls -la /home/allo-sso/htdocs/www.allo-sso.com/bootstrap/cache
```

### 4. تست API

```bash
curl -X GET "https://www.allo-sso.com/api/v1/check-allohash?allohash=test&api_key=your-api-key"
```

---

## مشکلات رایج

### خطای Permission Denied

```bash
chown -R allo-sso:allo-sso /home/allo-sso/htdocs/www.allo-sso.com
chmod -R 755 /home/allo-sso/htdocs/www.allo-sso.com
```

### خطای Git Authentication

```bash
# استفاده از Personal Access Token
git remote set-url origin https://YOUR_TOKEN@github.com/Majidpourakbari/allosso.git
```

### خطای Composer Memory Limit

```bash
php -d memory_limit=512M /usr/local/bin/composer install --optimize-autoloader --no-dev
```

### خطای CAPTCHA

```bash
# بررسی نصب extension gd
php -m | grep gd

# نصب در صورت نیاز
apt-get install php8.2-gd
systemctl restart php8.2-fpm
```

---

## Backup قبل از Deployment

```bash
# Backup دیتابیس
mysqldump -u main -p mainsso > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup فایل‌ها
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz /home/allo-sso/htdocs/www.allo-sso.com
```

---

## خلاصه دستورات سریع

```bash
# اتصال به سرور
ssh root@allolancer-8gb-nbg1

# رفتن به مسیر پروژه
cd /home/allo-sso/htdocs/www.allo-sso.com

# Pull و Deploy
git pull origin master
bash deploy.sh
```

---

**نکته مهم:** همیشه قبل از deployment، backup بگیرید!

