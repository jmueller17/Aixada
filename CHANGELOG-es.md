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

**Mejoras y cambios**
* Al hacer pedidos:
  * Se permite a las UF hacer pedidos de productos de stock (:bulb:`config.php`:
    `$orders_allow_stock`, es de utilidad conjuntamente con `$use_shop=false`)
  * Las casillas de las cantidades se muestran en blanco en vez de a 0.
  * Cuando una cantidad se pone a 0 o en blanco se borra el producto de la cesta.
  * Se mejora la sincronización de cantidades con la cesta en las pestañas de
    proveedor, categoría y buscar.
  * Se permite comentarios en los pedidos. Nuevo tipo de producto de comentarios
    (sin precio) que al hacer un pedido requiere texto en vez de cantidades
    (para enviar comentarios sobre los pedidos ya recibidos o para dar
    instrucciones de preparación del pedido)
* En gestión de pedidos:
  * Se permite reabrir un pedido cerrado por error.
  * Se puede cancelar un pedido abierto sin enviarlo al proveedor.
  * Más formatos y mejoras de presentación para enviar o imprimir pedidos
    (detallando: por productos+UF o UF+Productos) y se añade importe total.
  * Los pedidos se envían en el formato seleccionado para el proveedor y si no
    según configuración (:bulb:`config.php`: `$email_order_format' y
    `$email_order_prices`).
  * Nueva opción para seleccionar el formato en que se desea imprimir los pedidos.
* Al revisar pedidos:
  * Permite cambiar precios y re-calcula el importe y así poder cuadrar el
    importe del albarán con el importe calculado.
  * Muestra el nombre de la UF al ponerse sobre una celda.
  * Puede configurarse en que orden aparecen las columna de las UF
    (:bulb:`config.php`: `$order_review_uf_sequence`)
  * Permite asignar productos cualquier casilla en blanco de una UF que
    no haya pedido ese producto.
  * Permite añadir cantidades de cualquier producto del proveedor o UF activos,
    ver nuevo botón "*Añadir ítem*"
    (también se puede configurar para añadir al revisar la columna de una
    determinada UF, y así por ejemplo asignar deterioros de reparto
    :bulb:`config.php`: `$revision_fixed_uf`)
  * Se puede distribuir y validar directamente un pedido (:bulb:`config.php`:
    `$order_distribution_method`, `$order_distributeValidate_invoce`)
  * Al volver a revisar un pedido ya distribuido se permite conservar
    el trabajo de revisión hecho previamente.
* En validación de cestas:
  * Es posible configurar que al validar muestre todos los carros de la semana
    (:bulb:`config.php`: `$validate_show_carts`).
  * Se pueden crear cestas vacías para asignar compra a UF sin pedido o compra
    previa (:bulb:`config.php`: `$validate_btn_create_carts`) 
  * Se solventa el problema de poner productos en cestas vacías.
* Añadir edición de la mayoría de tablas auxiliares y mejorar la existente permitiendo edición en la lista.
* Mejorar el envío de correos:  
(envío de pedido al proveedor, re-establecimiento de contraseña y incidentes)
  * Formateo html de los mensajes.
  * Soportar acentos y caracteres especiales como `ç`, `ñ` etc.
  * Permitir el uso de servidores SMTP (:bulb:`config.php`: `$email_SMTP_host`).
  * Permite evitar un uso de reply-to que generaba sospechas de SPAM en algunos
    hostings (:bulb:`config.php`: `$email_safe_replyTo = true`) 
* Mejoras en soporte de plataformas diversas, servidores Windows, MariaDB, versiones de PHP >= 5.3...
 (entre otros #184)
  * Para trabajar en hostings con pocos recursos se recomientda sequenciar ajax
    (:bulb:`config.php`: `$use_ajaxQueue`).
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
* #185 Evitar cache de GET usando hostings NGINX comentado en #156.
* #195 En algunos hostings fallaba el envío de pedidos a proveedores.
* #194 No de importaba el tipo de IVA.

