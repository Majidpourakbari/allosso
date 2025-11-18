#!/bin/bash

# Quick fix script for database connection error
# Run this on the server: bash QUICK_FIX.sh

echo "=== AlloSSO Database Fix Script ==="
echo ""

# Navigate to project directory
cd /home/allo-sso/htdocs/www.allo-sso.com || exit

echo "1. Checking .env file..."
if [ ! -f .env ]; then
    echo "   .env file not found! Creating from .env.example..."
    cp .env.example .env
    echo "   Please edit .env file with correct database credentials"
    exit 1
fi

echo "2. Setting session driver to file in .env..."
# Add or update SESSION_DRIVER in .env
if grep -q "SESSION_DRIVER" .env; then
    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
else
    echo "SESSION_DRIVER=file" >> .env
fi

echo "3. Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "4. Rebuilding config cache..."
php artisan config:cache

echo "5. Testing database connection..."
php artisan tinker --execute="echo DB::connection()->getPdo() ? 'Database connection OK!' : 'Database connection FAILED!';"

echo ""
echo "=== Done! ==="
echo "If database connection is OK, you can now run:"
echo "  php artisan migrate --force"

