# Zabezpieczenie dokumentacji API (Nginx Basic Auth)

## 1. Utwórz plik z hasłami

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

W pliku konfiguracyjnym serwera (np. `/etc/nginx/sites-available/default`) **przed** blokiem `location /` dodaj:

```nginx
include /Applications/MAMP/htdocs/app/task\ list/backend/task/nginx/documentation-auth.conf;
```

**Ważne:** Edytuj `documentation-auth.conf` i ustaw prawidłową ścieżkę w `auth_basic_user_file` (pełna ścieżka do pliku `.htpasswd`).

## 3. Przeładuj Nginx

```bash
sudo nginx -t && sudo nginx -s reload
```

## Chronione ścieżki

- `/api/documentation` – Swagger UI
- `/api/document` – alias (przekierowanie)
- `/docs` – specyfikacja OpenAPI (JSON/YAML)
