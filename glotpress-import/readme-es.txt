=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Niveles de precios por volumen para WooCommerce. Define bandas de descuento por cantidad y muestra una tabla de precios renderizada por el servidor en las páginas de producto. Sin jQuery.

== Description ==

Tiers da a una tienda WooCommerce precios basados en la cantidad. Tú defines los umbrales: compra 5, ahorra un 5 %; compra 10, ahorra un 10 %, y el descuento se resta de la línea de pedido en el momento en que un cliente añade suficientes unidades al carrito. Los mismos umbrales se muestran como una tabla en la página del producto para que la gente vea el precio que pagaría antes de añadirlo al carrito.

El descuento se calcula en PHP en `woocommerce_before_calculate_totals`, así que la lógica de precios no carga ningún JavaScript en el frontend. La tabla de la página de producto es una `<table>` HTML sencilla, impresa en el servidor con `<th scope>` y una `<caption>`, de modo que se lee correctamente en los lectores de pantalla y no desplaza el diseño mientras se carga la página.

Cuando una cantidad coincide con más de un nivel, se aplica el nivel más alto que cumpla los requisitos: 12 unidades toman el precio «10+», no el «5+». Tiers tampoco sube nunca un precio, así que un producto que ya está en oferta conserva su precio más bajo.

Tiers declara compatibilidad con WooCommerce HPOS y con los bloques de Carrito/Pago. Lo guarda todo en una sola fila `wp_options` y no crea tablas propias, así que borrar el plugin deja la base de datos tal y como estaba.

<strong>Lo que obtienes</strong>

* Cualquier número de niveles de precios globales, cada uno con una cantidad mínima y un porcentaje de descuento (con una etiqueta opcional)
* Descuento automático en el carrito, en el que gana el nivel más alto que coincide
* Una tabla de precios en las páginas de producto individual, con la posibilidad de elegir dónde aparece: resumen del producto, antes o después del formulario de añadir al carrito, el área meta del producto, o en ningún sitio de forma automática
* Un shortcode `[tiers_table]`, un bloque de Gutenberg «Tabla de precios por volumen» y un widget de Elementor «Tabla de precios por volumen» para colocar la tabla a mano
* Un encabezado opcional encima de la tabla y una columna opcional «Ahorras»
* Una nota opcional «Ahorras» debajo de cada línea con descuento en el carrito
* Un creador de niveles en la administración que añade y elimina filas en el sitio, con una vista previa en directo de cómo se lee cada nivel
* Una traducción al polaco, más un archivo POT incluido para traducir a otros idiomas (dominio de texto `tiers`)
* Un filtro `tiers_product_tiers` que permite a Tiers PRO sustituir por niveles por producto o por rol

<strong>Documentación:</strong> https://plogins.com/es/tiers/docs/

= You may also like these plugins =

Más plugins gratuitos de WooCommerce de WPPoland:

* [Plogins Waitlist](https://wordpress.org/plugins/plogins-waitlist/) - lista de espera de reposición que envía un correo a los clientes en el momento en que un producto vuelve a estar disponible.
* [Sieve - Search & Filter](https://wordpress.org/plugins/sieve/) - búsqueda y filtrado de productos AJAX rápidos para WooCommerce, sin jQuery.
* [Polski for WooCommerce](https://wordpress.org/plugins/polski/) - cumplimiento del mercado polaco: GPSR, Omnibus, RGPD, facturas y módulos de tienda.

Explora el catálogo completo en https://plogins.com/es/ .

== Installation ==

1. Instala y activa WooCommerce (8.0 o posterior).
2. Sube la carpeta `tiers` a `/wp-content/plugins/` u obtén una copia desde https://github.com/wppoland/plogins-tiers.
3. Activa el plugin desde la pantalla <strong>Plugins</strong>.
4. Ve a <strong>WooCommerce → Tiers</strong> y añade al menos un nivel de precios (p. ej. 5 unidades → 5 % de descuento).
5. La tabla de precios aparece automáticamente en las páginas de producto, y los descuentos se aplican en el carrito.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Documentación</strong> - https://plogins.com/es/tiers/docs/
* <strong>Página del plugin</strong> - https://plogins.com/es/tiers/
* <strong>Código fuente</strong> - https://github.com/wppoland/plogins-tiers
* <strong>Informes de errores y peticiones de funciones</strong> - https://github.com/wppoland/plogins-tiers/issues


= Does Tiers require WooCommerce? =
Sí. Tiers es una extensión de WooCommerce y requiere WooCommerce 8.0 o posterior.

= Does the pricing table reload the page? =
No. La tabla de precios se renderiza en el servidor y se carga con la página, antes de que se ejecute ningún JavaScript. No hay ningún paso de AJAX ni de hidratación.

= How does the "highest tier wins" logic work? =
Si un cliente tiene 12 unidades de un producto en su carrito y tú tienes niveles «5+» (5 % de descuento) y «10+» (10 % de descuento), recibe un 10 % de descuento. Los niveles se evalúan de menor a mayor min_qty y gana la última coincidencia.

= Can I apply different tiers to different products? =
En la versión gratuita, los niveles son globales (se aplican a todos los productos). Tiers PRO añade anulaciones de niveles por producto desde la pantalla de edición del producto.

= Are discounts compatible with WooCommerce coupons? =
Sí. Tiers modifica el precio de la línea de pedido del carrito antes de que WooCommerce calcule los totales, así que los cupones estándar de WooCommerce funcionan con normalidad sobre el precio por niveles.

= What happens when I deactivate the plugin? =
Los descuentos dejan de aplicarse y la tabla de precios ya no aparece. Tus ajustes se conservan en la base de datos.

= What happens when I delete the plugin? =
La rutina de desinstalación elimina la opción `tiers_settings`. No se crean tablas propias, así que tu base de datos queda limpia.

= Does it work with taxes? =
Sí. Tiers modifica los precios antes del cálculo de impuestos de WooCommerce, así que la propia lógica fiscal de WooCommerce se aplica al precio con descuento.


= Does this plugin work on WordPress Multisite? =

Sí. Este plugin es compatible con WordPress Multisite. Actívalo para toda la red o en sitios individuales; cada sitio conserva sus propios ajustes y datos.

== Screenshots ==

1. La tabla de precios por volumen en la página de un producto muestra rangos de cantidad, porcentajes de descuento y los precios resultantes.
2. Página de ajustes de la administración: creador de niveles con añadir/eliminar filas y un interruptor para mostrar/ocultar la tabla.

== External Services ==

Tiers no se conecta a ningún servicio externo. Los niveles de precios se guardan en una sola fila `tiers_settings` de tu tabla de opciones de WordPress, y el descuento se calcula en PHP en tu propio servidor: ningún dato sale nunca de tu sitio. El plugin no envía correos ni hace peticiones remotas; la tabla de precios de la página de producto se renderiza localmente a partir de esos niveles guardados.

== Development ==

Tiers se desarrolla de forma abierta (código abierto). El PHP, el JS y el CSS que instalas son los mismos archivos del repositorio: nada se minimiza ni se genera en un paso de compilación. Lee el código, informa de un error o envía un parche en https://github.com/wppoland/plogins-tiers.

== Translations ==

Plogins Tiers incluye traducciones al polaco, al alemán y al español para la interfaz del plugin. El dominio de texto es `tiers`, por lo que los paquetes de idioma de WordPress.org también pueden sustituir o ampliar estas traducciones incluidas.

== Changelog ==

= 1.0.2 =
* Añadidas traducciones al polaco, al alemán y al español para la interfaz del plugin.

= 1.0.1 =
* Primera versión estable.

= 0.2.4 =
* Documentación: se añadió una sección «También te puede gustar» que enlaza los otros plugins gratuitos de WooCommerce de WPPoland. Sin cambios funcionales.

= 0.2.3 =
* Nuevo: widget de Elementor para la tabla de precios por volumen (funciona en Elementor 3.x y 4.0).

= 0.2.2 =
* Empaquetado: se excluye el directorio de recursos .wordpress-org de la descarga del plugin (solo recursos SVN de WordPress.org). Sin cambios funcionales.

= 0.2.1 =
* Renombrado a Plogins Tiers para WooCommerce para lograr un nombre de plugin más distintivo.

= 0.2.0 =
* Nuevo: colocación de tabla configurable (resumen del producto, antes/después del formulario de añadir al carrito, meta del producto o solo manual).
* Nuevo: shortcode `[tiers_table]` y un bloque «Tabla de precios por volumen» para colocar la tabla en cualquier lugar.
* Nuevo: encabezado de tabla personalizado opcional.
* Nuevo: columna opcional «Ahorras» en la tabla de precios.
* Nuevo: nota opcional «Ahorras» por línea en el carrito.
* Nuevo: compatibilidad con traducciones, con una ruta de dominio de `/languages`, un `tiers.pot` incluido y una traducción al polaco.
* Corrección: se define la constante `Tiers\PLUGIN_DIR` que faltaba para que el plugin arranque de forma fiable.
* Mantenimiento: se eliminó una plantilla sin usar; se amplió la cobertura de los estándares de código a plantillas y bloques.

= 0.1.0 =
* Versión inicial: niveles de precios por volumen globales, tabla de precios renderizada por el servidor en las páginas de producto, creador de niveles en la administración, filtro `tiers/product_tiers` para anulaciones PRO. Sin jQuery, sin saltos de diseño, WCAG 2.2 AA.
