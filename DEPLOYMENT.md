## CI/CD – automatyczny deploy backendu (Laravel + Docker + GitHub Actions)

Ten dokument opisuje, co trzeba zrobić, aby działał automatyczny deploy backendu na serwer po `push` do wybranej gałęzi (domyślnie `ci/cd`).

Pipeline jest zdefiniowany w pliku:
- `.github/workflows/deploy.yml`

---

## 1. Wymagania wstępne

- Repozytorium znajduje się na GitHubie.
- Na serwerze (VPN) masz możliwość logowania się po SSH.
- Na serwerze są lub będą zainstalowane:
  - `docker`
  - `docker compose` (lub `docker-compose`)
  - `git`

---

## 2. Przygotowanie serwera (jednorazowo)

1. Zaloguj się na serwer przez SSH:

   ```bash
   ssh USER@HOST
   ```

   - `USER` – nazwa użytkownika na serwerze (np. `deploy`, `root` lub inny),
   - `HOST` – adres serwera (IP lub domena).

2. Utwórz katalog na aplikację, np.:

   ```bash
   sudo mkdir -p /var/www/app-backend
   sudo chown -R $USER:$USER /var/www/app-backend
   cd /var/www/app-backend
   ```

3. Sklonuj repozytorium z GitHuba:

   ```bash
   git clone <URL_DO_REPOZYTORIUM> .
   git checkout ci/cd   # albo main, jeśli chcesz z main
   ```

4. Upewnij się, że w katalogu są:

   - `docker-compose.yml`
   - katalog `task/` z aplikacją Laravel
   - katalog `.github/` (z workflow).

5. Pierwsze uruchomienie kontenerów:

   ```bash
   docker compose up -d --build
   ```

   - Sprawdź, czy aplikacja działa (przez Nginx/proxy lub bezpośrednio na porcie).

---

## 3. Konfiguracja sekretów w GitHub Actions

1. Wejdź w repo na GitHubie:
   - `Settings` → `Secrets and variables` → `Actions`.

2. Dodaj sekrety (New repository secret):

   - **`SSH_HOST`**
     - adres serwera, np. `1.2.3.4` albo `my-server.local`.

   - **`SSH_USER`**
     - użytkownik SSH, np. `deploy`.

   - **`SSH_PASSWORD`**
     - hasło tego użytkownika.
     - (opcjonalnie później zastąpisz to kluczem SSH).

   - **`SERVER_APP_PATH`**
     - pełna ścieżka do katalogu z aplikacją na serwerze, np.:
       - `/var/www/app-backend`

3. (Opcjonalnie, bezpieczniej) przejście na klucz SSH:

   - Na swoim komputerze:

     ```bash
     ssh-keygen -t ed25519 -C "github-actions-deploy"
     ```

   - Publiczny klucz (`id_ed25519.pub`) dodaj do `~/.ssh/authorized_keys` na serwerze.
   - Prywatny (`id_ed25519`) skopiuj jako sekret w repo, np.:
     - **`SSH_PRIVATE_KEY`**
   - W pliku `.github/workflows/deploy.yml` masz zakomentowany blok z `webfactory/ssh-agent` – możesz go odkomentować i przejść z hasła na klucz.

---

## 4. Jak działa workflow `.github/workflows/deploy.yml`

Workflow ma dwa joby:

1. **`test`** (CI):
   - Odpala się na `push` do gałęzi `ci/cd` (można zmienić na `main`).
   - Kroki:
     - `actions/checkout@v4` – pobiera kod.
     - `docker/setup-buildx-action@v3` – przygotowuje środowisko Dockera.
     - `docker compose -f docker-compose.yml pull` – pobiera obrazy (jeśli są).
     - `docker compose -f docker-compose.yml build app` – buduje kontener `app`.
     - `docker compose -f docker-compose.yml run --rm app php artisan test` – uruchamia testy Laravel.

2. **`deploy`** (CD):
   - Odpala się po udanym `test` (`needs: test`).
   - Używa akcji `appleboy/ssh-action`, aby połączyć się z serwerem po SSH.
   - Na serwerze wykonuje m.in.:
     - `cd ${SERVER_APP_PATH}`
     - `git fetch --all`
     - `git checkout "${DEPLOY_BRANCH}"`
     - `git pull origin "${DEPLOY_BRANCH}"`
     - `docker compose pull || true`
     - `docker compose up -d --build`
     - `docker image prune -f || true`

### Zmiana docelowej gałęzi deployu

W jobie `deploy` w sekcji `env` jest:

```yaml
env:
  SERVER_APP_PATH: ${{ secrets.SERVER_APP_PATH }}
  DEPLOY_BRANCH: ci/cd
```

Jeśli chcesz deploy z `main`, zmień `ci/cd` na `main`.

---

## 5. Dostęp przez VPN / self-hosted runner

Jeśli serwer jest dostępny z Internetu (port 22), GitHub Actions może łączyć się bezpośrednio przez `SSH_HOST`.

Jeśli **serwer jest dostępny tylko przez VPN**:

1. Najlepsze rozwiązanie: **self-hosted runner** na serwerze.
   - W repo → `Settings` → `Actions` → `Runners` → `New self-hosted runner`.
   - Postępuj zgodnie z instrukcją (pobranie binarki, rejestracja, uruchomienie).
   - W workflow zamiast:

     ```yaml
     runs-on: ubuntu-latest
     ```

     ustaw:

     ```yaml
     runs-on: self-hosted
     ```

   - Wtedy kroki deployu możesz wykonać bez SSH (bezpośrednie `run: |`).

2. Alternatywa (mniej zalecana): wystawienie SSH na świat z dobrym firewallem i ograniczeniem IP.

---

## 6. Testowanie pipeline’u

1. Upewnij się, że:
   - Serwer ma sklonowane repo w `SERVER_APP_PATH`.
   - `docker compose up -d --build` działa poprawnie ręcznie.
   - Sekrety `SSH_HOST`, `SSH_USER`, `SSH_PASSWORD`, `SERVER_APP_PATH` są ustawione.

2. Wprowadź małą zmianę w kodzie (np. komentarz w pliku PHP w katalogu `task/`) i wypchnij ją na gałąź `ci/cd`:

   ```bash
   git add .
   git commit -m "Test CI/CD pipeline"
   git push origin ci/cd
   ```

3. Na GitHubie wejdź w zakładkę **Actions**:
   - Znajdź workflow `CI/CD Backend Docker Deploy`.
   - Sprawdź, czy job `test` i `deploy` zakończyły się sukcesem.

4. Na serwerze:
   - Sprawdź, czy kontenery działają:

     ```bash
     docker compose ps
     ```

   - Zobacz logi (np. aplikacji):

     ```bash
     docker compose logs app
     ```

   - Zweryfikuj aplikację w przeglądarce lub przez `curl`.

---

## 7. Dalsze ulepszenia (opcjonalne)

- Oddzielne gałęzie na staging/produkcję (np. `develop` → staging, `main` → prod).
- Osobne sekrety dla staging/produkcji (`SSH_HOST_STAGING`, `SSH_HOST_PROD` itp.).
- Health-check po deployu (krok w workflow, który odpala żądanie HTTP na endpoint `/health`).
- Rollback (tagowanie wersji i możliwość powrotu do poprzedniego taga).

