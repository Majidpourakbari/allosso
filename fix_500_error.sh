#!/bin/bash

# Script to fix 500 error on server
# Run this on the server: bash fix_500_error.sh

set -e

echo "🔧 شروع رفع خطای 500..."

cd /home/allo-sso/htdocs/www.allo-sso.com

echo "1. Pull کردن آخرین تغییرات..."
git pull origin master

echo "2. بررسی و تنظیم .env..."
# اضافه کردن تنظیمات اگر وجود ندارند
if ! grep -q "SESSION_DRIVER" .env; then
    echo "SESSION_DRIVER=file" >> .env
    echo "   ✓ SESSION_DRIVER اضافه شد"
fi

if ! grep -q "CACHE_STORE" .env; then
    echo "CACHE_STORE=file" >> .env
    echo "   ✓ CACHE_STORE اضافه شد"
fi

# تغییر به file اگر database است
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env

echo "3. پاک کردن همه cache ها..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "4. حذف فایل‌های cache..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php
rm -rf storage/framework/sessions/*
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*

echo "5. Cache کردن مجدد..."
php artisan config:cache
php artisan route:cache

echo "6. تنظیم permissions..."
chmod -R 775 storage bootstrap/cache
chown -R allo-sso:allo-sso storage bootstrap/cache

echo "7. تست سایت..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://www.allo-sso.com)

if [ "$HTTP_CODE" == "200" ]; then
    echo "✅ سایت با موفقیت کار می‌کند! (HTTP $HTTP_CODE)"
else
    echo "⚠️  سایت هنوز خطا می‌دهد (HTTP $HTTP_CODE)"
    echo "📋 بررسی log ها:"
    echo "   tail -n 50 storage/logs/laravel.log"
fi

echo ""
echo "✅ تمام!"

