#!/bin/bash

set -e

echo "🚀 شروع Deployment AlloSSO..."

PROJECT_DIR="/home/allo-sso/htdocs/www.allo-sso.com"
GIT_REPO="https://github.com/Majidpourakbari/allosso.git"
GIT_BRANCH="master"

cd "$PROJECT_DIR"

echo "📥 دریافت آخرین تغییرات از گیت..."
git fetch origin
git reset --hard origin/$GIT_BRANCH
git clean -fd

echo "📦 نصب Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

echo "📦 نصب NPM dependencies (در صورت نیاز)..."
if [ -f "package.json" ]; then
    npm ci --production
    npm run build
fi

echo "🧹 پاک کردن cache های قدیمی..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "💾 Cache کردن config, routes و views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📊 اجرای migrations..."
php artisan migrate --force

echo "🔐 تنظیم permissions..."
chown -R allo-sso:allo-sso "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage"
chmod -R 775 "$PROJECT_DIR/bootstrap/cache"

echo "✅ Deployment با موفقیت انجام شد!"
echo ""
echo "📋 بررسی‌های نهایی:"
echo "1. بررسی کنید که سایت در دسترس است: https://www.allo-sso.com"
echo "2. بررسی log ها در صورت نیاز: tail -f storage/logs/laravel.log"




