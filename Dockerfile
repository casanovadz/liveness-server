# استخدام صورة PHP مع Apache
FROM php:8.1-apache

# إصلاح تحذير ServerName في Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# تحديث الحزم وتثبيت التبعيات الأساسية
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# تثبيت إضافات PHP المطلوبة
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    gd \
    zip \
    opcache

# تمكين نمط إعادة الكتابة (mod_rewrite) في Apache
RUN a2enmod rewrite

# تحسين إعدادات Apache للأداء (خاصة للخطة المجانية)
RUN { \
    echo '<IfModule mpm_prefork_module>'; \
    echo '  StartServers            2'; \
    echo '  MinSpareServers         1'; \
    echo '  MaxSpareServers         2'; \
    echo '  MaxRequestWorkers       5'; \
    echo '  MaxConnectionsPerChild  1000'; \
    echo '</IfModule>'; \
    echo 'Timeout 300'; \
    echo 'KeepAlive On'; \
    echo 'MaxKeepAliveRequests 100'; \
    echo 'KeepAliveTimeout 5'; \
} >> /etc/apache2/conf-available/docker-php.conf

# نسخ ملفات التطبيق
COPY src/ /var/www/html/

# تعديل أذونات الملفات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# تنظيف الذاكرة المؤقتة
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تعيين المنفذ (اختياري - يمكن ضبطه في Render.com)
EXPOSE 80

# تشغيل Apache في الواجهة الأمامية
CMD ["apache2-foreground"]