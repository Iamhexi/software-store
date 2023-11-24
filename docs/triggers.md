# Triggery

## SourceCodeCreated

Po utworzeniu krotki w encji SourceCode, tworzona jest również krotka wersji oprogramowania (w encji SoftwareVersion) odpowiadającą krotce kodu źródłowego. Wersja jest o jeden większa w sekcji *path_version* od ostatniej. Tzn. jeżeli ostatnia wersja oprogramowania miała kod wersji X.Y.Z, to utworzona, będzie miała kod wersji X.Y.Z+1. Jeżeli nie istnieje poprzednia wersja, to powstaje wersja o kodzie wersji 0.0.1.

## CategoryDeleted

Przed usunięciem krotki z encji Category - definującej kategorię, do której oprogramowanie może należeć - należy usunąć wszystkie krotki z nią powiązane z encji SoftwareCategory. Trigger realizuje tę powinność przez odnalezienie wszystkich krotek z encji SoftwareCategory o atrybucie równym usuwanemu *category_id* i usunięciu tychże krotek.
