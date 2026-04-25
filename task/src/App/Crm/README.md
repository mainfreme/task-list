# Moduł Crm

Moduł realizuje **prostą warstwę CRM** — zarządzanie **klientami** (podmiotami biznesowymi) z poziomu API.

## Do czego służy

Pełny **CRUD** klientów pod `/v1/crm` (chronione `user.jwt`):

- lista z paginacją i filtrem po statusie (np. lead, prospect, active, inactive, archived),
- tworzenie klienta (m.in. nazwa, NIP, kraj, dane firmy),
- odczyt i aktualizacja pojedynczego rekordu,
- usunięcie.

## Warstwy

- **Domain** — model klienta, statusy, wyjątki (np. brak rekordu), interfejs repozytorium.
- **Application** — komendy (create, update, delete) i zapytania (get, list).
- **Infrastructure** — implementacja repozytorium (Eloquent), opcjonalnie cache zapytań listy (Redis).
- **UI** — `CrmController`, requesty walidacyjne, mapery HTTP → komendy/zapytania.

## Uwaga techniczna

W routingu identyfikator w ścieżce jest bindowany do value object UUID (spójnie z resztą API).
