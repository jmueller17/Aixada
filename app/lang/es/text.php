<?php
// Spanish translation file for aixada 
// contribute by 
// Cristóbal Cabeza-Cáceres, Daniel Mollà
// Email: cristobal.cabeza@gmail.com, dmollaca@gmail.com
require_once(asset('app/storage/config.php'));

return array(
  'lang' => 'es',
  'es' => "Castellano",
  'charset' => "utf-8",
  'text_dir' => "ltr", // ('ltr' for left to right, 'rtl' for right to left)


/** 
 *  		Global things
 */
  'coop_name' => configuration_vars::get_instance()->coop_name,
  'currency_sign' => configuration_vars::get_instance()->currency_sign,
  'currency_desc' => "Euros",
  'please_select' => "Seleccionar...",
  'loading' => "Por favor, espere mientras se cargan los datos...",
  'search' => "Buscar",
  'id' => "id",
  'uf_short' => "UF",
  'uf_long' => "Unidad familiar",

/** 
 *  		misc
 */
  'date_from' => "de",
  'date_to' => "a",
  'mon' => "Lunes",
  'tue' => "Martes",
  'wed' => "Miércoles",
  'thu' => "Jueves",
  'fri' => "Viernes",
  'sat' => "Sabado",
  'sun' => "Domingo",


/**
 * 				selects
 */
  'sel_provider' => "Seleccionar un proveedor...",
  'sel_product' => "Seleccionar un producto...",
  'sel_user' => "Seleccionar un usuario...",
  'sel_category' => "Seleccionar una categoría de productos...",
  'sel_uf' => "Seleccionar una unidad familiar...",
  'sel_uf_or_account' => "Seleccionar unidad familiar o cuenta...",
  'sel_account' => "Seleccionar una cuenta...",
  'sel_none' => "Ninguno",

/**
 * 				tabs
 */
  'by_provider' => "Por proveedor",
  'by_category' => "Por categoría de productos",
  'special_offer' => "Acumulativo",
  'search_add' => "Buscar y añadir otro artículo",
  'validate' => "Validar",


/**
 *  			titles for header <title></title>
 */
  'global_title' => configuration_vars::get_instance()->coop_name,
  'head_ti_order' => "Pedido",
  'head_ti_shop' => "Comprar productos",
  'head_ti_reports' => "Informes", 
  'head_ti_validate' => "Validar",
  'head_ti_active_products' => "Productos activos",
  'head_ti_arrived_products' => "Productos que han llegado",
  'head_ti_active_roles' => "Roles activos",
  'head_ti_account' => "Cuentas",
  'head_ti_manage_orders' => "Gestionar los pedidos",
  'head_ti_manage_dates' => "Activar Fechas Pedidos",
  'head_ti_manage' => "Gestionar",
  'head_ti_manage_uf' => "Unidades familiares/Miembros",
  'head_ti_incidents' => "Incidentes",
  'head_ti_stats' => "Estadísticas diarias",
  'head_ti_prev_orders' => "Mis compras anteriores", 
  'head_ti_cashbox' => "Control de caja", 




/**
 *  			titles for main pages <h1></h1>
 */
  'ti_mng_activate_products' => "Las UFs podrán pedir lo siguiente PARA el día  ",
  'ti_mng_arrived_products' => "Los siguientes productos han llegado el día ",
  'ti_mng_activate_roles' => "Gestionar los roles de usuario ",
  'ti_mng_activate_users' => "Activar usuarios como ",
  'ti_mng_move_orders' => "Cambiar un pedido",
  'ti_mng_activate_preorders' => "Convertir un prepedido en pedido",
  'ti_mng_members' => "Gestionar los miembros",
  'ti_mng_ufs' => "Gestionar las unidades familiares",
  'ti_mng_dates' => "De-/activar individualmente fechas para hacer pedidos",
  'ti_mng_dates_pattern' => "Bulk de-/activar fechas para hacer pedidos",
  'ti_mng_db' => "Copia de seguridad de la base de datos", 
  'ti_order' => "Hacer el pedido para ",
  'ti_shop' => "Comprar artículos ",
  'ti_report_report' => "Resumen de pedidos para ", 
  'ti_report_account' => "Informe de todas las cuentas", 
  'ti_report_my_account' => "Informe de mi cuenta ", 
  'ti_report_preorder' => "Resumen de prepedidos", 
  'ti_report_incidents' => "Incidentes de hoy",
  'ti_incidents' => "Incidentes",
  'ti_validate' => "Validar la compra de la unidad familiar ",
  'ti_stats' => "Estadísticas totales",
  'ti_my_account' => "Configuración",
  'ti_my_account_money' => "Dinero",
  'ti_my_prev_sales' => "Mis compras anteriores",
  'ti_all_sales' => "Todas las compras de la UF",
  'ti_login_news' => "Inicio de sesión y noticias",
  'ti_timeline' => "Informe línea de tiempo",
  'ti_report_torn' => "Resumen del turno de hoy",
  'ti_mng_cashbox' => "Control de caja",

/**
 * 				roles
 */
  'Consumer' => "Consumidor",
  'Checkout' => "Caja",
  'Consumer Commission' => "Comisión de consumo",
  'Econo-Legal Commission' => "Comisión econolegal",
  'Logistic Commission' => "Comisión de logística",
  'Hacker Commission' => "Comisión de informática",
  'Fifth Column Commission' => "La quinta",
  'Producer' => "Productor",


/**
 * 				Manage Products / Roles
 */
  'mo_inact_prod' => "No podrán pedir:",
  'mo_act_prod' => "Sí podrán pedir:",
  'mo_notarr_prod' => "Productos que no han llegado:",
  'mo_arr_prod' => "Productos que sí han llegado:",
  'mo_inact_role' => "Roles inactivos",
  'mo_act_role' => "Roles activos",
  'mo_inact_user' => "Usuarios inactivos",
  'mo_act_user' => "Usuarios activos",
  'msg_no_report' => "No se han encontrado productores/productos para la fecha.",


/**
 * 				uf member manage
 */

  'search_memberuf' => "Buscar nombre",
  'browse_memberuf' => "Navegar",
  'assign_members' => "Asignar miembros",
  'login' => "Inicio de sesión",
  'create_uf' => "Nueva unidad familiar",
  'name_person' => "Nombre",
  'address' => "Dirección",
  'zip' => "CP",
  'city' => "Localidad",
  'phone1' => "Teléfono1",
  'phone2' => "Teléfono2",
  'email' => "Correo electrónico",
  'web' => "URL",
  'last_logon' => "Visto por última vez",
  'created' => "Creado el día",
  'active' => "Activo",
  'participant' => "Participante",
  'roles' => "Roles",
  'active_roles' => "Roles activos",
  'products_cared_for' => "Productos de los que soy responsable",
  'providers_cared_for' => "Proveedores de los que soy responsable",
  'notes' => "Observaciones",
  'edit_uf' => "Editar UF",
  'members_uf' => "Miembros de la unidad familiar",
  'mentor_uf' => "Unidad familiar anfitriona",
  'unassigned_members' => "Miembros no asignados",
  'edit_my_settings' => "Editar mi configuración",

  'nif' => "NIF",
  'bank_name' => "Banco o Caja",
  'bank_account' => "Cuenta bancario",
  'picture' => "Imagen",
  'offset_order_close' => "Tiempo de procesamiento",
  'iva_percent_id' => "Typo de IVA",
  'percent' => "Porcentaje",
  'adult' => "Adulto",


/**
 *  			wiz stuff
 */
  'deposit_cashbox' => "Ingresar dinero en caja",
  'widthdraw_cashbox' => "Retirar dinero de la caja",
  'current_balance' => "Saldo actual",
  'deposit_type' => "Concepto de ingreso",
  'deposit_by_uf' => "Ingreso por parte de una UF",
  'deposit_other' => "Otros ingresos...",
  'make_deposit_4HU' => "Ingreso de la UF",
  'short_desc' => "Descripción",
  'withdraw_type' => "Tipo de pago",
  'withdraw_for_provider' => "Para un proveedor",
  'withdraw_other' => "Otros tipos de pago..",
  'withdraw_provider' => "Pago del proveedor",
  'btn_make_withdrawal' => "Paga",
  'correct_balance' => "Corregir balance",
  'set_balance' => "Actualizar balance de la caja",
  'current_balance' => "Balance actual",
  'name_cash_account' => "Caja",

/**
 *				validate
 */
  'set_date' => "Definir una fecha",
  'get_cart_4_uf' => "Recuperar la compra de la unidad familiar",
  'make_deposit' => "Ingreso de la unidad familiar",
  'success_deposit' => "El ingreso se ha realizado correctamente",
  'amount' => "Cantidad",
  'comment' => "Comentario",
  'deposit_other_uf' => "Realizar un ingreso para otra unidad familiar o cuenta",
  'latest_movements' => "Últimos movimientos",
  'time' => "Hora",
  'account' => "Cuenta",
  'consum_account' => "Cuenta de Consumo",
  'operator' => "Usuario",
  'balance' => "Balance",
  'dailyStats' => "Estadísticas diarias",
  'totalIncome' => "Ingresos totales",
  'totalSpending' => "Gastos totales",
  'negativeUfs' => "Unidades familiares en negativo",
  'lastUpdate' => "Última actualización",
  'negativeStock' => "Productos con stock negativo",
  'curStock' => "Stock actual",
  'minStock' => "Stock mínimo",
  'stock' => "Stock",



/**
 *              Shop and order
 */

  'info' => "Info",
  'quantity' => "Cantidad",
  'unit' => "Unidad",
  'price' => "Precio",
  'name_item' => "Nombre",
  'revtax_abbrev' => "ImpRev",
  'cur_stock' => "Stock actual",
  'date_for_shop' => "Fecha de compra",
  'ts_validated' => "Validada",


/**
 * 		Logon Screen
 */ 
  'welcome_logon' => "Bienvenid@s a " . configuration_vars::get_instance()->coop_name . "!",
  'logon' => "Usuario",
  'pwd' => "Contraseña",
  'retype_pwd' => "Vuelve a escribir la contraseña",
  'lang' => "Idioma",
  'msg_err_incorrectLogon' => "Acceso incorrecto",
  'msg_err_noUfAssignedYet' => "Todavía no has sido asignado a ninguna UF: Por favor, pide que te den de alta.",
  'msg_reg_success' => "Te has registrado correctamente, pero tu usuario aún no se ha aprobado. Registra el resto de miembros de tu UF y contacta con un responsable para finalizar el registro.",
  'register' => "Registro",
  'required_fields' => " son campos obligatorios",
  'old_pwd' => "Contraseña antigua",


/**
 *			Navigation
 */
  'nav_home' => "Inicio",
  'nav_wiz' => "Turno",
	  'nav_wiz_arrived' => "Productos que no han llegado",
	  'nav_wiz_validate' => "Validar",
	  'nav_wiz_open' => "Abrir",
	  'nav_wiz_close' => "Cerrar",
	  'nav_wiz_torn' => "Resumen torn",
	  'nav_wiz_cashbox' => "Caja",
  'nav_shop' => "Comprar hoy",
  'nav_order' => "Siguiente pedido",
  'nav_mng' => "Gestionar",
	  'nav_mng_uf' => "Unidades familiares",
	  'nav_mng_member' => "Miembros",
	  'nav_mng_providers' => "Proveedores",
	  'nav_mng_products' => "Productos",
	  'nav_mng_deactivate' => "Activar/desactivar productos",
	  'nav_mng_stock' => "Stock",
		  'nav_mng_units' => "Unidades",
	  'nav_mng_orders' => "Pedidos",
		  'nav_mng_setorderable' => "Activar Fechas para realizar pedidos",
		  'nav_mng_move' => "Cambiar la fecha del pedido",
		  'nav_mng_orders_overview' => "Gestionar pedidos",
		  'nav_mng_preorder' => "Convertir el prepedido en pedido",
	  'nav_mng_db' => "Copia de seguridad bd",
	  'nav_mng_roles' => "Roles",
  'nav_report' => "Informes",
  'nav_report_order' => "Pedido actual",
  'nav_report_account' => "Cuentas",
  'nav_report_preorder' => "Prepedidos",
  'nav_report_timelines' => "Evolución de " . configuration_vars::get_instance()->coop_name,
  'nav_report_timelines_uf' => "Por UFs",
  'nav_report_timelines_provider' => "Por Proveedores",
  'nav_report_timelines_product' => "Por Productos",
  'nav_report_daystats' => "Estadísticas diarias",
  'nav_report_incidents' => "Incidentes de hoy",
  'nav_report_shop_hu' => "Por UF",
  'nav_report_shop_pv' => "Por proveedores",


  'nav_incidents' => "Incidentes",
	  'nav_browse' => "Navegar / añadir",
  'nav_myaccount' => "Mi cuenta",
	  'nav_myaccount_settings' => "Configuración",
	  'nav_myaccount_account' => "Dinero",
	  'nav_changepwd' => "Cambia la contraseña", 
	  'nav_prev_orders' => "Compras anteriores",

  'nav_logout' => "Salir",
  'nav_signedIn' => "Has accedido como ",
  'nav_can_checkout' => "Puedes validar compras.",
  'nav_try_to_checkout' => "Empezar a validar",
  'nav_stop_checkout' => "Dejar de validar",



/**
 *			Buttons
 */
  'btn_login' => "Iniciar la sesión",
  'btn_submit' => "Enviar",
  'btn_save' => "Guardar",
  'btn_reset' => "Borrar",
  'btn_cancel' => "Cancelar",
  'btn_activate' => "Activar",
  'btn_deactivate' => "Desactivar",
  'btn_arrived' => "Ha llegado",
  'btn_notarrived' => "No ha llegado",
  'btn_move' => "Cambiar de fecha",
  'btn_ok' => "De acuerdo",
  'btn_assign' => "Asignar",
  'btn_create' => "Crear",
  'btn_close' => "Cerrar",
  'btn_make_deposit' => "Ingresar",
  'btn_new_incident' => "Incidente nuevo",
  'btn_reset_pwd' => "Reestablecer contraseña", 
  'btn_view_cart' => "Carrito", 
  'btn_view_cart_lng' => "Ver únicamente el carrito",
  'btn_view_list' => "Productos",
  'btn_view_list_lng' => "Ver únicamente los productos",
  'btn_view_both' => "Ambos",
  'btn_view_both_lng' => "Ver tanto el carrito como los productos",
  'btn_repeat_single' => "No, uno solo", 
  'btn_repeat_all' => "Ok, aplica a todas", 
 

/**
 * Incidents
 */
  'create_incident' => "Crear un incidente",
  'overview' => "Resumen",
  'subject' => "Asunto",
  'message' => "Mensaje",
  'priority' => "Prioridad",
  'status' => "Estado",
  'incident_type' => "Tipo",
  'status_open' => "Abierto",
  'status_closed' => "Cerrado",
  'ufs_concerned' => "UFs afectadas",
  'provider_concerned' => "Para el proveedor",
  'comi_concerned' => "Para la comisión",
  'created_by' => "Creado por",
  'edit_incident' => "Editar el incidente",

/**
 *  Reports
 */
  'provider_name' => "Proveedor",
  'product_name' => "Producto",
  'qty' => "Cantidad",
  'total_qty' => "Cantidad total",
  'total_price' => "Precio total",
  'total_amount' => "Suma total",
  'select_order' => "Mostrar los pedidos para la siguiente fecha:",
  'move_success' => "Los pedidos de la lista están activos para: ",
  'show_compact' => "Mostrar la lista reducida",
  'show_all_providers' => "Expandir los productos",
  'show_all_print' => "Expandir las impresiones",
  'nr_ufs' => "UFs total",
  'printout' => "Imprimir",
  'summarized_orders' => "Resumen del pedido",
  'detailed_orders' => "Detalles del pedido",



/**
 * 		Error / Warning Messages
 */
  'msg_err_incorrectLogon' => "El usuario o la contraseña son incorrectos. Inténtalo de nuevo.",
  'msg_err_pwdctrl' => "Las contraseñas no coinciden. Escríbelas de nuevo.",
  'msg_err_usershort' => "El nombre de usuario es demasiado corto. Tiene que tener tres caracteres como mínimo.",
  'msg_err_userexists' => "El nombre de usuario ya está ocupado. Elige otro.",
  'msg_err_passshort' => "La contraseña es demasiado corta. Tiene que tener entre 4 y 15 caracteres",
  'msg_err_notempty' => " no puede estar vacío.", 
  'msg_err_namelength' => "El nombre y apelido no puede estar vació y no puede tener más de 255 characters!", 
  'msg_err_only_num' => " sólo acepta cifras y no puede estar vacío.", 
  'msg_err_email' => "El formato del correo electrónico no es correcto. Tiene que ser del estilo nombre@dominio.com o parecido.",
  'msg_err_select_uf' => "Para asignar un nuevo miembro a una UF primero tienes que seleccionar la UF clicando sobre ella. Si quieres crear una nueva UF, hazlo clicando en + Nueva UF.",
  'msg_err_select_non_member' => "Para asignar un nuevo miembro a una UF, primero tienes que seleccionarlo de la lista de no miembros que hay a la derecha.", 
  'msg_err_insufficient_stock' => "No hay suficiente stock de ",
  'msg_err_validate_self' => "¡No puedes validarte a ti mismo!",

  'msg_edit_success' => "Los datos editados se han guardado correctamente.",
  'msg_edit_mysettings_success' => "La nueva configuración se ha guardado correctamente.",
  'msg_pwd_changed_success' => "La contraseña se ha cambiado correctamente.", 
  'msg_confirm_del' => "¿Seguro que quieres eliminar a este miembro?",
  'msg_enter_deposit_amount' => "El campo de cantidad del ingreso solo acepta cifras y no puede estar vacío.",
  'msg_please_set_ufid_deposit' => "No se ha definido la ID de la UF. Tienes que elegir una compra o seleccionar otra UF para realizar el depósito.",
  'msg_error_deposit' => "Se ha producido un error al hacer el ingreso. Inténtalo de nuevo. Los ingresos que se han hecho correctamente aparecen en la lista de cuentas. <br/>El error ha sido: ",
  'msg_deposit_success' => "El depósito se ha realizado correctamente.",
  'msg_withdrawal_success' => "El pago se ha realizado correctamente.",
  'msg_select_cart_first' => "Para añadir artículos para validar, antes tienes que seleccionar una UF o una compra.",
  'msg_err_move_date' => "Se ha producido un error mientras se cambiaba la fecha del pedido. Inténtalo de nuevo.",
  'msg_no_active_products' => "En estos momentos no hay productos activos para pedir.",
  'msg_no_movements' => "No hay movimientos para la cuenta y la fecha seleccionados.", 
  'msg_delete_incident' => "¿Seguro que quieres eliminar este incidente?",
//  'msg_err_selectFirstUF' => "There is no household selected. Choose one first and then its purchases. No hay ninguna UF seleccionada.  Elige una primero y luego sus compras.", //ADDED JAN 2012

/**
 *  Product categories
 */
  'SET_ME' => "Completar...",
  'prdcat_vegies' => "Verduras",
  'prdcat_fruit' => "Fruta",
  'prdcat_mushrooms' => "Setas",
  'prdcat_dairy' => "Leche y yogures", 			//leche fresca, yougur
  'prdcat_meat' => "Carne",							//pollo, ternera, cordero, etc.
  'prdcat_bakery' => "Panadería y harina",						//pan, pastas, harina
  'prdcat_cheese' => "Queso",
  'prdcat_sausages' => "Embutidos",					//jamon, morcillas, etc.
  'prdcat_infant' => "Nutrición infantil",
  'prdcat_cereals_pasta' => "Cereales y pasta",	//cereales y pasta
  'prdcat_canned' => "Conservas",
  'prdcat_cleaning' => "Limpieza",					//limpieza del hogar, detergentes, etc.
  'prdcat_body' => "Productos corporales",
  'prdcat_seasoning' => "Aliños y algas",
  'prdcat_sweets' => "Miel y dulces",		//mermelada, miel, azﾃｺcar, chocolate
  'prdcat_drinks_alcohol' => "Bebidas alcohólicas",			//vino, cerveza, etc.
  'prdcat_drinks_soft' => "Bebidas no alcohólicas",			//zumo, bebidas vegetales
  'prdcat_drinks_hot' => "Café y té",
  'prdcat_driedstuff' => "Picoteo y frutos secos",
  'prdcat_paper' => "Celulosa y papel",		//pañuelos, papel del váter, papel de cocina 
  'prdcat_health' => "Salud",		//papel del váter, papel de cocina
  'prdcat_misc']			= "El resto...",





/**
 *  Field names in database
 */

  'name' => "Nombre",
  'contact' => "Contacto",
  'fax' => "Fax",
  'responsible_mem_name' => "Miembro responsable",
  'responsible_uf' => "Unidad familiar responsable",
  'provider' => "Proveedor",
  'description' => "Descripción",
  'barcode' => "Código de barras",
  'orderable_type' => "Tipo de producto",
  'category' => "Categoría",
  'rev_tax_type' => "Tipo de impuesto revolucionario",
  'unit_price' => "Precio por unidad",
  'iva_percent' => "Porcentaje de IVA",
  'unit_measure_order' => "Unidades para el pedido",
  'unit_measure_shop' => "Unidades para la venta",
  'stock_min' => "Cantidad mínima para tener en stock",
  'stock_actual' => "Cantidad actual en stock",
  'delta_stock' => "Diferencia con el stock mínimo",
  'description_url' => "URL de descripción",
  'msg_err_preorder' => "El pedido acumulativo tiene que ser con una fecha futura.",
  'msg_preorder_success' => "El pedido acumulativo se ha activado correctamente para la fecha:",
  'msg_can_be_ordered' => "Se puede hacer un pedido en este día",
  'msg_has_ordered_items' => "Existen pedidos para este día; no se pueden borrar, solamente mover",
  'msg_today' => "Hoy",
  'msg_default_day' => "Días sin pedidos todavía",
  'activate_for_date' => "Activar para el ",
  'start_date' => "Mostrar los registros empezando por el ",
  'date' => "Fecha",
  'iva' => "IVA",

  'Download zip' => "Bajar fichero comprimido con todos los pedidos",
  'product_singular' => "producto",
  'product_plural' => "productos",
  'confirm_db_backup' => "¿Estás seguro de hacer una copia de seguridad de toda la base de datos? Esto llevará un tiempo",
  'show_date_field' => "Pulsa aquí para mostrar el campo de calendario y seleccionar una fecha diferente de hoy",

/**
 *   Home
 */
  'purchase_current' => "Mis compras",
  'items_bought' => "Compras anteriores",
  'purchase_future' => "Mis pedidos",
  'purchase_prev' => "Compras anteriores",
  'icon_order' => "Haz tu pedido aqui",
  'icon_purchase' => "Haz tu compra ahora",
  'icon_incidents' => "Incidencias",
  'purchase_date' => "Fecha de la compra",
  'purchase_validated' => "Fecha de la validación",
  'ordered_for' => "Pedido hecho para el",
  'not_validated' => "no validado",

/* definitely new stuff */

  'download_db_zipped' => "Descargar Base de Datos comprimida",
  'backup' => "¡Ok, haz una copia de la Bade de Datos!",
  'filter_incidents' => "Filtrar incidencias",
  'todays' => "Hoy",
  'recent_ones' => "Más recientes",
  'last_year' => "Último Año",
  'details' => "Detalles",
  'actions' => "Acciones",
  'incident_details' => "Detalles de Incidencias",
  'distribution_level' => "Nivel de Distribución",
  'internal_private' => "Internal (private)",
  'internal_email_private' => "Internal + email (private)",
  'internal_post' => "Internal + post to portal (public)",
  'internal_email_post' => "Internal + email + post (public)",

  'date' => "Fecha",
  'iva' => "IVA",
  'expected' => "Esperado",
  'not_yet_sent' => "Todavía no enviado",
  'ordered_for' => "Pedido para el día",
  'my_orders' => "Mi(s) Pedido(s)",
  'my_purchases' => "Mi(s) Compra(s)",
  'loading_status_info' => "Cargando Información de estado...",
  'previous' => "Previo",
  'next' => "Siguiente",
  'date_of_purchase' => "Fecha de Compra",
  'validated' => "Validado",
  'total' => "Total",
  'ordered' => "Pedido realizado",
  'delivered' => "Entregado",
  'price' => "Precio",
  'qu' => "Qu",
  'msg_err_deactivatedUser' => "¡Tu cuenta de usuario ha sido desactivada!",
  'order' => "Pedido",
  'order_pl' => "Pedidos",
  'msg_already_validated' => "La cesta seleccionada ya ha sido validada. ¿Quieres ver sus productos/items?",
  'validated_at' => "Validado en ", //Se refiere a fecha/hora


  'nothing_to_val' => "Nada que validar para UF",
  'cart_id' => "Id de la cesta",
  'msg_several_carts' => "La UF seleccionada tiene más de una cesta pendiente de validación.  Por favor, seleccione una:",
  'transfer_type' => "Tipo",
  'todays_carts' => "Cestas de hoy",
  'head_ti_torn' => "Mirada turno", 
  'btn_validate' => "Validar",
  'desc_validate' => "Validar cestas anteriores y actuales para las UFs. Hacer depósitos de dinero.",
  'nav_wiz_revise_order' => "Revisar",
  'desc_revise' => "Revisar pedidos individuales; comprobar si los productos han llegado y ajustar las cantidades si es necesario. Distribuir el pedido en cestas de compra individuales.",
  'desc_cashbox' => "Hacer ingresos y retiros monetarios. Al inicio del primer movimiento la cuenta tiene que ser reiniciada. La cantidad de esta cuenta debe reflejar el dinero real disponible.",
  'desc_stock' => "Añadir y/o controlar el stock de productos.",
  'desc_print_orders' => "Imprimir y descargar los pedidos para la semana siguiente. Los Pedidos deben estar finalizados, impresos y descargados en un fichero zip.",
  'nav_report_status' => "Estadísticas",
  'desc_stats' => "Descargar un resumen de los movimientos actuales incluyendo las incidencias de hoy, ufs en negativo, cuenta de gastos total y productos con stock negativo.",
  'order_closed' => "El pedido está cerrado para este proveedor.",
  'head_ti_sales' => "Listado de Ventas", 
  'not_yet_val' => "todavía no validado",
  'val_by' => "Validado por",
  'purchase_details' => "Detalle de la compra de la cesta #",
  'filter_uf' => "Filtrar por UF",
  'purchase_uf' => "Compra de la UF",
  'quantity_short' => "Qu",
  'incl_iva' => "incl. IVA",
  'incl_revtax' => "incl. ImpRev",
  'no_news_today' => "¡Ninguna noticia es una buena noticia: hoy no han habido incidencias!",
  'nav_mng_iva' => "Tipos de IVA",
  'nav_mng_money' => "Dinero",
  'nav_mng_admin' => "Admin",
  'nav_mng_users' => "Usuarios",
  'nav_mng_access_rights' => "Permisos de Acceso",
  'msg_sel_account' => "Elige una cuenta primero, después filtra los resultados!",
  'msg_err_nomovements' => "Lo siento, no hay movimientos para la cuenta seleccionada y fecha. Trate de ampliar el periodo de tiempo con el botón de filtro.",
  'active_changed_uf' => "Estado activo de la UF modificado",
  'msg_err_mentoruf' => "¡La UF anfitriona debe ser diferente de ella misma!",
  'msg_err_ufexists' => "La UF ya existe. Por favor, elija otra!",
  'msg_err_form_init' => "Parece que el formulario para crear un nuevo miembro no se ha inicializado correctamente. Recargue la página y inténtelo otra vez...   ",
  'ti_mng_hu_members' => "Gestionar UFs y sus miembros", 
  'list_ufs' => "Lista de UFs",
  'search_members' => "Búsqueda de miembros",
  'member_pl' => "Miembros",
  'mng_members_uf' => "Gestionar los miembros de la UF ",
  'uf_name' => "Nombre",
  'btn_new_member' => "Nuevo miembro",
  'ti_add_member' => "Añadir nuevo miembro a la UF",
  'custom_member_ref' => "Ref. personalizada",
  'theme' => "Tema",
  'member_id' => "Id del miembro",
  'ti_mng_stock' => "Gestionar stock",
  'msg_err_no_stock' => "Aparentemente este proveedor no tiene stock",
  'msg_err_qu' => "¡La cantidad debe ser numérica y mayor que 0!",
  'msg_correct_stock' => "¡Ajustar el stock de esta forma debería ser una excepción! El stock nuevo tiene que ser siempre AÑADIDO. Está seguro de corregir el stock de este producto? Como se enteren los informáticos le van a.....",
  'btn_yes_corret' => "¡Sí, haz la modificación!",
  'ti_mng_stock' => "Gestionar stock",
  'search_product' => "Buscar un producto",
  'add_stock' => "Añadir stock",
  'click_to_edit' => "¡Clicar la celdas para editar!",
  'no_results' => "La búsqueda no ha producido resultados.",
  'for' => "para", //as in order FOR Aurora
  'date_for_order' => "Fecha de entrega",
  'finished_loading' => "Carga Finalizada",
  'msg_err_unrevised' => "¡Hay items sin revisar en este pedido. Por favor, asegúrese de que todos los productos pedidos han llegado!",
  'btn_dis_anyway' => "Distribuir igualmente",
  'btn_remaining' => "Revisar los pendientes",
  'msg_err_edit_order' => "Este pedido no está completo. Solo se pueden añadir notas y referencias cuando se haya enviado.",
  'order_open' => "El Pedido está abierto",
  'finalize_now' => "Finalizar ahora",
  'msg_err_order_filter' => "No hay pedidos coincidentes con el criterio de búsqueda.",
  'msg_finalize' => "Está a punto de terminar un pedido. Esto significa que ya no podrá hacer modificaciones.  ¿Está seguro de continuar?",
  'msg_finalize_open' => "Este pedido está todavía abierto. Finalizar-lo implica que deberá cerrarlo antes de su fecha límite. ¿Está seguro de continuar?",
  'msg_wait_tbl' => "Las cabeceras de la tabla todavía se estan creando. En función de su conexión de internet puede llevar un tiempo.  Inténtelo otra vez en 5 segundos. ",
  'msg_err_invalid_id' => "¡No se encontró ningún ID válido para el pedido! ¡¡Este pedido no ha sido enviado al proveedor!!",
  'msg_revise_revised' => "Los ítems de este pedido han sido revisados y asignados a las cestas para la fecha de venta indicada.  Revisarlos otra vez implica perder la modificaciones ya hechas y interferir en las correcciones creadas por los usuarios. <br/><br/> ¡¡¿Está totalmente seguro de continuar?!! <br/><br/> Si pulsa OK borrará los ítems de las cestas existentes y iniciará el proceso de revisión del pedido otra vez.",
  'wait_reset' => "Por favor, espere mientras el pedido se reinicia...",
  'msg_err_already_val' => "Algunos o todos los ítems ya han sido validados! ¡¡Lo siento pero no es posible hacer mas cambios!!",
  'print_several' => "There is more than one order currently selected. Do you want to print them all in one go?",
  'btn_yes_all' => "Sí, imprimir todo",
  'btn_just_one' => "No, solo uno",
  'ostat_revised' => "Revisado",
  'ostat_finalized' => "Finalizado",
  'set_ostat_arrived' => "¡Recibido!",
  'set_ostat_postpone' => "¡Postpuesto!",
  'set_ostat_cancel' => "¡Cancelado!",
  'ostat_desc_sent' => "El pedido ha sido enviado al proveedor",
  'ostat_desc_nochanges' => "Revisado y distribuido sin cambios",
  'ostat_desc_postponed' => "El pedido ha sido pospuesto",
  'ostat_desc_cancel' => "El pedido ha sido cancelado",
  'ostat_desc_changes' => "Revisado con algunas modificaciones",
  'ostat_desc_incomp' => "Pedido ignorado. Falta información anterior a la v2.5",
  'set_ostat_desc_arrived' => "La mayoría de los ítems pedidos han llegado.  Procediendo a revisar y distribuir los productos en las cestas...",
  'set_ostat_desc_postpone' => "El pedido no ha llegado en la fecha indicada pero probablemente llegará en las próximas semanas.",
  'set_ostat_desc_cancel' => "Los ítems pedidos no llegaran nunca.",
  'msg_move_to_shop' => "Los ítems han sido distribuidos en la cestas para la fecha indicada.",
  'msg_err_noselect' => "Nada seleccionado!",
  'ti_revise' => "Revisar Pedido",
  'btn_revise' => "Revisar Pedido",
  'ti_order_detail' => "Detalle del pedido para",
  'ti_mng_orders' => "Gestionar Pedidos",
  'btn_distribute' => "¡Distribuir!",
  'distribute_desc' => "Distribuir los ítems del pedido en las cestas",
  'filter_orders' => "Filtrar pedidos",
  'btn_filter' => "Filtrar",
  'filter_acc_todays' => "Movimientos de hoy",
  'filter_recent' => "Recientes",
  'filter_year' => "Último Año",
  'filter_all' => "Todos",
  'filter_expected' => "Esperados para hoy",
  'filter_next_week' => "Próxima semana",
  'filter_future' => "Todos los pedidos futuros",
  'filter_month' => "Último mes",
  'filter_postponed' => "Postpuestos",
  'with_sel' => "Con selección...",
  'dwn_zip' => "Descargar comprimido",
  'closes_days' => "Cierra en días",
  'sent_off' => "Enviado al proveedor",
  'date_for_shop' => "Fecha de compra",
  'order_total' => "Total del pedido",
  'nie' => "NIE",
  'total_orginal_order' => "Pedido original",
  'total_after_revision' => "Después de revisión",
  'delivery_ref' => "Ref. de entrega",
  'payment_ref' => "Ref. de pago",
  'arrived' => "Llegado", //as in order items have arrived. this is a table heading
  'msg_cur_status' => "El estado actual del pedido es",
  'msg_change_status' => "Cambiar el estado del pedido a alguna de las siguientes opciones",
  'msg_confirm_move' => "¿Está seguro de que quiere hacer disponible el pedido para la compra? Todos los productos asociados seran distribuidos en las cestas para la fecha:",
  'alter_date' => "Escoja una fecha alternativa",
  'msg_err_miss_info' => "Aparentemente este pedido fue creado con una versión más antigua del software que es incompatible con la funcionalidad de revisión actual.  Lo siento, este pedido no puede ser revisado.",

//added 29.09

  'order_closes' => "El pedido se cierra el", //as in: order closes 20 SEP 2012
  'left_ordering' => " pendientes de pedir.", //as in 4 days left for ordering
  'ostat_closed' => "El pedido está cerrado",
  'ostat_desc_fin_send' => "El pedido se ha cerrado y enviado al proveedor. La referencia es: #",
  'msg_err_past' => "¡Esto es el pasado! <br/> Demasiado tarde para modificar cosas.",
  'msg_err_is_deactive_p' => "Este producto está desactivado. Para abrirlo para una fecha primero debes activarlo.",
  'msg_err_deactivate_p' => "Estás a punto de desactivar el producto. Esto quiere decir que las fechas en que se puede pedir también se eliminaran.<br/><br/>También podrias desactivar las fechas en que se puede pedir clicando en las casillas correspondientes.",
  'msg_err_closing_date' => "¡La fecha de cierre no puede ser posterior a la del pedido!",
  'msg_err_sel_col' => "¡La fecha seleccionada no tiene productos para pedir! Debe establecer un producto para pedir si quiere crear una  plantilla para esta fecha.",
  'msg_err_closing' => "Para modificar la fecha de cierre, hay que poner al menos un producto que puede pedir.",
  'msg_err_deactivate_sent' => "El producto escogido no puede ser (des)activado porque el pedido correspondiente ya ha sido enviado al proveedor. No se pueden hacer mas cambios!",
  'view_opt' => "Mostrar opciones",
  'days_display' => "Número de días a mostrar",
  'plus_seven' => "Mostrar +7 días",
  'minus_seven' => "Mostrar -7 días",
  'btn_earlier' => "Antes de", //cómo más temprano
  'btn_later' => "después de", //más tarde... futuro


//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
  'pattern_intro' => "Activa los productos i días para los próximos ",
  'pattern_scale' => "meses ",
  'week' => "cada semana",
  'second' => "cada 15 dias",  //2nd 
  'third' => "cada 3 semanas",
  'fourth' => "una vez al mes",
  'msg_pattern' => "¡NOTA: Esta acción regenerará los productos y fechas a partir de esta!",
  'sel_closing_date' => "Elegir una nueva fecha de cierre",
  'btn_mod_date' => "Modificar la fecha de cierre",
  'btn_repeat' => "¡Repetir el patrón!",
  'btn_entire_row' => "(Des)activa toda la columna",
  'btn_deposit' => "Depósito",
  'btn_withdraw' => "Reintegro",
  'deposit_desc' => "Ingreso en efectivo",
  'withdraw_desc' => "Reintegro de la caja",
  'btn_set_balance' => "Establecer el balance",
  'set_bal_desc' => "Corregir el balance al principio del primer turno.",
  'maintenance_account' => "Mantenimiento",
  'posted_by' => "Creado por", //Posted by
  'ostat_yet_received' => "pendiente de recibir",
  'ostat_is_complete' => "está completo",
  'ostat_postponed' => "postpuesto",
  'ostat_canceled' => "cancelado",
  'ostat_changes' => "con cambios",
  'filter_todays' => "De hoy",
  'bill' => "Factura",
  'member' => "Miembro",
  'cif_nif' => "CIF/NIF", //CIF/NIF
  'bill_product_name' => "Artículo", //concepte en cat... 
  'bill_total' => "Total", //Total factura 
  'phone_pl' => "Teléfonos",
  'net_amount' => "Importe neto", //importe netto 
  'gross_amount' => "Importe bruto", //importe brutto
  'add_pagebreak' => "Pulsar aquí para añadir un salto de página",
  'remove_pagebreak' => "Pulsar aquí para eliminar el salto de página",


  'show_deactivated' => "Mostrar desactivados", 
  'nav_report_sales' => "Ventas", 
  'nav_help' => "Ayuda", 
  'withdraw_from' => "Retirar dinero ",  //account
  'withdraw_to_bank' => "Retirar dinero de banco",
  'withdraw_uf' => "Retirar dinero de cuenta HU",
  'withdraw_cuota' => "Retirar dinero de quota",
  'msg_err_noorder' => "No hay pedidos para la selección!",
  'primer_torn' => "Primer Turno",
  'segon_torn' => "Segundo Turno",
  'dff_qty' => "Dif. cantidad",
  'dff_price' => "Dif. precio",
  'ti_mgn_stock_mov' => "Movimientos de stock",
  'stock_acc_loss_ever' => "Perdida acumulada",
  'closed' => "cerrado",
  'preorder_item' => "Este producto forma parte de un pedido acumulativo",
  'do_preorder' => "De-/activar cómo pedido acumulativo",
  'do_deactivate_prod' => "Activar/desactivar producto",
  'msg_make_preorder_p' => "Éste pedido es acumulativo, por lo tanto todavía no tiene fecha de entrega",
  'btn_ok_go' => "OK, adelante!",
  'msg_pwd_emailed' => "La nueva constraseña se ha enviado al usuario",
  'msg_pwd_change' => "La nueva constraseña es: ",
  'msg_err_emailed' => "¡Error de envío!",
  'msg_order_emailed' => "El pedido se ha enviado correctamente!",
  'msg_err_responsible_uf' => "No hay responsable para éste producto",
  'msg_err_finalize' => "Ups...¡Error al finalizar pedido!",
  'msg_err_cart_sync' => "Tu pedido no está sincronizado con la base de datos; se ha modificado tu cesta mientras estabas comprando. Vuelve a actualizar el pedido.",
  'msg_err_no_deposit' => "La última UF no ha realizado ningún depósito?!!!",
  'btn_load_cart' => "Continua con la siguiente UF",
  'btn_deposit_now' => "Haz el ingreso ahora",
  'msg_err_stock_mv' => "De momento, no han habido movimientos de estoc para este producto!",

  'ti_report_shop_pv' => "Total ventas por proveedor",
  'filter_all_sales' => "Todas las ventas",
  'filter_exact' => "Elegir periodo",
  'total_4date' => "Total para la fecha",
  'total_4provider' => "Suma",
  'sel_sales_dates' => "Muestra ventas por proveedor y el periodo selecionado:",
  'sel_sales_dates_ti' => "Elige un periodo",
 
  'instant_repeat' => "Repetir directamente",
  'msg_confirm_delordereditems' => "Este producto ya se ha pedido este día. Estás seguro de desactivarlo? Esto  borrará el pedido de las cestas. ",
  'msg_confirm_instantr' => "Quieres repetir la misma accion para el resto de las fechas activas? ",
  'msg_err_delorerable' => "Existe un pedido para este producto y fecha. No se puede borrar.",
  'msg_pre2Order' => "Convierte el pedido acumulativo en un pedido regular. Se crea una fecha de entrega. ",

  'msg_err_modified_order' => "Someone has modified the orderable products for the current date while you were ordering. Some products that you had ordered are no longer available and will disappear from your cart after it has been reloaded.",
  'btn_confirm_del' => "Sí, eliminar!!",
  'print_new_win' => "Ventana nueva",
  'print_pdf' => "Descarga pdf",
  'msg_incident_emailed' => "El incidente se ha enviado por correo correctamente.",
  'upcoming_orders' => "Próximos pedidos",

  'msg_confirm_del_mem' => "Estas seguro de eliminar el usuario de la base de datos??",
  'btn_del' => "Eliminar",

  'btn_new_provider' => "Nuevo proveedor",
  'btn_new_product' => "Añadir producto",
  'orderable' => "Pedido directo", //product type
  'msg_err_providershort' => "El nombre del proveedor no puede quedar vacío y debe contener al menos 2 caracteres.",
  'msg_err_productshort' => "El nombre del producto no puede quedar vacío y debe contener al menos 2 caracteres.",
  'msg_err_select_responsibleuf' => "¿Quién se encarga? Hay que seleccionar un responsable.",
  'msg_err_product_category' => "Hay que seleccionar una categoría de producto.",
  'msg_err_order_unit' => "Hay que seleccionar una unidad de medida para el pedido.",
  'msg_err_shop_unit' => "Hay que seleccionar una unidad de medida para la venta.",
  'click_row_edit' => "Haz clic para editar.",
  'click_to_list' => "Haz clic para desplegar la lista de productos.",
  'head_ti_provider' => "Gestión de proveedores y productos",
  'edit' => "Editar",
  'ti_create_provider' => "Añadir proveedor",
  'ti_add_product' => "Añadir producto",
  'order_min' => "Cantidad mínima para el pedido",
  'msg_confirm_del_product' => "¿Seguro que quieres borrar este producto?",
  'msg_err_del_product' => "No se puede borrar este producto porque hay entradas que dependen de él en la base de datos. Mensaje de error: ",
  'msg_err_del_member' => "No se puede borrar este usuario porque hay referencias al mismo en la base de datos<br/> Mensaje de error: ",
  'msg_confirm_del_provider' => "¿Seguro que quieres borrar este proveedor?",
  'msg_err_del_provider' => "No se puede borrar este proveedor. Borra sus productos antes y vuelve a probar.",
  'price_net' => "Precio neto",
  'custom_product_ref' => "Id externo", 
  'btn_back_products' => "Volver a productos",

  'copy_column' => "Copiar columna",
  'paste_column' => "Pegar",

  'search_provider' => "Buscar proveidor",
  'msg_err_export' => "Error al exportar datos",
  'export_uf' => "Exportar Miembros",
  'btn_export' => "Exportar",

  'ti_visualization' => "Visualizaciones",
  'file_name' => "Nombre de archivo",
  'active_ufs' => "Sólo UF activas",
  'export_format' => "Formato de exportación",
  'google_account' => "Google account",
  'other_options' => "Otras opciones",
  'export_publish' => "Hacer público el archivo de exportación en:",
  'export_options' => "Opciones de exportación",
  'correct_stock' => "Corregir stock",
  'btn_edit_stock' => "Editar stock",
  'consult_mov_stock' => "Consultar movimientos",
  'add_stock_frase' => "Stock total = stock actual de ", //complete frase is: total stock = current stock of X units + new stock
  'correct_stock_frase' => "El stock actual no es ",
  'stock_but' => "sino", //current stock is not x units but...
  'stock_info' => "Nota: se pueden consultar todos los cambios de stock (adiciones, correcciones, pérdidas) con sólo hacer clic en el nombre del producto aquí abajo.",
  'stock_info_product' => "Nota: se pueden consultar todos los cambios de stock (adiciones, correcciones, pérdidas totales) desde la sección Informes &gt; Stock.",


  'msg_confirm_prov' => "¿Seguro que quieres exportar todos los proveedores?", 
  'msg_err_upload' => "Se ha producido un error en la carga del archivo ", 
  'msg_import_matchcol' => "Hay que hacer coincidir las entradas de la base de datos con las filas de la tabla. Debes asignar la columna que corresponde a ", //+ here then comes the name of the matching column, e.g. custom_product_ref
  'msg_import_furthercol' => "¿Qué otras columnas de la tabla quieres importar además de la columna necesaria?", 
  'msg_import_success' => "La importación ha funcionado correctamente. ¿Quieres importar otro archivo?", 
  'btn_import_another' => "Importar otro", 
  'btn_nothx' => "No, gracias", 
  'import_allowed' => "Formatos compatibles", //as in allowed file formats
  'import_file' => "Archivo de importación", 
  'public_url' => "URL pública",
  'btn_load_file' => "Cargar archivo",
  'msg_uploading' => "Se está cargando el archivo y se está generando la vista previa. Espera...",
  'msg_parsing' => "Se está leyendo el archivo del servidor y se está analizando. Espera...",
  'import_step1' => "Selección de archivo",
  'import_step2' => "Vista previa de los datos y asignacíón de columnas",
  'import_reqcol' => "Columna necesaria",
  'import_auto' => "Tengo buenas noticias: casi todos los datos (columnas) se han podido reconocer y puedes intentar la importación automática. No obstante, la opción más segura es previsualizar primero el contenido y luego realizar la asignación de columnas de la tabla manualmente.",
  'import_qnew' => "¿Qué quieres hacer con los datos que no existen en la base de datos actual?",
  'import_createnew' => "Crear entradas nuevas",
  'import_update' => "Sólo actualizar las filas existentes",
  'btn_imp_direct' => "Importar directamente",
  'btn_import' => "Importar",
  'btn_preview' => "Vista previa", 
  'sel_matchcol' => "Asignar columna...", 
  'ti_import_products' => "Importar o actualizar los productos de ", 
  'ti_import_providers' => "Importar proveedores", 
  'head_ti_import' => "Asistente de importación",

  'withdraw_desc_banc' => "Retirar dinero de la cuenta o transferir para pago a proveedores.",
  'deposit_desc_banc' => "Registrar todo el dinero entrante a la cuenta de consumo.",
  'deposit_banc' => "Depositar en la cuenta de consumo",
  'withdraw_banc' => "Retirar de la cuenta de consumo",
  'deposit_sales_cash' => "Depósito en efectivo de ventas",
  'ti_stock_report' => "Reporte de stock para ", 
  'netto_stock' => "Valor neto del stock", 
  'brutto_stock' => "Valor bruto del stock", 
  'total_netto_stock' => "Valor neto total del stock", 
  'total_brutto_stock' => "Valor bruto total del stock", 
  'sales_total_pv' => "Ventas totales del proveedor ",
  'dates_breakdown' => "Fechas de vencimiento", //decía "break down"
  'price_brutto' => "Precio bruto", 
  'total_brutto' => "Total bruto",
  'total_netto' => "Total neto",
  'msg_err_oldPwdWrong' => "Disculpe, su clave vieja no es correcta. Por vafor, intente de nuevo. ", 
  'msg_err_adminStuff' => "Privilegios de acceso insuficientes. ¡Solo un administrador puede hacer eso!",

  'msg_err_deactivate_prdrow' => "This product cannot be deactivated because it has ordered items for certain dates. Deactivate the product for those individual dates first!",
  'msg_err_deactivate_ir' => "You cannot deactivate several dates for this product since certain dates contain already ordered items. Either turn off Instant Repeat or deactivate the ordered products/date individually.",
  'msg_err_deactivate_product' => "There are open orders for this product. Deactivating it will remove these items from the corresponding order carts. Deleting order items cannot be undone.",
  'msg_activate_prod_ok' => "The product has been activated successfuly.", 
  'msg_deactivate_prod_ok' => "The product has been deactivated successfuly.", 
  'msg_activate_prov_ok' => "The provider has been activated successfuly.", 
  'msg_deactivate_prov_ok' => "The provider has been deactivated successfuly.", 
  'no_stock' => "Out of stock!!",
  'stock_mov_type' => "Movement type",

    );

?>