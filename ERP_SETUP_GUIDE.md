# راهنمای تنظیم ERP در allo-sso.com/erp

این راهنما نحوه اجرای یک سورس مجزا (غیر Laravel) در مسیر `/erp` را توضیح می‌دهد.

## پیش‌نیازها

1. فایل‌های سورس ERP شما آماده باشد
2. دسترسی SSH به سرور
3. دسترسی به CloudPanel

---

## مراحل تنظیم

### 1. آپلود فایل‌های ERP

#### روش 1: استفاده از File Manager در CloudPanel

1. در CloudPanel، به **File Manager** بروید
2. به مسیر `/home/allo-sso/htdocs/www.allo-sso.com/` بروید
3. پوشه جدیدی با نام `erp` ایجاد کنید
4. فایل‌های ERP خود را در این پوشه آپلود کنید

#### روش 2: استفاده از SSH/FTP

```bash
# اتصال به سرور از طریق SSH
ssh root@allolancer-8gb-nbg1

# ایجاد پوشه erp (اگر وجود ندارد)
mkdir -p /home/allo-sso/htdocs/www.allo-sso.com/erp

# آپلود فایل‌های ERP به این پوشه
# می‌توانید از FTP/SFTP یا Git استفاده کنید
```

**مسیر نهایی:** `/home/allo-sso/htdocs/www.allo-sso.com/erp`

---

### 2. تنظیم Permissions

```bash
# تنظیم مالکیت
chown -R allo-sso:allo-sso /home/allo-sso/htdocs/www.allo-sso.com/erp

# تنظیم مجوزها
chmod -R 755 /home/allo-sso/htdocs/www.allo-sso.com/erp
```

---

### 3. تنظیم Nginx در CloudPanel

در CloudPanel، منوهای موجود:
- Settings
- Vhost
- Databases
- Varnish Cache
- SSL/TLS
- Security
- SSH/FTP
- File Manager
- Cron Jobs
- Logs

#### گزینه 1: استفاده از Vhost (پیشنهادی)

1. در CloudPanel، به **Vhost** بروید
2. سایت `allo-sso.com` را انتخاب کنید
3. به دنبال گزینه‌ای مثل **Edit Vhost** یا **Nginx Config** بگردید
4. اگر گزینه ویرایش Nginx وجود دارد، این کد را اضافه کنید (قبل از location `/`):

#### گزینه 2: ویرایش مستقیم فایل Nginx از طریق SSH/FTP

اگر در Vhost امکان ویرایش مستقیم نیست، از SSH استفاده کنید:

```nginx
# مسیر ERP - قبل از location / اصلی
location /erp {
    alias /home/allo-sso/htdocs/www.allo-sso.com/erp;
    index index.php index.html index.htm;
    
    # اگر ERP شما PHP است
    try_files $uri $uri/ /erp/index.php?$query_string;
    
    # اگر ERP شما فقط HTML/JS است
    # try_files $uri $uri/ /erp/index.html;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        include fastcgi_params;
    }
}

# جلوگیری از تداخل با Laravel
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 4. ویرایش فایل Nginx از طریق SSH

اگر در CloudPanel امکان ویرایش مستقیم Nginx نیست، از SSH استفاده کنید:

```bash
# اتصال به سرور
ssh root@allolancer-8gb-nbg1

# پیدا کردن فایل Nginx مربوط به سایت
# معمولاً در یکی از این مسیرها است:
ls -la /etc/nginx/sites-enabled/
# یا
ls -la /etc/nginx/conf.d/

# پیدا کردن فایل مربوط به allo-sso.com
grep -r "allo-sso.com" /etc/nginx/

# ویرایش فایل (مثلاً اگر فایل www.allo-sso.com.conf است)
nano /etc/nginx/sites-enabled/www.allo-sso.com.conf
# یا
nano /etc/nginx/conf.d/www.allo-sso.com.conf
```

در فایل Nginx، این کد را **قبل از** location `/` اضافه کنید:

```nginx
# مسیر ERP - باید قبل از location / باشد
location /erp {
    alias /home/allo-sso/htdocs/www.allo-sso.com/erp;
    index index.php index.html index.htm;
    
    # اگر ERP شما PHP است
    try_files $uri $uri/ /erp/index.php?$query_string;
    
    # اگر ERP شما فقط HTML/JS است
    # try_files $uri $uri/ /erp/index.html;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        include fastcgi_params;
    }
}

# جلوگیری از تداخل با Laravel
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

بعد از ویرایش:
```bash
# تست تنظیمات
nginx -t

# اگر OK بود، reload کنید
systemctl reload nginx
```

### 5. استفاده از .htaccess (اگر از Apache استفاده می‌کنید)

اگر سرور شما Apache است، یک فایل `.htaccess` در پوشه `erp` ایجاد کنید:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /erp/
    
    # اگر index.php دارید
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L]
    
    # یا اگر index.html دارید
    # RewriteCond %{REQUEST_FILENAME} !-f
    # RewriteCond %{REQUEST_FILENAME} !-d
    # RewriteRule ^(.*)$ index.html [L]
</IfModule>
```

---

### 6. تنظیمات خاص بر اساس نوع ERP

#### اگر ERP شما PHP است:

1. مطمئن شوید که فایل `index.php` در پوشه `erp` وجود دارد
2. اگر نیاز به تنظیمات خاص PHP دارید، در CloudPanel:
   - **Sites** → **allo-sso.com** → **PHP**
   - تنظیمات مورد نیاز را اعمال کنید

#### اگر ERP شما Node.js است:

1. در CloudPanel، یک **Node.js App** جدید ایجاد کنید
2. یا از PM2 استفاده کنید و در Nginx یک reverse proxy تنظیم کنید:

```nginx
location /erp {
    proxy_pass http://localhost:3000;  # پورت Node.js شما
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
}
```

#### اگر ERP شما React/Vue/Angular (Static) است:

```nginx
location /erp {
    alias /home/allo-sso/htdocs/www.allo-sso.com/erp/dist;  # یا build
    try_files $uri $uri/ /erp/index.html;
    index index.html;
}
```

---

### 7. تست تنظیمات

```bash
# تست دسترسی به فایل‌ها
ls -la /home/allo-sso/htdocs/www.allo-sso.com/erp

# تست Nginx configuration
nginx -t

# Reload Nginx
systemctl reload nginx
```

---

### 8. بررسی در مرورگر

بعد از تنظیمات، آدرس زیر را در مرورگر تست کنید:

```
https://allo-sso.com/erp
```

---

## مشکلات رایج و راه‌حل

### خطای 404 Not Found

**علت:** مسیر درست تنظیم نشده یا فایل index وجود ندارد

**راه‌حل:**
```bash
# بررسی وجود فایل index
ls -la /home/allo-sso/htdocs/www.allo-sso.com/erp/index.*

# بررسی Nginx logs
tail -f /var/log/nginx/error.log
```

### خطای 403 Forbidden

**علت:** مشکل permissions

**راه‌حل:**
```bash
chown -R allo-sso:allo-sso /home/allo-sso/htdocs/www.allo-sso.com/erp
chmod -R 755 /home/allo-sso/htdocs/www.allo-sso.com/erp
```

### Laravel routes با /erp تداخل دارد

**راه‌حل:** مطمئن شوید که location block `/erp` قبل از location `/` در Nginx قرار دارد:

```nginx
# اول ERP
location /erp {
    ...
}

# بعد Laravel
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### PHP در /erp کار نمی‌کند

**راه‌حل:** مطمئن شوید که fastcgi_param درست تنظیم شده:

```nginx
location ~ ^/erp/.+\.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $request_filename;
    include fastcgi_params;
}
```

---

## ساختار پیشنهادی

```
/home/allo-sso/htdocs/www.allo-sso.com/
├── app/                    # Laravel app
├── public/                 # Laravel public (Document Root)
│   └── index.php
└── erp/                    # ERP application
    ├── index.php (یا index.html)
    ├── assets/
    ├── config/
    └── ...
```

---

## نکات مهم

1. **Document Root:** Document Root باید همچنان `/home/allo-sso/htdocs/www.allo-sso.com/public` باشد
2. **مسیرهای نسبی:** در کد ERP، از مسیرهای نسبی استفاده کنید (مثلاً `/erp/assets/style.css`)
3. **Session:** اگر ERP شما PHP است و نیاز به session دارد، مطمئن شوید که session path درست تنظیم شده
4. **CORS:** اگر ERP شما API جداگانه دارد، ممکن است نیاز به تنظیم CORS داشته باشید

---

## مثال کامل Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name allo-sso.com www.allo-sso.com;
    
    root /home/allo-sso/htdocs/www.allo-sso.com/public;
    index index.php index.html;
    
    # ERP Location - باید قبل از location / باشد
    location /erp {
        alias /home/allo-sso/htdocs/www.allo-sso.com/erp;
        index index.php index.html;
        
        try_files $uri $uri/ /erp/index.php?$query_string;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }
    
    # Laravel Location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

**نکته:** بعد از هر تغییر در Nginx configuration، حتماً `nginx -t` را اجرا کنید و سپس Nginx را reload کنید.

