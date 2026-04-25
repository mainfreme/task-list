# Moduł Settings

Moduł grupuje **konfigurację systemu** dostępną przez API: pojedyncze wpisy ustawień, integracje zewnętrzne oraz definicje wykresów.

## Do czego służy (API v1)

Wszystkie trasy pod prefixem **`/v1/settings`** (middleware `user.jwt`):

1. **Chart definitions** — CRUD definicji wykresów (np. konfiguracja wizualizacji w panelu).
2. **Integration accounts** — CRUD kont integracji oraz **`PATCH .../enabled`** do włączania/wyłączania konta.
3. **Setting entries** — odczyt pogrupowany (`entries/grouped`), po grupie (`groups/{groupKey}`), pojedynczy wpis, **`PUT entries`** (upsert), usuwanie.

## Mechanizmy pomocnicze

- **Cache zapytań** (Redis) dla części odczytów — skraca obciążenie przy częstym pobieraniu ustawień.
- **Zdarzenia domenowe** — `SettingsChangedEvent` i dispatcher (np. invalidacja cache / powiadamianie innych warstw po zmianie).

## Warstwy

- **Domain** — encje/zdarzenia związane ze zmianą ustawień.
- **Application** — komendy (tworzenie, aktualizacja, usuwanie, upsert) i zapytania listujące; DTO dla API.
- **Infrastructure** — repozytoria, listenery, adapter cache i eventów.
- **UI** — kontrolery `SettingEntryController`, `IntegrationAccountController`, `ChartDefinitionController`.
