<?php
// Spanish translation file for aixada 
// contribute by 
// Cristóbal Cabeza-Cáceres, Daniel Mollà
// Email: cristobal.cabeza@gmail.com, dmollaca@gmail.com

require_once(__ROOT__. 'local_config/config.php');

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
$Text['loading'] = "Por favor, espere mientras se cargan los datos...";
$Text['search'] = "Buscar";
$Text['id'] = "id";
$Text['uf_short'] = "UF";
$Text['uf_long'] = "Unidad familiar";

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
$Text['sat'] = 'Sábado';
$Text['sun'] = 'Domingo';


/**
 * 				selects
 */
$Text['sel_provider'] = "Seleccionar un proveedor...";
$Text['sel_product'] = "Seleccionar un producto...";
$Text['sel_user'] = "Seleccionar un usuario...";
$Text['sel_category'] = "Seleccionar una categoría de productos...";
$Text['sel_uf'] = "Seleccionar una unidad familiar...";
$Text['sel_uf_or_account'] = "Seleccionar unidad familiar o cuenta...";
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
$Text['global_title'] = configuration_vars::get_instance()->coop_name;
$Text['head_ti_order'] = "Pedido"; // Unused!
$Text['head_ti_shop'] = "Comprar productos"; // Unused!
$Text['head_ti_reports'] = "Informes"; // Unused!
$Text['head_ti_validate'] = "Validar";

$Text['head_ti_active_products'] = "Activar/Desactivar Productos para pedidos";
$Text['head_ti_arrived_products'] = "Productos que han llegado"; // Unused!
$Text['head_ti_active_roles'] = "Roles activos";
$Text['head_ti_account'] = "Cuentas";
$Text['head_ti_manage_orders'] = "Gestionar los pedidos";
$Text['head_ti_manage_dates'] = "Activar Fechas Pedidos";
$Text['head_ti_manage'] = "Gestionar";
$Text['head_ti_manage_uf'] = "Unidades familiares/Miembros";
$Text['head_ti_incidents'] = "Incidentes";
$Text['head_ti_stats'] = "Estadísticas diarias";
$Text['head_ti_prev_orders'] = "Mis compras anteriores"; 
$Text['head_ti_cashbox'] = "Control de dinero"; 




/**
 *  			titles for main pages <h1></h1>
 */
$Text['ti_mng_activate_products'] = "Las UFs podrán pedir lo siguiente PARA el día  ";
$Text['ti_mng_arrived_products'] = "Los siguientes productos han llegado el día "; // Unused!
$Text['ti_mng_activate_roles'] = "Gestionar los roles de usuario ";
$Text['ti_mng_activate_users'] = "Activar usuarios como ";
$Text['ti_mng_move_orders'] = "Cambiar un pedido";
$Text['ti_mng_activate_preorders'] = "Convertir un prepedido en pedido";
$Text['ti_mng_members'] = "Gestionar los miembros";
$Text['ti_mng_ufs'] = "Gestionar las unidades familiares";
$Text['ti_mng_dates'] = "De-/activar individualmente fechas para hacer pedidos";
$Text['ti_mng_dates_pattern'] = "Bulk de-/activar fechas para hacer pedidos"; // Unused!
$Text['ti_mng_db'] = "Copia de seguridad de la base de datos"; 
$Text['ti_order'] = "Hacer el pedido para ";
$Text['ti_shop'] = "Comprar artículos ";
$Text['ti_report_report'] = "Resumen de pedidos para "; 
$Text['ti_report_account'] = "Informe de todas las cuentas"; 
$Text['ti_report_my_account'] = "Informe de mi cuenta "; 
$Text['ti_report_preorder'] = "Resumen de prepedidos"; 
$Text['ti_report_incidents'] = "Incidentes de hoy";
$Text['ti_incidents'] = "Incidentes";
$Text['ti_validate'] = "Validar la compra de la unidad familiar ";
$Text['ti_stats'] = "Estadísticas totales";
$Text['ti_my_account'] = "Configuración";
$Text['ti_my_account_money'] = "Dinero";
$Text['ti_my_prev_sales'] = "Mis compras anteriores";
$Text['ti_all_sales'] = "Todas las compras de la UF";
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
$Text['Econo-Legal Commission'] = 'Comisión econolegal';
$Text['Logistic Commission'] = 'Comisión de logística';
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
$Text['create_uf'] = "Nueva unidad familiar";
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
$Text['edit_uf'] = "Editar UF";
$Text['members_uf'] = "Miembros de la unidad familiar";
$Text['mentor_uf'] = "Unidad familiar anfitriona";
$Text['unassigned_members'] = "Miembros no asignados";
$Text['edit_my_settings'] = "Editar mi configuración";

$Text['nif'] = "NIF";
$Text['bank_name'] = "Banco o Caja";
$Text['bank_account'] = "Cuenta bancario";
$Text['picture']  = "Imagen";
$Text['offset_order_close'] = "Tiempo de procesamiento";
$Text['iva_percent_id'] = "Tipo de IVA";
$Text['percent'] = "Porcentaje";
$Text['type'] = "Tipo";
$Text['treasury'] = "Tesorería";
$Text['service'] = "Servicio";
$Text['adult'] = "Adulto";


/**
 *  			wiz stuff
 */
$Text['deposit_cashbox'] = 'Ingresar dinero en caja';
$Text['widthdraw_cashbox'] = 'Retirar dinero de la caja';
$Text['current_balance'] = 'Saldo actual';
$Text['deposit_type'] = 'Concepto de ingreso';
$Text['deposit_by_uf'] = 'Ingreso por parte de una UF';
$Text['deposit_other'] = 'Otros ingresos...';
$Text['make_deposit_4HU'] = 'Ingreso de la UF';
$Text['short_desc'] = 'Descripción';
$Text['withdraw_type'] = 'Tipo de pago';
$Text['withdraw_for_provider'] = 'Para un proveedor';
$Text['withdraw_other'] = 'Otros tipos de pago..';
$Text['withdraw_provider'] = 'Reintegro para pago a proveedores';
$Text['btn_make_withdrawal'] = 'Paga';
$Text['correct_balance'] = 'Corregir balance';
$Text['set_balance'] = 'Actualizar balance de la caja';
$Text['name_cash_account'] = 'Caja';

// See `account_operations.config.php`
//     * Translated keys of $config_account_operations with the prefix 'mon_op_'
$Text['mon_op_deposit_uf'] = 'Ingreso de UF';
$Text['mon_op_deposit_others'] = 'Ingreso otros';
$Text['mon_op_debit_uf'] = 'Cargo a UF';
$Text['mon_op_pay_pr'] = 'Pago a Prv.';
$Text['mon_op_refund_uf'] = 'Devolución a UF.';
$Text['mon_op_withdraw_others'] = 'Pago otros';
$Text['mon_op_invoice_pr'] = 'Factura de Prv.';
$Text['mon_op_move'] = 'Mover';
$Text['mon_op_correction'] = 'Ajustar saldo';
$Text['mon_op_a_debit_uf'] = 'Anular Cargo a UF';
$Text['mon_op_a_pay_pr'] = 'Anular Pag.Prv.';
$Text['mon_op_a_invoice_pr'] = 'Anular Factura Prv.';
// See `account_operations.config.php`
//   * Translated sub-keys values from 'default_desc' and 'auto_desc'
//     on $config_account_operations with the prefix 'mon_desc_'
$Text['mon_desc_deposit_uf'] = 'Deposito de UF';
$Text['mon_desc_deposit_from_uf'] = 'Deposito de UF #{uf_from_id} {comment}';
$Text['mon_desc_payment'] = 'Pago a proveedor';
$Text['mon_desc_payment_to_provider'] = 'Pago a #{provider_to_id} {comment}';
$Text['mon_desc_refund_to_uf'] = 'Restitución a  UF: #{uf_to_id} {comment}';
$Text['mon_desc_invoice'] = 'Factura de proveedor.';
$Text['mon_desc_treasury_movement'] = 'Movimiento de tesorería.';
$Text['mon_desc_move_from'] = 'Movimiento viene de #{account_from_id} {comment}';
$Text['mon_desc_move_to'] = 'Movimiento va a #{account_to_id} {comment}';
$Text['mon_desc_correction'] = 'Corrección de saldo';
$Text['mon_desc_a_payment_to_provider'] = 'ANULAR Pago a #{provider_from_id} {comment}';
$Text['mon_desc_a_payment'] = 'ANULAR Pago a proveedor';
$Text['mon_desc_a_invoice'] = 'ANULAR Factura de proveedor.';
// Used in manege_money.php and controllers
$Text['mon_ops_standard'] = 'Normales';
$Text['mon_ops_corrections'] = 'Correcciones';
$Text['mon_send'] = 'Confirmar';
$Text['mon_from'] = 'De';
$Text['mon_to'] = 'A';
$Text['mon_all_active_uf'] = '* Todas las UF activas *';
$Text['mon_success'] = 'La operación ha tenido éxito, {count} anotaciones!';
$Text['mon_war_no_all_hu'] = "{mon_all_active_uf} No puede usarse en esta operación.";
$Text['mon_war_decimals'] = "La cantidad no debe tener más de dos decimales!";
$Text['mon_war_gt_zero'] = "Cantidad debe ser mayor que cero!";
$Text['mon_war_accounts_not_set'] = "Alguna de las cuentas requeridas no constan.";
$Text['mon_war_description'] = "Debe escribir un breve comentario.";
$Text['mon_dailyTreasurySummary'] = "Resumen Tesorería de hoy";
$Text['mon_balance'] = "Saldo";
$Text['mon_amount'] = "Importe";
$Text['mon_dailyBalance'] = "Saldo del día";
$Text['mon_accountBalances'] = "Saldo de las cuentas";
$Text['mon_uf_balances'] = "Saldo de las UFs";
$Text['mon_provider_balances'] = "Saldo de Proveedores";
$Text['mon_result'] = "Resultado";
$Text['mon_lastOper'] = "Última operación";
$Text['mon_operation_account'] = "Hacer operaciones";
$Text['mon_list_account'] = "Consultar operaciones de una cuenta";

/**
 *				validate
 */
$Text['set_date'] = "Definir una fecha";
$Text['get_cart_4_uf'] = "Recuperar la compra de la unidad familiar";
$Text['make_deposit'] = "Ingreso de la unidad familiar";
$Text['success_deposit'] = "El ingreso se ha realizado correctamente";
$Text['amount'] = "Cantidad";
$Text['comment'] = "Comentario";
$Text['deposit_other_uf'] = "Realizar un ingreso para otra unidad familiar o cuenta";
$Text['latest_movements'] = "Últimos movimientos";
$Text['time'] = "Hora";
$Text['account'] = "Cuenta";
$Text['consum_account'] = "Cuenta de Consumo";
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
$Text['old_pwd'] = "Contraseña antigua";
$Text['retype_pwd']	= "Vuelve a escribir la contraseña";
$Text['lang']	= "Idioma";
$Text['msg_err_incorrectLogon'] = "Acceso incorrecto";
$Text['msg_err_noUfAssignedYet'] = "Todavía no has sido asignado a ninguna UF: Por favor, pide que te den de alta.";


$Text['msg_reg_success'] = "Te has registrado correctamente, pero tu usuario aún no se ha aprobado. Registra el resto de miembros de tu UF y contacta con un responsable para finalizar el registro.";
$Text['register'] = "Registro";
$Text['required_fields'] = " son campos obligatorios";


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
	$Text['nav_mng_uf'] = "Unidades familiares";
	$Text['nav_mng_member'] = "Miembros";
	$Text['nav_mng_providers'] = "Proveedores";
	$Text['nav_mng_products'] = "Productos";
	$Text['nav_mng_deactivate'] = "Activar/desactivar productos";
	$Text['nav_mng_stock'] = "Stock";
		$Text['nav_mng_units'] = "Unidades";
	$Text['nav_mng_orders'] = "Pedidos";
		$Text['nav_mng_setorderable'] = "Activar Fechas para realizar pedidos";
		$Text['nav_mng_move'] = "Cambiar la fecha del pedido";
		$Text['nav_mng_orders_overview'] = "Gestionar pedidos";
		$Text['nav_mng_preorder'] = "Convertir el prepedido en pedido";
	$Text['nav_mng_db'] = "Copia seguridad de bd";
	$Text['nav_mng_roles'] = "Roles";
$Text['nav_report'] = "Informes";
$Text['nav_report_order'] = "Pedido actual";
$Text['nav_report_account'] = "Cuentas";
$Text['nav_report_timelines'] = "Evolución de " . configuration_vars::get_instance()->coop_name;;
$Text['nav_report_timelines_uf'] = "Por UFs";
$Text['nav_report_timelines_provider'] = "Por Proveedores";
$Text['nav_report_timelines_product'] = "Por Productos";
$Text['nav_report_daystats'] = "Estadísticas diarias";
$Text['nav_report_preorder'] = "Prepedidos";
$Text['nav_report_incidents'] = "Incidentes de hoy";
$Text['nav_report_shop_hu'] = "Por UF";
$Text['nav_report_shop_pv'] = "Por proveedores";


$Text['nav_incidents'] = "Incidentes";
	$Text['nav_browse'] = "Navegar / añadir";
$Text['nav_myaccount'] = "Mi cuenta";
	$Text['nav_myaccount_settings'] = "Configuración";
	$Text['nav_myaccount_account'] = "Dinero";
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
$Text['btn_new_incident'] = "Incidente nuevo";
$Text['btn_reset_pwd'] = "Re-establecer contraseña"; 
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
$Text['create_incident'] = "Crear un incidente";
$Text['overview'] = "Resumen";
$Text['subject'] = "Asunto";
$Text['message'] = "Mensaje";
$Text['priority'] = "Prioridad";
$Text['status'] = "Estado";
$Text['incident_type'] = "Tipo";
$Text['status_open'] = "Abierto";
$Text['status_closed'] = "Cerrado";
$Text['ufs_concerned'] = "UFs afectadas";
$Text['provider_concerned'] = "Para el proveedor";
$Text['comi_concerned'] = "Para la comisión";
$Text['created_by'] = "Creado por";
$Text['edit_incident'] = "Editar el incidente";

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
$Text['nr_ufs'] = "UFs total";
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
$Text['msg_err_namelength'] = "El nombre y apellido no puede estar vació y no puede tener más de 255 caracteres!"; 
$Text['msg_err_only_num'] = " sólo acepta cifras y no puede estar vacío."; 
$Text['msg_err_email'] = "El formato del correo electrónico no es correcto. Tiene que ser del estilo nombre@dominio.com o parecido.";
$Text['msg_err_select_uf'] = "Para asignar un nuevo miembro a una UF primero tienes que seleccionar la UF clicando sobre ella. Si quieres crear una nueva UF, hazlo clicando en + Nueva UF.";
$Text['msg_err_select_non_member'] = "Para asignar un nuevo miembro a una UF, primero tienes que seleccionarlo de la lista de no miembros que hay a la derecha."; 
$Text['msg_err_insufficient_stock'] = 'No hay suficiente stock de ';


$Text['msg_edit_success'] = "Los datos editados se han guardado correctamente.";
$Text['msg_edit_mysettings_success'] = "La nueva configuración se ha guardado correctamente.";
$Text['msg_pwd_changed_success'] = "La contraseña se ha cambiado correctamente."; 
$Text['msg_confirm_del'] = "¿Seguro que quieres eliminar a este miembro?";
$Text['msg_enter_deposit_amount'] = "El campo de cantidad del ingreso solo acepta cifras y no puede estar vacío.";
$Text['msg_please_set_ufid_deposit'] = "No se ha definido la ID de la UF. Tienes que elegir una compra o seleccionar otra UF para realizar el depósito.";
$Text['msg_error_deposit'] = "Se ha producido un error al hacer el ingreso. Inténtalo de nuevo. Los ingresos que se han hecho correctamente aparecen en la lista de cuentas. <br/>El error ha sido: ";
$Text['msg_deposit_success'] = "El depósito se ha realizado correctamente.";
$Text['msg_withdrawal_success'] = "El pago se ha realizado correctamente.";
$Text['msg_select_cart_first'] = "Para añadir artículos para validar, antes tienes que seleccionar una UF o una compra.";
$Text['msg_err_move_date'] = "Se ha producido un error mientras se cambiaba la fecha del pedido. Inténtalo de nuevo.";
$Text['msg_no_active_products'] = "En estos momentos no hay productos activos para pedir.";
$Text['msg_no_movements'] = "No hay movimientos para la cuenta y la fecha seleccionados."; 
$Text['msg_delete_incident'] = "¿Seguro que quieres eliminar este incidente?";
//$Text['msg_err_selectFirstUF'] = "There is no household selected. Choose one first and then its purchases. No hay ninguna UF seleccionada.  Elige una primero y luego sus compras."; //ADDED JAN 2012

$Text['click_to_change'] = "Clic par cambiar!";
$Text['cart_date'] = "Fecha de la cesta";
$Text['create_cart'] = "Crear Cesta";

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
$Text['responsible_uf'] = 'Unidad familiar responsable';
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


/**
 * added after 14.5
 */
$Text['msg_err_validate_self'] = '¡No puedes validarte a ti mismo!';
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


$Text['purchase_current'] = 'Mis compras';
$Text['items_bought'] = 'Compras anteriores';
$Text['purchase_future'] = 'Mis pedidos';
$Text['purchase_prev'] = 'Compras anteriores';
$Text['icon_order'] = 'Haz tu pedido aqui';
$Text['icon_purchase'] = 'Haz tu compra ahora';
$Text['icon_incidents'] = 'Incidencias';
$Text['purchase_date'] = 'Fecha de la compra';
$Text['purchase_validated'] = 'Fecha de la validación';
//$Text['ordered_for'] = 'Pedido hecho para el'; //!!DUPLICATE
$Text['not_validated'] = 'no validado';

/* definitely new stuff */

$Text['download_db_zipped'] = 'Descargar Base de Datos comprimida';
$Text['backup'] = '¡Ok, haz una copia de la Base de Datos!';
$Text['filter_incidents'] = 'Filtrar incidencias';
$Text['todays'] = "Hoy";
$Text['recent_ones'] = 'Más recientes';
$Text['last_year'] = 'Último Año';
$Text['details'] = 'Detalles';
$Text['actions'] = 'Acciones';
$Text['incident_details'] = 'Detalles de Incidencias';
$Text['distribution_level'] = 'Nivel de Distribución';
$Text['internal_private'] = 'Interno (privado)';
$Text['internal_email_private'] = 'Interno + email (privado)';
$Text['internal_post'] = 'Interno + Enviar al portal (público)';
$Text['internal_email_post'] = 'Interno + email + Portal (público)';

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


$Text['nothing_to_val'] = "Nada que validar para UF";
$Text['cart_id'] = "Id de la cesta";
$Text['msg_several_carts'] = "La UF seleccionada tiene más de una cesta pendiente de validación.  Por favor, seleccione una:";
$Text['transfer_type'] = "Tipo";
$Text['todays_carts'] = "Cestas de hoy";
$Text['week_carts'] = "Cestas de la semana";
$Text['head_ti_torn'] = "Mirada turno"; 
$Text['btn_validate'] = "Validar";
$Text['desc_validate'] = "Validar cestas anteriores y actuales para las UFs. Hacer depósitos de dinero.";
$Text['nav_wiz_revise_order'] = "Revisar";
$Text['desc_revise'] = "Revisar pedidos individuales; comprobar si los productos han llegado y ajustar las cantidades si es necesario. Distribuir el pedido en cestas de compra individuales.";
$Text['desc_cashbox'] = "Hacer ingresos y retiros monetarios. Al inicio del primer movimiento la cuenta tiene que ser reiniciada. La cantidad de esta cuenta debe reflejar el dinero real disponible.";
$Text['desc_stock'] = "Añadir y/o controlar el stock de productos.";
$Text['desc_print_orders'] = "Imprimir y descargar los pedidos para la semana siguiente. Los Pedidos deben estar finalizados, impresos y descargados en un fichero zip.";
$Text['nav_report_status'] = "Estadísticas";
$Text['desc_stats'] = "Descargar un resumen de los movimientos actuales incluyendo las incidencias de hoy, ufs en negativo, cuenta de gastos total y productos con stock negativo.";
$Text['order_closed'] = "El pedido está cerrado para este proveedor.";
$Text['head_ti_sales'] = "Listado de Ventas"; 
$Text['not_yet_val'] = "todavía no validado";
$Text['val_by'] = "Validado por";
$Text['purchase_details'] = "Detalle de la compra de la cesta #";
$Text['filter_uf'] = "Filtrar por UF";
$Text['purchase_uf'] = "Compra de la UF";
$Text['quantity_short'] = "Can";
$Text['incl_iva'] = "incl. IVA";
$Text['incl_revtax'] = "incl. ImpRev";
$Text['no_news_today'] = "¡Ninguna noticia es una buena noticia: hoy no han habido incidencias!";
$Text['nav_mng_iva'] = "Tipos de IVA";
$Text['nav_mng_revtax'] = "Imp. ImpRev";
$Text['nav_mng_accdec'] = "Cuentas"; 
$Text['nav_mng_paymeth'] = "Tipo ingreso/pago";
$Text['nav_mng_movtype'] = "Tipo Estoc";
$Text['nav_mng_money'] = "Dinero";
$Text['nav_mng_admin'] = "Admin";
$Text['nav_mng_users'] = "Usuarios";
$Text['nav_mng_access_rights'] = "Permisos de Acceso";
$Text['nav_mng_aux'] = "Mantenimientos auxiliares";
$Text['dataman_consult'] = "Consultar \"{data}\"";
$Text['dataman_edit'] = "Mantenimiento de \"{data}\"";
$Text['dataman_err_related'] = "Hay datos relacionados en \"{related}\"";


$Text['msg_sel_account'] = "Elige una cuenta primero, después filtra los resultados!";
$Text['msg_err_nomovements'] = "Lo siento, no hay movimientos para la cuenta seleccionada y fecha. Trate de ampliar el periodo de tiempo con el botón de filtro.";
$Text['active_changed_uf'] = "Estado activo de la UF modificado";
$Text['msg_err_mentoruf'] = "¡La UF anfitriona debe ser diferente de ella misma!";
$Text['msg_err_ufexists'] = "La UF ya existe. Por favor, elija otra!";
$Text['msg_err_form_init'] = "Parece que el formulario para crear un nuevo miembro no se ha inicializado correctamente. Recargue la página y inténtelo otra vez...   ";
$Text['ti_mng_hu_members'] = "Gestionar UFs y sus miembros"; 
$Text['list_ufs'] = "Lista de UFs";
$Text['search_members'] = "Búsqueda de miembros";
$Text['member_pl'] = "Miembros";
$Text['mng_members_uf'] = "Gestionar los miembros de la UF ";
$Text['uf_name'] = "Nombre";
$Text['btn_new_member'] = "Nuevo miembro";
$Text['ti_add_member'] = "Añadir nuevo miembro a la UF";
$Text['custom_member_ref'] = "Ref. personalizada";
$Text['theme'] = "Tema";
$Text['member_id'] = "Id del miembro";
$Text['ti_mng_stock'] = "Gestionar stock";
$Text['msg_err_no_stock'] = "Aparentemente este proveedor no tiene stock";
$Text['msg_err_qu'] = "¡La cantidad debe ser numérica y mayor que 0!";
$Text['msg_correct_stock'] = "¡Ajustar el stock de esta forma debería ser una excepción! El stock nuevo tiene que ser siempre AÑADIDO. Está seguro de corregir el stock de este producto? Como se enteren los informáticos le van a.....";
$Text['btn_yes_corret'] = "¡Sí, haz la modificación!";
$Text['search_product'] = "Buscar un producto";
$Text['add_stock'] = "Añadir stock";
$Text['click_to_edit'] = "¡Clica la celda para editar!";
$Text['no_results'] = "La búsqueda no ha producido resultados.";
$Text['for'] = "para"; //as in order FOR Aurora
$Text['date_for_order'] = "Fecha de entrega";
$Text['finished_loading'] = "Carga Finalizada";
$Text['msg_err_unrevised'] = "¡Hay ítems sin revisar en este pedido. Por favor, asegúrese de que todos los productos pedidos han llegado!";
$Text['btn_dis_anyway'] = "Distribuir igualmente";
$Text['btn_remaining'] = "Revisar los pendientes";
$Text['btn_disValitate'] = "Distribuye y valida";
$Text['msg_con_disValitate'] =
    "Distribuir y validar no se puede anular!!:<ul>
    <li>se anotarán los productos como compras de las UF en la fecha del pedido</li>
    <li>y los importes de las compras se anotarán como deuda a las cuentas de las UF</li>
    </ul>";
$Text['msg_con_disValitate_prvInv'] =
    "Distribuir y validar no se puede anular!!:<ul>
    <li>se anotarán los productos como compras de las UF en la fecha del pedido,</li>
    <li>los importes de las compras se anotarán como deuda a las cuentas de las UF</li>
    <li>y el importe del albarán se anotará en la cuenta del proveedor como factura.</li>
    </ul>";
$Text['msg_err_disValitate'] = "Error al distribuir y validar el pedido #";
$Text['msg_err_disVal_nonEmpyCatrs'] =
    "Hay validaciones pendientes para la fecha {date_for_shop}.<br>No es posible \"Distribuir y validar\" para la misma fecha si hay validaciones pendientes!";
$Text['btn_disValitate_ok'] = "Entendidos: distribuye y valida!";
$Text['btn_bakToRevise'] = "Todavía no: quiero seguir revisando";
$Text['btn_disValitate_done'] = "Correcto!<br>El pedido #{order_id} ha sido distribuido y validado.";
$Text['wait_work'] = "Por favor, espera mientras se hace el trabajo...";
$Text['msg_err_edit_order'] = "Este pedido no está completo. Solo se pueden añadir notas y referencias cuando se haya enviado.";
$Text['order_open'] = "El Pedido está abierto";
$Text['finalize_now'] = "Finalizar ahora";
$Text['msg_err_order_filter'] = "No hay pedidos coincidentes con el criterio de búsqueda.";
$Text['msg_finalize'] = "Está a punto de terminar un pedido. Esto significa que ya no podrá hacer modificaciones.  ¿Está seguro de continuar?";
$Text['msg_finalize_open'] = "Este pedido está todavía abierto. Finalizar-lo implica que deberá cerrarlo antes de su fecha límite. ¿Está seguro de continuar?";
$Text['msg_wait_tbl'] = "Las cabeceras de la tabla todavía se están creando. En función de su conexión de Internet puede llevar un tiempo.  Inténtelo otra vez en 5 segundos. ";
$Text['msg_err_invalid_id'] = "¡No se encontró ningún ID válido para el pedido! ¡¡Este pedido no ha sido enviado al proveedor!!";
$Text['msg_revise_revised'] =
    "Los elementos del pedido ya han sido revisados y cargados en las cestas de los usuarios. <br>
    Volverlos a revisar puede interferir con modificaciones hechas por los propios usuarios. <br>
    Las opciones posibles son o <b>modificar</b> la revisión hecha o <b>borrarla</b> y empezar de nuevo.";
$Text['btn_modify'] = "Modificar";
$Text['btn_delete'] = "Borrar";
$Text['wait_reset'] = "Por favor, espere mientras el pedido se reinicia...";
$Text['msg_done'] = "Hecho!";
$Text['msg_err_already_val'] = "Algunos o todos los ítems ya han sido validados! ¡¡Lo siento pero no es posible hacer mas cambios!!";
$Text['print_several'] = "Hay más de un pedido seleccionado. ¿Quieres imprimirlos todos?";
$Text['btn_yes_all'] = "Sí, imprimir todo";
$Text['btn_just_one'] = "No, solo uno";
$Text['ostat_revised'] = "Revisado";
$Text['ostat_finalized'] = "Finalizado";
$Text['set_ostat_arrived'] = "¡Recibido!";
$Text['set_ostat_postpone'] = "¡Postpuesto!";
$Text['set_ostat_cancel'] = "¡Cancelado!";
$Text['ostat_desc_sent'] = "El pedido ha sido enviado al proveedor";
$Text['ostat_desc_nochanges'] = "Revisado y distribuido sin cambios";
$Text['ostat_desc_postponed'] = "El pedido ha sido pospuesto";
$Text['ostat_desc_cancel'] = "El pedido ha sido cancelado";
$Text['ostat_desc_changes'] = "Revisado con algunas modificaciones";
$Text['ostat_desc_incomp'] = "Pedido ignorado. Falta información anterior a la v2.5";
$Text['set_ostat_desc_arrived'] = "La mayoría de los ítems pedidos han llegado.  Procediendo a revisar y distribuir los productos en las cestas...";
$Text['set_ostat_desc_postpone'] = "El pedido no ha llegado en la fecha indicada pero probablemente llegará en las próximas semanas.";
$Text['set_ostat_desc_cancel'] = "Los ítems pedidos no llegaran nunca.";
$Text['msg_move_to_shop'] = "Los ítems han sido distribuidos en la cestas para la fecha indicada.";
$Text['msg_err_noselect'] = "Nada seleccionado!";
$Text['ti_revise'] = "Revisar Pedido";
$Text['btn_revise'] = "Revisar Pedido";
$Text['ti_order_detail'] = "Detalle del pedido para";
$Text['ti_mng_orders'] = "Gestionar Pedidos";
$Text['btn_distribute'] = "¡Distribuir!";
$Text['distribute_desc'] = "Distribuir los ítems del pedido en las cestas";
$Text['filter_orders'] = "Filtrar pedidos";
$Text['btn_filter'] = "Filtrar";
$Text['filter_acc_todays'] = "Movimientos de hoy";
$Text['filter_recent'] = "Recientes";
$Text['filter_year'] = "Último Año";
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
$Text['tit_set_orStatus'] = "Establecer estado del pedido";
$Text['tit_set_shpDate'] = "Establecer fecha de compra";
$Text['msg_cur_status'] = "El estado actual del pedido es";
$Text['msg_change_status'] = "Cambia el estado a";
$Text['msg_confirm_move'] = "¿Está seguro de que quiere hacer disponible el pedido para la compra? Todos los productos asociados seran distribuidos en las cestas para la fecha:";
$Text['alter_date'] = "Escoja una fecha alternativa";
$Text['msg_err_miss_info'] = "Aparentemente este pedido fue creado con una versión más antigua del software que es incompatible con la funcionalidad de revisión actual.  Lo siento, este pedido no puede ser revisado.";


//added 29.09

$Text['order_closes'] = "El pedido se cierra el"; //as in: order closes 20 SEP 2012
$Text['left_ordering'] = " pendientes de pedir."; //as in 4 days left for ordering
$Text['ostat_closed'] = "El pedido está cerrado";
$Text['ostat_desc_fin_send'] = "El pedido se ha cerrado. La referencia es: #";
$Text['msg_err_past'] = "¡Esto es el pasado! <br/> Demasiado tarde para modificar cosas.";
$Text['msg_err_is_deactive_p'] = "Este producto está desactivado. Para abrirlo para una fecha primero debes activarlo.";
$Text['msg_err_deactivate_p'] = "Estás a punto de desactivar el producto. Esto quiere decir que las fechas en que se puede pedir también se eliminaran.<br/><br/>También podrías desactivar las fechas en que se puede pedir clicando en las casillas correspondientes.";
$Text['msg_err_closing_date'] = "¡La fecha de cierre no puede ser posterior a la del pedido!";
$Text['msg_err_sel_col'] = "¡La fecha seleccionada no tiene productos para pedir! Debe establecer un producto para pedir si quiere crear una  plantilla para esta fecha.";
$Text['msg_err_closing'] = "Para modificar la fecha de cierre, hay que poner al menos un producto que puede pedir.";
$Text['msg_err_deactivate_sent'] = "El producto escogido no puede ser (des)activado porque el pedido correspondiente ya ha sido enviado al proveedor. No se pueden hacer mas cambios!";
$Text['view_opt'] = "Mostrar opciones";
$Text['days_display'] = "Número de días a mostrar";
$Text['plus_seven'] = "Mostrar +7 días";
$Text['minus_seven'] = "Mostrar -7 días";
$Text['btn_earlier'] = "Antes de"; //cómo más temprano
$Text['btn_later'] = "después de"; //más tarde... futuro

//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
$Text['pattern_intro'] = "Activa los productos i días para los próximos ";
$Text['pattern_scale'] = "meses ";
$Text['week'] = "cada semana";
$Text['second'] = "cada 15 días";  //2nd 
$Text['third'] = "cada 3 semanas";
$Text['fourth'] = "una vez al mes";
$Text['msg_pattern'] = "¡NOTA: Esta acción regenerará los productos y fechas a partir de esta!";
$Text['sel_closing_date'] = "Elegir una nueva fecha de cierre";
$Text['btn_mod_date'] = "Modificar la fecha de cierre";
$Text['btn_repeat'] = "¡Repetir el patrón!";
$Text['btn_entire_row'] = "Activa/desactiva toda la columna";
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
$Text['ostat_postponed'] = "pospuesto";
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
$Text['withdraw_to_bank'] = "Retirar dinero de caja para el banco";
$Text['withdraw_uf'] = "Retirar dinero de cuenta HU";
$Text['withdraw_cuota'] = "Retirar dinero de cuota";
$Text['msg_err_noorder'] = "No hay pedidos para la selección!";
$Text['primer_torn'] = "Primer Turno";
$Text['segon_torn'] = "Segundo Turno";
$Text['dff_qty'] = "Dif. cantidad";
$Text['dff_price'] = "Dif. precio";
$Text['ti_mgn_stock_mov'] = "Movimientos de stock";
$Text['stock_acc_loss_ever'] = "Perdida acumulada";
$Text['closed'] = "cerrado";
$Text['preorder_item'] = "Este producto forma parte de un pedido acumulativo";
$Text['do_preorder'] = "De-/activar cómo pedido acumulativo";
$Text['do_deactivate_prod'] = "Activar/desactivar producto";
$Text['msg_make_preorder_p'] = "Éste pedido es acumulativo, por lo tanto todavía no tiene fecha de entrega";
$Text['btn_ok_go'] = "OK, adelante!";
$Text['msg_pwd_emailed'] = "La nueva contraseña se ha enviado al usuario";
$Text['msg_pwd_email_reset'] = 'Tu contraseña se ha restablecido.';
$Text['msg_pwd_email_logon'] = 'Identificate como {user} con la nueva contraseña.';
$Text['msg_pwd_email_change'] = 'Podrás cambiar la contraseña en: {menu}.';
$Text['msg_pwd_change'] = "La nueva contraseña es: ";
$Text['msg_err_emailed'] = "¡Ha fallado el envío del email!";
$Text['msg_order_emailed'] = "El pedido se ha enviado correctamente!";
$Text['msg_err_responsible_uf'] = "No hay responsable para éste producto";
$Text['msg_err_finalize'] = "Ups...¡Error al finalizar pedido!";
$Text['msg_err_cart_sync'] = "Tu pedido no está sincronizado con la base de datos; se ha modificado tu cesta mientras estabas comprando. Vuelve a actualizar el pedido.";
$Text['msg_err_no_deposit'] = "La última UF no ha realizado ningún depósito?!!!";
$Text['btn_load_cart'] = "Continua con la siguiente UF";
$Text['btn_deposit_now'] = "Haz el ingreso ahora";
$Text['msg_err_stock_mv'] = "De momento, no han habido movimientos de stock para este producto!";

$Text['ti_report_shop_pv'] = "Total ventas por proveedor";
$Text['filter_all_sales'] = "Todas las ventas";
$Text['filter_exact'] = "Elegir periodo";
$Text['total_4date'] = "Total para la fecha";
$Text['total_4provider'] = "Suma";
$Text['sel_sales_dates'] = "Muestra ventas por proveedor y el periodo seleccionado:";
$Text['sel_sales_dates_ti'] = "Elige un periodo";

$Text['instant_repeat'] = "Repetir directamente";
$Text['msg_confirm_delordereditems'] = "Este producto ya se ha pedido este día. Estás seguro de desactivarlo? Esto  borrará el pedido de las cestas. ";
$Text['msg_confirm_instantr'] = "Quieres repetir la misma acción para el resto de las fechas activas? ";
$Text['msg_err_delorerable'] = "Existe un pedido para este producto y fecha. No se puede borrar.";
$Text['msg_pre2Order'] = "Convierte el pedido acumulativo en un pedido regular. Se crea una fecha de entrega. ";

$Text['msg_err_modified_order'] = "Alguien ha modificado los productos a pedir para fecha actual. Algunos productos que habías pedido ya no están disponibles y desaparecerán de tu carrito una vez recargado.";
$Text['msg_err_modif_order_closed'] = "Se ha intentado modificar algún pedido que ya está cerrado.";
$Text['msg_err_cart_reloaded'] = "Su cesta se mostrará de nuevo.";
$Text['btn_confirm_del'] = "Sí, eliminar!!";
$Text['print_new_win'] = "Ventana nueva";
$Text['print_pdf'] = "Descarga pdf";
$Text['msg_incident_emailed'] = "El incidente se ha enviado por correo correctamente.";
$Text['upcoming_orders'] = "Próximos pedidos";

$Text['msg_confirm_del_mem'] = "Estas seguro de eliminar el usuario de la base de datos??";
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

$Text['search_provider'] = "Buscar proveedor";
$Text['msg_err_export'] = "Error al exportar datos";
$Text['export_uf'] = "Exportar Miembros";
$Text['btn_export'] = "Exportar";

$Text['ti_visualization'] = "Visualizaciones";
$Text['file_name'] = "Nombre de archivo";
$Text['active_ufs'] = "Sólo UF activas";
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
$Text['stock_info_product'] = "Nota: se pueden consultar todos los cambios de stock (adiciones, correcciones, pérdidas totales) desde la sección Informes &gt; Stock.";


$Text['msg_success'] = "Fin correcto";
$Text['msg_confirm'] = "Confirmación";
$Text['msg_warning'] = "Advertencia";
$Text['msg_confirm_prov'] = "¿Seguro que quieres exportar todos los proveedores?"; 
$Text['msg_err_upload'] = "Se ha producido un error en la carga del archivo "; 
$Text['msg_import_matchcol'] = "Hay que hacer coincidir las entradas de la base de datos con las filas de la tabla. Debes asignar la columna que corresponde a "; //+ here then comes the name of the matching column, e.g. custom_product_ref
$Text['msg_import_furthercol'] = "¿Qué otras columnas de la tabla quieres importar además de la columna necesaria?"; 
$Text['msg_import_done'] = 'Se han importado {$rows} líneas.'; 
$Text['msg_import_another'] = "¿Quieres importar otro archivo?"; 
$Text['btn_import_another'] = "Importar otro"; 
$Text['btn_nothx'] = "No, gracias"; 
$Text['direct_import_template'] = "Plantilla de importación directa";
$Text['import_allowed'] = "Formatos compatibles"; //as in allowed file formats
$Text['import_file'] = "Archivo de importación"; 
$Text['public_url'] = "URL pública";
$Text['btn_load_file'] = "Cargar archivo";
$Text['msg_uploading'] = "Se está cargando el archivo y se está generando la vista previa. Espera...";
$Text['msg_parsing'] = "Se está leyendo el archivo del servidor y se está analizando. Espera...";
$Text['import_step1'] = "Selección de archivo";
$Text['import_step2'] = "Vista previa de los datos y asignación de columnas";
$Text['import_reqcol'] = "Columna necesaria";
$Text['import_ignore_rows'] = '(las líneas sin "{$match_field}" se ignorarán)';
$Text['import_ignore_value'] = '(el valor de la columna "{$match_field}" se ignorará)';
$Text['import_auto'] = "Tengo buenas noticias: casi todos los datos (columnas) se han podido reconocer y puedes intentar la importación automática. No obstante, la opción más segura es previsualizar primero el contenido y luego realizar la asignación de columnas de la tabla manualmente.";
$Text['import_qnew'] = "¿Qué quieres hacer con los datos que no existen en la base de datos actual?";
$Text['import_create_update'] = "Crear entradas nuevas y actualizar las existentes";
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
$Text['msg_err_oldPwdWrong'] = "Disculpe, su clave vieja no es correcta. Por favor, inténtelo de nuevo. "; 
$Text['msg_err_adminStuff'] = "Privilegios de acceso insuficientes. ¡Solo un administrador puede hacer eso!";
$Text['set_c_balance'] = "Actualizar balance de la cuenta de consumo";

$Text['msg_err_deactivate_prdrow'] = "Este producto no puede ser desactivado ya que tiene algunas fechas activadas para poder hacer pedidos. Desactive primero individualmente aquestas fechas del producto!";
$Text['msg_err_deactivate_ir'] = "Nos se puede desactivar algunas fechas de este producto ya que hay ítemes pedidos. Desactive usando Repetir directamente o desactive individualmente los ítemes producto/fecha.";
$Text['msg_err_deactivate_product'] = "Hay pedidos abiertos para este producto. Al desactivar eliminará este artículo de los pedidos correspondientes. La eliminación de artículos de un pedido no se puede deshacer.";

$Text['msg_activate_prod_ok'] = "El producto se ha activado con éxito."; 
$Text['msg_deactivate_prod_ok'] = "El producto se ha desactivado con éxito."; 
$Text['msg_activate_prov_ok'] = "El proveedor se ha activado con éxito."; 
$Text['msg_deactivate_prov_ok'] = "El proveedor se ha desactivado con éxito."; 
$Text['no_stock'] = "Sin stock!!";
$Text['stock_mov_type'] = "Tipo de movimiento";

// Orders
$Text['or_prv_prices'] = 'Precios del proveedor (sin imp.rev.)';
$Text['or_gross_price'] = 'Precio';
$Text['or_suma'] = 'Suma';
$Text['or_gross_total'] = 'Imp.Prov.';
$Text['or_net_price'] = 'Precio+IVA';
$Text['or_net_total'] = 'Importe+IVA';
$Text['or_click_to_edit_total'] = 'Clic para ajustar la cantidad total';
$Text['or_click_to_edit_gprice'] = 'Clic para ajustar el precio';
$Text['or_saving'] = 'Guardando';
$Text['or_ostat_desc_validated'] = 'Los productos de este pedido han sido validados';
$Text['os_reopen_order_a'] = "Re-abrir";
$Text['os_reopen_order'] =
    "¿Seguro que deseas re-abrir este pedido?<hr><br>
    NOTA:<br>
    El pedido puede haberse enviado por correo.<br>
    Si se vuelve a abrir
    <b>se debe hablar con el proveedor</b>
    para decirle que el pedido ha sido cancelado!";

?>
