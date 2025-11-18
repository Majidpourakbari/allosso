# رفع خطای Database Connection

## مشکل
```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
```

این خطا به این معنی است که:
1. فایل `.env` وجود ندارد یا تنظیمات دیتابیس درست نیست
2. Cache قدیمی وجود دارد که تنظیمات قدیمی را استفاده می‌کند

## راه‌حل سریع

### 1. اتصال به سرور
```bash
ssh root@allolancer-8gb-nbg1
cd /home/allo-sso/htdocs/www.allo-sso.com
```

### 2. بررسی وجود فایل .env
```bash
ls -la .env
```

اگر فایل `.env` وجود ندارد:
```bash
cp .env.example .env
```

### 3. ویرایش فایل .env
```bash
nano .env
```

اطمینان حاصل کنید که این بخش‌ها درست تنظیم شده باشند:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mainsso
DB_USERNAME=main
DB_PASSWORD=Dehghan_339*
```

**نکته مهم:** پسورد را درون کوتیشن قرار ندهید مگر اینکه کاراکتر خاصی داشته باشد.

### 4. تغییر Session Driver (مهم!)
قبل از اجرای migration، session driver را به file تغییر دهید:

```bash
# ویرایش config/session.php یا در .env
nano .env
```

در `.env` اضافه کنید یا تغییر دهید:
```env
SESSION_DRIVER=file
```

یا مستقیماً در `config/session.php`:
```php
'driver' => env('SESSION_DRIVER', 'file'),
```

### 5. پاک کردن Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 6. تست اتصال دیتابیس
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

اگر خطا داد، خارج شوید (`exit`) و دوباره `.env` را بررسی کنید.

### 6. اگر هنوز مشکل دارید

#### بررسی دسترسی دیتابیس:
```bash
mysql -u main -p mainsso
# پسورد: Dehghan_339*
```

اگر وارد شد، دیتابیس درست است. اگر خطا داد، مشکل از دسترسی کاربر است.

#### بررسی فایل .env دوباره:
```bash
cat .env | grep DB_
```

باید این خروجی را ببینید:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mainsso
DB_USERNAME=main
DB_PASSWORD=Dehghan_339*
```

### 7. بررسی Permissions
```bash
chmod 644 .env
chown allo-sso:allo-sso .env
```

### 8. اگر از CloudPanel استفاده می‌کنید

ممکن است نیاز باشد که در CloudPanel:
- Database User را بررسی کنید
- دسترسی‌های دیتابیس را بررسی کنید
- مطمئن شوید که کاربر `main` به دیتابیس `mainsso` دسترسی دارد

## دستورات کامل (Copy & Paste)

```bash
cd /home/allo-sso/htdocs/www.allo-sso.com

# پاک کردن cache
php artisan config:clear
php artisan cache:clear

# بررسی .env
cat .env | grep DB_

# اگر .env درست است، cache را rebuild کنید
php artisan config:cache

# تست
php artisan migrate:status
```

## اگر مشکل حل نشد

1. بررسی log ها:
```bash
tail -f storage/logs/laravel.log
```

2. بررسی دسترسی‌های دیتابیس در CloudPanel

3. تست مستقیم MySQL:
```bash
mysql -u main -p'mainsso' -h 127.0.0.1 -e "USE mainsso; SHOW TABLES;"
```

