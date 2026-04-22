# Moduł Task

Główny moduł **domeny zadań** — tworzenie, przeglądanie, edycja, usuwanie oraz rozszerzenia: **statusy**, **czas pracy** i **statystyki**.

## Do czego służy

- **CRUD zadań** (`/v1/tasks`) — pola m.in. tytuł, URL strony, opis, kontakt, adres, termin, adres dostawy; filtrowanie listy m.in. po statusie, `application_manager_id` lub listach ID (także pod kątem uprawnień administracyjnych).
- **Zmiana statusu** — dedykowany endpoint `PATCH /v1/tasks/{id}/status`.
- **Czas na zadaniu** — `GET/POST /v1/tasks/{id}/time`: stan licznika / sesji czasu dla **zalogowanego użytkownika** i danego zadania.
- **Statystyki** — `GET /v1/tasks/stats/status`: zliczenia zadań wg statusu z opcjonalnymi filtrami (site, status, application manager).

Wszystkie powyższe endpointy użytkownika są chronione **`user.jwt`**.

## Warstwy

- **Domain** — encja zadania, statusy, wyjątki (np. brak zadania), repozytoria.
- **Application** — komendy (create, update, delete, zmiana statusu, zapis czasu) i zapytania (get, list, statystyki, odczyt czasu).
- **Infrastructure** — Eloquent, cache (np. listy), ewentualne kolejki.
- **UI** — `TaskController`, `TaskTimeController`, `TaskStatsController`, requesty i mapery.

## Powiązania

Zadania mogą być powiązane z **Application Manager** (filtrowanie i przypisanie kontekstu aplikacji/kanału).
