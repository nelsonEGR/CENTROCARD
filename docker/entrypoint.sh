#!/bin/sh
set -eu

# Los hosts tipo Render exponen el puerto en $PORT (por defecto 80 en local).
PORT="${PORT:-80}"

# Genera el .env interno de CodeIgniter a partir de las variables de entorno
# del contenedor. Asi la conexion se controla por variables (Docker/Render).
if [ -n "${DB_HOSTNAME:-}" ]; then
    {
        printf 'CI_ENVIRONMENT = %s\n' "${CI_ENVIRONMENT:-production}"
        printf "app.baseURL = '%s'\n" "${APP_BASE_URL:-http://localhost/}"
        printf 'database.default.hostname = %s\n' "${DB_HOSTNAME}"
        printf 'database.default.database = %s\n' "${DB_DATABASE:-}"
        printf 'database.default.username = %s\n' "${DB_USERNAME:-}"
        printf 'database.default.password = %s\n' "${DB_PASSWORD:-}"
        printf 'database.default.DBDriver = Postgre\n'
        printf 'database.default.port = %s\n' "${DB_PORT:-5432}"
        printf 'database.default.schema = %s\n' "${DB_SCHEMA:-public}"
        printf 'database.default.sslmode = %s\n' "${DB_SSLMODE:-require}"
        printf "database.default.options = '%s'\n" "${DB_OPTIONS:-}"
        printf 'IMPUESTO = %s\n' "${IMPUESTO:-10}"
        printf 'GANANCIA = %s\n' "${GANANCIA:-25}"
    } > .env
    chown www-data:www-data .env
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    attempt=1
    until php spark migrate --all; do
        if [ "$attempt" -ge 30 ]; then
            echo "No fue posible conectar a la base de datos." >&2
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
