#!/bin/sh
set -e

# Los hosts tipo Render exponen el puerto en $PORT (por defecto 80 en local).
PORT="${PORT:-80}"

sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
