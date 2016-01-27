Changelog
=========

v1.0
----
Hecho por Gilad Buzi en 2005
 

v2.0
----
Re-lanzamiento: principales cambios en la estructura de base de datos y la interfaz que se basa ahora jQuey

v2.5
----

v2.6
----

v2.7
----
Publicado 2 de abril 2013

v2.8 (a publicar en breve)
----
Pasos para actualizar a la versión *2.8* desde *2.7* o un punto intermedio de *master*:
 1. Sobrescribir en el servidor todos los archivos (php, js, css, ...).
 2. Ejecutar desde phpMyadmin:
   * `sql/dbUpgradeTo2.8.sql` (el podrecimiento detecta el estado actual de BD y solo actualiza lo que falta)
   * `sql/setup/aixada_queries_all.sql`. 
 3. De forma optativa revisar los nuevos parámetros de configuración que puedan ser de utilidad.

**Nuevas funcionalidades**
* Importación productos usando platillas: permite crear los nuevos productos y en los existente actualizar precios y descripciones semanalmente cargando las hojas de calculo del proveedor. De forma opcional los producto que se han importado pueden quedar desactivados (:bulb:`config.php`: `$import_from_char_encoding`, `$import_templates[...]`)
* Permite la personalización solo usando `config.php`: imagen inicial de login y en los informes el logo y los datos propios de la coope.   
Ahora también se puede desactivar la compra directa, permitir validarse a uno mismo y no requerir depósitos (`$login_header_*`, `$coop_*`, `$validate_self`, `$validate_deposit`, `$use_shop`)
* Nueva gestión del dinero que permite nuevas operaciones y anulaciones.
 En la misma pagina se muestran los cambios que se van haciendo,
 y entre ellos se puede ver el resultado resumido de la cooperativa.   
 También se pueden usar cuentas de proveedores (factura y pagos) y ver sus saldos
 (:bulb:`config.php`: `$accounts[...]` *en particular* `$accounts['use_providers']`)   
 Se permite pequeños ajustes en la presentación de importes y fechas (:bulb:`config.php`: `$type_formats[...]`)

**Cambios**
* Se permite a las UF hacer pedidos de productos de stock (:bulb:`config.php`: `$orders_allow_stock`, es de utilidad conjuntamente con `$use_shop=false`)
* La revisión de pedidos permite cambiar precios y re-calcula el importe para así poder cuadrar el importe del albarán con el importe calculado con una precisión de 0,01.   
AL ponerse sobre una celda muestra también el nombre de la UF también puede configurar el orden de las UF.   
Permite asignar productos a UF que no lo han pedido pero que sí que se lo han quedado (:bulb:`config.php`: `$order_review_uf_sequence`, `$revision_fixed_uf`)
* Se puede distribuir y validar directamente un pedido (:bulb:`config.php`: `$order_distribution_method`, `$order_distributeValidate_invoce`)
* Es posible configurar que al validar muestre todos los carros de la semana en vez de solo los del día (:bulb:`config.php`: `$validate_show_carts`).
* Al validar se pueden crear cestas vacías para asignar compra a UF si pedido o compra previa (:bulb:`config.php`: `$validate_btn_create_carts`) y se solventa el problema de poner productos en cestas vacias.
* Añadir edición de la mayoría de tablas auxiliares y mejorar la existente permitiendo edición en la lista.
* Al volver a revisar un pedido que ya se ha distribuido se puede conservar el trabajo de revisión hecho previamente.
* Se permite reabrir un pedido cerrado por error.
* Mejorar el envío de correos conservando acentos y añadiendo un poco de formateo en: envío de pedido al proveedor, re-establecimiento de contraseña y incidentes. Ahora se soportar acentos y caracteres especiales como `ç`, `ñ` etc.
* Mejoras en soporte de plataformas diversas, servidores Windows, MariaDB, versiones de PHP >= 5.3...
 (entre otros #184)
* La página report_stock muestra en valor total del stock de productos y adiciones/correcciones.
* En la página proveedor/producto ahora se puede filtrar por descripción de los productos.
* Exportación rudimentaria de pedido a csv
* Las revisiones de pedidos ahora no se borran de la tabla `aixada_order_to_shop`. Era necesario para realizar un seguimiento del total de recibido revisado y validado.
* Símbolo de moneda está ahora en `config.php` y la descripción moneda en archivos lang.
* Desde Aixada se pueden usar un método alternativo de copia de bases de datos; pero si el método convencional falla la copia se realiza con el otro método.   Se puede forzar que siempre se utilice el alternativo (:bulb:`config.php`: `$db_backup_method`)
* #79 Evita que compren artículos con `current_stock = 0` (:bulb:`config.php`: `$prevent_out_of_stock_purchase`).
* Poner un poco más de ayuda.
* #193 Mejora de rendimiento al activar fechas de productos para pedidos (pruebas satisfactorias con más de 500)

**Corrección de errores**
* Formulario de edición del producto, cálculo y visualización de precio bruto.
* #49 Proveedor: Si faltaba UF responsable no se mostraba.
* #51 La contraseña ahora trabaja con la longitud total pero es compatible con versiones anteriores.
* #52, #78 Arreglar desactivar productos.
* #134, #151 Se ha arreglado el problema en algunas instalaciones de XML cortados.
* #183 Arreglar caso en que no se ejecutaba auto salvar de carros de la compra.
* #185 Evitar cache de GET usando hostins NGINX comentado en #156.
* #195 En algunos hostins fallaba el envío de pedidos a proveedores.
* #194 No de importaba el tipo de IVA.

