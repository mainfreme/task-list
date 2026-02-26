# Przegląd testów jednostkowych – rekomendacje

**Wytyczne dla agenta / przyszłych testów:** reguła Cursora `.cursor/rules/unit-tests.mdc` – stosuj ją przy pisaniu i review testów.


## 1. Zasady na przyszłość

1. **Unikać asercji typu „czy coś jest równe same sobie”** – np. `assertSame($x, $x)` lub przechwycenie argumentu mocka i sprawdzenie, że to ten sam obiekt, który wcześniej podaliśmy (chyba że cel to weryfikacja referencji).
2. **Każdy test ma weryfikować jeden konkretny scenariusz** – np. „gdy task ma dueDate, DTO zwraca ten dueDate”, „gdy whitelist jest pusta (nie null), middleware przepuszcza”.
3. **Przypadki brzegowe:** null vs pusty, min/max długość, nieprawidłowy format, idempotencja (ta sama operacja dwa razy), „nie znaleziono” + brak wywołań dalszych metod (np. save/softDelete).
4. **Value objects:** Testować w testach domeny (Title, Email, Uuid…); w testach aplikacji/infrastruktury **nie** powtarzać logiki value objectów (np. isEmpty() dla IpWhitelist w ApiKeyMiddlewareTest).

