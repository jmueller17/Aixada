<?php
// Spanish translation file for aixada 
// contribute by 
// Cristóbal Cabeza-Cáceres, Daniel Mollà
// Email: cristobal.cabeza@gmail.com, dmollaca@gmail.com

$Text['es'] = 'Castellano';
$Text['charset'] = "utf-8";
$Text['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)


/** 
 *  		Global things
 */
$Text['coop_name'] = configuration_vars::get_instance()->coop_name;
$Text['currency_sign'] = configuration_vars::get_instance()->currency_sign;
$Text['currency_desc'] = "Euros";
$Text['please_select'] = "Seleccionar...";
$Text['loading'] = "Por favor, espera mientras se cargan los datos...";
$Text['search'] = "Buscar";
$Text['id'] = "id";
$Text['uf_short'] = "Cesta";
$Text['uf_long'] = "Cesta";

/** 
 *  		misc
 */
$Text['date_from'] = 'de';
$Text['date_to'] = 'a';
$Text['mon'] = 'Lunes';
$Text['tue'] = 'Martes';
$Text['wed'] = 'Miércoles';
$Text['thu'] = 'Jueves';
$Text['fri'] = 'Viernes';
$Text['sat'] = 'Sabado';
$Text['sun'] = 'Domingo';


/**
 * 				selects
 */
$Text['sel_provider'] = "Seleccionar un proveedor...";
$Text['sel_product'] = "Seleccionar un producto...";
$Text['sel_user'] = "Seleccionar un usuario...";
$Text['sel_category'] = "Seleccionar una categoría de productos...";
$Text['sel_uf'] = "Seleccionar una cesta...";
$Text['sel_uf_or_account'] = "Seleccionar cesta o cuenta...";
$Text['sel_account'] = "Seleccionar una cuenta...";
$Text['sel_none'] = "Ninguno";

/**
 * 				tabs
 */
$Text['by_provider'] = "Por proveedor";
$Text['by_category'] = "Por categoría de productos";
$Text['special_offer'] = "Acumulativo";
$Text['search_add'] = "Buscar y añadir otro artículo";
$Text['validate'] = "Validar";


/**
 *  			titles for header <title></title>
 */
$Text['global_title'] = configuration_vars::get_instance()->coop_name;;
$Text['head_ti_order'] = "Pedido";
$Text['head_ti_shop'] = "Comprar productos";
$Text['head_ti_reports'] = "Informes"; 
$Text['head_ti_validate'] = "Validar";
$Text['head_ti_active_products'] = "Productos activos";
$Text['head_ti_arrived_products'] = "Productos que han llegado";
$Text['head_ti_active_roles'] = "Roles activos";
$Text['head_ti_account'] = "Cuentas";
$Text['head_ti_manage_orders'] = "Gestionar los pedidos";
$Text['head_ti_manage_dates'] = "Activar fechas de pedidos";
$Text['head_ti_manage'] = "Gestionar";
$Text['head_ti_manage_uf'] = "Cestas/Miembros";
$Text['head_ti_incidents'] = "Incidencias";
$Text['head_ti_stats'] = "Estadísticas diarias";
$Text['head_ti_prev_orders'] = "Mis compras anteriores"; 
$Text['head_ti_cashbox'] = "Control de caja"; 




/**
 *  			titles for main pages <h1></h1>
 */
$Text['ti_mng_activate_products'] = "Las cestas podrán pedir lo siguiente para el día ";
$Text['ti_mng_arrived_products'] = "Los siguientes productos han llegado el día ";
$Text['ti_mng_activate_roles'] = "Gestionar los roles de usuario ";
$Text['ti_mng_activate_users'] = "Activar usuarios como ";
$Text['ti_mng_move_orders'] = "Cambiar un pedido";
$Text['ti_mng_activate_preorders'] = "Convertir un prepedido en pedido";
$Text['ti_mng_members'] = "Gestionar los miembros";
$Text['ti_mng_ufs'] = "Gestionar las cestas";
$Text['ti_mng_dates'] = "Activar/desactivar individualmente fechas para hacer pedidos";
$Text['ti_mng_dates_pattern'] = "Activar/desactivar varias fechas para hacer pedidos";
$Text['ti_mng_db'] = "Copia de seguridad de la base de datos"; 
$Text['ti_order'] = "Hacer el pedido para ";
$Text['ti_shop'] = "Comprar artículos ";
$Text['ti_report_report'] = "Resumen de pedidos para "; 
$Text['ti_report_account'] = "Informe de todas las cuentas"; 
$Text['ti_report_my_account'] = "Informe de mi cuenta "; 
$Text['ti_report_preorder'] = "Resumen de prepedidos"; 
$Text['ti_report_incidents'] = "Incidencias de hoy";
$Text['ti_incidents'] = "Incidencias";
$Text['ti_validate'] = "Validar la compra de la cesta ";
$Text['ti_stats'] = "Estadísticas totales";
$Text['ti_my_account'] = "Configuración";
$Text['ti_my_account_money'] = "Monedero";
$Text['ti_my_prev_sales'] = "Mis compras anteriores";
$Text['ti_all_sales'] = "Todas las compras de la cesta";
$Text['ti_login_news'] = "Inicio de sesión y noticias";
$Text['ti_timeline'] = "Informe línea de tiempo";
$Text['ti_report_torn'] = "Resumen del turno de hoy";
$Text['ti_mng_cashbox'] = "Control de caja";

/**
 * 				roles
 */
$Text['Consumer'] = 'Consumidor';
$Text['Checkout'] = 'Caja';
$Text['Consumer Commission'] = 'Comisión de consumo';
$Text['Econo-Legal Commission'] = 'Comisión económica';
$Text['Logistic Commission'] = 'Comisión de proveedores';
$Text['Hacker Commission'] = "Comisión de informática";
$Text['Fifth Column Commission'] = 'La quinta';
$Text['Producer'] = 'Productor';


/**
 * 				Manage Products / Roles
 */
$Text['mo_inact_prod'] = "No podrán pedir:";
$Text['mo_act_prod'] = "Sí podrán pedir:";
$Text['mo_notarr_prod'] = "Productos que no han llegado:";
$Text['mo_arr_prod'] = "Productos que sí han llegado:";
$Text['mo_inact_role'] = "Roles inactivos";
$Text['mo_act_role'] = "Roles activos";
$Text['mo_inact_user'] = "Usuarios inactivos";
$Text['mo_act_user'] = "Usuarios activos";
$Text['msg_no_report'] = "No se han encontrado productores/productos para la fecha.";


/**
 * 				uf member manage
 */

$Text['search_memberuf'] = "Buscar nombre";
$Text['browse_memberuf'] = "Navegar";
$Text['assign_members'] = "Asignar miembros";
$Text['login'] = "Inicio de sesión";
$Text['create_uf'] = "Nueva cesta";
$Text['name_person'] = "Nombre";
$Text['address'] = "Dirección";
$Text['zip'] = "CP";
$Text['city'] = "Localidad";
$Text['phone1'] = "Teléfono1";
$Text['phone2'] = "Teléfono2";
$Text['email'] = "Correo electrónico";
$Text['web'] = "URL";
$Text['last_logon'] = "Visto por última vez";
$Text['created'] = "Creado el día";
$Text['active'] = "Activo";
$Text['participant'] = "Participante";
$Text['roles'] = "Roles";
$Text['active_roles'] = "Roles activos";
$Text['products_cared_for'] = "Productos de los que soy responsable";
$Text['providers_cared_for'] = "Proveedores de los que soy responsable";
$Text['notes'] = "Observaciones";
$Text['edit_uf'] = "Editar cesta";
$Text['members_uf'] = "Miembros de la cesta";
$Text['mentor_uf'] = "Cesta anfitriona";
$Text['unassigned_members'] = "Miembros no asignados";
$Text['edit_my_settings'] = "Editar mi configuración";

$Text['nif'] = "NIF";
$Text['bank_name'] = "Banco o Caja";
$Text['bank_account'] = "Cuenta bancaria";
$Text['picture']  = "Imagen";
$Text['offset_order_close'] = "Tiempo de procesamiento";
$Text['iva_percent_id'] = "Tipo de IVA";
$Text['percent'] = "Porcentaje";
$Text['adult'] = "Adulto";


/**
 *  			wiz stuff
 */
$Text['deposit_cashbox'] = 'Ingresar dinero en caja';
$Text['widthdraw_cashbox'] = 'Retirar dinero de la caja';
$Text['current_balance'] = 'Saldo actual';
$Text['deposit_type'] = 'Concepto de ingreso';
$Text['deposit_by_uf'] = 'Ingreso por parte de una cesta';
$Text['deposit_other'] = 'Otros ingresos...';
$Text['make_deposit_4HU'] = 'Ingreso de la cesta';
$Text['short_desc'] = 'Descripción';
$Text['withdraw_type'] = 'Tipo de pago';
$Text['withdraw_for_provider'] = 'Para un proveedor';
$Text['withdraw_other'] = 'Otros tipos de pago..';
$Text['withdraw_provider'] = 'Pago del proveedor';
$Text['btn_make_withdrawal'] = 'Paga';
$Text['correct_balance'] = 'Corregir balance';
$Text['set_balance'] = 'Actualizar balance de la caja';
$Text['current_balance'] = 'Balance actual';
$Text['name_cash_account'] = 'Caja';

/**
 *				validate
 */
$Text['set_date'] = "Definir una fecha";
$Text['get_cart_4_uf'] = "Recuperar la compra de la cesta";
$Text['make_deposit'] = "Ingreso de la cesta";
$Text['success_deposit'] = "El ingreso se ha realizado correctamente";
$Text['amount'] = "Cantidad";
$Text['comment'] = "Comentario";
$Text['deposit_other_uf'] = "Realizar un ingreso para otra cesta o cuenta";
$Text['latest_movements'] = "Últimos movimientos";
$Text['time'] = "Hora";
$Text['account'] = "Cuenta";
$Text['consum_account'] = "Cuenta de consumo";
$Text['operator'] = "Usuario";
$Text['balance'] = "Balance";
$Text['dailyStats'] = "Estadísticas diarias";
$Text['totalIncome'] = "Ingresos totales";
$Text['totalSpending'] = "Gastos totales";
$Text['negativeUfs']= "Unidades familiares en negativo";
$Text['lastUpdate']= "Última actualización";
$Text['negativeStock']="Productos con stock negativo";
$Text['curStock'] = "Stock actual";
$Text['minStock'] = "Stock mínimo";
$Text['stock'] = "Stock";



/**
 *              Shop and order
 */

$Text['info'] = "Info";
$Text['quantity'] = "Cantidad";
$Text['unit'] = "Unidad";
$Text['price'] = "Precio";
$Text['name_item'] = "Nombre";
$Text['revtax_abbrev'] = "ImpRev";
$Text['cur_stock'] = "Stock actual";
$Text['date_for_shop'] = 'Fecha de compra';
$Text['ts_validated'] = 'Validada';


/**
 * 		Logon Screen
 */ 
$Text['welcome_logon'] = "Bienvenid@s a " . configuration_vars::get_instance()->coop_name . "!";
$Text['logon'] = "Usuario";
$Text['pwd']	= "Contraseña";
$Text['retype_pwd']	= "Vuelve a escribir la contraseña";
$Text['lang']	= "Idioma";
$Text['msg_err_incorrectLogon'] = "Acceso incorrecto";
$Text['msg_err_noUfAssignedYet'] = "Todavía no has sido asignado a ninguna cesta. Por favor, pide que te den de alta.";
$Text['msg_reg_success'] = "Te has registrado correctamente, pero tu usuario aún no se ha aprobado. Registra el resto de miembros de tu cesta y contacta con un responsable para finalizar el registro.";
$Text['register'] = "Registro";
$Text['required_fields'] = " son campos obligatorios";
$Text['old_pwd'] = "Contraseña antigua";


/**
 *			Navigation
 */
$Text['nav_home'] = "Inicio";
$Text['nav_wiz'] = "Turno";
	$Text['nav_wiz_arrived'] = "Productos que no han llegado";
	$Text['nav_wiz_validate'] = "Validar";
	$Text['nav_wiz_open'] = "Abrir";
	$Text['nav_wiz_close'] = "Cerrar";
	$Text['nav_wiz_torn'] = "Resumen turno";
	$Text['nav_wiz_cashbox'] = "Caja";
$Text['nav_shop'] = "Comprar hoy";
$Text['nav_order'] = "Siguiente pedido";
$Text['nav_mng'] = "Gestionar";
	$Text['nav_mng_uf'] = "Cestas";
	$Text['nav_mng_member'] = "Miembros";
	$Text['nav_mng_providers'] = "Proveedores";
	$Text['nav_mng_products'] = "Productos";
	$Text['nav_mng_deactivate'] = "Activar/desactivar productos";
	$Text['nav_mng_stock'] = "Stock";
		$Text['nav_mng_units'] = "Unidades";
	$Text['nav_mng_orders'] = "Pedidos";
		$Text['nav_mng_setorderable'] = "Activar fechas para realizar pedidos";
		$Text['nav_mng_move'] = "Cambiar la fecha del pedido";
		$Text['nav_mng_orders_overview'] = "Gestionar pedidos";
		$Text['nav_mng_preorder'] = "Convertir el prepedido en pedido";
	$Text['nav_mng_db'] = "Copia de seguridad bd";
	$Text['nav_mng_roles'] = "Roles";
$Text['nav_report'] = "Informes";
$Text['nav_report_order'] = "Pedido actual";
$Text['nav_report_account'] = "Cuentas";
$Text['nav_report_preorder'] = "Prepedidos";
$Text['nav_report_timelines'] = "Evolución de " . configuration_vars::get_instance()->coop_name;;
$Text['nav_report_timelines_uf'] = "Por cestas";
$Text['nav_report_timelines_provider'] = "Por Proveedores";
$Text['nav_report_timelines_product'] = "Por Productos";
$Text['nav_report_daystats'] = "Estadísticas diarias";
$Text['nav_report_incidents'] = "Incidencias de hoy";
$Text['nav_report_shop_hu'] = "Por cesta";
$Text['nav_report_shop_pv'] = "Por proveedores";


$Text['nav_incidents'] = "Incidencias";
	$Text['nav_browse'] = "Navegar / añadir";
$Text['nav_myaccount'] = "Mi cuenta";
	$Text['nav_myaccount_settings'] = "Configuración";
	$Text['nav_myaccount_account'] = "Monedero";
	$Text['nav_changepwd'] = "Cambia la contraseña"; 
	$Text['nav_prev_orders'] = "Compras anteriores";

$Text['nav_logout'] = "Salir";
$Text['nav_signedIn'] = "Has accedido como ";
$Text['nav_can_checkout'] = "Puedes validar compras.";
$Text['nav_try_to_checkout'] = "Empezar a validar";
$Text['nav_stop_checkout'] = "Dejar de validar";



/**
 *			Buttons
 */
$Text['btn_login'] = "Iniciar la sesión";
$Text['btn_submit'] = "Enviar";
$Text['btn_save'] = "Guardar";
$Text['btn_reset'] = "Borrar";
$Text['btn_cancel'] = "Cancelar";
$Text['btn_activate'] = "Activar";
$Text['btn_deactivate'] = "Desactivar";
$Text['btn_arrived'] = "Ha llegado";
$Text['btn_notarrived'] = "No ha llegado";
$Text['btn_move'] = "Cambiar de fecha";
$Text['btn_ok'] = "De acuerdo";
$Text['btn_assign'] = "Asignar";
$Text['btn_create'] = "Crear";
$Text['btn_close'] = "Cerrar";
$Text['btn_make_deposit'] = "Ingresar";
$Text['btn_new_incident'] = "Incidencia nueva";
$Text['btn_reset_pwd'] = "Reestablecer contraseña"; 
$Text['btn_view_cart'] = "Carrito"; 
$Text['btn_view_cart_lng'] = "Ver únicamente el carrito";
$Text['btn_view_list'] = "Productos";
$Text['btn_view_list_lng'] = "Ver únicamente los productos";
$Text['btn_view_both'] = "Ambos";
$Text['btn_view_both_lng'] = "Ver tanto el carrito como los productos";
$Text['btn_repeat_single'] = "No, uno solo"; 
$Text['btn_repeat_all'] = "Ok, aplica a todas"; 
 

/**
 * Incidents
 */
$Text['create_incident'] = "Crear una incidencia";
$Text['overview'] = "Resumen";
$Text['subject'] = "Asunto";
$Text['message'] = "Mensaje";
$Text['priority'] = "Prioridad";
$Text['status'] = "Estado";
$Text['incident_type'] = "Tipo";
$Text['status_open'] = "Abierto";
$Text['status_closed'] = "Cerrado";
$Text['ufs_concerned'] = "Cestas afectadas";
$Text['provider_concerned'] = "Para el proveedor";
$Text['comi_concerned'] = "Para la comisión";
$Text['created_by'] = "Creado por";
$Text['edit_incident'] = "Editar la incidencia";

/**
 *  Reports
 */
$Text['provider_name'] = "Proveedor";
$Text['product_name'] = "Producto";
$Text['qty'] = "Cantidad";
$Text['total_qty'] = "Cantidad total";
$Text['total_price'] = "Precio total";
$Text['total_amount'] = "Suma total";
$Text['select_order'] = "Mostrar los pedidos para la siguiente fecha:";
$Text['move_success'] = "Los pedidos de la lista están activos para: ";
$Text['show_compact'] = "Mostrar la lista reducida";
$Text['show_all_providers'] = "Expandir los productos";
$Text['show_all_print'] = "Expandir las impresiones";
$Text['nr_ufs'] = "Cestas total";
$Text['printout'] = "Imprimir";
$Text['summarized_orders'] = "Resumen del pedido";
$Text['detailed_orders'] = "Detalles del pedido";



/**
 * 		Error / Warning Messages
 */
$Text['msg_err_incorrectLogon'] = "El usuario o la contraseña son incorrectos. Inténtalo de nuevo.";
$Text['msg_err_pwdctrl'] = "Las contraseñas no coinciden. Escríbelas de nuevo.";
$Text['msg_err_usershort'] = "El nombre de usuario es demasiado corto. Tiene que tener tres caracteres como mínimo.";
$Text['msg_err_userexists'] = "El nombre de usuario ya está ocupado. Elige otro.";
$Text['msg_err_passshort'] = "La contraseña es demasiado corta. Tiene que tener entre 4 y 15 caracteres";
$Text['msg_err_notempty'] = " no puede estar vacío."; 
$Text['msg_err_namelength'] = "El nombre y apellidos no pueden estar vacíos y no pueden tener más de 255 caracteres."; 
$Text['msg_err_only_num'] = " sólo acepta cifras y no puede estar vacío."; 
$Text['msg_err_email'] = "El formato del correo electrónico no es correcto. Tiene que ser del estilo nombre@dominio.com o parecido.";
$Text['msg_err_select_uf'] = "Para asignar un nuevo miembro a una cesta primero tienes que seleccionar la cesta clicando sobre ella. Si quieres crear una nueva cesta, hazlo clicando en + Nueva cesta.";
$Text['msg_err_select_non_member'] = "Para asignar un nuevo miembro a una cesta, primero tienes que seleccionarlo de la lista de no miembros que hay a la derecha."; 
$Text['msg_err_insufficient_stock'] = 'No hay suficiente stock de ';
$Text['msg_err_validate_self'] = '¡No puedes validarte a ti mismo!';

$Text['msg_edit_success'] = "Los datos editados se han guardado correctamente.";
$Text['msg_edit_mysettings_success'] = "La nueva configuración se ha guardado correctamente.";
$Text['msg_pwd_changed_success'] = "La contraseña se ha cambiado correctamente."; 
$Text['msg_confirm_del'] = "¿Seguro que quieres eliminar a este miembro?";
$Text['msg_enter_deposit_amount'] = "El campo de cantidad del ingreso solo acepta cifras y no puede estar vacío.";
$Text['msg_please_set_ufid_deposit'] = "No se ha definido la ID de la cesta. Tienes que elegir una compra o seleccionar otra cesta para realizar el depósito.";
$Text['msg_error_deposit'] = "Se ha producido un error al hacer el ingreso. Inténtalo de nuevo. Los ingresos que se han hecho correctamente aparecen en la lista de cuentas. <br/>El error ha sido: ";
$Text['msg_deposit_success'] = "El depósito se ha realizado correctamente.";
$Text['msg_withdrawal_success'] = "El pago se ha realizado correctamente.";
$Text['msg_select_cart_first'] = "Para añadir artículos para validar, antes tienes que seleccionar una cesta o una compra.";
$Text['msg_err_move_date'] = "Se ha producido un error mientras se cambiaba la fecha del pedido. Inténtalo de nuevo.";
$Text['msg_no_active_products'] = "En estos momentos no hay productos activos para pedir.";
$Text['msg_no_movements'] = "No hay movimientos para la cuenta y la fecha seleccionados."; 
$Text['msg_delete_incident'] = "¿Seguro que quieres eliminar esta incidencia?";
//$Text['msg_err_selectFirstUF'] = "No has seleccionado una cesta. Selecciona una y después, la compra. No hay ninguna cesta seleccionada. Elige una primero y luego sus compras."; //ADDED JAN 2012

/**
 *  Product categories
 */
$Text['SET_ME'] 			= 'Completar...';
$Text['prdcat_vegies']		 	= "Verduras";
$Text['prdcat_fruit'] 			= "Fruta";
$Text['prdcat_mushrooms'] 		= "Setas";
$Text['prdcat_dairy'] 			= "Leche y yogures"; 			//leche fresca, yougur
$Text['prdcat_meat'] 			= "Carne";							//pollo, ternera, cordero, etc.
$Text['prdcat_bakery'] 			= "Panadería y harina";						//pan, pastas, harina
$Text['prdcat_cheese'] 			= "Queso";
$Text['prdcat_sausages'] 		= "Embutidos";					//jamon, morcillas, etc.
$Text['prdcat_infant'] 			= "Nutrición infantil";
$Text['prdcat_cereals_pasta']	= "Cereales y pasta";	//cereales y pasta
$Text['prdcat_canned'] 			= "Conservas";
$Text['prdcat_cleaning'] 		= "Limpieza";					//limpieza del hogar, detergentes, etc.
$Text['prdcat_body'] 			= "Productos corporales";
$Text['prdcat_seasoning'] 		= "Aliños y algas";
$Text['prdcat_sweets'] 			= "Miel y dulces";		//mermelada, miel, azﾃｺcar, chocolate
$Text['prdcat_drinks_alcohol'] 	= "Bebidas alcohólicas";			//vino, cerveza, etc.
$Text['prdcat_drinks_soft'] 	= "Bebidas no alcohólicas";			//zumo, bebidas vegetales
$Text['prdcat_drinks_hot'] 		= "Café y té";
$Text['prdcat_driedstuff'] 		= "Picoteo y frutos secos";
$Text['prdcat_paper'] 			= "Celulosa y papel";		//pañuelos, papel del váter, papel de cocina 
$Text['prdcat_health'] 			= "Salud";		//papel del váter, papel de cocina
$Text['prdcat_misc']			= "El resto..." ;





/**
 *  Field names in database
 */

$Text['name'] = 'Nombre';
$Text['contact'] = 'Contacto';
$Text['fax'] = 'Fax';
$Text['responsible_mem_name'] = 'Miembro responsable';
$Text['responsible_uf'] = 'Cesta responsable';
$Text['provider'] = 'Proveedor';
$Text['description'] = 'Descripción';
$Text['barcode'] = 'Código de barras';
$Text['orderable_type'] = 'Tipo de producto';
$Text['category'] = 'Categoría';
$Text['rev_tax_type'] = 'Tipo de impuesto revolucionario';
$Text['unit_price'] = 'Precio por unidad';
$Text['iva_percent'] = 'Porcentaje de IVA';
$Text['unit_measure_order'] = 'Unidades para el pedido';
$Text['unit_measure_shop'] = 'Unidades para la venta';
$Text['stock_min'] = 'Cantidad mínima para tener en stock';
$Text['stock_actual'] = 'Cantidad actual en stock';
$Text['delta_stock'] = "Diferencia con el stock mínimo";
$Text['description_url'] = 'URL de descripción';
$Text['msg_err_preorder'] = 'El pedido acumulativo tiene que ser con una fecha futura.';
$Text['msg_preorder_success'] = 'El pedido acumulativo se ha activado correctamente para la fecha:';
$Text['msg_can_be_ordered'] =  'Se puede hacer un pedido en este día';
$Text['msg_has_ordered_items'] = 'Existen pedidos para este día; no se pueden borrar, solamente mover';
$Text['msg_today'] = 'Hoy';
$Text['msg_default_day'] = 'Días sin pedidos todavía';
$Text['activate_for_date'] = 'Activar para el ';
$Text['start_date'] = 'Mostrar los registros empezando por el ';
$Text['date'] = 'Fecha';
$Text['iva'] = 'IVA';

$Text['Download zip'] = 'Bajar fichero comprimido con todos los pedidos';
$Text['product_singular'] = 'producto';
$Text['product_plural'] = 'productos';
$Text['confirm_db_backup'] = '¿Estás seguro de hacer una copia de seguridad de toda la base de datos? Esto llevará un tiempo';
$Text['show_date_field'] = "Pulsa aquí para mostrar el campo de calendario y seleccionar una fecha diferente de hoy";

/**
 *   Home
 */
$Text['purchase_current'] = 'Mis compras';
$Text['items_bought'] = 'Compras anteriores';
$Text['purchase_future'] = 'Mis pedidos';
$Text['purchase_prev'] = 'Compras anteriores';
$Text['icon_order'] = 'Haz tu pedido aqui';
$Text['icon_purchase'] = 'Haz tu compra ahora';
$Text['icon_incidents'] = 'Incidencias';
$Text['purchase_date'] = 'Fecha de la compra';
$Text['purchase_validated'] = 'Fecha de la validación';
$Text['ordered_for'] = 'Pedido hecho para el';
$Text['not_validated'] = 'no validado';

/* definitely new stuff */

$Text['download_db_zipped'] = 'Descargar Base de Datos comprimida';
$Text['backup'] = '¡Ok, haz una copia de la Bade de Datos!';
$Text['filter_incidents'] = 'Filtrar incidencias';
$Text['todays'] = "Hoy";
$Text['recent_ones'] = 'Más recientes';
$Text['last_year'] = 'Último Año';
$Text['details'] = 'Detalles';
$Text['actions'] = 'Acciones';
$Text['incident_details'] = 'Detalles de Incidencias';
$Text['distribution_level'] = 'Nivel de Distribución';
$Text['internal_private'] = 'Internal (private)';
$Text['internal_email_private'] = 'Internal + email (private)';
$Text['internal_post'] = 'Internal + post to portal (public)';
$Text['internal_email_post'] = 'Internal + email + post (public)';

$Text['date'] = "Fecha";
$Text['iva'] = "IVA";
$Text['expected'] = 'Esperado';
$Text['not_yet_sent'] = 'Todavía no enviado';
$Text['ordered_for'] = 'Pedido para el día';
$Text['my_orders'] = 'Mi(s) Pedido(s)';
$Text['my_purchases'] = 'Mi(s) Compra(s)';
$Text['loading_status_info'] = 'Cargando Información de estado...';
$Text['previous'] = 'Previo';
$Text['next'] = 'Siguiente';
$Text['date_of_purchase'] = 'Fecha de Compra';
$Text['validated'] = 'Validado';
$Text['total'] = 'Total';
$Text['ordered'] = 'Pedido realizado';
$Text['delivered'] = 'Entregado';
$Text['price'] = 'Precio';
$Text['qu'] = 'Qu';
$Text['msg_err_deactivatedUser'] = "¡Tu cuenta de usuario ha sido desactivada!";
$Text['order'] = 'Pedido';
$Text['order_pl'] = 'Pedidos';
$Text['msg_already_validated'] = 'La cesta seleccionada ya ha sido validada. ¿Quieres ver sus productos/items?';
$Text['validated_at'] = "Validado en "; //Se refiere a fecha/hora


$Text['nothing_to_val'] = "Nada que validar para cesta";
$Text['cart_id'] = "Id de la cesta";
$Text['msg_several_carts'] = "La cesta seleccionada tiene más de un pedido pendiente de validación. Por favor, seleccione una:";
$Text['transfer_type'] = "Tipo";
$Text['todays_carts'] = "Cestas de hoy";
$Text['head_ti_torn'] = "Mirada turno"; 
$Text['btn_validate'] = "Validar";
$Text['desc_validate'] = "Validar pedidos anteriores y actuales para las cestas. Hacer depósitos de dinero.";
$Text['nav_wiz_revise_order'] = "Revisar";
$Text['desc_revise'] = "Revisar pedidos individuales; comprobar si los productos han llegado y ajustar las cantidades si es necesario. Distribuir el pedido en cestas de compra individuales.";
$Text['desc_cashbox'] = "Hacer ingresos y retiros monetarios. Al inicio del primer movimiento la cuenta tiene que ser reiniciada. La cantidad de esta cuenta debe reflejar el dinero real disponible.";
$Text['desc_stock'] = "Añadir y/o controlar el stock de productos.";
$Text['desc_print_orders'] = "Imprimir y descargar los pedidos para la semana siguiente. Los Pedidos deben estar finalizados, impresos y descargados en un fichero zip.";
$Text['nav_report_status'] = "Estadísticas";
$Text['desc_stats'] = "Descargar un resumen de los movimientos actuales incluyendo las incidencias de hoy, cestas en negativo, cuenta de gastos total y productos con stock negativo.";
$Text['order_closed'] = "El pedido está cerrado para este proveedor.";
$Text['head_ti_sales'] = "Listado de Ventas"; 
$Text['not_yet_val'] = "todavía no validado";
$Text['val_by'] = "Validado por";
$Text['purchase_details'] = "Detalle de la compra de la cesta #";
$Text['filter_uf'] = "Filtrar por cesta";
$Text['purchase_uf'] = "Compra de la cesta";
$Text['quantity_short'] = "Qu";
$Text['incl_iva'] = "incl. IVA";
$Text['incl_revtax'] = "incl. ImpRev";
$Text['no_news_today'] = "¡Ninguna noticia es una buena noticia: hoy no han habido incidencias!";
$Text['nav_mng_iva'] = "Tipos de IVA";
$Text['nav_mng_money'] = "Monedero";
$Text['nav_mng_admin'] = "Admin";
$Text['nav_mng_users'] = "Usuarios";
$Text['nav_mng_access_rights'] = "Permisos de Acceso";
$Text['msg_sel_account'] = "Elige una cuenta primero, después filtra los resultados!";
$Text['msg_err_nomovements'] = "Lo siento, no hay movimientos para la cuenta seleccionada y fecha. Trate de ampliar el periodo de tiempo con el botón de filtro.";
$Text['active_changed_uf'] = "Estado activo de la cesta modificado";
$Text['msg_err_mentoruf'] = "¡La cesta anfitriona debe ser diferente de ella misma!";
$Text['msg_err_ufexists'] = "La cesta ya existe. Por favor, elija otra!";
$Text['msg_err_form_init'] = "Parece que el formulario para crear un nuevo miembro no se ha inicializado correctamente. Recargue la página y inténtelo otra vez... ";
$Text['ti_mng_hu_members'] = "Gestionar cestas y sus miembros"; 
$Text['list_ufs'] = "Lista de cestas";
$Text['search_members'] = "Búsqueda de miembros";
$Text['member_pl'] = "Miembros";
$Text['mng_members_uf'] = "Gestionar los miembros de la cesta ";
$Text['uf_name'] = "Nombre";
$Text['btn_new_member'] = "Nuevo miembro";
$Text['ti_add_member'] = "Añadir nuevo miembro a la cesta";
$Text['custom_member_ref'] = "Ref. personalizada";
$Text['theme'] = "Tema";
$Text['member_id'] = "Id del miembro";
$Text['ti_mng_stock'] = "Gestionar stock";
$Text['msg_err_no_stock'] = "Aparentemente este proveedor no tiene stock";
$Text['msg_err_qu'] = "¡La cantidad debe ser numérica y mayor que 0!";
$Text['msg_correct_stock'] = "¡Ajustar el stock de esta forma debería ser una excepción! El stock nuevo tiene que ser siempre AÑADIDO. Está seguro de corregir el stock de este producto? Como se enteren los informáticos le van a.....";
$Text['btn_yes_corret'] = "¡Sí, haz la modificación!";
$Text['ti_mng_stock'] = "Gestionar stock";
$Text['search_product'] = "Buscar un producto";
$Text['add_stock'] = "Añadir stock";
$Text['click_to_edit'] = "¡Clicar la celdas para editar!";
$Text['no_results'] = "La búsqueda no ha producido resultados.";
$Text['for'] = "para"; //as in order FOR Aurora
$Text['date_for_order'] = "Fecha de entrega";
$Text['finished_loading'] = "Carga Finalizada";
$Text['msg_err_unrevised'] = "¡Hay items sin revisar en este pedido. Por favor, asegúrese de que todos los productos pedidos han llegado!";
$Text['btn_dis_anyway'] = "Distribuir igualmente";
$Text['btn_remaining'] = "Revisar los pendientes";
$Text['msg_err_edit_order'] = "Este pedido no está completo. Solo se pueden añadir notas y referencias cuando se haya enviado.";
$Text['order_open'] = "El Pedido está abierto";
$Text['finalize_now'] = "Finalizar ahora";
$Text['msg_err_order_filter'] = "No hay pedidos coincidentes con el criterio de búsqueda.";
$Text['msg_finalize'] = "Está a punto de terminar un pedido. Esto significa que ya no podrá hacer modificaciones. ¿Seguro que quieres continuar?";
$Text['msg_finalize_open'] = "Este pedido está todavía abierto. Finalizarlo implica que deberás cerrarlo antes de su fecha límite. ¿Seguro que quieres continuar?";
$Text['msg_wait_tbl'] = "Las cabeceras de la tabla todavía se estan creando. En función de la conexión a Internet, puede llevar un tiempo. Inténtalo otra vez en 5 segundos. ";
$Text['msg_err_invalid_id'] = "¡No se encontró ningún ID válido para el pedido! ¡¡Este pedido no ha sido enviado al proveedor!!";
$Text['msg_revise_revised'] = "Los ítems de este pedido han sido revisados y asignados a las cestas para la fecha de venta indicada. Revisarlos otra vez implica perder la modificaciones ya hechas e interferir en las correcciones creadas por los usuarios. <br/><br/> ¡¡¿Está totalmente seguro de continuar?!! <br/><br/> Si pulsas OK borrarás los ítems de las cestas existentes e iniciarás el proceso de revisión del pedido otra vez.";
$Text['wait_reset'] = "Por favor, espera mientras el pedido se reinicia...";
$Text['msg_err_already_val'] = "Algunos o todos los ítems ya se han validado. ¡¡Lo siento pero no es posible hacer mas cambios!!";
$Text['print_several'] = "Hay más de un pedido seleccionado. ¿Quieres imprimirlos todos?";
$Text['btn_yes_all'] = "Sí, imprimir todos";
$Text['btn_just_one'] = "No, solo uno";
$Text['ostat_revised'] = "Revisado";
$Text['ostat_finalized'] = "Finalizado";
$Text['set_ostat_arrived'] = "¡Recibido!";
$Text['set_ostat_postpone'] = "¡Postpuesto!";
$Text['set_ostat_cancel'] = "¡Cancelado!";
$Text['ostat_desc_sent'] = "Se ha enviado el pedido al proveedor";
$Text['ostat_desc_nochanges'] = "Revisado y distribuido sin cambios";
$Text['ostat_desc_postponed'] = "Se ha postpuesto el pedido";
$Text['ostat_desc_cancel'] = "Se ha cancelado el pedido";
$Text['ostat_desc_changes'] = "Revisado con algunas modificaciones";
$Text['ostat_desc_incomp'] = "Pedido ignorado. Falta información anterior a la v2.5";
$Text['set_ostat_desc_arrived'] = "La mayoría de los ítems pedidos han llegado. Procediendo a revisar y distribuir los productos en las cestas...";
$Text['set_ostat_desc_postpone'] = "El pedido no ha llegado en la fecha indicada pero probablemente llegará en las próximas semanas.";
$Text['set_ostat_desc_cancel'] = "Los ítems pedidos no llegarán nunca.";
$Text['msg_move_to_shop'] = "Los ítems han sido distribuidos en la cestas para la fecha indicada.";
$Text['msg_err_noselect'] = "¡Nada seleccionado!";
$Text['ti_revise'] = "Revisar pedido";
$Text['btn_revise'] = "Revisar pedido";
$Text['ti_order_detail'] = "Detalle del pedido para";
$Text['ti_mng_orders'] = "Gestionar pedidos";
$Text['btn_distribute'] = "¡Distribuir!";
$Text['distribute_desc'] = "Distribuir los ítems del pedido en las cestas";
$Text['filter_orders'] = "Filtrar pedidos";
$Text['btn_filter'] = "Filtrar";
$Text['filter_acc_todays'] = "Movimientos de hoy";
$Text['filter_recent'] = "Recientes";
$Text['filter_year'] = "Último año";
$Text['filter_all'] = "Todos";
$Text['filter_expected'] = "Esperados para hoy";
$Text['filter_next_week'] = "Próxima semana";
$Text['filter_future'] = "Todos los pedidos futuros";
$Text['filter_month'] = "Último mes";
$Text['filter_postponed'] = "Postpuestos";
$Text['with_sel'] = "Con selección...";
$Text['dwn_zip'] = "Descargar comprimido";
$Text['closes_days'] = "Cierra en días";
$Text['sent_off'] = "Enviado al proveedor";
$Text['date_for_shop'] = "Fecha de compra";
$Text['order_total'] = "Total del pedido";
$Text['nie'] = "NIE";
$Text['total_orginal_order'] = "Pedido original";
$Text['total_after_revision'] = "Después de revisión";
$Text['delivery_ref'] = "Ref. de entrega";
$Text['payment_ref'] = "Ref. de pago";
$Text['arrived'] = "Llegado"; //as in order items have arrived. this is a table heading
$Text['msg_cur_status'] = "El estado actual del pedido es";
$Text['msg_change_status'] = "Cambiar el estado del pedido a alguna de las siguientes opciones";
$Text['msg_confirm_move'] = "¿Estás seguro de que quieres hacer disponible el pedido para la compra? Todos los productos asociados seran distribuidos en las cestas para la fecha:";
$Text['alter_date'] = "Elije una fecha alternativa";
$Text['msg_err_miss_info'] = "Aparentemente este pedido fue creado con una versión más antigua del software que es incompatible con la funcionalidad de revisión actual. Lo siento, este pedido no puede ser revisado.";

//added 29.09

$Text['order_closes'] = "El pedido se cierra el"; //as in: order closes 20 SEP 2012
$Text['left_ordering'] = " pendientes de pedir."; //as in 4 days left for ordering
$Text['ostat_closed'] = "El pedido está cerrado";
$Text['ostat_desc_fin_send'] = "El pedido se ha cerrado y enviado al proveedor. La referencia es: #";
$Text['msg_err_past'] = "¡Esto es el pasado! <br/> Demasiado tarde para modificar cosas.";
$Text['msg_err_is_deactive_p'] = "Este producto está desactivado. Para abrirlo para una fecha primero debes activarlo.";
$Text['msg_err_deactivate_p'] = "Estás a punto de desactivar el producto. Esto quiere decir que las fechas en que se puede pedir también se eliminaran.<br/><br/>También podrias desactivar las fechas en que se puede pedir clicando en las casillas correspondientes.";
$Text['msg_err_closing_date'] = "¡La fecha de cierre no puede ser posterior a la del pedido!";
$Text['msg_err_sel_col'] = "¡La fecha seleccionada no tiene productos para pedir! Debes establecer un producto para pedir si quieres crear una plantilla para esta fecha.";
$Text['msg_err_closing'] = "Para modificar la fecha de cierre, hay que poner al menos un producto que puede pedir.";
$Text['msg_err_deactivate_sent'] = "El producto escogido no puede ser (des)activado porque el pedido correspondiente ya ha sido enviado al proveedor. ¡No se pueden hacer mas cambios!";
$Text['view_opt'] = "Mostrar opciones";
$Text['days_display'] = "Número de días a mostrar";
$Text['plus_seven'] = "Mostrar +7 días";
$Text['minus_seven'] = "Mostrar -7 días";
$Text['btn_earlier'] = "Antes de"; //cómo más temprano
$Text['btn_later'] = "después de"; //más tarde... futuro


//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
$Text['pattern_intro'] = "Activa los productos y días para los próximos ";
$Text['pattern_scale'] = "meses ";
$Text['week'] = "cada semana";
$Text['second'] = "cada 15 dias";  //2nd 
$Text['third'] = "cada 3 semanas";
$Text['fourth'] = "una vez al mes";
$Text['msg_pattern'] = "¡NOTA: Esta acción regenerará los productos y fechas a partir de esta!";
$Text['sel_closing_date'] = "Elegir una nueva fecha de cierre";
$Text['btn_mod_date'] = "Modificar la fecha de cierre";
$Text['btn_repeat'] = "¡Repetir el patrón!";
$Text['btn_entire_row'] = "(Des)activa toda la columna";
$Text['btn_deposit'] = "Depósito";
$Text['btn_withdraw'] = "Reintegro";
$Text['deposit_desc'] = "Ingreso en efectivo";
$Text['withdraw_desc'] = "Reintegro de la caja";
$Text['btn_set_balance'] = "Establecer el balance";
$Text['set_bal_desc'] = "Corregir el balance al principio del primer turno.";
$Text['maintenance_account'] = "Mantenimiento";
$Text['posted_by'] = "Creado por"; //Posted by
$Text['ostat_yet_received'] = "pendiente de recibir";
$Text['ostat_is_complete'] = "está completo";
$Text['ostat_postponed'] = "postpuesto";
$Text['ostat_canceled'] = "cancelado";
$Text['ostat_changes'] = "con cambios";
$Text['filter_todays'] = "De hoy";
$Text['bill'] = "Factura";
$Text['member'] = "Miembro";
$Text['cif_nif'] = "CIF/NIF"; //CIF/NIF
$Text['bill_product_name'] = "Artículo"; //concepte en cat... 
$Text['bill_total'] = "Total"; //Total factura 
$Text['phone_pl'] = "Teléfonos";
$Text['net_amount'] = "Importe neto"; //importe netto 
$Text['gross_amount'] = "Importe bruto"; //importe brutto
$Text['add_pagebreak'] = "Pulsar aquí para añadir un salto de página";
$Text['remove_pagebreak'] = "Pulsar aquí para eliminar el salto de página";


$Text['show_deactivated'] = "Mostrar desactivados"; 
$Text['nav_report_sales'] = "Ventas"; 
$Text['nav_help'] = "Ayuda"; 
$Text['withdraw_from'] = "Retirar dinero ";  //account
$Text['withdraw_to_bank'] = "Retirar dinero de banco";
$Text['withdraw_uf'] = "Retirar dinero de cuenta HU";
$Text['withdraw_cuota'] = "Retirar dinero de quota";
$Text['msg_err_noorder'] = "¡No hay pedidos para la selección!";
$Text['primer_torn'] = "Primer Turno";
$Text['segon_torn'] = "Segundo Turno";
$Text['dff_qty'] = "Dif. cantidad";
$Text['dff_price'] = "Dif. precio";
$Text['ti_mgn_stock_mov'] = "Movimientos de stock";
$Text['stock_acc_loss_ever'] = "Pérdida acumulada";
$Text['closed'] = "cerrado";
$Text['preorder_item'] = "Este producto forma parte de un pedido acumulativo";
$Text['do_preorder'] = "Activar/desactivar como pedido acumulativo";
$Text['do_deactivate_prod'] = "Activar/desactivar producto";
$Text['msg_make_preorder_p'] = "Este pedido es acumulativo, por lo tanto todavía no tiene fecha de entrega";
$Text['btn_ok_go'] = "OK, ¡adelante!";
$Text['msg_pwd_emailed'] = "La nueva contraseña se ha enviado al usuario";
$Text['msg_pwd_change'] = "La nueva contraseña es: ";
$Text['msg_err_emailed'] = "¡Error de envío!";
$Text['msg_order_emailed'] = "¡El pedido se ha enviado correctamente!";
$Text['msg_err_responsible_uf'] = "No hay responsable para este producto";
$Text['msg_err_finalize'] = "Ups...¡Error al finalizar pedido!";
$Text['msg_err_cart_sync'] = "Tu pedido no está sincronizado con la base de datos; se ha modificado tu cesta mientras estabas comprando. Vuelve a actualizar el pedido.";
$Text['msg_err_no_deposit'] = "¡La última cesta no ha realizado ningún depósito!";
$Text['btn_load_cart'] = "Continúa con la siguiente cesta";
$Text['btn_deposit_now'] = "Haz el ingreso ahora";
$Text['msg_err_stock_mv'] = "De momento no ha habido movimientos de stock para este producto.";

$Text['ti_report_shop_pv'] = "Total ventas por proveedor";
$Text['filter_all_sales'] = "Todas las ventas";
$Text['filter_exact'] = "Elegir periodo";
$Text['total_4date'] = "Total para la fecha";
$Text['total_4provider'] = "Suma";
$Text['sel_sales_dates'] = "Muestra ventas por proveedor y el periodo selecionado:";
$Text['sel_sales_dates_ti'] = "Elige un periodo";
 
$Text['instant_repeat'] = "Repetir directamente";
$Text['msg_confirm_delordereditems'] = "Este producto ya se ha pedido este día. ¿Estás seguro de desactivarlo? Esto borrará el pedido de las cestas. ";
$Text['msg_confirm_instantr'] = "¿Quieres repetir la misma accion para el resto de las fechas activas? ";
$Text['msg_err_delorerable'] = "Existe un pedido para este producto y fecha. No se puede borrar.";
$Text['msg_pre2Order'] = "Convierte el pedido acumulativo en un pedido regular. Se crea una fecha de entrega. ";

$Text['msg_err_modified_order'] = "Se han modificado los productos que se pueden pedir para la fecha actual mientras hacías el pedido. Algunos de los productos de tu pedido ya no están disponibles y se quitarán de tu cesta.";
$Text['btn_confirm_del'] = "Sí, eliminar.";
$Text['print_new_win'] = "Ventana nueva";
$Text['print_pdf'] = "Descarga pdf";
$Text['msg_incident_emailed'] = "La incidencia se ha enviado por correo correctamente.";
$Text['upcoming_orders'] = "Próximos pedidos";

$Text['msg_confirm_del_mem'] = "¿Seguro que quieres eliminar el usuario de la base de datos?";
$Text['btn_del'] = "Eliminar";

$Text['btn_new_provider'] = "Nuevo proveedor";
$Text['btn_new_product'] = "Añadir producto";
$Text['orderable'] = "Pedido directo"; //product type
$Text['msg_err_providershort'] = "El nombre del proveedor no puede quedar vacío y debe contener al menos 2 caracteres.";
$Text['msg_err_productshort'] = "El nombre del producto no puede quedar vacío y debe contener al menos 2 caracteres.";
$Text['msg_err_select_responsibleuf'] = "¿Quién se encarga? Hay que seleccionar un responsable.";
$Text['msg_err_product_category'] = "Hay que seleccionar una categoría de producto.";
$Text['msg_err_order_unit'] = "Hay que seleccionar una unidad de medida para el pedido.";
$Text['msg_err_shop_unit'] = "Hay que seleccionar una unidad de medida para la venta.";
$Text['click_row_edit'] = "Haz clic para editar.";
$Text['click_to_list'] = "Haz clic para desplegar la lista de productos.";
$Text['head_ti_provider'] = "Gestión de proveedores y productos";
$Text['edit'] = "Editar";
$Text['ti_create_provider'] = "Añadir proveedor";
$Text['ti_add_product'] = "Añadir producto";
$Text['order_min'] = "Cantidad mínima para el pedido";
$Text['msg_confirm_del_product'] = "¿Seguro que quieres borrar este producto?";
$Text['msg_err_del_product'] = "No se puede borrar este producto porque hay entradas que dependen de él en la base de datos. Mensaje de error: ";
$Text['msg_err_del_member'] = "No se puede borrar este usuario porque hay referencias al mismo en la base de datos<br/> Mensaje de error: ";
$Text['msg_confirm_del_provider'] = "¿Seguro que quieres borrar este proveedor?";
$Text['msg_err_del_provider'] = "No se puede borrar este proveedor. Borra sus productos antes y vuelve a probar.";
$Text['price_net'] = "Precio neto";
$Text['custom_product_ref'] = "Id externo"; 
$Text['btn_back_products'] = "Volver a productos";

$Text['copy_column'] = "Copiar columna";
$Text['paste_column'] = "Pegar";

$Text['search_provider'] = "Buscar proveidor";
$Text['msg_err_export'] = "Error al exportar datos";
$Text['export_uf'] = "Exportar miembros";
$Text['btn_export'] = "Exportar";

$Text['ti_visualization'] = "Visualizaciones";
$Text['file_name'] = "Nombre de archivo";
$Text['active_ufs'] = "Sólo cestas activas";
$Text['export_format'] = "Formato de exportación";
$Text['google_account'] = "Google account";
$Text['other_options'] = "Otras opciones";
$Text['export_publish'] = "Hacer público el archivo de exportación en:";
$Text['export_options'] = "Opciones de exportación";
$Text['correct_stock'] = "Corregir stock";
$Text['btn_edit_stock'] = "Editar stock";
$Text['consult_mov_stock'] = "Consultar movimientos";
$Text['add_stock_frase'] = "Stock total = stock actual de "; //complete frase is: total stock = current stock of X units + new stock
$Text['correct_stock_frase'] = "El stock actual no es ";
$Text['stock_but'] = "sino"; //current stock is not x units but...
$Text['stock_info'] = "Nota: se pueden consultar todos los cambios de stock (adiciones, correcciones, pérdidas) con sólo hacer clic en el nombre del producto aquí abajo.";
$Text['stock_info_product'] = "Nota: se pueden consultar todos los cambios de stock (adiciones, correcciones, pérdidas totales) desde la sección Gestionar &gt; Productos &gt; Stock.";


$Text['msg_confirm_prov'] = "¿Seguro que quieres exportar todos los proveedores?"; 
$Text['msg_err_upload'] = "Se ha producido un error en la carga del archivo "; 
$Text['msg_import_matchcol'] = "Hay que hacer coincidir las entradas de la base de datos con las filas de la tabla. Debes asignar la columna que corresponde a "; //+ here then comes the name of the matching column, e.g. custom_product_ref
$Text['msg_import_furthercol'] = "¿Qué otras columnas de la tabla quieres importar además de la columna necesaria?"; 
$Text['msg_import_success'] = "La importación ha funcionado correctamente. ¿Quieres importar otro archivo?"; 
$Text['btn_import_another'] = "Importar otro"; 
$Text['btn_nothx'] = "No, gracias"; 
$Text['import_allowed'] = "Formatos compatibles"; //as in allowed file formats
$Text['import_file'] = "Archivo de importación"; 
$Text['public_url'] = "URL pública";
$Text['btn_load_file'] = "Cargar archivo";
$Text['msg_uploading'] = "Se está cargando el archivo y se está generando la vista previa. Espera...";
$Text['msg_parsing'] = "Se está leyendo el archivo del servidor y se está analizando. Espera...";
$Text['import_step1'] = "Selección de archivo";
$Text['import_step2'] = "Vista previa de los datos y asignacíón de columnas";
$Text['import_reqcol'] = "Columna necesaria";
$Text['import_auto'] = "Tengo buenas noticias: casi todos los datos (columnas) se han podido reconocer y puedes intentar la importación automática. No obstante, la opción más segura es previsualizar primero el contenido y luego realizar la asignación de columnas de la tabla manualmente.";
$Text['import_qnew'] = "¿Qué quieres hacer con los datos que no existen en la base de datos actual?";
$Text['import_createnew'] = "Crear entradas nuevas";
$Text['import_update'] = "Sólo actualizar las filas existentes";
$Text['btn_imp_direct'] = "Importar directamente";
$Text['btn_import'] = "Importar";
$Text['btn_preview'] = "Vista previa"; 
$Text['sel_matchcol'] = "Asignar columna..."; 
$Text['ti_import_products'] = "Importar o actualizar los productos de "; 
$Text['ti_import_providers'] = "Importar proveedores"; 
$Text['head_ti_import'] = "Asistente de importación";

$Text['withdraw_desc_banc'] = "Retirar dinero de la cuenta o transferir para pago a proveedores.";
$Text['deposit_desc_banc'] = "Registrar todo el dinero entrante a la cuenta de consumo.";
$Text['deposit_banc'] = "Depositar en la cuenta de consumo";
$Text['withdraw_banc'] = "Retirar de la cuenta de consumo";
$Text['deposit_sales_cash'] = "Depósito en efectivo de ventas";
$Text['ti_stock_report'] = "Reporte de stock para "; 
$Text['netto_stock'] = "Valor neto del stock"; 
$Text['brutto_stock'] = "Valor bruto del stock"; 
$Text['total_netto_stock'] = "Valor neto total del stock"; 
$Text['total_brutto_stock'] = "Valor bruto total del stock"; 
$Text['sales_total_pv'] = "Ventas totales del proveedor ";
$Text['dates_breakdown'] = "Fechas de vencimiento"; //decía "break down"
$Text['price_brutto'] = "Precio bruto"; 
$Text['total_brutto'] = "Total bruto";
$Text['total_netto'] = "Total neto";
$Text['msg_err_oldPwdWrong'] = "Disculpa, tu clave actual no es correcta. Por favor, vuelve a intentarlo. "; 
$Text['msg_err_adminStuff'] = "Privilegios de acceso insuficientes. ¡Solo un administrador puede hacer eso!";
$Text['msg_err_deactivate_prdrow'] = "This product cannot be deactivated because it has ordered items for certain dates. Deactivate the product for those individual dates first!";
$Text['msg_err_deactivate_ir'] = "You cannot deactivate several dates for this product since certain dates contain already ordered items. Either turn off Instant Repeat or deactivate the ordered products/date individually.";


?>
