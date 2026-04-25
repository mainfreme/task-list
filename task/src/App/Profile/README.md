# Moduł Profile

Moduł obsługuje **profil zalogowanego użytkownika** — dane prezentowane i edytowalne w kontekście „moje konto”.

## Do czego służy

- **`GET /v1/me`** — odczyt profilu dla identyfikatora użytkownika z kontekstu JWT (handler `GetUserProfile`).
- **`PUT /v1/me`** — aktualizacja podstawowych pól (np. nazwa, e-mail) przez `UpdateProfileHandler`.

Oba endpointy wymagają middleware **`user.jwt`**.

## Warstwy

- **Domain** — reguły i typy związane z profilem (w granicach modułu).
- **Application** — komenda aktualizacji oraz zapytanie o profil (DTO wyjściowe pod JSON).
- **Infrastructure** — odczyt/zapis danych użytkownika z infrastruktury aplikacji.
- **UI** — `ProfileController`, `UpdateProfileRequest`, mapper profilu.

## Relacja do Auth

Moduł **Auth** dostarcza endpoint `/v1/auth/me` w kontekście sesji uwierzytelniania; **Profile** koncentruje się na **bogatszym lub dedykowanym widoku profilu** i jego **edycji** pod ścieżką `/v1/me`.
