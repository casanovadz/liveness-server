FROM php:8.1-apache

# تثبيت امتدادات SQLite وعلب المساعدة
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# نسخ الكود
COPY src/ /var/www/html/

# صلاحيات
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# تفعيل mod_rewrite إن احتجت (ليس ضروريًا هنا لكن مفيد)
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]