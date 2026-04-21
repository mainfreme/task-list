#!/usr/bin/env bash
set -euo pipefail

# Katalog z plikiem docker-compose.yml (folder nadrzędny względem aplikacji Laravel w task/).
BACKEND_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$BACKEND_ROOT"

echo "==> Zatrzymywanie kontenerów..."
docker compose down

echo "==> Budowanie obrazów od zera..."
docker compose build --no-cache

echo "==> Uruchamianie środowiska..."
docker compose up -d

echo "==> Composer install w kontenerze app..."
docker compose exec -T app composer install --no-interaction

echo "==> Oczekiwanie na gotowość PostgreSQL..."
for i in $(seq 1 60); do
  if docker compose exec -T db pg_isready -U user -d db >/dev/null 2>&1; then
    echo "Baza gotowa."
    break
  fi
  if [ "$i" -eq 60 ]; then
    echo "Timeout: PostgreSQL nie odpowiada." >&2
    exit 1
  fi
  sleep 1
done

echo "==> Migracje w kontenerze app..."
docker compose exec -T app php artisan migrate

echo "==> Generowanie dokumentacji Swagger..."
docker compose exec -T app php artisan l5-swagger:generate

echo "Gotowe."
