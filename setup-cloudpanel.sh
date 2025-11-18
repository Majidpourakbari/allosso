#!/bin/bash

# اسکریپت تنظیم Laravel در CloudPanel
# اجرا در: /home/allo-sso/htdocs/www.allo-sso.com

echo "🚀 شروع تنظیمات Laravel در CloudPanel..."

# بررسی وجود فایل .env
if [ ! -f .env ]; then
    echo "📝 ایجاد فایل .env..."
    cp .env.example .env
fi

# ویرایش فایل .env با تنظیمات صحیح
echo "⚙️  تنظیم فایل .env..."

cat > .env << 'EOF'
APP_NAME=allosso
APP_ENV=production
APP_KEY=base64:tKcoyWCvvbPzNadPtIUzjDkeTkATZjkS01bUZ6slAmQ=
APP_DEBUG=false
APP_URL=https://allo-sso.com
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
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
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@allo-sso.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
EOF

echo "✅ فایل .env تنظیم شد"

# پاک کردن cache
echo "🧹 پاک کردن cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache کردن config
echo "💾 Cache کردن config..."
php artisan config:cache

# تنظیم دسترسی‌ها
echo "🔐 تنظیم دسترسی‌ها..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# تست اتصال دیتابیس
echo "🔌 تست اتصال دیتابیس..."
php artisan db:show || echo "⚠️  خطا در اتصال دیتابیس - لطفاً اطلاعات دیتابیس را بررسی کنید"

# اجرای migrations
echo "📊 اجرای migrations..."
php artisan migrate --force

# Cache کردن routes و views
echo "💾 Cache کردن routes و views..."
php artisan route:cache
php artisan view:cache

echo "✅ تنظیمات کامل شد!"
echo ""
echo "📋 مراحل بعدی:"
echo "1. بررسی کنید که دیتابیس 'mainsso' و کاربر 'main' وجود دارد"
echo "2. تنظیمات mail را در صورت نیاز تغییر دهید"
echo "3. بررسی کنید که APP_URL درست تنظیم شده است"

