# Projekt "Sklep z oprogramowaniem"
Autorzy:
- Igor Sosnowicz 263897
- Dawid Szeląg 264008
- Kacper Natusiewicz 263892

Termin: piątek 13:15 - 15:00 (grupa z 14:15)
Prowadzący: dr inż. Roman Ptak
Przedmiot: Bazy Danych 2 - Projekt
## Cel i zakres
Sklep przeznaczony jest niewielkiej grupy użytkowników do 1000 dziennie. Oferuje tylko bezpłatne oprogramowanie. Oprogramowanie dodawane jest w formie kodu źródłowego i kompilowane do pliku wykonywalnego w zależności od architektury urządzenia klienta, np. .exe lub .deb. W tej formie jest pobierane przez użytkownika (klienta sklepu), który jest odpowiedzialny za instalację pobranego pliku.

Istnieję trzy rodzaje użytkowników:
- administrator - osoba zarządzająca sklepem oraz użytkownikami. Posiada najwyższy stopień uprawnień;
- autor oprogramowania - osoba wgrywająca pliki źródłowe do sklepu, zarządzająca swoim oprogramowaniem;
- klient sklepu - osoba przeglądająca i pobierająca oprogramowanie ze sklepu za darmo.

**Wyjaśnienia skrótów:**
AO = autor oprogramowania
ADM = administrator
## Wymagania funkcjonalne:
1. Użytkownik niezarejestrowany może wyłącznie założyć konto klienta sklepu.
2. Klient/AO/ADM może wyszukiwać oprogramowanie.
3. Klient/AO/ADM może przeglądać oprogramowanie.
4. Klient/AO/ADM może pobierać oprogramowanie.
5. Klient/AO/ADM może wystawiać słowną recenzję.
6. Klient/AO/ADM może edytować swoją słowną recenzję.
7. Klient/AO/ADM może wystawiać ocenę w skali 1 do 5.
8. Klient/AO/ADM może przeglądać recenzje i średnią z ocen.
9. Klient/AO/ADM może zgłaszać błędy w oprogramowaniu do jego autorów.
10. Klient/AO/ADM może zgłaszać naruszenia regulaminu do administratora.
11. Klient/AO/ADM może wysłać prośbę do administratora o zmianę jego typu konta na autora oprogramowania.
12. Autor oprogramowania/ADM może dodawać nowe oprogramowanie do sklepu: wgrywanie kodu źródłowego na serwer, dodanie grafiki promocyjnej, wybór kategorii, opis słowny.
13. Autor oprogramowania/ADM może aktualizować oprogramowanie, które wcześniej dodał.
14. Autor oprogramowania/ADM może czytać nadesłane zgłoszenia błędów.
15. Autor oprogramowanie/ADM może usuwać nadesłane przez siebie oprogramowanie.
16. Administrator może odrzucać/akceptować zgłoszenia o zmianę typu konta (z klienta na autora autora oprogramowania).
17. Administrator może przeglądać zgłoszenia klientów dot. łamania przez oprogramowanie regulaminu sklepu.
18. Administrator może usuwać (pernamentnie) lub zawieszać (tymczasowo) dowolne oprogramowanie ze sklepu.
19. Administrator może usuwać dowolne komentarze ze sklepu.
20. Administrator może usuwać konta klientów i autorów oprogramowania.

## Wymagania niefunkcjonalne
1. Sklep funkcjonuje jako aplikacja uruchamiana na serwerze, zaś dostępem dla użytkowników realizowany jest przez aplikację webową.
2. Sklep ze względu na niewielką skalę działalności (najwięcej encji dla ocen oprogramowania do 12 000 przy założeniu 100 ocen na oprogramowanie i 120 jednostek oprogramowania wgranych do sklepu: 120) korzysta z relacyjnej bazy danych Maria DB.
3. System powinien być napisany: backend - w PHP, frontend - w JavaScript, TypeScript, React.JS.
4. Kompilacja z kodu źródłowego oprogramowania wgranego do sklepu do pliku wykonywalnego odbywa się po stronie serwera.
5. Serwer działa pod systemem operacyjnym Linux.
6. Logowanie do konta użytkownika odbywa się przez podanie loginu i hasła.
7. Hasła w bazie danych są przechowywane w postaci zahashowanej.
8. Jeden użytkownik może umieścić maksymalnie jedną recenzję słowną pod danym oprogramowaniem.
9. Jeden użytkownik może umieścić maksymalnie jedną ocenę pod danych oprogramowaniem.
