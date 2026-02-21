# Task List API

Laravel API do zbierania danych z różnych serwisów. Aplikacja wykorzystuje architekturę **DDD** (Domain-Driven Design) z modułową strukturą i wzorzec **CQRS**.

## Wymagania

- **PHP** ^8.2 (rozszerzenia: json, pdo, mbstring, openssl, sodium)
- **Composer** ^2.0
- **MySQL** 8.0+ lub **PostgreSQL** lub **SQLite**
- **Redis** (opcjonalnie, do cache – projekt używa Predis)

## Instalacja

### 1. Klonowanie i zależności

```bash
git clone <repo-url> task-list-api
cd task-list-api
composer install
```

### 2. Konfiguracja środowiska

Skopiuj plik środowiska i dostosuj zmienne:

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Konfiguracja pliku `.env`

Uzupełnij poniższe zmienne w pliku `.env`:

```env
# Aplikacja
APP_NAME="Task List API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Baza danych (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_list
DB_USERNAME=root
DB_PASSWORD=

# JWT (wymagane dla autentykacji)
JWT_SECRET=<wygeneruj-bezpieczny-klucz-min-32-znaki>
JWT_ALGORITHM=HS256
JWT_EXPIRATION_MINUTES=1440

# Swagger (opcjonalnie)
L5_SWAGGER_CONST_HOST=http://localhost
```

**Generowanie JWT_SECRET:**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

### 4. Baza danych

Utwórz bazę danych i uruchom migracje:

```bash
# Utwórz bazę (MySQL)
mysql -u root -p -e "CREATE DATABASE task_list CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migracje
php artisan migrate:fresh

# Seedowanie (dane testowe)
php artisan db:seed
```

### 5. Swagger / dokumentacja API

Wygeneruj dokumentację OpenAPI:

```bash
php artisan l5-swagger:generate
```

## Uruchomienie

### Serwer deweloperski

```bash
php artisan serve
```

Aplikacja będzie dostępna pod adresem: **http://localhost:8000**

### MAMP / Apache / Nginx

Ustaw katalog główny serwera na folder `public/`:

- **Document Root:** `.../task/public`
- **URL:** np. `http://localhost/task/public` lub skonfigurowany virtual host

## Endpointy API

| Ścieżka | Opis |
|---------|------|
| `GET /api/documentation` | Dokumentacja Swagger UI |
| `POST /api/v1/auth/register` | Rejestracja użytkownika |
| `POST /api/v1/auth/login` | Logowanie użytkownika |
| `GET /api/v1/auth/me` | Aktualny użytkownik (wymaga JWT) |
| `POST /api/v1/applications/{id}/generate-api-key` | Generowanie klucza API |
| `POST /api/v1/applications/{id}/generate-jwt-token` | Generowanie tokenu JWT |

## Pierwsze kroki po uruchomieniu

1. **Pobierz ID aplikacji** (po seedzie):
   ```bash
   php artisan tinker
   >>> \DB::table('applications')->first()->id;
   ```

2. **Wygeneruj klucz API i JWT** (np. dla pierwszej aplikacji):
   ```bash
   curl -X POST http://localhost:8000/api/v1/applications/{APPLICATION_ID}/generate-api-key
   curl -X POST http://localhost:8000/api/v1/applications/{APPLICATION_ID}/generate-jwt-token
   ```

3. **Zarejestruj użytkownika**:
   ```bash
   curl -X POST http://localhost:8000/api/v1/auth/register \
     -H "Content-Type: application/json" \
     -d '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
   ```

## Skrypty

| Komenda | Opis |
|---------|------|
| `composer cs-fix` | Automatyczna naprawa stylu kodu (PHP CS Fixer) |
| `composer cs-fix-check` | Sprawdzenie stylu bez zmian (dry-run) |
| `php artisan migrate:fresh` | Reset bazy i migracje |
| `php artisan db:seed` | Seedowanie danych |
| `php artisan l5-swagger:generate` | Generowanie dokumentacji Swagger |
| `php artisan test` | Uruchomienie testów |

## Struktura projektu

```
src/App/
├── Auth/           # Autentykacja użytkowników (JWT)
├── Task/            # Moduł zadań
├── Crm/             # Moduł CRM (klienci, kontakty, notatki)
├── Profile/         # Profile użytkowników
├── ApplicationManager/  # Zarządzanie aplikacjami i kluczami API
└── Shared/          # Komponenty współdzielone
```

Szczegóły architektury: [ARCHITECTURE.md](ARCHITECTURE.md)

## Moduły

- **Task** – zarządzanie zadaniami
- **Auth** – rejestracja, logowanie, JWT użytkowników
- **Crm** – klienci, kontakty, adresy, notatki, relacje
- **Profile** – profile użytkowników
- **ApplicationManager** – aplikacje zewnętrzne, klucze API, tokeny JWT
