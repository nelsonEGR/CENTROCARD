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
RUN a2enmod rewrite setenvif \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && printf '%s\n' 'SetEnvIf X-Forwarded-Proto "^https$" HTTPS=on' > /etc/apache2/conf-available/forwarded-https.conf \
    && a2enconf forwarded-https

WORKDIR /var/www/html

# Código de la aplicación
COPY . .

# Dependencias de produccion + archivos iniciales para volumenes locales
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && install -D -m 644 public/img/productos/default.jpg /opt/centrocard/default.jpg \
    && chown -R www-data:www-data writable \
    && chmod -R 775 writable

# Entrypoint que respeta la variable PORT (Render/otros)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
