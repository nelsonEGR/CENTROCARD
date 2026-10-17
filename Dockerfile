FROM php:8.3-apache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Librerías del sistema necesarias para las extensiones PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libpq-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" intl mbstring pgsql pdo_pgsql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache: document root en /public y permitir .htaccess (rewrite de CI4)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Código de la aplicación
COPY . .

# Dependencias de producción + permisos de escritura
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data writable \
    && chmod -R 775 writable

# Entrypoint que respeta la variable PORT (Render/otros)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
