=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Progi cenowe ilościowe dla WooCommerce. Ustaw poziomy rabatów za ilość i pokaż na stronie produktu tabelę cen renderowaną po stronie serwera. Bez jQuery.

== Description ==

Tiers dodaje sklepowi WooCommerce ceny zależne od ilości. Ustawiasz progi: kup 5, zyskaj 5%; kup 10, zyskaj 10%, a rabat jest odejmowany od pozycji koszyka w chwili, gdy klient doda odpowiednią liczbę sztuk. Te same progi pokazują się jako tabela na stronie produktu, więc klient widzi cenę, którą zapłaci, zanim doda produkt do koszyka.

Rabat jest liczony w PHP w akcji `woocommerce_before_calculate_totals`, więc logika cen nie wysyła żadnego JavaScriptu do przeglądarki. Tabela na stronie produktu to zwykła tabela HTML `<table>` renderowana po stronie serwera, z `<th scope>` i `<caption>`, więc jest poprawnie odczytywana przez czytniki ekranu i nie przesuwa układu podczas ładowania strony.

Gdy ilość pasuje do więcej niż jednego progu, obowiązuje najgłębszy kwalifikujący się próg: 12 sztuk otrzymuje cenę „10+", nie „5+". Tiers nigdy też nie podnosi ceny, więc produkt już przeceniony zachowuje niższą cenę.

Tiers deklaruje zgodność z WooCommerce HPOS oraz blokami Koszyka i Zamówienia. Wszystko przechowuje w jednym wierszu `wp_options` i nie tworzy własnych tabel, więc usunięcie wtyczki pozostawia bazę danych w stanie sprzed instalacji.

**Co otrzymujesz**

* Dowolną liczbę globalnych progów cenowych, każdy z minimalną ilością i procentem rabatu (z opcjonalną etykietą)
* Automatyczne naliczanie rabatu w koszyku, gdzie wygrywa najwyższy pasujący próg
* Tabelę cen na stronach pojedynczego produktu, z wyborem miejsca: podsumowanie produktu, przed lub za formularzem dodawania do koszyka, obszar meta produktu, albo nigdzie automatycznie
* Shortcode `[tiers_table]` i blok „Tabela cen ilościowych" do ręcznego wstawienia tabeli
* Opcjonalny nagłówek nad tabelą i opcjonalną kolumnę „Oszczędzasz"
* Opcjonalną informację „Oszczędzasz" pod każdą przecenioną pozycją w koszyku
* Kreator progów w panelu, który dodaje i usuwa wiersze w miejscu, z podglądem na żywo, jak czyta się każdy próg
* Polskie tłumaczenie oraz dołączony plik POT do tłumaczenia na inne języki (domena tekstowa `tiers`)
* Filtr `tiers_product_tiers`, który pozwala Tiers PRO podmienić progi per produkt lub per rola

**Dokumentacja:** https://plogins.com/tiers/docs/

== Installation ==

1. Zainstaluj i aktywuj WooCommerce (8.0 lub nowsze).
2. Wgraj folder `tiers` do `/wp-content/plugins/` albo pobierz kopię z https://github.com/wppoland/plogins-tiers.
3. Aktywuj wtyczkę na ekranie **Wtyczki**.
4. Przejdź do **WooCommerce → Tiers** i dodaj co najmniej jeden próg cenowy (np. 5 sztuk → 5% rabatu).
5. Tabela cen pojawia się automatycznie na stronach produktów, a rabaty obowiązują w koszyku.

== Frequently Asked Questions ==

= Dokumentacja i odnośniki =

* **Dokumentacja** - https://plogins.com/tiers/docs/
* **Strona wtyczki** - https://plogins.com/tiers/
* **Kod źródłowy** - https://github.com/wppoland/plogins-tiers
* **Zgłoszenia błędów i propozycje funkcji** - https://github.com/wppoland/plogins-tiers/issues


= Czy Tiers wymaga WooCommerce? =
Tak. Tiers jest rozszerzeniem WooCommerce i wymaga WooCommerce 8.0 lub nowszego.

= Czy tabela cen przeładowuje stronę? =
Nie. Tabela cen jest renderowana po stronie serwera, ładuje się razem ze stroną, zanim uruchomi się jakikolwiek JavaScript. Nie ma kroku AJAX ani hydratacji.

= Jak działa logika „wygrywa najwyższy próg"? =
Jeśli klient ma w koszyku 12 sztuk produktu, a Ty masz progi „5+" (5% rabatu) i „10+" (10% rabatu), otrzymuje 10% rabatu. Progi są sprawdzane od najniższej do najwyższej min_qty i wygrywa ostatnie dopasowanie.

= Czy mogę stosować różne progi do różnych produktów? =
W wersji darmowej progi są globalne (stosowane do wszystkich produktów). Tiers PRO dodaje nadpisania progów per produkt na ekranie edycji produktu.

= Czy rabaty są zgodne z kuponami WooCommerce? =
Tak. Tiers zmienia cenę pozycji koszyka, zanim WooCommerce policzy sumy, więc standardowe kupony WooCommerce działają normalnie na cenie z progu.

= Co się dzieje po dezaktywacji wtyczki? =
Rabaty przestają być naliczane, a tabela cen znika. Ustawienia pozostają w bazie danych.

= Co się dzieje po usunięciu wtyczki? =
Procedura odinstalowania usuwa opcję `tiers_settings`. Nie są tworzone żadne własne tabele, więc baza danych zostaje czysta.

= Czy działa z podatkami? =
Tak. Tiers zmienia ceny przed obliczeniem podatku przez WooCommerce, więc własna logika podatkowa WooCommerce obowiązuje na cenie z rabatem.


= Czy ta wtyczka działa na WordPress Multisite? =

Tak. Wtyczka jest zgodna z WordPress Multisite. Aktywuj ją sieciowo albo na poszczególnych witrynach; każda witryna zachowuje własne ustawienia i dane.

== Screenshots ==

1. Tabela cen ilościowych na stronie produktu, pokazuje zakresy ilości, procenty rabatu i wynikowe ceny.
2. Strona ustawień w panelu, kreator progów z dodawaniem i usuwaniem wierszy oraz przełącznikiem pokazywania tabeli.

== External Services ==

Tiers nie łączy się z żadnymi usługami zewnętrznymi. Progi cenowe są przechowywane w jednym wierszu `tiers_settings` w tabeli opcji WordPressa, a rabat jest liczony w PHP na Twoim własnym serwerze, żadne dane nigdy nie opuszczają Twojej witryny. Wtyczka nie wysyła e-maili i nie wykonuje zdalnych żądań; tabela cen na stronie produktu jest renderowana lokalnie z tych zapisanych progów.

== Development ==

Tiers jest tworzony otwarcie. Pliki PHP, JS i CSS, które instalujesz, to te same pliki co w repozytorium, nic nie jest minifikowane ani generowane przez krok budowania. Przeczytaj kod, zgłoś błąd lub prześlij poprawkę na https://github.com/wppoland/plogins-tiers.

== Changelog ==

= 0.2.2 =
* Pakowanie: wyłączenie katalogu zasobów .wordpress-org z paczki wtyczki (zasoby SVN WordPress.org). Bez zmian funkcjonalnych.

= 0.2.1 =
* Zmiana nazwy na Plogins Tiers for WooCommerce dla bardziej wyróżniającej się nazwy wtyczki.

= 0.2.0 =
* Nowość: konfigurowalne umiejscowienie tabeli (podsumowanie produktu, przed/za formularzem dodawania do koszyka, meta produktu, albo tylko ręcznie).
* Nowość: shortcode `[tiers_table]` i blok „Tabela cen ilościowych" do umieszczenia tabeli w dowolnym miejscu.
* Nowość: opcjonalny własny nagłówek tabeli.
* Nowość: opcjonalna kolumna „Oszczędzasz" w tabeli cen.
* Nowość: opcjonalna informacja „Oszczędzasz" per pozycja w koszyku.
* Nowość: obsługa tłumaczeń, z Domain Path `/languages`, dołączonym `tiers.pot` i polskim tłumaczeniem.
* Poprawka: zdefiniowanie brakującej stałej `Tiers\PLUGIN_DIR`, aby wtyczka uruchamiała się niezawodnie.
* Porządki: usunięcie nieużywanego szablonu; rozszerzenie kontroli standardów kodowania na szablony i bloki.

= 0.1.0 =
* Pierwsze wydanie: globalne progi cen ilościowych, tabela cen renderowana po stronie serwera na stronach produktów, kreator progów w panelu, filtr `tiers/product_tiers` do nadpisań w PRO. Bez jQuery, bez przesunięcia układu, WCAG 2.2 AA.
