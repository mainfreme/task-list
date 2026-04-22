# Moduł ApplicationManager

Moduł obsługuje **rejestr zewnętrznych aplikacji** (np. integracji lub instancji klienckich), które komunikują się z backendem.

## Do czego służy

- Tworzenie, odczyt, aktualizacja i lista wpisów „Application Manager”.
- Zmiana statusu aktywności aplikacji.
- **Generowanie kluczy API** oraz **tokenów JWT** dla wybranej aplikacji (endpointy poza standardową grupą JWT użytkownika — patrz `routes/api.php`).
- Pola domenowe m.in.: nazwa, adres żądań (`request_url`), flaga aktywności, **biała lista IP**.

## Warstwy

- **Domain** — encje, wyjątki, kontrakty repozytoriów.
- **Application** — komendy i zapytania (CQRS): tworzenie/aktualizacja, zmiana statusu, generowanie kluczy, listy i pojedynczy odczyt.
- **Infrastructure** — implementacja persystencji (np. Eloquent).
- **UI** — kontroler HTTP API v1, requesty, mapery żądań na komendy/zapytania, dokumentacja OpenAPI.

## API (skrót)

Endpointy pod `/v1/applications` (większość chroniona middleware `user.jwt`). Służą do zarządzania katalogiem aplikacji i credentialami dla integracji.
