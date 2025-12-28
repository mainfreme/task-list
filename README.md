# Task List Laravel API

API aplikacji do zarządzania zadaniami zbudowane w architekturze DDD/CQRS z wykorzystaniem Laravel 11.

## Wymagania systemowe

- PHP 8.2+
- Composer
- PostgreSQL lub MySQL
- Node.js (opcjonalnie, dla frontendu)

## Instalacja i uruchomienie

### Opcja 1: Uruchomienie z Docker (Zalecane)

1. **Sklonuj repozytorium i przejdź do katalogu projektu:**
   ```bash
   cd /path/to/task-list
   ```

2. **Uruchom kontenery Docker:**
   ```bash
   docker-compose up -d
   ```

3. **Zainstaluj zależności PHP (w kontenerze app):**
   ```bash
   docker-compose exec app composer install
   ```

4. **Skopiuj plik konfiguracyjny środowiska:**
   ```bash
   docker-compose exec app cp .env.example .env
   ```

5. **Wygeneruj klucz aplikacji:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Uruchom migracje bazy danych:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. **Aplikacja będzie dostępna pod adresem:**
   - **API:** `http://localhost:8080/api/v1/`
   - **Dokumentacja Swagger:** `http://localhost:8080/api/documentation`

### Opcja 2: Uruchomienie z MAMP/XAMPP

1. **Sklonuj repozytorium i przejdź do katalogu projektu:**
   ```bash
   cd /path/to/task-list/task
   ```

2. **Zainstaluj zależności PHP:**
   ```bash
   composer install
   ```

3. **Skopiuj plik konfiguracyjny środowiska:**
   ```bash
   cp .env.example .env
   ```

4. **Skonfiguruj połączenie z bazą danych w pliku `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_list
   DB_USERNAME=root
   DB_PASSWORD=root
   ```

5. **Utwórz bazę danych:**
   - Otwórz phpMyAdmin w MAMP
   - Utwórz nową bazę danych o nazwie `task_list`

6. **Wygeneruj klucz aplikacji:**
   ```bash
   php artisan key:generate
   ```

7. **Uruchom migracje bazy danych:**
   ```bash
   php artisan migrate
   ```

8. **Skonfiguruj MAMP:**
   - Otwórz MAMP → Preferences → Web Server
   - Ustaw Document Root na: `/path/to/task-list/task/public`
   - Uruchom ponownie serwery

9. **Aplikacja będzie dostępna pod adresem:**
   - **API:** `http://localhost:8080/api/v1/`
   - **Dokumentacja Swagger:** `http://localhost:8080/api/documentation`

## Testowanie API przez Postman

### 1. Import kolekcji Postman

Możesz użyć gotowej kolekcji Postman lub ręcznie skonfigurować endpoints.

### 2. Dostępne endpoints API

#### Aplikacje (Application Managers)

**GET /api/v1/applications**
- Pobiera listę wszystkich aplikacji
- Headers: `Authorization: Bearer {token}`

**GET /api/v1/applications/{id}**
- Pobiera szczegóły aplikacji
- Headers: `Authorization: Bearer {token}`

**POST /api/v1/applications**
- Tworzy nową aplikację
- Headers: `Authorization: Bearer {token}`
- Body (JSON):
  ```json
  {
    "name": "Nazwa aplikacji",
    "description": "Opis aplikacji"
  }
  ```

**PUT /api/v1/applications/{id}**
- Aktualizuje aplikację
- Headers: `Authorization: Bearer {token}`
- Body (JSON):
  ```json
  {
    "name": "Zaktualizowana nazwa",
    "description": "Zaktualizowany opis"
  }
  ```

**POST /api/v1/applications/{id}/generate-api-key**
- Generuje nowy klucz API dla aplikacji
- Headers: `Authorization: Bearer {token}`

#### Zadania (Tasks)

**GET /api/v1/tasks**
- Pobiera listę wszystkich zadań
- Headers: `Authorization: Bearer {token}`

**GET /api/v1/tasks/{id}**
- Pobiera szczegóły zadania
- Headers: `Authorization: Bearer {token}`

**PUT /api/v1/tasks/{id}**
- Aktualizuje zadanie
- Headers: `Authorization: Bearer {token}`
- Body (JSON):
  ```json
  {
    "title": "Zaktualizowany tytuł",
    "description": "Zaktualizowany opis"
  }
  ```

**PATCH /api/v1/tasks/{id}/status**
- Aktualizuje status zadania
- Headers: `Authorization: Bearer {token}`
- Body (JSON):
  ```json
  {
    "status": "completed"
  }
  ```
  Dostępne statusy: `pending`, `in_progress`, `completed`

### 3. Uwierzytelnianie

Większość endpoints wymaga uwierzytelniania poprzez API Key. Aby uzyskać API Key:

1. Utwórz aplikację przez endpoint `POST /api/v1/applications`
2. Wygeneruj klucz API przez endpoint `POST /api/v1/applications/{id}/generate-api-key`
3. Używaj klucza w headerze: `X-API-Key: {api_key}`

### 4. Przykład użycia w Postman

1. **Utwórz nową aplikację:**
   - Method: POST
   - URL: `http://localhost:8080/api/v1/applications`
   - Headers: `Content-Type: application/json`
   - Body:
     ```json
     {
       "name": "Test App",
       "description": "Aplikacja testowa"
     }
     ```

2. **Wygeneruj API Key:**
   - Method: POST
   - URL: `http://localhost:8080/api/v1/applications/{id}/generate-api-key`
   - Headers: `Content-Type: application/json`

3. **Utwórz zadanie:**
   - Method: POST
   - URL: `http://localhost:8080/api/v1/tasks`
   - Headers:
     - `Content-Type: application/json`
     - `X-API-Key: {wygenerowany_klucz}`
   - Body:
     ```json
     {
       "title": "Pierwsze zadanie",
       "description": "Opis pierwszego zadania"
     }
     ```

## Architektura

Projekt wykorzystuje Domain-Driven Design (DDD) z Command Query Responsibility Segregation (CQRS):

- **Domain**: Logika biznesowa (encje, value objects, repository interfaces)
- **Application**: Use cases (commands, queries, handlers, DTOs)
- **Infrastructure**: Implementacje (Eloquent models, repositories, middleware)

## Dostępne komendy Artisan

- `php artisan migrate` - Uruchom migracje bazy danych
- `php artisan db:seed` - Wypełnij bazę danych przykładowymi danymi
- `php artisan key:generate` - Wygeneruj klucz aplikacji
- `php artisan l5-swagger:generate` - Wygeneruj dokumentację API

## Troubleshooting

### Błąd "File not found"
- Upewnij się, że Document Root w MAMP wskazuje na katalog `public`
- Sprawdź czy plik `.htaccess` istnieje w katalogu `public`

### Błąd połączenia z bazą danych
- Sprawdź konfigurację w pliku `.env`
- Upewnij się, że baza danych istnieje i jest dostępna

### Problemy z uprawnieniami
- Ustaw uprawnienia 775 dla katalogów `storage` i `bootstrap/cache`
- W systemie Linux/Mac: `chmod -R 775 storage bootstrap/cache`

## Wsparcie

W przypadku problemów sprawdź logi aplikacji w katalogu `storage/logs/laravel.log`.
