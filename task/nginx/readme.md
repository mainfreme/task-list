# Zabezpieczenie dokumentacji API (Nginx Basic Auth)

## 1. Utwórz plik z hasłami

Na **hoście** (nie w kontenerze), w katalogu `nginx/`:

```bash
cd nginx

# Opcja A: htpasswd (jeśli masz Apache/htpasswd)
htpasswd -c .htpasswd admin

# Opcja B: openssl (dostępne na macOS/Linux)
printf "admin:$(openssl passwd -apr1)\n" > .htpasswd
# (wpisz hasło gdy zostaniesz poproszony)
```

**Dodanie kolejnego użytkownika** (bez `-c`, żeby nie nadpisać pliku):
```bash
htpasswd .htpasswd drugi_uzytkownik
```

## 2. Skonfiguruj Nginx

### Docker (zalecane)

Plik `backend/docker/nginx/default.conf` już zawiera:
```nginx
include /var/www/nginx/documentation-auth.conf;
```

Plik `.htpasswd` jest montowany w kontenerze jako `/var/www/nginx/.htpasswd` (katalog `task/` → `/var/www`).

### MAMP / lokalny nginx

W pliku konfiguracyjnym serwera (np. `/etc/nginx/sites-available/default`) **przed** blokiem `location /` dodaj:

```nginx
include /Applications/MAMP/htdocs/app/task\ list/backend/task/nginx/documentation-auth.conf;
```

Edytuj `documentation-auth.conf` i ustaw prawidłową ścieżkę w `auth_basic_user_file` (pełna ścieżka do pliku `.htpasswd` na hoście).

## 3. Przeładuj Nginx

### Docker

Uruchom z **katalogu backend** na hoście (nie wewnątrz kontenera):

```bash
cd /path/to/task-list/backend
docker compose restart nginx
```

Alternatywnie (bez restartu całego kontenera):
```bash
docker exec nginx nginx -t && docker exec nginx nginx -s reload
```

### MAMP / lokalny nginx

```bash
sudo nginx -t && sudo nginx -s reload
```

## Chronione ścieżki

- `/api/documentation` – Swagger UI
- `/api/document` – alias (przekierowanie)
- `/docs` – specyfikacja OpenAPI (JSON/YAML)
