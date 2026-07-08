=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Poziomy cenowe dla WooCommerce. Ustaw zakresy rabatów ilościowych i pokaż tabelę cen renderowaną przez serwer na stronach produktów. Bez jQuery.

== Description ==

Tiers zapewnia sklepowi WooCommerce ceny oparte na ilości. Ustawiasz punkty przerwania, kup 5, zaoszczędź 5%; kup 10, zaoszczędź 10%, a rabat zostanie odjęty od pozycji zamówienia w momencie, gdy kupujący zakupi wystarczającą liczbę jednostek. Te same punkty przerwania są pokazane w tabeli na stronie produktu, dzięki czemu użytkownicy mogą zobaczyć cenę, jaką zapłacą, zanim dodadzą produkt do koszyka.

Rabat jest obliczany w PHP na podstawie `woocommerce_before_calculate_totals`, więc logika cenowa nie zawiera interfejsu JavaScript. Tabela stron produktu to zwykła `<tabela>` w formacie HTML drukowana po stronie serwera z `<tym zakresem>` i `<podpisem>`, dzięki czemu jest poprawnie czytana przez czytniki ekranu i nie zmienia układu podczas ładowania strony.

Jeśli ilość odpowiada więcej niż jednemu poziomowi, stosuje się najgłębszy poziom kwalifikacyjny, 12 jednostek przyjmuje cenę „10+”, a nie cenę „5+”. Tiers również nigdy nie podnosi ceny, więc produkt, który jest już w promocji, zachowuje niższą cenę.

Tiers deklaruje kompatybilność z WooCommerce HPOS i blokami koszyka/kasy. Przechowuje wszystko w jednym wierszu `wp_options` i nie tworzy niestandardowych tabel, więc usunięcie wtyczki pozostawia bazę danych bez zmian.

<strong>Co otrzymujesz</strong>

* Dowolna liczba globalnych poziomów cenowych, każdy z minimalną ilością i procentem rabatu (z opcjonalną etykietą)
* Automatyczne rabaty w koszyku, przy czym wygrywa najwyższy pasujący poziom
* Tabela cen na stronach poszczególnych produktów z możliwością wyboru miejsca jej wyświetlania: podsumowanie produktu, przed lub po formularzu dodawania do koszyka, metaobszar produktu lub nigdzie automatycznie
* Krótki kod `[tiers_table]` i blok „Tabela cen wolumenowych” do ręcznego umieszczenia tabeli
* Opcjonalny nagłówek nad tabelą i opcjonalna kolumna „Oszczędzasz”.
* Opcjonalna notatka „Oszczędzasz” pod każdą przecenioną linią w koszyku
* Kreator poziomów administracyjnych, który dodaje i usuwa wiersze na miejscu, z podglądem na żywo odczytu każdego poziomu
* Tłumaczenie na język polski oraz dołączony plik POT do tłumaczenia na inne języki (poziomy domeny tekstowej)
* Filtr „tiers_product_tiers”, który umożliwia Tiers PRO zamianę poziomów według produktu lub ról

<strong>Dokumentacja:</strong> https://plogins.com/pl/tiers/docs/

= You may also like these plugins =

Więcej darmowych wtyczek WooCommerce od WPPoland:

* [Plogins Waitlist](https://wordpress.org/plugins/plogins-waitlist/) - lista oczekujących na powrót do magazynu, która wysyła e-mail do kupujących w momencie zwrotu produktu.
* [Sieve - Search & Filter](https://wordpress.org/plugins/sieve/) - szybkie wyszukiwanie i filtrowanie produktów AJAX dla WooCommerce, bez jQuery.
* [Polski for WooCommerce](https://wordpress.org/plugins/polski/) - Zgodność z rynkiem polskim: GPSR, Omnibus, RODO, faktury i moduły sklepowe.

Pełny katalog znajdziesz na https://plogins.com/pl/.

== Installation ==

1. Zainstaluj i aktywuj WooCommerce (8.0 lub nowszy).
2. Prześlij folder `tiers` do `/wp-content/plugins/` lub pobierz kopię z https://github.com/wppoland/plogins-tiers.
3. Aktywuj wtyczkę na ekranie <strong>Wtyczki<strong>. 4. Przejdź do </strong>WooCommerce → Poziomy</strong> i dodaj co najmniej jeden poziom cenowy (np. 5 jednostek → 5% zniżki).
5. Cennik pojawia się automatycznie na stronach produktów, a rabaty naliczają się w koszyku.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentacja</strong> - https://plogins.com/pl/tiers/docs/
* <strong>Strona wtyczki</strong> - https://plogins.com/pl/tiers/
* <strong>Kod źródłowy</strong> - https://github.com/wppoland/plogins-tiers
* <strong>Raporty o błędach i prośby o nowe funkcje</strong> - https://github.com/wppoland/plogins-tiers/issues


= Does Tiers require WooCommerce? =
Tak. Tiers jest rozszerzeniem WooCommerce i wymaga WooCommerce 8.0 lub nowszego.

= Does the pricing table reload the page? =
Nie. Tabela cen jest renderowana przez serwer i ładuje się wraz ze stroną, zanim zostanie uruchomiony jakikolwiek JavaScript. Nie ma etapu AJAX ani nawodnienia.

= How does the "highest tier wins" logic work? =
Jeśli klient ma w koszyku 12 sztuk produktu, a Ty masz poziomy „5+” (5% zniżki) i „10+” (10% zniżki), otrzyma 10% zniżki. Poziomy są oceniane od najniższego do najwyższego min_qty, a ostatni mecz wygrywa.

= Can I apply different tiers to different products? =
W wersji bezpłatnej poziomy mają charakter globalny (dotyczy wszystkich produktów). Tiers PRO dodaje zastąpienia poziomów dla poszczególnych produktów na ekranie edycji produktu.

= Are discounts compatible with WooCommerce coupons? =
Tak. Poziomy modyfikuje cenę pozycji koszyka, zanim WooCommerce obliczy sumę, więc standardowe kupony WooCommerce działają normalnie na górze ceny wielopoziomowej.

= What happens when I deactivate the plugin? =
Rabaty przestają być stosowane, a tabela cenowa nie jest już wyświetlana. Twoje ustawienia zostaną zapisane w bazie danych.

= What happens when I delete the plugin? =
Procedura deinstalacji usuwa opcję `tiers_settings`. Nie są tworzone żadne niestandardowe tabele, więc baza danych pozostaje czysta.

= Does it work with taxes? =
Tak. Tiers modyfikuje ceny przed obliczeniem podatku WooCommerce, więc logika podatkowa WooCommerce ma zastosowanie do obniżonej ceny.


= Does this plugin work on WordPress Multisite? =

Tak. Ta wtyczka jest kompatybilna z WordPress Multisite. Aktywuj go w sieci lub aktywuj na poszczególnych stronach; każda witryna przechowuje własne ustawienia i dane.

== Screenshots ==

1. Tabela cen hurtowych na stronie produktu, pokazuje zakresy ilościowe, procent rabatów i wynikające z nich ceny.
2. Strona ustawień administratora, kreator poziomów z możliwością dodawania/usuwania wierszy i przełącznikiem pokazywania/ukrywania tabeli.

== External Services ==

Tiers nie łączy się z żadnymi usługami zewnętrznymi. Poziomy cenowe są przechowywane w pojedynczym wierszu „tiers_settings” w tabeli opcji WordPress, a rabat jest obliczany w PHP na Twoim własnym serwerze, żadne dane nie opuszczają Twojej witryny. Wtyczka nie wysyła wiadomości e-mail ani nie wysyła żadnych zdalnych żądań; tabela cen na stronie produktu jest renderowana lokalnie z tych przechowywanych warstw.

== Development ==

Poziomy są rozwijane na otwartej przestrzeni. Instalowane PHP, JS i CSS to te same pliki w repozytorium, nic nie jest minimalizowane ani generowane w kroku kompilacji. Przeczytaj kod, zgłoś błąd lub wyślij łatkę na https://github.com/wppoland/plogins-tiers.

== Changelog ==

= 1.0.1 =
* Pierwsza stabilna wersja.

= 0.2.4 =
* Dokumenty: dodano sekcję „Możesz też polubić” łączącą inne bezpłatne wtyczki WPPoland WooCommerce. Żadnych zmian funkcjonalnych.

= 0.2.3 =
* Nowość: Widżet Elementora dla tabeli cen hurtowych (działa na Elementorze 3.x i 4.0).

= 0.2.2 =
* Opakowanie: wyklucz katalog zasobów .wordpress-org z pobierania wtyczki (tylko zasoby SVN WordPress.org). Żadnych zmian funkcjonalnych.

= 0.2.1 =
* Zmieniono nazwę na Poziomy Plogins dla WooCommerce, aby uzyskać bardziej charakterystyczną nazwę wtyczki.

= 0.2.0 =
* Nowość: konfigurowalne rozmieszczenie tabel (podsumowanie produktu, formularz przed/po dodaniu do koszyka, meta produktu lub tylko instrukcja).
* Nowość: krótki kod `[tiers_table]` i blok „Tabela cen wolumenowych” umożliwiający umieszczenie tabeli w dowolnym miejscu.
* Nowość: opcjonalny niestandardowy nagłówek tabeli.
* Nowość: opcjonalna kolumna „Oszczędzasz” w cenniku.
* Nowość: opcjonalna notatka „Oszczędzasz” w wierszu koszyka.
* Nowość: obsługa tłumaczeń ze ścieżką domeny `/languages`, dołączonym plikiem `tiers.pot` i tłumaczeniem na język polski.
* Poprawka: zdefiniuj brakującą stałą `Tiers\PLUGIN_DIR`, aby wtyczka uruchamiała się niezawodnie.
* Sprzątanie: usunięto nieużywany szablon; rozszerzony zakres standardów kodowania na szablony i bloki.

= 0.1.0 =
* Wersja pierwsza: globalne poziomy cenowe, tabela cen renderowana przez serwer na stronach produktów, narzędzie do tworzenia warstw administracyjnych, filtr „poziomy/poziomy_produktu” dla nadpisań PRO. Bez jQuery, bez zmiany układu, WCAG 2.2 AA.
