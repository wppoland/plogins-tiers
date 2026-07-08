=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Volumenpreisstufen für WooCommerce. Lege Mengenrabattbereiche fest und zeige auf Produktseiten eine vom Server gerenderte Preistabelle an. Kein jQuery.

== Description ==

Tiers bietet einem WooCommerce-Shop eine mengenbasierte Preisgestaltung. Du legst die Haltepunkte fest, kaufen 5, sparen 5 %; Kaufe 10, spare 10 % und der Rabatt wird von der Werbebuchung abgezogen, sobald ein Käufer genügend Einheiten in den Warenkorb legt. Dieselben Haltepunkte werden als Tabelle auf der Produktseite angezeigt, sodass Benutzer den Preis sehen können, den sie zahlen würden, bevor sie sie in den Warenkorb legen.

Der Rabatt wird in PHP auf „woocommerce_before_calculate_totals“ berechnet, sodass die Preislogik kein Front-End-JavaScript liefert. Die Produktseitentabelle ist eine einfache, serverseitig gedruckte HTML-Tabelle „<table>“ mit „<th Scope>“ und einer „<caption>“, sodass sie für Screenreader korrekt gelesen wird und das Layout beim Laden der Seite nicht verschoben wird.

Wenn eine Menge mit mehr als einer Stufe übereinstimmt, gilt die niedrigste Qualifikationsstufe, bei 12 Einheiten gilt der Preis „10+“, nicht der Preis „5+“. Auch bei Tiers wird nie der Preis erhöht, sodass ein Produkt, das bereits im Angebot ist, seinen niedrigeren Preis behält.

Tiers erklärt die Kompatibilität mit WooCommerce HPOS und den Cart/Checkout Blocks. Es speichert alles in einer einzigen „wp_options“-Zeile und erstellt keine benutzerdefinierten Tabellen, sodass die Datenbank beim Löschen des Plugins unverändert bleibt.

<strong>Was du bekommen</strong>

* Beliebig viele globale Preisstufen, jeweils eine Mindestmenge und ein Rabattprozentsatz (mit optionaler Beschriftung)
* Automatischer Rabatt im Warenkorb, wobei die höchste passende Stufe gewinnt
* Eine Preistabelle auf einzelnen Produktseiten, mit der Wahl, wo sie angezeigt wird: Produktzusammenfassung, vor oder nach dem Add-to-Cart-Formular, im Produkt-Metabereich oder nirgendwo automatisch
* Ein „[tiers_table]“-Shortcode und ein „Volume Pricing Table“-Block zum manuellen Einfügen der Tabelle
* Eine optionale Überschrift über der Tabelle und eine optionale Spalte „Du sparst“.
* Ein optionaler „Du sparst“-Hinweis unter jeder rabattierten Zeile im Warenkorb
* Ein Admin-Ebenen-Builder, der Zeilen an Ort und Stelle hinzufügt und entfernt, mit einer Live-Vorschau der Anzeige jeder Ebene
* Eine polnische Übersetzung sowie eine gebündelte POT-Datei zur Übersetzung in andere Sprachen (Textdomäne „Tiers“)
* Ein „tiers_product_tiers“-Filter, der es Tiers PRO ermöglicht, die Stufen pro Produkt oder rollenbasiert auszutauschen

<strong>Dokumentation:</strong> https://plogins.com/de/tiers/docs/

= You may also like these plugins =

Weitere kostenlose WooCommerce-Plugins von WPPoland:

* [Plogins Waitlist](https://wordpress.org/plugins/plogins-waitlist/) – Warteliste für wieder verfügbare Lagerbestände, die Käufer per E-Mail benachrichtigt, sobald ein Produkt zurückkommt.
* [Sieve - Search & Filter](https://wordpress.org/plugins/sieve/) – schnelle AJAX-Produktsuche und -Filterung für WooCommerce, ohne jQuery.
* [Polski for WooCommerce](https://wordpress.org/plugins/polski/) – Einhaltung des polnischen Marktes: GPSR, Omnibus, DSGVO, Rechnungen und Storefront-Module.

Durchsuche den vollständigen Katalog unter https://plogins.com/de/ .

== Installation ==

1. Installieren und aktiviere WooCommerce (8.0 oder höher).
2. Lade den Ordner „tiers“ nach „/wp-content/plugins/“ hoch oder hol dir eine Kopie von https://github.com/wppoland/plogins-tiers.
3. Aktiviere das Plugin über den Bildschirm <strong>Plugins<strong>. 4. Gehe zu </strong>WooCommerce → Stufen</strong> und füge mindestens eine Preisstufe hinzu (z. B. 5 Einheiten → 5 % Rabatt).
5. Die Preistabelle erscheint automatisch auf den Produktseiten und im Warenkorb gelten Rabatte.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentation</strong> - https://plogins.com/de/tiers/docs/
* <strong>Plugin-Seite</strong> - https://plogins.com/de/tiers/
* <strong>Quellcode</strong> – https://github.com/wppoland/plogins-tiers
* <strong>Fehlerberichte und Funktionsanfragen</strong> – https://github.com/wppoland/plogins-tiers/issues


= Does Tiers require WooCommerce? =
Ja. Tiers ist eine WooCommerce-Erweiterung und erfordert WooCommerce 8.0 oder höher.

= Does the pricing table reload the page? =
Nein. Die Preistabelle wird vom Server gerendert und mit der Seite geladen, bevor JavaScript ausgeführt wird. Es gibt keinen AJAX- oder Hydratationsschritt.

= How does the "highest tier wins" logic work? =
Wenn ein Kunde 12 Einheiten eines Produkts in seinem Warenkorb hat und du die Stufen „5+“ (5 % Rabatt) und „10+“ (10 % Rabatt) haben, erhält er 10 % Rabatt. Die Stufen werden vom niedrigsten zum höchsten Min_Qty bewertet und das letzte Spiel gewinnt.

= Can I apply different tiers to different products? =
In der kostenlosen Version sind die Stufen global (gilt für alle Produkte). Tiers PRO fügt Produktstufenüberschreibungen über den Produktbearbeitungsbildschirm hinzu.

= Are discounts compatible with WooCommerce coupons? =
Ja. Durch Stufen wird der Einzelpostenpreis im Warenkorb geändert, bevor WooCommerce die Gesamtsummen berechnet, sodass Standard-WooCommerce-Gutscheine normalerweise zusätzlich zum Staffelpreis funktionieren.

= What happens when I deactivate the plugin? =
Rabatte werden nicht mehr angewendet und die Preistabelle wird nicht mehr angezeigt. deine Einstellungen bleiben in der Datenbank erhalten.

= What happens when I delete the plugin? =
Die Deinstallationsroutine entfernt die Option „tiers_settings“. Es werden keine benutzerdefinierten Tabellen erstellt, sodass deine Datenbank sauber bleibt.

= Does it work with taxes? =
Ja. Stufen ändern die Preise vor der Steuerberechnung von WooCommerce, sodass die eigene Steuerlogik von WooCommerce auf den reduzierten Preis angewendet wird.


= Does this plugin work on WordPress Multisite? =

Ja. Dieses Plugin ist mit WordPress Multisite kompatibel. Aktiviere es im Netzwerk oder auf einzelnen Websites. Jede Site behält ihre eigenen Einstellungen und Daten.

== Screenshots ==

1. Die Mengenpreistabelle auf einer Produktseite zeigt Mengenbereiche, Rabattprozentsätze und resultierende Preise.
2. Admin-Einstellungsseite, Ebenenersteller mit Hinzufügen/Entfernen von Zeilen und einem Umschalter zum Ein-/Ausblenden der Tabelle.

== External Services ==

Tiers stellt keine Verbindung zu externen Diensten her. Preisstufen werden in einer einzigen Zeile „tiers_settings“ in deiner WordPress-Optionstabelle gespeichert und der Rabatt wird in PHP auf deinem eigenen Server berechnet, es verlassen nie Daten deine Website. Das Plugin sendet keine E-Mails und stellt keine Remote-Anfragen; Die Preistabelle auf der Produktseite wird lokal aus diesen gespeicherten Ebenen gerendert.

== Development ==

Tiers wird im Freien entwickelt. Bei den von dir installierten PHP-, JS- und CSS-Dateien handelt es sich um dieselben Dateien im Repository. Durch einen Build-Schritt wird nichts minimiert oder generiert. Lese den Code, melde einen Fehler oder sende einen Patch unter https://github.com/wppoland/plogins-tiers.

== Changelog ==

= 1.0.1 =
* Erste stabile Version.

= 0.2.4 =
* Dokumente: Es wurde ein Abschnitt „Das könnte dir auch gefallen“ hinzugefügt, der die anderen kostenlosen WPPoland WooCommerce-Plugins verlinkt. Keine funktionalen Änderungen.

= 0.2.3 =
* Neu: Elementor-Widget für die Volumenpreistabelle (funktioniert auf Elementor 3.x und 4.0).

= 0.2.2 =
* Verpackung: Schließe das .wordpress-org-Assets-Verzeichnis vom Plugin-Download aus (nur WordPress.org-SVN-Assets). Keine funktionalen Änderungen.

= 0.2.1 =
* Für einen eindeutigeren Plugin-Namen in Plogins Tiers für WooCommerce umbenannt.

= 0.2.0 =
* Neu: Konfigurierbare Tabellenplatzierung (Produktzusammenfassung, Vorher/Nachher-Formular zum Hinzufügen zum Warenkorb, Produkt-Meta oder nur Handbuch).
* Neu: Shortcode „[tiers_table]“ und ein „Volume Pricing Table“-Block zum Platzieren der Tabelle an einer beliebigen Stelle.
* Neu: optionale benutzerdefinierte Tabellenüberschrift.
* Neu: optionale Spalte „Du sparst“ in der Preistabelle.
* Neu: optionaler pro Zeile „Du sparst“-Hinweis im Warenkorb.
* Neu: Übersetzungsunterstützung mit einem Domain-Pfad von „/Sprachen“, einem gebündelten „tiers.pot“ und einer polnischen Übersetzung.
* Fix: Definiere die fehlende Konstante „Tiers\PLUGIN_DIR“, damit das Plugin zuverlässig startet.
* Housekeeping: Eine nicht verwendete Vorlage wurde entfernt; Erweiterte Abdeckung von Codierungsstandards auf Vorlagen und Blöcke.

= 0.1.0 =
* Erstveröffentlichung: globale Volumenpreisstufen, vom Server gerenderte Preistabelle auf Produktseiten, Builder für Administratorstufen, Filter „tiers/product_tiers“ für PRO-Überschreibungen. Kein jQuery, keine Layoutverschiebung, WCAG 2.2 AA.
