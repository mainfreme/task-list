# Moduł Event

Moduł jest **placeholderem** pod przyszłą obsługę **zdarzeń domenowych lub integracji opartej o eventy** (np. publiczne API webhooków, saga, powiadomienia).

## Stan implementacji

Struktura folderów (`Application`, `UI/Http` z podkatalogami na kontrolery, requesty, routery) jest utworzona, ale **nie zawiera jeszcze działającego kodu PHP** — katalogi są puste lub niekompletne.

## Przeznaczenie (planowane)

Docelowo może grupować endpointy lub konsumentów zdarzeń niezależnie od modułów takich jak `Task` czy `Auth`, z zachowaniem tej samej konwencji warstw.
