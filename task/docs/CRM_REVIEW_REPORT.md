# Raport przeglądu modułu CRM – DDD, CQRS, SOLID, Clean Code

**Data:** 2025-03-11  
**Zakres:** Moduł `src/App/Crm` oraz testy `tests/Unit/Application/Crm`, `tests/Unit/Domain/Crm`

---

## 1. Mocne strony

### DDD
- **Struktura warstw** – moduł ma poprawną strukturę Domain / Application / Infrastructure / UI zgodną z `ARCHITECTURE.md`.
- **Interfejsy repozytoriów w Domain** – `ClientRepositoryInterface`, `AddressRepositoryInterface` są w warstwie domenowej; implementacje Eloquent w Infrastructure.
- **Value Objects** – NIP, Regon, PESEL, ClientName, Country, ClientRating, ClientNotes, IsCompany, IsDeleted itd. są zaimplementowane jako VO z walidacją (NIP, PESEL checksum).
- **Encja Client** – używa factory `create()` i `fromDatabase()`, metody domenowe `delete()`, `restore()`, `touch()`.
- **Wyjątek domenowy** – `ClientNotFoundException` w `Domain/Exception`, rzucany przez repozytorium i handlerów.
- **Enum ClientStatus** – status klienta jako enum zamiast surowych stringów.

### CQRS
- **Rozdział Command/Query** – CreateClient, UpdateClient, DeleteClient jako Command; GetClient, ListClients jako Query.
- **Struktura folderów** – każda komenda/zapytanie w osobnym folderze z `*Command.php` i `*Handler.php`.
- **Proste nośniki danych** – Command i Query są DTO bez logiki biznesowej.
- **Handlery** – tylko orkiestracja (domena + repozytorium), bez HTTP.

### SOLID
- **DIP** – handlery zależą od `ClientRepositoryInterface`, nie od implementacji Eloquent.
- **SRP** – handlery mają jedną odpowiedzialność; mappery mapują request → command/query.
- **Kontroler** – deleguje do handlerów; obsługa try/catch dla `ClientNotFoundException` w `show`, `update`, `destroy`.

### Infrastructure
- **EloquentClientRepository** – mapowanie model ↔ encja w `toEntity()`; save ustawia ID na encji po `create()`.
- **ClientModel** – używa `HasUuids`, `SoftDeletes`; `fillable` i `casts` ograniczone do potrzeb.

### Testy
- **Pokrycie handlerów** – CreateClient, UpdateClient, DeleteClient, GetClient, ListClients mają testy.
- **Przypadki brzegowe** – testy dla `not found`, idempotencja, opcjonalne pola, pusty wynik.
- **Testy domeny** – ClientEntity, ClientNotFoundException, ClientStatus, Nip.
- **Zgodność z regułami** – weryfikacja zachowania (np. `save` z encją z odpowiednim statusem), nie tylko referencji.

---

## 2. Miejsca do poprawy

### Krytyczne

#### 2.1 Błędny import w CrmController
**Plik:** `src/App/Crm/UI/Http/Controllers/Api/V1/CrmController.php`
```php
use App\Crm\Application\Command\CreateClient\CreateClientCommandMapper;
```
- `CreateClientCommandMapper` znajduje się w `App\Crm\UI\Http\Mappers`, nie w `Application\Command\CreateClient`.
- Powoduje błąd „Class not found” przy próbie utworzenia klienta.
- **Rekomendacja:** Zmienić import na `use App\Crm\UI\Http\Mappers\CreateClientCommandMapper;`

#### 2.2 ListClientsHandler – błąd paginacji i filtrowania
**Plik:** `src/App/Crm/Application/Query/ListClients/ListClientsHandler.php`
- `findByStatus()` nie przyjmuje `limit`/`offset` – zwraca wszystkich klientów o danym statusie, bez paginacji.
- `count()` zwraca liczbę wszystkich klientów; przy filtrowaniu po statusie powinno zwracać liczbę dla tego statusu.
- **Rekomendacja:** Zmienić `ClientRepositoryInterface::findByStatus()` na `findByStatus(ClientStatus $status, int $limit, int $offset): array` i dodać `countByStatus(?ClientStatus $status): int` lub `count(?ClientStatus $status): int`; użyć w `ListClientsHandler`.

#### 2.3 CrmClientAggregate – naruszenie immutability
**Plik:** `src/App/Crm/Domain/Aggregate/CrmClientAggregate.php`
- Komentarz mówi o immutability, ale `softDelete()`, `addNote()`, `addAddress()` itd. mutują stan (`$this->isDeleted = ...`, `$this->addresses->add()`).
- `removeNote()` wywołuje `$this->clientNoteDto->softDelete()` bez sprawdzenia `$this->clientNoteDto === null`; może powodować błąd.
- **Rekomendacja:** Albo dopasować implementację do immutability (zwracanie nowych instancji), albo usunąć komentarz o immutability; w `removeNote()` dodać sprawdzenie `$this->clientNoteDto !== null`.

### Umiarkowane

#### 2.4 CQRS – komendy zwracające DTO
**Plik:** `CreateClientHandler`, `UpdateClientHandler`
- `ARCHITECTURE.md` mówi: „Komendy nie powinny zwracać danych (oprócz ID)”.
- CreateClientHandler i UpdateClientHandler zwracają `ClientDTO`.
- **Uwaga:** Moduł Task robi tak samo (`CreateTaskHandler` zwraca `TaskDTO`), więc jest to spójne z konwencją projektu.
- **Rekomendacja:** Albo dopasować dokumentację do zwykłego zwracania DTO po create/update, albo zmienić na zwracanie ID (np. `Uuid`) i osobne zapytanie po dane.

#### 2.5 Duplikacja mapowania Client → ClientDTO
**Pliki:** `CreateClientHandler`, `UpdateClientHandler`, `GetClientHandler`, `ListClientsHandler`
- Logika mapowania encja → DTO jest powielona w czterech miejscach (ok. 15 linii każda).
- **Rekomendacja:** Wydzielić `ClientToClientDTOMapper` (np. w `Application/DTO/` lub `Application/Mapper/`) i używać go we wszystkich handlerach.

#### 2.6 ListClientsQuery – string zamiast ClientStatus
**Plik:** `src/App/Crm/Application/Query/ListClients/ListClientsQuery.php`
```php
public readonly ?string $status = null,
```
- `status` jest stringiem; w handlerze jest używane `ClientStatus::from($query->status)`.
- **Rekomendacja:** Użyć `?ClientStatus $status = null` w Query; walidacja w mapperze (np. `ClientStatus::tryFrom()`).

#### 2.7 Podwójność modelu: Client vs CrmClientAggregate
- `Client` (Entity) – używana w repozytorium i handlerach.
- `CrmClientAggregate` – zawiera kolekcje (Address, Contact, Tag, ClientNote), używa `ClientDto` z `Domain/Dto`.
- `ClientDto` (Domain) vs `ClientDTO` (Application) – różne konwencje nazewnictwa.
- **Rekomendacja:** Ujednolicić: albo używać jednego modelu (Client jako agregat), albo jasno opisać, kiedy używać Client, a kiedy CrmClientAggregate; rozważyć przeniesienie `ClientDto` do Application lub usunięcie, jeśli nie jest używane.

### Drobne

#### 2.8 IsDeleted / IsCompany – brak walidacji
- Value Objects `IsDeleted`, `IsCompany` przyjmują dowolny bool; nie ma walidacji domenowej.
- **Rekomendacja:** Zostawić jak jest; ewentualnie dodać walidację, jeśli pojawią się reguły biznesowe.

#### 2.9 ClientNotes – brak walidacji
- `ClientNotes::fromString($model->notes)` – przyjmuje `null`; brak walidacji długości.
- **Rekomendacja:** Dodać walidację, jeśli notatki mają ograniczenie (np. max długość).

#### 2.10 UpdateClientRequest – brak customowej obsługi błędów walidacji
- `CreateClientRequest` ma `failedValidation()` z `HttpResponseException`; `UpdateClientRequest` nie.
- **Rekomendacja:** Dla spójności API dodać `failedValidation()` w `UpdateClientRequest` lub wynieść do wspólnej klasy bazowej.

#### 2.11 UpdateClientCommandMapper – różnice w obsłudze null
- `array_key_exists('regon', $validated)` vs `isset($validated['regon'])` – różne zachowanie dla `null` vs brak klucza.
- **Rekomendacja:** Ujednolicić: `array_key_exists` gdy chcemy rozróżnić „brak pola” od „pole = null” w PATCH.

---

## 3. Propozycja kolejności refaktoryzacji

1. **Krytyczne:** Popraw import `CreateClientCommandMapper` w CrmController.
2. **Krytyczne:** Popraw paginację i filtrowanie w `ListClientsHandler` (interfejs repozytorium + implementacja).
3. **Krytyczne:** Popraw `CrmClientAggregate::removeNote()` (null check) lub dopasuj immutability.
4. **Umiarkowane:** Wydziel `ClientToClientDTOMapper` i usuń duplikację.
5. **Umiarkowane:** Ujednolicić `Client` vs `CrmClientAggregate` – dokumentacja lub refaktoryzacja.
6. **Drobne:** Ujednolicić `UpdateClientRequest` (failedValidation), `ClientStatus` w `ListClientsQuery`.

---

## 4. Podsumowanie

| Kryterium | Ocena | Uwagi |
|-----------|-------|-------|
| **DDD** | Dobra | Poprawna struktura, VO, encje, repozytoria; do dopracowania: agregat vs encja |
| **CQRS** | Dobra | Rozdział Command/Query; komendy zwracają DTO (spójne z Task) |
| **SOLID** | Dobra | DIP, SRP zachowane; możliwa ekstrakcja mappera |
| **Clean Code** | Dobra | Duplikacja mapowania Client→DTO; drobne niespójności w mapperach |

Moduł CRM jest w dużej mierze zgodny z architekturą DDD/CQRS i zasadami SOLID. Najważniejsze są poprawki krytyczne (import, paginacja, `removeNote`), a następnie redukcja duplikacji i doprecyzowanie modelu domenowego.
