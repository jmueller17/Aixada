# Relación de cambios en Aixada

## ¿Cómo actualizar la versión?:

**¡La rama `marter` se considera estable!**  
*(ya no se sigue un sistema de versiones, **¡NO use la última `tag 2.7.0.1`!**)*

Ver en wiki: [Actualización](https://github.com/jmueller17/Aixada/wiki/Actualizaci%C3%B3n)

***

# Cambios hasta febrero-2022

## Mejoras

### Aixada ahora funciona en PHP7.4 y PHP8.1.
  * Se han solventado syntaxis PHP ahora consideradas obsoletas y se han hecho
    importantes cambios en los programas externos usados.
    * Desaparece el uso de GDrive en importación/exportación.
    * Para el correo SMTP se usa *SwiftMailer* hasta PHP7.3 y *Synfony-mailer* para PHP7.4 o
      superior.
    * Cuestiones técnicas:
      * Desaparece la opción `$development` en `config.php` y se deja de
        usar *FirePHP*.
      * La carpeta de programas externos pasa de `/php/external/` 
        a `/external/phpXX/` (donde *phpXX* es la menor versión PHP soportada)
  
### Uso de MariaDB
  * Se corrigen problemas usando la base de datos MariaDB 10.3.13
    (caso [#251](https://github.com/jmueller17/Aixada/issues/251))
    * `config.php`: Ahora en :bulb:`config.php`:`$db_host` se permite
      especificar puerto, y deja de usarse `$db_type`.
  
### Uso de `install.php`
  * Mejora la legibilidad.
  
## Corrección de errores
  * [#292](https://github.com/jmueller17/Aixada/issues/292) No se desactivaban los productos con referencia
  a nulo al importar usando una plantilla con `'deactivate_products'=>true`.
  * [#267](https://github.com/jmueller17/Aixada/issues/267) Some Dockerization improvements.
  * [#258](https://github.com/jmueller17/Aixada/issues/258) Usando PHP mail() *cc* y *bcc* se ignoraban.

***

# Cambios hasta abril-2019

## Mejoras

### Aixada funciona en PHP7.
  * Se han arreglado diversos problema, ahora Aixada funciona hasta PHP7.3.
  
### Mejoras de seguridad
  * Se ha cambiado la gestión de sesiones.
  * Ahora las sesiones inactivas más de 30 días se cierran automáticamente.
  * Se han subsanado alguno problemas de seguridad.

***

# Cambios año 2018

## Mejoras

### Hacer pedidos y compras
  *  Solo permitir productos de stock en compras (:bulb:`config.php`:
   `$use_shop='only_stock'`)
  * Al hacer pedidos aparece una lista de botones con las fechas activas (visión
    alternativa al calendario).  
    Si hay pedidos acumulativos activos el primer botón de fechas permite acceder
    a ellos (se han eliminado las pestañas de acumulativos)
  * Permitir los mismos filtros en pedidos acumulativos como para el resto
    de pedos.

### Gestión de pedidos
  * Permitir activar un pedido acumulativo para todos los productos de un proveedor.
    
### Otras mejoras
  * Instalación y actualización automática de Aixada vía `install.php`.
    
## Corrección de errores
  * Se arreglas la cancelación de pedidos acumulativos.
  * Para pedidos acumulativos se deja de mostrar los días restantes y en vez de
    la fecha 1234-01-23 se informa que és un pedido acumulativo.
  * #233 Arreglar que no se puedan cambiar productos de anotaciones si el pedido
    está cerrado.
  * #230 Arreglar algunos informes que fallaban.
  * #227 Al activar perdidos para una fecha evitar parpadeo y perder el foco del
    menú desplegado.
  * #228 Error "cut GROUP_CONCAT()" al consultar algún miembro.
  * #229 Los informes de compras y pedidos por UF no funcionaban.

***

# Cambios año 2017

## Mejoras

### Hacer pedidos y compras
  * Se permite comentarios en los pedidos. Nuevo tipo de producto de comentarios
    que en los pedidos requiere texto en vez de cantidades y no tiene precio.  
    (puede servir para enviar comentarios sobre los pedidos ya recibidos o para
    dar instrucciones de preparación del pedido al proveedor)
  * Las casillas de las cantidades se muestran en blanco en vez de a 0.
  * Cuando una cantidad se pone a 0 o en blanco se borra el producto de la cesta.
  * Se mejora la sincronización de cantidades con la cesta en las pestañas de
  proveedor, categoría y buscar.

### Gestión de pedidos
  * Se puede cancelar un pedido abierto sin enviarlo al proveedor.
  * Más formatos y mejoras de presentación para enviar o imprimir pedidos
  (detallando: por productos+UF o UF+Productos) y se añade importe total.
  * Los pedidos se envían en el formato seleccionado para el proveedor y si no
  según configuración (:bulb:`config.php`: `$email_order_format' y
  `$email_order_prices`).
  * Nueva opción para seleccionar el formato en que se desea imprimir los pedidos.

### Revisión de pedidos
  * Permite añadir cantidades de cualquier producto del proveedor o UF activos,
  ver nuevo botón "*Añadir ítem*"
  (también se puede configurar para añadir al revisar la columna de una
  determinada UF, y así por ejemplo asignar deterioros de reparto
  :bulb:`config.php`: `$revision_fixed_uf`)

### Otras mejoras
  * Para trabajar en hostings con pocos recursos se recomienda secuenciar ajax
  (:bulb:`config.php`: `$use_ajaxQueue`).

***
  
# Cambios año 2016

## Mejoras

### Envío de correos
  * Permitit el uso de servidores SMTP (:bulb:`config.php`: `$email_SMTP_host`)

## Corrección de errores
  * #205, #206 Al hacer pedidos se comprueba que el pedido no esté ya cerrado.
  * #204 Usar precio final también en pedidos acumulativos.
  * #202 Permitir catalogar más de 127 unidades.
  * #201 Control el estado del pedido en el servidor para evitar peticiones
    incoherentes.
  * #200 Evitar errores por carros de compra vacíos

***
  
# Cambios año 2015

## Nuevas funcionalidades
  * Importación productos usando platillas: permite crear los nuevos productos y
  en los existente actualizar precios y descripciones semanalmente cargando
  las hojas de calculo del proveedor. De forma opcional los producto que se
  han importado pueden quedar desactivados (:bulb:`config.php`:
  `$import_from_char_encoding`, `$import_templates[...]`)
  * Permite la personalización solo usando `config.php`: imagen inicial de login
  y en los informes el logo y los datos propios de la coope.   
  * Se puede desactivar la compra directa, permitir validarse a uno mismo y no
  requerir depósitos (`$login_header_*`, `$coop_*`, `$validate_self`,
  `$validate_deposit`, `$use_shop`)
  * Nueva gestión del dinero que permite nuevas operaciones y anulaciones.  
  En la misma pagina se muestran los cambios que se van haciendo,
  y entre ellos se puede ver el resultado resumido de la cooperativa.   
  También se pueden usar cuentas de proveedores (factura y pagos) y ver sus saldos
  (:bulb:`config.php`: `$accounts[...]` *en particular* `$accounts['use_providers']`)   
  Se permite pequeños ajustes en la presentación de importes y fechas
  (:bulb:`config.php`: `$type_formats[...]`)

## Mejoras

### Hacer pedidos y compras
  * Se permite a las UF hacer pedidos de productos de stock (:bulb:`config.php`:
  `$orders_allow_stock`)
  * Se puede desactivar la compra directa con (:bulb:`config.php`:`$use_shop=false`)

### Gestión de pedidos
  * Mejora de rendimiento al activar fechas de productos para pedidos (pruebas
    satisfactorias con más de 500)
  * Se permite reabrir un pedido cerrado por error.

### Revisar pedidos
  * Permite cambiar precios y re-calcula el importe y así poder cuadrar el
  importe del albarán con el importe calculado.
  * Muestra el nombre de la UF al ponerse sobre una celda.
  * Puede configurarse en que orden aparecen las columna de las UF
  (:bulb:`config.php`: `$order_review_uf_sequence`)
  * Permite asignar productos cualquier casilla en blanco de una UF que
  no haya pedido ese producto.
  * Se puede distribuir y validar directamente un pedido (:bulb:`config.php`:
  `$order_distribution_method`, `$order_distributeValidate_invoce`)
  * Al volver a revisar un pedido ya distribuido se permite conservar
  el trabajo de revisión hecho previamente.

### Validación de cestas
  * Es posible configurar que al validar muestre todos los carros de la semana
    (:bulb:`config.php`: `$validate_show_carts`).
  * Se pueden crear cestas vacías para asignar compra a UF sin pedido o compra
    previa (:bulb:`config.php`: `$validate_btn_create_carts`) 
  * Se solventa el problema de poner productos en cestas vacías.

### Envío de correos  
(envío de pedido al proveedor, re-establecimiento de contraseña e incidentes)

  * Formateo html de los mensajes.
  * Soportar acentos y caracteres especiales como `ç`, `ñ` etc.

### Otras mejoras
  * Mejoras en soporte de plataformas diversas, servidores Windows, MariaDB,
    versiones de PHP >= 5.3...
  * Añadir edición de la mayoría de tablas auxiliares y mejorar la existente
    permitiendo edición en la lista.
  * Desde Aixada se pueden usar un método alternativo de copia de bases de
    datos; pero si el método convencional falla la copia se realiza con el
    otro método.   
    Se puede forzar que siempre se utilice el alternativo (:bulb:`config.php`:
    `$db_backup_method`)
  * Poner un poco más de ayuda.

## Corrección de errores
  * #195 En algunos hostins fallaba el envío de pedidos a proveedores.
  * #194 No de importaba el tipo de IVA.
  * #185 Evitar cache de GET usando hostins NGINX comentado en #156.
  * #183 Arreglar caso en que no se ejecutaba auto salvar de carros de la compra.
  * #184 Evitar advertencias usando PHP-5.3
  * #134, #151 Se ha arreglado el problema en algunas instalaciones de XML cortados.

***
  
# Cambios años anteriores

Para más detalles ver: [CHANGELOG.md (en inglés)](https://github.com/jmueller17/Aixada/blob/master/CHANGELOG.md)

### Algunas mejoras
  * La página report_stock muestra en valor total del stock de productos y
    adiciones/correcciones.
  * En la página proveedor/producto ahora se puede filtrar por descripción de los productos.
  * Exportación rudimentaria de pedido a csv
  * Las revisiones de pedidos ahora no se borran de la tabla `aixada_order_to_shop`.
    Era necesario para realizar un seguimiento del total de recibido revisado
    y validado.
  * Símbolo de moneda está ahora en `config.php` y la descripción moneda en
    archivos de idiomas.
  * Evitar comprar o pedir artículos con current_stock = 0 (:bulb:`config.php`: 
    `$prevent_out_of_stock_purchase`)

### Algunas correcciones de errores
  * Formulario de edición del producto, cálculo y visualización de precio bruto.
  * #49 Proveedor: Si faltaba UF responsable no se mostraba.
  * #51 La contraseña ahora trabaja con la longitud total pero es compatible con
  versiones anteriores.
  * #52, #78 Arreglar desactivar productos.
  * #134, #151 Se ha arreglado el problema en algunas instalaciones de XML cortados.

