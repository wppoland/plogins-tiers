=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Progi cen hurtowych dla WooCommerce. Ustaw zakresy rabatów ilościowych i pokaż na stronach produktów tabelę cen renderowaną po stronie serwera. Bez jQuery.

== Description ==

Tiers daje sklepowi WooCommerce ceny zależne od ilości. Ustawiasz progi: kup 5 — zaoszczędź 5%; kup 10 — zaoszczędź 10%, a rabat jest odejmowany od pozycji zamówienia w chwili, gdy klient doda do koszyka wystarczającą liczbę sztuk. Te same progi są pokazywane w tabeli na stronie produktu, aby klienci widzieli cenę, jaką zapłacą, zanim dodadzą produkt do koszyka.

Rabat jest obliczany w PHP w zdarzeniu `woocommerce_before_calculate_totals`, więc logika cen nie ładuje żadnego front-endowego JavaScriptu. Tabela na stronie produktu to zwykła tabela HTML `<table>` wypisywana po stronie serwera z `<th scope>` i `<caption>`, dzięki czemu jest poprawnie odczytywana przez czytniki ekranu i nie przesuwa układu podczas ładowania strony.

Gdy ilość pasuje do więcej niż jednego progu, stosowany jest najwyższy kwalifikujący się próg — 12 sztuk otrzymuje cenę „10+”, a nie „5+”. Tiers nigdy też nie podnosi ceny, więc produkt już przeceniony zachowuje swoją niższą cenę.

Tiers deklaruje zgodność z WooCommerce HPOS oraz blokami Koszyka/Kasy. Przechowuje wszystko w jednym wierszu `wp_options` i nie tworzy żadnych niestandardowych tabel, więc usunięcie wtyczki pozostawia bazę danych w niezmienionym stanie.

<strong>Co otrzymujesz</strong>

* Dowolna liczba globalnych progów cenowych, każdy z minimalną ilością i procentem rabatu (z opcjonalną etykietą)
* Automatyczne rabaty w koszyku, gdzie wygrywa najwyższy pasujący próg
* Tabela cen na stronach pojedynczych produktów, z wyborem miejsca wyświetlania: podsumowanie produktu, przed lub po formularzu dodawania do koszyka, obszar meta produktu albo nigdzie automatycznie
* Shortcode `[tiers_table]`, blok Gutenberga „Tabela cen hurtowych” oraz widżet Elementora „Tabela cen hurtowych” do ręcznego wstawiania tabeli
* Opcjonalny nagłówek nad tabelą i opcjonalna kolumna „Oszczędzasz”
* Opcjonalna informacja „Oszczędzasz” pod każdą przecenioną pozycją w koszyku
* Kreator progów w panelu, który dodaje i usuwa wiersze w miejscu, z podglądem na żywo tego, jak brzmi każdy próg
* Tłumaczenie na polski oraz dołączony plik POT do tłumaczenia na inne języki (domena tekstowa `tiers`)
* Filtr `tiers_product_tiers`, który pozwala Tiers PRO podmienić progi zależne od produktu lub roli

<strong>Dokumentacja:</strong> https://plogins.com/pl/tiers/docs/

= You may also like these plugins =

Więcej darmowych wtyczek WooCommerce od WPPoland:

* [Plogins Waitlist](https://wordpress.org/plugins/plogins-waitlist/) - lista powiadomień o dostępności, która wysyła e-mail do klientów w chwili, gdy produkt wraca do sprzedaży.
* [Sieve - Search & Filter](https://wordpress.org/plugins/sieve/) - szybkie wyszukiwanie i filtrowanie produktów AJAX dla WooCommerce, bez jQuery.
* [Polski for WooCommerce](https://wordpress.org/plugins/polski/) - zgodność z polskim rynkiem: GPSR, Omnibus, RODO, faktury i moduły sklepowe.

Przejrzyj pełny katalog na https://plogins.com/pl/ .

== Installation ==

1. Zainstaluj i włącz WooCommerce (8.0 lub nowszy).
2. Wgraj folder `tiers` do `/wp-content/plugins/` lub pobierz kopię z https://github.com/wppoland/plogins-tiers.
3. Włącz wtyczkę na ekranie <strong>Wtyczki</strong>.
4. Przejdź do <strong>WooCommerce → Tiers</strong> i dodaj co najmniej jeden próg cenowy (np. 5 sztuk → 5% rabatu).
5. Tabela cen pojawia się automatycznie na stronach produktów, a rabaty są naliczane w koszyku.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentacja</strong> - https://plogins.com/pl/tiers/docs/
* <strong>Strona wtyczki</strong> - https://plogins.com/pl/tiers/
* <strong>Kod źródłowy</strong> - https://github.com/wppoland/plogins-tiers
* <strong>Zgłoszenia błędów i propozycje funkcji</strong> - https://github.com/wppoland/plogins-tiers/issues


= Does Tiers require WooCommerce? =
Tak. Tiers jest rozszerzeniem WooCommerce i wymaga WooCommerce 8.0 lub nowszego.

= Does the pricing table reload the page? =
Nie. Tabela cen jest renderowana po stronie serwera i ładuje się razem ze stroną, zanim uruchomi się jakikolwiek JavaScript. Nie ma etapu AJAX ani hydratacji.

= How does the "highest tier wins" logic work? =
Jeśli klient ma w koszyku 12 sztuk produktu, a Ty masz progi „5+” (5% rabatu) i „10+” (10% rabatu), otrzyma 10% rabatu. Progi są oceniane od najniższego do najwyższego min_qty i wygrywa ostatnie dopasowanie.

= Can I apply different tiers to different products? =
W wersji darmowej progi są globalne (obejmują wszystkie produkty). Tiers PRO dodaje nadpisania progów dla poszczególnych produktów na ekranie edycji produktu.

= Are discounts compatible with WooCommerce coupons? =
Tak. Tiers modyfikuje cenę pozycji koszyka, zanim WooCommerce obliczy sumy, więc standardowe kupony WooCommerce działają normalnie na przecenionej cenie progowej.

= What happens when I deactivate the plugin? =
Rabaty przestają być stosowane, a tabela cen znika. Twoje ustawienia pozostają zapisane w bazie danych.

= What happens when I delete the plugin? =
Procedura odinstalowania usuwa opcję `tiers_settings`. Nie są tworzone żadne niestandardowe tabele, więc baza danych pozostaje czysta.

= Does it work with taxes? =
Tak. Tiers modyfikuje ceny przed obliczeniem podatku przez WooCommerce, więc podatkowa logika WooCommerce działa na przecenionej cenie.


= Does this plugin work on WordPress Multisite? =

Tak. Ta wtyczka jest zgodna z WordPress Multisite. Włącz ją w całej sieci lub na poszczególnych witrynach; każda witryna zachowuje własne ustawienia i dane.

== Screenshots ==

1. Tabela cen hurtowych na stronie produktu — pokazuje zakresy ilości, procenty rabatu i wynikowe ceny.
2. Strona ustawień w panelu — kreator progów z dodawaniem/usuwaniem wierszy i przełącznikiem pokazywania/ukrywania tabeli.

== External Services ==

Tiers nie łączy się z żadnymi usługami zewnętrznymi. Progi cenowe są przechowywane w jednym wierszu `tiers_settings` w tabeli opcji WordPressa, a rabat jest obliczany w PHP na Twoim własnym serwerze — żadne dane nigdy nie opuszczają Twojej witryny. Wtyczka nie wysyła e-maili ani nie wykonuje żadnych zdalnych żądań; tabela cen na stronie produktu jest renderowana lokalnie z tych zapisanych progów.

== Development ==

Tiers jest rozwijany otwarcie (open source). Instalowane pliki PHP, JS i CSS to te same pliki co w repozytorium — nic nie jest minifikowane ani generowane w kroku budowania. Przeczytaj kod, zgłoś błąd lub wyślij poprawkę na https://github.com/wppoland/plogins-tiers.

== Translations ==

Plogins Tiers zawiera polskie, niemieckie i hiszpańskie tłumaczenia interfejsu wtyczki. Domena tekstowa to `tiers`, więc pakiety językowe z WordPress.org mogą też nadpisywać lub rozszerzać dołączone tłumaczenia.

== Changelog ==

= 1.0.2 =
* Dodano dołączone polskie, niemieckie i hiszpańskie tłumaczenia interfejsu wtyczki.

= 1.0.1 =
* Pierwsza stabilna wersja.

= 0.2.4 =
* Dokumentacja: dodano sekcję „Może Ci się też spodobać”, linkującą pozostałe darmowe wtyczki WooCommerce od WPPoland. Bez zmian funkcjonalnych.

= 0.2.3 =
* Nowość: widżet Elementora dla tabeli cen hurtowych (działa w Elementorze 3.x i 4.0).

= 0.2.2 =
* Pakowanie: wykluczenie katalogu zasobów .wordpress-org z pobieranej wtyczki (zasoby tylko dla SVN WordPress.org). Bez zmian funkcjonalnych.

= 0.2.1 =
* Zmieniono nazwę na Plogins Tiers dla WooCommerce, aby uzyskać bardziej charakterystyczną nazwę wtyczki.

= 0.2.0 =
* Nowość: konfigurowalne umiejscowienie tabeli (podsumowanie produktu, przed/po formularzu dodawania do koszyka, meta produktu lub tylko ręcznie).
* Nowość: shortcode `[tiers_table]` oraz blok „Tabela cen hurtowych” do umieszczania tabeli w dowolnym miejscu.
* Nowość: opcjonalny własny nagłówek tabeli.
* Nowość: opcjonalna kolumna „Oszczędzasz” w tabeli cen.
* Nowość: opcjonalna informacja „Oszczędzasz” dla poszczególnych pozycji w koszyku.
* Nowość: obsługa tłumaczeń ze ścieżką domeny `/languages`, dołączonym plikiem `tiers.pot` i tłumaczeniem na polski.
* Poprawka: zdefiniowano brakującą stałą `Tiers\PLUGIN_DIR`, aby wtyczka uruchamiała się niezawodnie.
* Porządki: usunięto nieużywany szablon; rozszerzono pokrycie standardów kodowania na szablony i bloki.

= 0.1.0 =
* Pierwsze wydanie: globalne progi cen hurtowych, tabela cen renderowana po stronie serwera na stronach produktów, kreator progów w panelu, filtr `tiers/product_tiers` do nadpisań w wersji PRO. Bez jQuery, bez przeskoków układu, WCAG 2.2 AA.
