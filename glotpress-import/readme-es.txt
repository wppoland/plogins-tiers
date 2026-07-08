=== Plogins Tiers - Tiered Pricing for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, volume pricing, quantity discount, bulk pricing, tiered pricing
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Niveles de precios por volumen para WooCommerce. Establezca bandas de descuento por cantidad y muestre una tabla de precios renderizada por el servidor en las páginas de productos. Sin jQuery.

== Description ==

Tiers ofrece precios basados ​​en la cantidad de una tienda WooCommerce. Tú estableces los puntos de interrupción, compras 5, ahorras un 5%; compre 10, ahorre un 10 % y el descuento se descontará de la línea de pedido en el momento en que un comprador cargue suficientes unidades. Los mismos puntos de interrupción se muestran como una tabla en la página del producto para que las personas puedan ver el precio que pagarían antes de agregarlo al carrito.

El descuento se calcula en PHP en `woocommerce_before_calculate_totals`, por lo que la lógica de precios no incluye JavaScript frontal. La tabla de página de producto es una `<tabla>` HTML simple impresa en el lado del servidor con `<ésimo alcance>` y un `<título>`, por lo que se lee correctamente en los lectores de pantalla y no cambia el diseño a medida que se carga la página.

Cuando una cantidad coincide con más de un nivel, se aplica el nivel de calificación más profundo; 12 unidades toman el precio "10+", no el precio "5+". Los niveles tampoco aumentan nunca el precio, por lo que un producto que ya está en oferta mantiene su precio más bajo.

Tiers declara compatibilidad con WooCommerce HPOS y los bloques de carrito/pago. Almacena todo en una sola fila `wp_options` y no crea tablas personalizadas, por lo que eliminar el complemento deja la base de datos como estaba.

<strong>Lo que obtienes</strong>

* Cualquier número de niveles de precios globales, cada uno con una cantidad mínima y un porcentaje de descuento (con una etiqueta opcional)
* Descuento automático en el carrito, ganando el nivel más alto correspondiente
* Una tabla de precios en páginas de un solo producto, con la opción de elegir dónde aparece: resumen del producto, antes o después del formulario para añadir al carrito, metaárea del producto o en ningún lugar automático.
* Un shortcode `[tiers_table]` y un bloque "Tabla de precios por volumen" para colocar la tabla a mano
* Un encabezado opcional encima de la tabla y una columna opcional "Guardas"
* Una nota opcional "Ahorras" debajo de cada línea con descuento en el carrito
* Un generador de niveles de administración que añade y elimina filas en el lugar, con una vista previa en vivo de cómo se lee cada nivel
* Una traducción al polaco, además de un archivo POT incluido para traducir a otros idiomas (dominio de texto `tiers`)
* Un filtro `tiers_product_tiers` que permite intercambiar Tiers PRO en niveles por producto o basados en roles.

<strong>Documentación:</strong> https://plogins.com/es/tiers/docs/

= You may also like these plugins =

Más complementos gratuitos de WooCommerce de WPPoland:

* [Plogins Waitlist](https://wordpress.org/plugins/plogins-waitlist/): lista de espera de disponibilidad de existencias que envía un correo electrónico a los compradores en el momento en que regresa un producto.
* [Sieve - Search & Filter](https://wordpress.org/plugins/sieve/): búsqueda y filtrado rápido de productos AJAX para WooCommerce, sin jQuery.
* [Polski for WooCommerce](https://wordpress.org/plugins/polski/) - Cumplimiento del mercado polaco: GPSR, Omnibus, GDPR, facturas y módulos de escaparate.

Consulta el catálogo completo en https://plogins.com/es/.

== Installation ==

1. Instale y active WooCommerce (8.0 o posterior).
2. Cargue la carpeta `tiers` en `/wp-content/plugins/`, o obtenga una copia de https://github.com/wppoland/plogins-tiers.
3. Active el complemento a través de la pantalla <strong>Complementos<strong>. 4. Vaya a </strong>WooCommerce → Niveles</strong> y añade al menos un nivel de precios (por ejemplo, 5 unidades → 5 % de descuento).
5. La tabla de precios aparece automáticamente en las páginas de productos y se aplican descuentos en el carrito.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Documentación</strong> - https://plogins.com/es/tiers/docs/
* <strong>Página de complementos</strong> - https://plogins.com/es/tiers/
* <strong>Código fuente</strong> - https://github.com/wppoland/plogins-tiers
* <strong>Informes de errores y solicitudes de funciones</strong> - https://github.com/wppoland/plogins-tiers/issues


= Does Tiers require WooCommerce? =
Sí. Tiers es una extensión de WooCommerce y requiere WooCommerce 8.0 o posterior.

= Does the pricing table reload the page? =
No. La tabla de precios se procesa en el servidor, se carga con la página, antes de que se ejecute JavaScript. No hay AJAX ni paso de hidratación.

= How does the "highest tier wins" logic work? =
Si un cliente tiene 12 unidades de un producto en su carrito y tienes niveles de "5+" (5 % de descuento) y "10+" (10 % de descuento), recibirá un 10 % de descuento. Los niveles se evalúan de menor a mayor min_qty y gana el último partido.

= Can I apply different tiers to different products? =
En la versión gratuita, los niveles son globales (se aplican a todos los productos). Tiers PRO añade anulaciones de niveles por producto a través de la pantalla de edición de productos.

= Are discounts compatible with WooCommerce coupons? =
Sí. Los niveles modifican el precio de la línea de pedido del carrito antes de que WooCommerce calcule los totales, por lo que los cupones estándar de WooCommerce funcionan normalmente además del precio escalonado.

= What happens when I deactivate the plugin? =
Los descuentos dejan de aplicarse y ya no aparece la tabla de precios. Su configuración se conserva en la base de datos.

= What happens when I delete the plugin? =
La rutina de desinstalación elimina la opción `tiers_settings`. No se crean tablas personalizadas, por lo que tu base de datos queda limpia.

= Does it work with taxes? =
Sí. Los niveles modifican los precios antes del cálculo de impuestos de WooCommerce, por lo que la propia lógica fiscal de WooCommerce se aplica al precio con descuento.


= Does this plugin work on WordPress Multisite? =

Sí. Este complemento es compatible con WordPress Multisite. Activarlo en red o activarlo en sitios individuales; Cada sitio mantiene su propia configuración y datos.

== Screenshots ==

1. La tabla de precios por volumen en la página de un producto muestra rangos de cantidades, porcentajes de descuento y precios resultantes.
2. Página de configuración de administrador, generador de niveles con añadir/eliminar filas y alternar entre mostrar/ocultar tabla.

== External Services ==

Tiers no se conecta a ningún servicio externo. Los niveles de precios se almacenan en una sola fila `tiers_settings` en su tabla de opciones de WordPress, y el descuento se calcula en PHP en su propio servidor, ningún dato sale de tu sitio. El complemento no envía correos electrónicos ni realiza solicitudes remotas; la tabla de precios de la página del producto se representa localmente a partir de esos niveles almacenados.

== Development ==

Tiers se desarrolla al aire libre. El PHP, JS y CSS que instala son los mismos archivos en el repositorio, nada se minimiza ni genera en un paso de compilación. Lea el código, registre un error o envíe un parche en https://github.com/wppoland/plogins-tiers.

== Changelog ==

= 1.0.1 =
* Primera versión estable.

= 0.2.4 =
* Documentos: se agregó una sección "También te puede gustar" que vincula los otros complementos gratuitos de WPPoland WooCommerce. Sin cambios funcionales.

= 0.2.3 =
* Nuevo: widget de Elementor para la tabla de precios por volumen (funciona en Elementor 3.x y 4.0).

= 0.2.2 =
* Empaquetado: excluya el directorio de activos .wordpress-org de la descarga del complemento (solo activos SVN de WordPress.org). Sin cambios funcionales.

= 0.2.1 =
* Renombrado a Plogins Tiers para WooCommerce para obtener un nombre de complemento más distintivo.

= 0.2.0 =
* Nuevo: ubicación de tabla configurable (resumen del producto, formulario antes/después de añadir al carrito, meta del producto o manual solamente).
* Nuevo: código abreviado `[tiers_table]` y un bloque "Tabla de precios por volumen" para colocar la tabla en cualquier lugar.
* Nuevo: encabezado de tabla personalizado opcional.
* Nuevo: columna opcional "Guardas" en la tabla de precios.
* Nuevo: nota opcional "Guardas" por línea en el carrito.
* Nuevo: soporte de traducción, con una ruta de dominio de `/languages`, un `tiers.pot` incluido y una traducción al polaco.
* Solución: defina la constante `Tiers\PLUGIN_DIR` que falta para que el complemento arranque de manera confiable.
* Limpieza: se eliminó una plantilla no utilizada; Cobertura ampliada de estándares de codificación a plantillas y bloques.

= 0.1.0 =
* Lanzamiento inicial: niveles de precios por volumen global, tabla de precios representada por el servidor en páginas de productos, creador de niveles de administración, filtro `tiers/product_tiers` para anulaciones PRO. Sin jQuery, sin cambio de diseño, WCAG 2.2 AA.
