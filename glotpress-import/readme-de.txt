=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Volumenpreisstufen für WooCommerce. Lege Mengenrabatt-Bereiche fest und zeige auf Produktseiten eine serverseitig gerenderte Preistabelle. Kein jQuery.

== Description ==

Tiers gibt einem WooCommerce-Shop mengenbasierte Preise. Du legst die Schwellen fest: kaufe 5, spare 5 %; kaufe 10, spare 10 % – und der Rabatt wird vom Bestellposten abgezogen, sobald die Kundschaft genügend Einheiten in den Warenkorb legt. Dieselben Schwellen werden als Tabelle auf der Produktseite gezeigt, damit man den zu zahlenden Preis sieht, bevor man in den Warenkorb legt.

Der Rabatt wird in PHP im Hook `woocommerce_before_calculate_totals` berechnet, sodass die Preislogik kein Frontend-JavaScript lädt. Die Tabelle auf der Produktseite ist eine schlichte HTML-Tabelle `<table>`, serverseitig ausgegeben mit `<th scope>` und `<caption>`, sodass sie für Screenreader korrekt vorgelesen wird und das Layout beim Laden der Seite nicht verschiebt.

Passt eine Menge zu mehr als einer Stufe, greift die höchste qualifizierende Stufe – 12 Einheiten erhalten den „10+“-Preis, nicht den „5+“-Preis. Tiers erhöht außerdem nie einen Preis, sodass ein bereits reduziertes Produkt seinen niedrigeren Preis behält.

Tiers erklärt die Kompatibilität mit WooCommerce HPOS und den Cart/Checkout-Blocks. Es speichert alles in einer einzigen `wp_options`-Zeile und legt keine eigenen Tabellen an, sodass die Datenbank beim Löschen des Plugins unverändert bleibt.

<strong>Was du bekommst</strong>

* Beliebig viele globale Preisstufen, je mit einer Mindestmenge und einem Rabatt-Prozentsatz (mit optionaler Beschriftung)
* Automatischer Rabatt im Warenkorb, wobei die höchste passende Stufe gewinnt
* Eine Preistabelle auf einzelnen Produktseiten, mit Wahl des Anzeigeorts: Produktzusammenfassung, vor oder nach dem In-den-Warenkorb-Formular, im Produkt-Meta-Bereich oder nirgends automatisch
* Ein Shortcode `[tiers_table]`, ein Gutenberg-Block „Volumenpreistabelle“ und ein Elementor-Widget „Volumenpreistabelle“, um die Tabelle von Hand einzufügen
* Eine optionale Überschrift über der Tabelle und eine optionale Spalte „Du sparst“
* Ein optionaler Hinweis „Du sparst“ unter jeder rabattierten Zeile im Warenkorb
* Ein Stufen-Builder im Adminbereich, der Zeilen an Ort und Stelle hinzufügt und entfernt, mit einer Live-Vorschau, wie jede Stufe klingt
* Eine polnische Übersetzung sowie eine mitgelieferte POT-Datei zur Übersetzung in andere Sprachen (Textdomain `tiers`)
* Ein Filter `tiers_product_tiers`, mit dem Tiers PRO produkt- oder rollenbasierte Stufen einsetzen kann

<strong>Dokumentation:</strong> https://plogins.com/de/tiers/docs/

= You may also like these plugins =

Weitere kostenlose WooCommerce-Plugins von WPPoland:

* [Plogins Waitlist](https://wordpress.org/plugins/plogins-waitlist/) - Warteliste für wieder verfügbare Artikel, die der Kundschaft eine E-Mail schickt, sobald ein Produkt zurück ist.
* [Sieve - Search & Filter](https://wordpress.org/plugins/sieve/) - schnelle AJAX-Produktsuche und -Filterung für WooCommerce, ohne jQuery.
* [Polski for WooCommerce](https://wordpress.org/plugins/polski/) - Konformität für den polnischen Markt: GPSR, Omnibus, DSGVO, Rechnungen und Shop-Module.

Durchstöbere den vollständigen Katalog unter https://plogins.com/de/ .

== Installation ==

1. Installiere und aktiviere WooCommerce (8.0 oder neuer).
2. Lade den Ordner `tiers` nach `/wp-content/plugins/` hoch oder hol dir eine Kopie von https://github.com/wppoland/plogins-tiers.
3. Aktiviere das Plugin über den Bildschirm <strong>Plugins</strong>.
4. Gehe zu <strong>WooCommerce → Tiers</strong> und füge mindestens eine Preisstufe hinzu (z. B. 5 Einheiten → 5 % Rabatt).
5. Die Preistabelle erscheint automatisch auf den Produktseiten, und im Warenkorb werden die Rabatte angewendet.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentation</strong> - https://plogins.com/de/tiers/docs/
* <strong>Plugin-Seite</strong> - https://plogins.com/de/tiers/
* <strong>Quellcode</strong> - https://github.com/wppoland/plogins-tiers
* <strong>Fehlerberichte und Funktionswünsche</strong> - https://github.com/wppoland/plogins-tiers/issues


= Does Tiers require WooCommerce? =
Ja. Tiers ist eine WooCommerce-Erweiterung und erfordert WooCommerce 8.0 oder neuer.

= Does the pricing table reload the page? =
Nein. Die Preistabelle wird serverseitig gerendert und lädt mit der Seite, bevor JavaScript läuft. Es gibt keinen AJAX- oder Hydration-Schritt.

= How does the "highest tier wins" logic work? =
Hat ein Kunde 12 Einheiten eines Produkts im Warenkorb und du hast die Stufen „5+“ (5 % Rabatt) und „10+“ (10 % Rabatt), erhält er 10 % Rabatt. Die Stufen werden von der niedrigsten zur höchsten min_qty ausgewertet, und die letzte Übereinstimmung gewinnt.

= Can I apply different tiers to different products? =
In der kostenlosen Version sind die Stufen global (gelten für alle Produkte). Tiers PRO ergänzt produktspezifische Stufen-Überschreibungen über den Produktbearbeitungs-Bildschirm.

= Are discounts compatible with WooCommerce coupons? =
Ja. Tiers ändert den Preis des Warenkorb-Postens, bevor WooCommerce die Summen berechnet, sodass normale WooCommerce-Gutscheine zusätzlich zum Staffelpreis wie gewohnt funktionieren.

= What happens when I deactivate the plugin? =
Rabatte werden nicht mehr angewendet und die Preistabelle erscheint nicht mehr. Deine Einstellungen bleiben in der Datenbank erhalten.

= What happens when I delete the plugin? =
Die Deinstallationsroutine entfernt die Option `tiers_settings`. Es werden keine eigenen Tabellen angelegt, sodass deine Datenbank sauber bleibt.

= Does it work with taxes? =
Ja. Tiers ändert die Preise vor der Steuerberechnung von WooCommerce, sodass die eigene Steuerlogik von WooCommerce auf den reduzierten Preis angewendet wird.


= Does this plugin work on WordPress Multisite? =

Ja. Dieses Plugin ist mit WordPress Multisite kompatibel. Aktiviere es netzwerkweit oder auf einzelnen Websites; jede Website behält ihre eigenen Einstellungen und Daten.

== Screenshots ==

1. Volumenpreistabelle auf einer Produktseite – zeigt Mengenbereiche, Rabatt-Prozentsätze und die daraus resultierenden Preise.
2. Admin-Einstellungsseite – Stufen-Builder mit Hinzufügen/Entfernen von Zeilen und einem Schalter zum Ein-/Ausblenden der Tabelle.

== External Services ==

Tiers stellt keine Verbindung zu externen Diensten her. Die Preisstufen werden in einer einzigen `tiers_settings`-Zeile in deiner WordPress-Optionstabelle gespeichert, und der Rabatt wird in PHP auf deinem eigenen Server berechnet – es verlassen nie Daten deine Website. Das Plugin sendet keine E-Mails und stellt keine Remote-Anfragen; die Preistabelle auf der Produktseite wird lokal aus diesen gespeicherten Stufen gerendert.

== Development ==

Tiers wird quelloffen entwickelt. Die installierten PHP-, JS- und CSS-Dateien sind dieselben Dateien wie im Repository – nichts wird durch einen Build-Schritt minifiziert oder generiert. Lies den Code, melde einen Fehler oder schicke einen Patch unter https://github.com/wppoland/plogins-tiers.

== Translations ==

Plogins Tiers enthält deutsche, polnische und spanische Übersetzungen für die Plugin-Oberfläche. Die Textdomain ist `tiers`, sodass Sprachpakete von WordPress.org diese mitgelieferten Übersetzungen ebenfalls überschreiben oder erweitern können.

== Changelog ==

= 1.0.2 =
* Deutsche, polnische und spanische Übersetzungen für die Plugin-Oberfläche mitgeliefert.

= 1.0.1 =
* Erste stabile Version.

= 0.2.4 =
* Doku: Ein Abschnitt „Das könnte dir auch gefallen“ hinzugefügt, der die anderen kostenlosen WooCommerce-Plugins von WPPoland verlinkt. Keine funktionalen Änderungen.

= 0.2.3 =
* Neu: Elementor-Widget für die Volumenpreistabelle (funktioniert mit Elementor 3.x und 4.0).

= 0.2.2 =
* Verpackung: Das Verzeichnis .wordpress-org-Assets vom Plugin-Download ausschließen (nur WordPress.org-SVN-Assets). Keine funktionalen Änderungen.

= 0.2.1 =
* In Plogins Tiers für WooCommerce umbenannt, für einen unverwechselbareren Plugin-Namen.

= 0.2.0 =
* Neu: konfigurierbare Tabellenplatzierung (Produktzusammenfassung, vor/nach dem In-den-Warenkorb-Formular, Produkt-Meta oder nur manuell).
* Neu: Shortcode `[tiers_table]` und ein Block „Volumenpreistabelle“, um die Tabelle an beliebiger Stelle zu platzieren.
* Neu: optionale eigene Tabellenüberschrift.
* Neu: optionale Spalte „Du sparst“ in der Preistabelle.
* Neu: optionaler Hinweis „Du sparst“ pro Zeile im Warenkorb.
* Neu: Übersetzungsunterstützung mit einem Domain Path von `/languages`, einer mitgelieferten `tiers.pot` und einer polnischen Übersetzung.
* Fix: die fehlende Konstante `Tiers\PLUGIN_DIR` definiert, damit das Plugin zuverlässig startet.
* Aufräumen: eine ungenutzte Vorlage entfernt; die Abdeckung der Coding-Standards auf Templates und Blöcke ausgeweitet.

= 0.1.0 =
* Erste Veröffentlichung: globale Volumenpreisstufen, serverseitig gerenderte Preistabelle auf Produktseiten, Stufen-Builder im Adminbereich, Filter `tiers/product_tiers` für PRO-Überschreibungen. Kein jQuery, keine Layout-Verschiebung, WCAG 2.2 AA.
