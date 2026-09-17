#!/bin/bash
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$DIR"

echo "=== [1/5] Checking .env configuration ==="
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

echo "=== [2/5] Building & Starting Docker Containers ==="
docker compose -f docker-compose.prod.yml up -d --build

echo "=== [3/5] Waiting for Database and App to be ready ==="
sleep 5
docker compose -f docker-compose.prod.yml ps

echo "=== [4/5] Running Laravel Initial Setup ==="
if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=base64:" .env; then
    echo "-> Generating APP_KEY..."
    KEY=$(docker compose -f docker-compose.prod.yml exec -T app php artisan key:generate --show)
    if grep -q "^APP_KEY=" .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=$KEY|" .env
    else
        echo "APP_KEY=$KEY" >> .env
    fi
    docker compose -f docker-compose.prod.yml restart app
else
    echo "-> APP_KEY is already set, skipping."
fi

echo "-> Running Migrations and Seeders..."
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --seed --force

echo "-> Creating Storage Symlink..."
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link || true

echo "-> Setting Directory Permissions..."
docker compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose -f docker-compose.prod.yml exec -T app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "-> Optimizing application for production..."
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

echo ""
echo "=========================================================="
echo "   Deployment Complete! Aplikasi siap digunakan!         "
echo "=========================================================="
echo "Akses aplikasi via browser:"
echo " - Local:     http://localhost:8081"
echo " - LAN:       http://192.168.0.104:8081"
echo " - Tailscale: http://100.117.108.52:8081"
echo ""
echo "Akun Default:"
echo " - Super Admin : superadmin@fotoapp.com / password"
echo " - Admin       : admin@fotoapp.com / password"
echo " - Fotografer  : photographer@fotoapp.com / password"
echo " - Customer    : andi@example.com / password"
echo "=========================================================="
