# Procedury

## PurgeSoftware(software_id) 

Przyjmuje software_id i usuwa wszystkie wiersze związane z oprogramowaniem z tabel: SoftwareUnit, SoftwareVersion, SourceCode, Rating, Review, BugReport, ale nie StatuteViolationReport, ponieważ zawiera powód usunięcia oprogramowania.

## BlockSoftware(software_id)

Przymyjmuje software_id i na tej podstawie ustawia kolumnę is_blocked z tabeli SoftwareUnit na true, co sprawi, że jednostka oprogramowania będzie niewidoczna (więc niemożliwa do wyszukiwania, przeglądania i pobierania dla klienta sklepu). Procedura nie powoduje usunięcia jednostki oprogramowania ze sklepu. Nie wywiera efektu na wcześniej zablokowane jednostki oprogramowania.

## UnblockSoftware(software_id)

Przymyjmuje software_id i na tej podstawie ustawia kolumnę is_blocked z tabeli SoftwareUnit na false, co sprawi, że jednostka oprogramowania będzie ponownie widoczna w sklepie (więc możliwa do wyszukiwania, przeglądania i pobierania dla klienta sklepu). Nie wywiera efektu na wcześniej odblokowane jednostki oprogramowania.

## PurgeUser(user_id)

Pobiera user_id, jeżeli użytkownik jest klientem sklepu to: procedura usuwa użytkownika i wszelkie ślady jego aktywności ze sklepu. Obejmuje to usunięcie wierszy związanych z użytkownikiem z tabel: User, Rating, Review, AccountChangeRequest, BugReport i Download. Jeżeli użytkownik jest autorem oprogramowania, to usuwane zostają także dodatkowo jego jednostki oprogramowania i wszelkie powiązania do nich z tabel: SoftwareUnit, SoftwareVersion, SourceCode, Executable. Jeżeli podejmowana jest próba usunięcia administratora, procedura kończy się niepowodzeniem -- nic nie zostaje usunięte.

## ProcessAcountChangeRequest(request_id , isAccepted, justification)

Przyjmuje: 

- id prośby o zmianę typu konta użytkownika
- zmienną typu logicznego informująca, czy prośba ma być zaakceptowana (1, true) czy odrzucona (0, false)
- uzasadnienie decyzji podjętej przez administratora

Aktualizuje wskazaną prośbę zmiany typu z klienta sklepu na autora oprogramowania, przyjmując ją lub odrzucając, lecz dostarczając także uzasadnienia tej decyzji. Aktualizuje encję User, jeżeli uprawnienia zostały przyznane.

## DeleteSoftwareVersion(version_id)

Przyjmuje id wersji oprogramowania. Wskazaną wersję oprogramowania, wraz z odpowiadającym wersji kodem źródłowym, usuwa ze sklepu. Usuwa także krotki z encji Executable (możliwość pobierania plików wykonywalnych tej wersji oprogramowania) i Download (wcześniej dokonane statystyki pobrań usuniętej wersji oprogramowania).
