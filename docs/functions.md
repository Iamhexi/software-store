 # Funkcje

## GetCommaSeparatedCategories(software_id)

Funkcja pobiera *software_id*. Dla wybranej krotki wyszukuje wszystkie kategorie, której krotka jest przyporządkowana i zwraca te nazwy kategorii w postaci listy elementów oddzielanych przecinkami, np. category1,category2,category3. Działanie funkcji polega na konkatenacji wielu krotek spełniających warunek pod względem zgodności atrybutu *software_id*. Zwraca wymienioną powyżej listę w formie łańcucha tekstowego. Jeżeli jednostka oprogramowania nie jest skojarzona z żadną kategorią, zwraca napis: "uncategorised".

## GetMostPopularSofwareAuthor()

Funkcja nie przyjmuje parametrów. Zwraca *user_id* najbardziej popularnego autora oprogramowania autora w całym sklepie pod względem łącznej liczby pobrań wszystkich jego jednostek oprogramowania. Użyteczne, aby pokazań klientowi sklepu konto i aplikacje takiego użytkownika.

## GetMostPopularSoftwareUnit(category_id)

Funkcja pobiera *category_id* kategorii. Zwraca *software_id* najbardziej popularnej jednostki oprogramowania pod względem liczby pobrań w wybranej kategorii oprogramowania.

## GetBestQualitySoftwareUnit()

Funkcja nie przyjmuje parametrów. Zwraca *software_id* jednostki oprogramowania, która mając przynajmniej 100 pobrań w ogóle, posiadała najmniej zgłoszeń błędów (krotek w encji BugReport) w ciągu ostatnich 3 miesięcy.
