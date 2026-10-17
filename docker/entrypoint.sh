#!/bin/sh
set -eu

# Los hosts tipo Render exponen el puerto en $PORT (por defecto 80 en local).
PORT="${PORT:-80}"

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    attempt=1
    until php spark migrate --all; do
        if [ "$attempt" -ge 30 ]; then
            echo "No fue posible conectar a la base de datos local." >&2
            exit 1
        fi

        attempt=$((attempt + 1))
        sleep 2
    done

    php spark db:seed DevelopmentSeeder
fi

if [ ! -f "$APACHE_DOCUMENT_ROOT/img/productos/default.jpg" ]; then
    install -D -o www-data -g www-data -m 644 \
        /opt/centrocard/default.jpg \
        "$APACHE_DOCUMENT_ROOT/img/productos/default.jpg"
fi

chown -R www-data:www-data writable "$APACHE_DOCUMENT_ROOT/img/productos"

sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
