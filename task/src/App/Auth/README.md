# Moduł Auth

Moduł odpowiada za **uwierzytelnianie użytkowników** oraz powiązaną obsługę sesji w API.

## Do czego służy

- **Rejestracja** (`POST /v1/auth/register`) — tworzenie konta, walidacja, obsługa konfliktu (zajęty e-mail).
- **Logowanie** (`POST /v1/auth/login`) — weryfikacja danych, zwrot tokenu (Bearer JWT) i metadanych sesji.
- **Wylogowanie** oraz **bieżący użytkownik** (`/v1/auth/logout`, `/v1/auth/me`) — dla żądań z ważnym JWT (`user.jwt`).
- **Log aktywności** (`POST /v1/auth/activity`) — rejestrowanie zdarzeń użytkownika (URL, IP, user agent, akcja); w opisie API kierowane do kolejki (np. RabbitMQ) w celu asynchronicznego przetwarzania.

## Warstwy

- **Domain** — encja użytkownika, wyjątki domenowe (np. błędne dane, duplikat e-maila).
- **Application** — handlery komend (register, login, logout, log aktywności) i zapytania (aktualny użytkownik).
- **Infrastructure** — m.in. persystencja, tokeny, integracja z kolejką na potrzeby aktywności.
- **UI** — `AuthController`, `ActivityController`, formularze żądań (Request), routing `routes/api.php`.

## Zależności względem innych modułów

Korzysta ze wspólnych typów z `App\Shared` (np. `Email`, `Uuid`) i bazowej obsługi odpowiedzi API.
