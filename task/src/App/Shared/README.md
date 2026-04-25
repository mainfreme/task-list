# Moduł Shared

Moduł **nie jest** osobnym „bounded contextem” biznesowym — zawiera **współdzielony kod** używany przez pozostałe moduły (`Auth`, `Task`, `Crm`, itd.).

## Do czego służy

- **Domain** — value objecty i kontrakty ponad modułami, m.in. `Uuid`, `Email`, `Phone`, `Address`, interfejs serwisu Redis.
- **Infrastructure** — implementacje techniczne:
  - **Redis** (provider + `LaravelRedisService`),
  - **RabbitMQ** (połączenie, kolejki, producer),
  - **Mapper** żądań (`GenericRequestMapper`, atrybuty mapowania),
  - **Provider** rejestrujący implementacje repozytoriów i usług (`RepositoryServiceProvider`, `RedisServiceProvider`, migracje/konsola).
- **UI** — bazowy **`ApiController`** (jednolite odpowiedzi JSON), pomocniczo OpenAPI.

## Zasady użycia

Nowe moduły powinny **importować typy i serwisy z Shared** zamiast duplikować je lokalnie. Zmiany tutaj mogą wpływać na całą aplikację, więc warto je trzymać **generycznymi** i stabilnymi.
