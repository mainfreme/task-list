# Architektura aplikacji - DDD z modułową strukturą

## Wprowadzenie

Aplikacja wykorzystuje architekturę Domain-Driven Design (DDD) z modułową organizacją kodu. Każdy moduł jest samowystarczalną jednostką biznesową zawierającą wszystkie warstwy potrzebne do realizacji funkcjonalności.

## Struktura modułowa

### Organizacja folderów

```
src/App/
├── Task/                    # Moduł zarządzania zadaniami
│   ├── Application/         # Warstwa aplikacji (CQRS)
│   ├── Domain/              # Warstwa domenowa (biznesowa logika)
│   ├── Infrastructure/      # Warstwa infrastruktury (implementacje)
│   └── UI/                  # Warstwa prezentacji (kontrolery, requesty)
├── ApplicationManager/      # Moduł zarządzania aplikacjami
│   ├── Application/
│   ├── Domain/
│   ├── Infrastructure/
│   └── UI/
└── Shared/                  # Moduł współdzielony
    ├── Application/         # Współdzielone serwisy aplikacyjne
    ├── Domain/              # Współdzielone encje domenowe
    ├── Infrastructure/      # Współdzielone implementacje infrastruktury
    └── UI/                  # Współdzielone komponenty UI
```

## Warstwy architektury

### 1. Domain (Domena)

Warstwa domenowa zawiera czystą logikę biznesową bez zależności od frameworka czy bazy danych.

**Struktura:**
- `Entity/` - Encje domenowe (agregaty)
- `ValueObject/` - Obiekty wartości
- `Repository/` - Interfejsy repozytoriów
- `Exception/` - Wyjątki domenowe

**Przykład namespace:**
```php
namespace App\Task\Domain\Entity;
namespace App\Task\Domain\ValueObject;
namespace App\Task\Domain\Repository;
namespace App\Task\Domain\Exception;
```

### 2. Application (Aplikacja)

Warstwa aplikacji implementuje wzorzec CQRS (Command Query Responsibility Segregation).

**Struktura:**
- `Command/` - Komendy (zapisy)
  - `{CommandName}/` - Folder dla każdej komendy
    - `{CommandName}Command.php` - Obiekt komendy
    - `{CommandName}Handler.php` - Handler komendy
- `Query/` - Zapytania (odczyty)
  - `{QueryName}/` - Folder dla każdego zapytania
    - `{QueryName}Query.php` - Obiekt zapytania
    - `{QueryName}Handler.php` - Handler zapytania
- `DTO/` - Data Transfer Objects

**Przykład namespace:**
```php
namespace App\Task\Application\Command\CreateTask;
namespace App\Task\Application\Query\GetTask;
namespace App\Task\Application\DTO;
```

### 3. Infrastructure (Infrastruktura)

Warstwa infrastruktury zawiera implementacje zależności zewnętrznych.

**Struktura:**
- `Repository/` - Implementacje repozytoriów (np. Eloquent)
- `Eloquent/` - Modele Eloquent
- `Middleware/` - Middleware specyficzne dla modułu
- `Providers/` - Service Providers (tylko w Shared)

**Przykład namespace:**
```php
namespace App\Task\Infrastructure\Repository;
namespace App\Task\Infrastructure\Eloquent;
namespace App\ApplicationManager\Infrastructure\Middleware;
```

### 4. UI (Prezentacja)

Warstwa prezentacji zawiera kontrolery i requesty HTTP.

**Struktura:**
- `Http/Controllers/Api/V1/` - Kontrolery API
- `Http/Requests/V1/` - Requesty walidacyjne

**Przykład namespace:**
```php
namespace App\Task\UI\Http\Controllers\Api\V1;
namespace App\Task\UI\Http\Requests\V1;
```

## Moduł Shared

Moduł `Shared` zawiera komponenty współdzielone między różnymi modułami aplikacji.

### Zasady umieszczania w Shared

Umieszczaj w Shared tylko te komponenty, które:
1. Są używane przez więcej niż jeden moduł
2. Nie należą do konkretnej domeny biznesowej
3. Są infrastrukturalne lub techniczne

### Przykłady komponentów Shared

- **UI:**
  - `ApiController` - Bazowa klasa kontrolera API z metodami odpowiedzi HTTP

- **Infrastructure:**
  - `RepositoryServiceProvider` - Rejestracja bindingów repozytoriów
  - Współdzielone middleware (jeśli używane przez wiele modułów)

- **Domain:**
  - Współdzielone Value Objects (jeśli potrzebne)
  - Współdzielone interfejsy (jeśli potrzebne)

- **Application:**
  - Współdzielone serwisy aplikacyjne (jeśli potrzebne)

## Przykłady namespace'ów

### Moduł Task

```php
// Domain
namespace App\Task\Domain\Entity;
namespace App\Task\Domain\Repository;
namespace App\Task\Domain\ValueObject;
namespace App\Task\Domain\Exception;

// Application
namespace App\Task\Application\Command\CreateTask;
namespace App\Task\Application\Query\GetTask;
namespace App\Task\Application\DTO;

// Infrastructure
namespace App\Task\Infrastructure\Repository;
namespace App\Task\Infrastructure\Eloquent;

// UI
namespace App\Task\UI\Http\Controllers\Api\V1;
namespace App\Task\UI\Http\Requests\V1;
```

### Moduł ApplicationManager

```php
// Domain
namespace App\ApplicationManager\Domain\Entity;
namespace App\ApplicationManager\Domain\Repository;
namespace App\ApplicationManager\Domain\ValueObject;
namespace App\ApplicationManager\Domain\Exception;

// Application
namespace App\ApplicationManager\Application\Command\CreateApplicationManager;
namespace App\ApplicationManager\Application\Query\GetApplicationManager;
namespace App\ApplicationManager\Application\DTO;

// Infrastructure
namespace App\ApplicationManager\Infrastructure\Repository;
namespace App\ApplicationManager\Infrastructure\Eloquent;
namespace App\ApplicationManager\Infrastructure\Middleware;

// UI
namespace App\ApplicationManager\UI\Http\Controllers\Api\V1;
```

### Moduł Shared

```php
// UI
namespace App\Shared\UI\Http\Controllers\Api;

// Infrastructure
namespace App\Shared\Infrastructure\Providers;
```

## Dodawanie nowego modułu

Aby dodać nowy moduł do aplikacji:

1. **Utwórz strukturę folderów:**
   ```
   src/App/{ModuleName}/
   ├── Application/
   ├── Domain/
   ├── Infrastructure/
   └── UI/
   ```

2. **Zdefiniuj encje domenowe** w `Domain/Entity/`

3. **Utwórz interfejsy repozytoriów** w `Domain/Repository/`

4. **Zaimplementuj repozytoria** w `Infrastructure/Repository/`

5. **Utwórz komendy i zapytania** w `Application/Command/` i `Application/Query/`

6. **Zaimplementuj kontrolery** w `UI/Http/Controllers/Api/V1/`

7. **Zarejestruj repozytoria** w `Shared/Infrastructure/Providers/RepositoryServiceProvider.php`

8. **Dodaj routing** w `routes/api.php`

## Zasady i najlepsze praktyki

### 1. Izolacja modułów

- Każdy moduł powinien być możliwie niezależny
- Unikaj bezpośrednich zależności między modułami
- Używaj modułu Shared dla współdzielonych komponentów

### 2. Dependency Injection

- Wszystkie zależności powinny być wstrzykiwane przez konstruktor
- Używaj interfejsów z warstwy Domain, nie implementacji z Infrastructure

### 3. CQRS

- Rozdzielaj komendy (zapisy) i zapytania (odczyty)
- Komendy nie powinny zwracać danych (oprócz ID)
- Zapytania nie powinny modyfikować stanu

### 4. Value Objects

- Używaj Value Objects dla wartości domenowych (np. Email, Money, Status)
- Value Objects powinny być immutable

### 5. Exceptions

- Używaj wyjątków domenowych dla błędów biznesowych
- Umieszczaj je w `Domain/Exception/`

## Przykład przepływu danych

```
Request → Controller (UI)
    ↓
Command/Query (Application)
    ↓
Handler (Application)
    ↓
Repository Interface (Domain)
    ↓
Repository Implementation (Infrastructure)
    ↓
Database
```

## Konfiguracja

### Service Providers

Service Providers rejestrują bindingi w pliku `bootstrap/app.php`:

```php
->withProviders([
    RepositoryServiceProvider::class,
])
```

### Routing

Routing jest zdefiniowany w `routes/api.php`:

```php
use App\Task\UI\Http\Controllers\Api\V1\TaskController;
use App\ApplicationManager\UI\Http\Controllers\Api\V1\ApplicationManagerController;
```

### Middleware

Middleware są rejestrowane w `bootstrap/app.php`:

```php
$middleware->alias([
    'api.key' => App\ApplicationManager\Infrastructure\Middleware\ApiKeyMiddleware::class,
]);
```

## Podsumowanie

Ta architektura zapewnia:
- **Modularność** - każdy moduł jest niezależną jednostką
- **Testowalność** - łatwe testowanie dzięki interfejsom
- **Skalowalność** - łatwe dodawanie nowych modułów
- **Czytelność** - jasna struktura i organizacja kodu
- **Maintainability** - łatwa konserwacja i rozwój
