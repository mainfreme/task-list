# Moduł Ops

Moduł wspiera **operacje i observability** związane z wdrożeniami — w szczególności **zbieranie informacji o nieudanych deployach** z zewnętrznych systemów.

## Do czego służy

- Endpoint webhooka: **`POST /v1/deploy/error`** (prefix i middleware `deploy.webhook` w `routes/api.php`).
- Przyjmuje zwalidowany opis błędu (projekt, repozytorium, kontener, stage, komunikat, hostname itd.).
- Zapisuje rekord w bazie jako encję **DeployFailure** i zwraca identyfikator oraz potwierdzenie odbioru.

## Warstwy

- **Domain** — encja `DeployFailure`, repozytorium (interfejs + reguły zapisu).
- **Application** — komenda `RecordDeployFailure` i handler zapisujący zdarzenie.
- **Infrastructure** — implementacja persystencji repozytorium.
- **UI** — `DeployWebhookController`, request `DeployErrorRequest`, mapper na komendę.

Moduł jest świadomie **wąski**: nie zarządza pełnym CI/CD, lecz **rejestruje raporty błędów** z zaufanych źródeł (zabezpieczonych webhookiem).
