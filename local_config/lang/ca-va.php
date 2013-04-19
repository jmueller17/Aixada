<?php
// Traducció al Català per a l'aixada 
// contribute by Cristóbal Cabeza-Cáceres; Jordi Losantos
// Email: cristobal.cabeza@gmail.com; jordi@losantos.name

require_once(__ROOT__. 'local_config/config.php');


$Text['ca-va'] = 'Català';
$Text['charset'] = "utf-8";
$Text['text_dir'] = "ltr"; // ('ltr' significa d'esquerra a dreta, 'rtl' per aescriure de dreta a esquerra)


/** 
 *  		Elements globals
 */
$Text['coop_name'] = configuration_vars::get_instance()->coop_name;
$Text['please_select'] = "Seleccioneu...";
$Text['loading'] = "Si us plau, espere mentre es carreguen les dades...";
$Text['search'] = "Cerca";
$Text['id'] = "id";
$Text['uf_short'] = "UF";
$Text['uf_long'] = "Unitat familiar";


/** 
 *  		miscelànea
 */
$Text['date_from'] = 'de';
$Text['date_to'] = 'a';
$Text['mon'] = 'Dilluns';
$Text['tue'] = 'Dimarts';
$Text['wed'] = 'Dimecres';
$Text['thu'] = 'Dijous';
$Text['fri'] = 'Divendres';
$Text['sat'] = 'Dissabte';
$Text['sun'] = 'Diumenge';

/**
 * 				seleccions
 */
$Text['sel_provider'] = "Seleccioneu un proveïdor...";
$Text['sel_product'] = "Seleccioneu un producte...";
$Text['sel_user'] = "Seleccioneu un usuari...";
$Text['sel_category'] = "Seleccioneu una categoria de productes...";
$Text['sel_uf'] = "Seleccioneu una unitat familiar...";
$Text['sel_uf_or_account'] = "Seleccioneu unitat familiar o compte...";
$Text['sel_account'] = "Seleccioneu un compte...";
$Text['sel_none'] = "Cap";

/**
 * 				tabs
 */
$Text['by_provider'] = "Per proveïdor";
$Text['by_category'] = "Per categoria de productes";
$Text['special_offer'] = "Comanda acumulativa";
$Text['search_add'] = "Cerca i afegeix un altre article";
$Text['validate'] = "Valida";


/**
 *  			titles for header <title></title>
 */
$Text['global_title'] = configuration_vars::get_instance()->coop_name;
$Text['head_ti_validate'] = "Valida";

$Text['head_ti_active_products'] = "Activa/Desactiva Productes demanables";
$Text['head_ti_active_roles'] = "Rols actius";
$Text['head_ti_account'] = "Comptes";
$Text['head_ti_manage_orders'] = "Gestiona les comandes";
//$Text['head_ti_manage_dates'] = "Estableix dates de comanda";
$Text['head_ti_manage'] = "Gestiona";
$Text['head_ti_manage_uf'] = "Unitats familiars/Membres";
$Text['head_ti_incidents'] = "Incidents";
$Text['head_ti_stats'] = "Estadístiques diàries";
$Text['head_ti_prev_orders'] = "Les meves compres anteriors"; 
$Text['head_ti_cashbox'] = "Control de caixa"; 



/**
 *  			capçaleres de les pàgines principals <h1></h1>
 */
$Text['ti_mng_activate_products'] = "Les UFs podran demanar els següents productes PER EL dia ";
//$Text['ti_mng_arrived_products'] = "Els següents productes han arribat el dia ";
$Text['ti_mng_activate_roles'] = "Gestiona els rols d'usuari ";
$Text['ti_mng_activate_users'] = "Activa usuaris com a ";
//$Text['ti_mng_move_orders'] = "Canvia una comanda";
$Text['ti_mng_activate_preorders'] = "Converteix una comanda acumulativa en comanda";
$Text['ti_mng_members'] = "Gestiona els membres";
//$Text['ti_mng_ufs'] = "Gestiona les unitats familiars";
//$Text['ti_mng_dates'] = "De-/activar individualment dates per fer comandes";
//$Text['ti_mng_dates_pattern'] = "Bulk de-/activar dates per fer comandes";
$Text['ti_mng_db'] = "Còpia de seguretat de la base de dades"; 
$Text['ti_order'] = "Fes la comanda per a ";
$Text['ti_shop'] = "Compra articles ";
//$Text['ti_report_report'] = "Resum de comandes per a "; 
$Text['ti_report_account'] = "Informe de tots els comptes"; 
//$Text['ti_report_my_account'] = "Informe del meu compte "; 
$Text['ti_report_preorder'] = "Resum de comandes acumulatives"; 
$Text['ti_report_incidents'] = "Incidents d'avui";
$Text['ti_incidents'] = "Incidents";
$Text['ti_validate'] = "Valida la compra de la UF";
$Text['ti_stats'] = "Estadístiques totals";
$Text['ti_my_account'] = "Configuració";
$Text['ti_my_account_money'] = "Diners";
//$Text['ti_my_prev_sales'] = "Les meves compres anteriors";
$Text['ti_all_sales'] = "Totes les compres anteriors de la UF";
$Text['ti_login_news'] = "Inici de sessió i notícies";
$Text['ti_timeline'] = "Informe línia de temps";
$Text['ti_report_torn'] = "Resum del torn d'aviu";
//$Text['ti_mng_cashbox'] = "Caixa";




/**
 * 				roles
 */
$Text['Consumer'] = 'Consumidor';
$Text['Checkout'] = 'Caixa';
$Text['Consumer Commission'] = 'Comissió de consum';
$Text['Econo-Legal Commission'] = 'Comissió econolegal';
$Text['Logistic Commission'] = 'Comissió de logística';
$Text['Hacker Commission'] = "Comissió d'informàtica";
$Text['Fifth Column Commission'] = 'La cinquena';
$Text['Producer'] = 'Productor';


/**
 * 				Manage Products / Roles
 */
//$Text['mo_inact_prod'] = "No podran demanar:";
//$Text['mo_act_prod'] = "Si podran demanar:";
//$Text['mo_notarr_prod'] = "Productes que no han arribat:";
//$Text['mo_arr_prod'] = "Productes que si han arribat:";
$Text['mo_inact_role'] = "Rols inactius";
//$Text['mo_act_role'] = "Rols actius";
$Text['mo_inact_user'] = "Usuaris inactius";
$Text['mo_act_user'] = "Usuaris actius";
//$Text['msg_no_report'] = "No s'han trobat productors/productes per a la data!";


/**
 * 				uf member manage
 */
$Text['search_memberuf'] = "Cerca un nom o inici de sessió";
$Text['browse_memberuf'] = "Navega";
$Text['assign_members'] = "Assigna membres";
$Text['login'] = "Inici de sessió";
$Text['create_uf'] = "Nova unitat familiar";
$Text['name_person'] = "Nom";
$Text['address'] = "Adreça";
$Text['zip'] = "CP";
$Text['city'] = "Localitat";
$Text['phone1'] = "Telèfon1";
$Text['phone2'] = "Telèfon2";
$Text['email'] = "Correu-e";
$Text['web'] = "URL";
$Text['last_logon'] = "Últim accés";
$Text['created'] = "Creat el dia";
$Text['active'] = "Actiu";
$Text['participant'] = "Participant";
$Text['roles'] = "Rols";
$Text['active_roles'] = "Rols actius";
$Text['products_cared_for'] = "Productes de què sóc responsable";
$Text['providers_cared_for'] = "Proveïdors de què sóc responsable";
$Text['notes'] = "Observacions";
$Text['edit_uf'] = "Edita UF";
//$Text['members_uf'] = "Membres de la unitat familiar";
$Text['mentor_uf'] = "Unitat familiar amfitriona";
$Text['unassigned_members'] = "Membres no assignats";
$Text['edit_my_settings'] = "Edita la meva configuració";

$Text['nif'] = "NIF";
$Text['bank_name'] = "Banc o Caixa";
$Text['bank_account'] = "Compte bancari";
$Text['picture']  = "Imatge";
$Text['offset_order_close'] = "Temps de procés";
$Text['iva_percent_id'] = "Tipus d'IVA";
$Text['percent'] = "Percentatge";
$Text['adult'] = "Adult";


/**
 *  			wiz stuff
 */
$Text['deposit_cashbox'] = 'Ingrés a caixa';
$Text['widthdraw_cashbox'] = 'Retirar diners de la caixa';
$Text['current_balance'] = 'Balanç actual';
$Text['deposit_type'] = "Concepte d'ingrés";
$Text['deposit_by_uf'] = 'Ingrés per la UF';
$Text['deposit_other'] = 'Altres tipus de ingressos...';
$Text['make_deposit_4HU'] = 'Ingrés de la UF';
$Text['short_desc'] = 'Breu descripció';
$Text['withdraw_type'] = 'Tipus de reintegrament';
$Text['withdraw_for_provider'] = 'Fer un reintegrament a un proveïdor';
$Text['withdraw_other'] = 'Altres reintegraments...';
$Text['withdraw_provider'] = 'Proveïdor del reintegrament';
$Text['btn_make_withdrawal'] = 'Reintegrament';
$Text['correct_balance'] = 'Corregir balanç';
$Text['set_balance'] = 'Actualitzar balanç de la caixa';
$Text['name_cash_account'] = 'Caixa';




/**
 *				validar
 */
$Text['set_date'] = "Defineix una data";
$Text['get_cart_4_uf'] = "Recupera la compra de la unitat familiar";
$Text['make_deposit'] = "Ingrés de la unitat familiar";
$Text['success_deposit'] = "L'ingrés s'ha fet correctament";
$Text['amount'] = "Quantitat";
$Text['comment'] = "Comentari";
//$Text['deposit_other_uf'] = "Fes un ingrés per a una altra unitat familiar o compte";
$Text['latest_movements'] = "Darrers moviments";
$Text['time'] = "Hora";
$Text['account'] = "Compte";
$Text['consum_account'] = "Compte de Consum";
$Text['operator'] = "Usuari";
$Text['balance'] = "Balanç";
$Text['dailyStats'] = "Estadístiques diàries";
$Text['totalIncome'] = "Ingressos totals";
$Text['totalSpending'] = "Despeses totals";
$Text['negativeUfs']= "Unitats familiars en negatiu";
$Text['lastUpdate']= "Última actualització";
$Text['negativeStock']="Productes amb estoc negatiu";
$Text['curStock'] = "Estoc actual";
$Text['minStock'] = "Estoc mínim";
$Text['stock'] = "Estoc";



/**
 *              Shop and order
 */

$Text['info'] = "Info";
$Text['quantity'] = "Quantitat";
$Text['unit'] = "Unitat";
$Text['price'] = "Preu";
$Text['name_item'] = "Nom";
$Text['revtax_abbrev'] = "ImpRev";
//$Text['cur_stock'] = "Estoc actual";
$Text['date_for_shop'] = 'Data de compra';
$Text['ts_validated'] = 'Validada';

/**
 * 		Logon Screen
 */ 
$Text['welcome_logon'] = "Benvinguts/des a " . configuration_vars::get_instance()->coop_name . "!";
$Text['logon'] = "Usuari";
$Text['pwd']	= "Contrasenya";
$Text['old_pwd'] = "Contrasenya antiga";
$Text['retype_pwd']	= "Torneu a escriure la contrasenya";
$Text['lang']	= "Llengua";
$Text['msg_err_incorrectLogon'] = "Accés incorrecte";
$Text['msg_err_noUfAssignedYet'] = "Encara no estàs assignat a cap UF. Si us plau, demana a algú que et registri.";


//$Text['msg_reg_success'] = "Us heu registrat correctament, però el vostre usuari encara no s'ha aprovat. Registreu la resta de membres de la vostra UF i contacteu amb un responsable per finalitzar el registre.";
//$Text['register'] = "Registre";
$Text['required_fields'] = " són camps obligatoris";


/**
 *			Navigation
 */
$Text['nav_home'] = "Inici";
$Text['nav_wiz'] = "Torn";
//  $Text['nav_wiz_arrived'] = "Productes que no han arribat";
	$Text['nav_wiz_validate'] = "Valida";
//  $Text['nav_wiz_open'] = "Obre";
//  $Text['nav_wiz_close'] = "Tanca";
  $Text['nav_wiz_torn'] = "Resum torn";
	$Text['nav_wiz_cashbox'] = "Caixa";
$Text['nav_shop'] = "Compra avui";
$Text['nav_order'] = "Propera comanda";
$Text['nav_mng'] = "Gestiona";
	//$Text['nav_mng_uf'] = "Unitats familiars";
	$Text['nav_mng_member'] = "Membres";
	$Text['nav_mng_providers'] = "Proveïdors";
	$Text['nav_mng_products'] = "Productes";
		$Text['nav_mng_deactivate'] = "Activa/desactiva productes";
		$Text['nav_mng_stock'] = "Estoc";
		$Text['nav_mng_units'] = "Unitats";
	$Text['nav_mng_orders'] = "Comandes";
		//$Text['nav_mng_setorderable'] = "Establiu dates ordenables";
		//$Text['nav_mng_move'] = "Canvia la data de la comanda";
		//$Text['nav_mng_orders_overview'] = "Gestiona comandes";
		$Text['nav_mng_preorder'] = "Converteix la comanda acumulativa en comanda";
	$Text['nav_mng_db'] = "Backup bd";
	$Text['nav_mng_roles'] = "Rols";
$Text['nav_report'] = "Informes";
//$Text['nav_report_order'] = "Comanda actual";
$Text['nav_report_account'] = "Comptes";
$Text['nav_report_timelines'] = "Evolució " . configuration_vars::get_instance()->coop_name;
$Text['nav_report_timelines_uf'] = "Per UFs";
$Text['nav_report_timelines_provider'] = "Per proveïdors";
$Text['nav_report_timelines_product'] = "Per productes";
$Text['nav_report_daystats'] = "Estadístiques diàries";
$Text['nav_report_preorder'] = "Comandes acumulatives";
$Text['nav_report_incidents'] = "Incidents d'avui";
$Text['nav_report_shop_hu'] = "Per UF";
$Text['nav_report_shop_pv'] = "Per Proveïdor";

$Text['nav_incidents'] = "Incidents";
	$Text['nav_browse'] = "Navega / afegeix";
$Text['nav_myaccount'] = "El meu compte";
	$Text['nav_myaccount_settings'] = "Configuració";
	$Text['nav_myaccount_account'] = "Diners";
	$Text['nav_changepwd'] = "Canvia la contrasenya"; 
	$Text['nav_prev_orders'] = "Compres anteriors";

$Text['nav_logout'] = "Surt/Ix";
$Text['nav_signedIn'] = "Heu accedit com a ";
$Text['nav_can_checkout'] = "Podeu validar compres.";
$Text['nav_try_to_checkout'] = "Comença a validar";
$Text['nav_stop_checkout'] = "Deixa de validar";



/**
 *			Buttons
 */
$Text['btn_login'] = "Inicia la sessió";
$Text['btn_submit'] = "Envia";
$Text['btn_save'] = "Desa";
$Text['btn_reset'] = "Esborra";
$Text['btn_cancel'] = "Cancel·la";
$Text['btn_activate'] = "Activa";
$Text['btn_deactivate'] = "Desactiva";
//$Text['btn_arrived'] = "Ha arribat";
//$Text['btn_notarrived'] = "No ha arribat";
//$Text['btn_move'] = "Canvia de data";
$Text['btn_ok'] = "D'acord";
$Text['btn_assign'] = "Assigna";
$Text['btn_create'] = "Crea";
$Text['btn_close'] = "Tanca";
$Text['btn_make_deposit'] = "Ingressa";
$Text['btn_new_incident'] = "Incident nou";
$Text['btn_reset_pwd'] = "Reestableix contrasenya"; 
$Text['btn_view_cart'] = "Cistella"; 
$Text['btn_view_cart_lng'] = "Només veure la cistella";
$Text['btn_view_list'] = "Productes";
$Text['btn_view_list_lng'] = "Només veure els productes";
$Text['btn_view_both'] = "Tot";
$Text['btn_view_both_lng'] = "Veure tant productes com cistella";
$Text['btn_repeat'] = "Entesos, repetim això!";
$Text['btn_repeat_single'] = "No, únicament aquest"; 
$Text['btn_repeat_all'] = "Sí a tots"; 




/**
 * Incidents
 */
$Text['create_incident'] = "Crea un incident";
$Text['overview'] = "Resum";
$Text['subject'] = "Assumpte";
$Text['message'] = "Missatge";
$Text['priority'] = "Prioritat";
$Text['status'] = "Estat";
$Text['incident_type'] = "Tipus";
$Text['status_open'] = "Obert";
$Text['status_closed'] = "Tancat";
$Text['ufs_concerned'] = "UFs afectades";
$Text['provider_concerned'] = "Per al proveïdor";
$Text['comi_concerned'] = "Per a la comissió";
$Text['created_by'] = "Creat per";
$Text['edit_incident'] = "Edita l'incident";

/**
 *  Reports
 */
$Text['provider_name'] = "Proveïdor";
$Text['product_name'] = "Producte";
//$Text['qty'] = "Quantitat";
$Text['total_qty'] = "Quantitat total";
$Text['total_price'] = "Preu total";
$Text['total_amount'] = "Suma total";
//$Text['select_order'] = "Mostra les comandes per a la data següent:";
//$Text['move_success'] = "Les comandes de la llista estan actives per a: ";
//$Text['show_compact'] = "Mostra la llista reduïda";
//$Text['show_all_providers'] = "Expandeix els productes";
//$Text['show_all_print'] = "Expandeix les impressions";
$Text['nr_ufs'] = "UFs total";
$Text['printout'] = "Imprimeix";
$Text['summarized_orders'] = "Resum de la comanda";
$Text['detailed_orders'] = "Detalls de la comanda";


/**
 * 		Error / Warning Messages
 */
$Text['msg_err_incorrectLogon'] = "L'usuari o la contrasenya són incorrectes. Intenteu-ho de nou!";
$Text['msg_err_pwdctrl'] = "Les contrasenyes no coincideixen. Escriviu-les de nou!";
$Text['msg_err_usershort'] = "El nom d'usuari és massa curt. Ha de tindre un mínim de tres caràcters";
$Text['msg_err_userexists'] = "El nom d'usuari ja està agafat. Trieu-ne un altre.";
$Text['msg_err_passshort'] = "La contrasenya és massa curta. Ha de tenir entre 4 i 15 caràcters";
$Text['msg_err_notempty'] = " no pot estar buit!"; 
$Text['msg_err_namelength'] = "El nom i cognom no poden estar buits i no poden tenir més de 255 caràcters!"; 
$Text['msg_err_only_num'] = " només accepta xifres i no pot estar buit!"; 
$Text['msg_err_email'] = "El format del correu-e no és correcte. Ha de ser del tipus nom@domini.com o semblant a això.";
//$Text['msg_err_select_uf'] = "Per assignar un membre nou a una UF primer heu de seleccionar la UF fent-hi clic! Si voleu crear una UF nova, feu-ho fent clic en + Nova UF.";
//$Text['msg_err_select_non_member'] = "Per assignar un membre nou a una UF, primer heu de seleccionar-lo de la llista de no membres que hi ha a la dreta!"; 
//$Text['msg_err_insufficient_stock'] = 'No hi ha prou estoc de ';


$Text['msg_edit_success'] = "Les dades editades s'han desat correctament!";
//$Text['msg_edit_mysettings_success'] = "La nova configuració s'ha desat correctament!";
$Text['msg_pwd_changed_success'] = "La contrasenya s'ha canviat correctament!"; 
$Text['msg_confirm_del'] = "Segur que voleu eliminar aquest membre?";
$Text['msg_enter_deposit_amount'] = "El camp de quantitat de l'ingrés només accepta xifres i no pot estar buit!";
$Text['msg_please_set_ufid_deposit'] = "No s'ha definit l'ID de la UF. Heu de triar una cistella o seleccionar una altra UF per fer el dipòsit!";
//$Text['msg_error_deposit'] = "S'ha produït un error en fer l'ingrés. Intenteu-ho de nou. Els ingressos que s'han fet correctament apareixen en la llista de comptes. <br/>L'error ha sigut: ";
$Text['msg_deposit_success'] = "El dipòsit s'ha fet correctament!";
$Text['msg_withdrawal_success'] = "El pagament s'ha fet correctament!";
$Text['msg_select_cart_first'] = "Per afegir articles per validar abans heu de seleccionar una UF o una cistella!";
//$Text['msg_err_move_date'] = "S'ha produït un error mentre es canviava la data de la comanda. Intenteu-ho de nou.";
$Text['msg_no_active_products'] = "En aquests moments no hi ha productes actius per fer la comanda!";
//$Text['msg_no_movements'] = "No hi ha moviments per al compte i la data seleccionats!"; 
$Text['msg_delete_incident'] = "Segur que voleu eliminar aquest incident?";
//$Text['msg_err_selectFirstUF'] = "No hi ha UF seleccionada. Selecciona una primer i després les seves compres."; //ADDED JAN 2012


/**
 *  Product categories
 */
$Text['SET_ME'] 			= 'SET_ME';

$Text['prdcat_vegies']		 	= "Verdures";
$Text['prdcat_fruit'] 			= "Fruita";
$Text['prdcat_mushrooms'] 		= "Bolets";
$Text['prdcat_dairy'] 			= "Llet i iogurts"; 			//llet fresca, iogurt
$Text['prdcat_meat'] 			= "Carn";							//pollastre, vedella, xai/corder, etc.
$Text['prdcat_bakery'] 			= "Productes de forn i farina";						//pa, pastes, farina
$Text['prdcat_cheese'] 			= "Formatge";
$Text['prdcat_sausages'] 		= "Embotits";					//pernil, botifarres, etc.
$Text['prdcat_infant'] 			= "Nutrició infantil";
$Text['prdcat_cereals_pasta']	= "Cereals i pasta";	//cereals i pasta
$Text['prdcat_canned'] 			= "Conserves";
$Text['prdcat_cleaning'] 		= "Neteja";					//neteja de la llar, detergents, etc.
$Text['prdcat_body'] 			= "Productes corporals";
$Text['prdcat_seasoning'] 		= "Adobs i algues";
$Text['prdcat_sweets'] 			= "Mel i dolços";		//melmelada, mel, sucre, xocolata
$Text['prdcat_drinks_alcohol'] 	= "Begudes alcohòliques";			//vi, cervesa, etc.
$Text['prdcat_drinks_soft'] 	= "Begudes no alcohòliques";			//suc, begudes vegetals
$Text['prdcat_drinks_hot'] 		= "Cafè i te";
$Text['prdcat_driedstuff'] 		= "Coses per a picar i fruits secs";
$Text['prdcat_paper'] 			= "Cel·lulosa i paper";		//mocadors, paper del vàter, paper de cuina 
$Text['prdcat_health'] 			= "Salut";		//paper del vàter, paper de cuina 1
$Text['prdcat_misc']			= "Tota la resta..." ;





/**
 *  Field names in database
 */

$Text['name'] = 'Nom';
$Text['contact'] = 'Contacte';
$Text['fax'] = 'Fax';
$Text['responsible_mem_name'] = 'Membre responsable';
$Text['responsible_uf'] = 'Unitat familiar responsable';
$Text['provider'] = 'Proveïdor';
$Text['description'] = 'Descripció';
$Text['barcode'] = 'Codi de barres';
$Text['orderable_type'] = 'Tipus de producte';
$Text['category'] = 'Categoria';
$Text['rev_tax_type'] = "Tipus d'impost revolucionari";
$Text['unit_price'] = 'Preu per unitat';
$Text['iva_percent'] = "Percentatge d'IVA";
$Text['unit_measure_order'] = 'Unitat de comanda';
$Text['unit_measure_shop'] = 'Unitat de venda';
$Text['stock_min'] = 'Quantitat mínima per tindre en estoc';
$Text['stock_actual'] = 'Quantitat actual en estoc';
$Text['delta_stock'] = "Diferència amb l'estoc mínim";
$Text['description_url'] = "URL de descripció";


/**
 * afegits després 14.5
 */
$Text['msg_err_validate_self'] = 'No pots validar-te a tu mateix';
$Text['msg_err_preorder'] = 'La comanda acumulativa ha de ser amb data futura!';
$Text['msg_preorder_success'] = "La comanda acumulativa s'ha activat correctament per a la data:";
//$Text['msg_can_be_ordered'] =  'Es pot fer comanda en aquesta data';
//$Text['msg_has_ordered_items'] = 'Hi ha comandes per aquest dia. No es poden esborrar, només moure de data';
//$Text['msg_today'] = 'Avui';
//$Text['msg_default_day'] = 'Dies sense comanda encara';
//$Text['activate_for_date'] = 'Activa per al ';
//$Text['start_date'] = 'Mostra els registres començant pel ';


//$Text['Download zip'] = 'Baixar fitxer amb totes les comandes';
$Text['product_singular'] = 'producte';
$Text['product_plural'] = 'productes';
$Text['confirm_db_backup'] = 'Segur que vols copiar tota la base de dades? Això trigarà una estona...';
$Text['show_date_field'] = "Prem aquí per a obrir el calendari i triar una data que no sigui la d'avui.";


$Text['purchase_current'] = 'Les meves compres';
//$Text['items_bought'] = "Compres anteriors";
$Text['purchase_future'] = 'Les meves comandes';
//$Text['purchase_prev'] = 'Compres anteriors';
$Text['icon_order'] = 'La propera comanda';
$Text['icon_purchase'] = 'Fes la teva compra';
$Text['icon_incidents'] = 'Fes una incidencia';
$Text['purchase_date'] = 'Data de la compra';
//$Text['purchase_validated'] = 'Data de la validació';
//$Text['ordered_for'] = 'Comanda pel';
$Text['not_validated'] = 'sense validar';






/* Novetats */

$Text['download_db_zipped'] = 'Descarrega base de dades comprimida';
$Text['backup'] = 'Entesos, copia la base de dades!';
$Text['filter_incidents'] = 'Filtra incidents';
$Text['todays'] = "d'avui";
$Text['recent_ones'] = 'Recents';
$Text['last_year'] = 'Últim any';
$Text['details'] = 'Detalls';
$Text['actions'] = 'Accions';
$Text['incident_details'] = "Detalls de l'incident";
$Text['distribution_level'] = 'Nivell de distribució';
$Text['internal_private'] = 'Intern (privat)';
$Text['internal_email_private'] = 'Intern + email (privat)';
$Text['internal_post'] = 'Intern + envia al portal (públic)';
$Text['internal_email_post'] = 'Intern + email + envia (públic)';

$Text['date'] = "Data";
$Text['iva'] = "IVA";
$Text['expected'] = 'Esperat';
$Text['not_yet_sent'] = "Pendent d'enviar";
$Text['ordered_for'] = 'Demanat per';
$Text['my_orders'] = 'Les meves comandes';
$Text['my_purchases'] = 'Les meves compres';
$Text['loading_status_info'] = "Carregant informació d'estat...";
$Text['previous'] = 'Anterior';
$Text['next'] = 'Següent';
$Text['date_of_purchase'] = 'Data de compra';
$Text['validated'] = 'Validat';
$Text['total'] = 'Total';
$Text['ordered'] = 'Demanat';
$Text['delivered'] = 'Entregat';
$Text['price'] = 'Preu';
$Text['qu'] = 'Qu';
$Text['msg_err_deactivatedUser'] = "El teu compte d'usuari ha estat desactivat!";
$Text['order'] = 'Comanda';
$Text['order_pl'] = 'Comandes';
$Text['msg_already_validated'] = 'La cistella sel·leccionada ja està validada. Vols veure els productes que hi ha?';
$Text['validated_at'] = "Validat en data "; //refers to a date/hour


$Text['nothing_to_val'] = "Res a validar per a la UF";
$Text['cart_id'] = "Id cistella";
$Text['msg_several_carts'] = "La UF seleccionada té més d'una cistella pendent de validar. Tria'n una si us plau:";
$Text['transfer_type'] = "Tipus";
$Text['todays_carts'] = "Cistelles d'avui";
$Text['head_ti_torn'] = "Resum del torn"; 
$Text['btn_validate'] = "Validar";
$Text['desc_validate'] = "Validar cistelles actuals i antigues de les UF. Ingressar efectiu.";
$Text['nav_wiz_revise_order'] = "Revisar";
$Text['desc_revise'] = "Revisar comandes individuals; verificar si han arribat els productes i ajustar les quantitats si cal. Distribuir la comanda en cistelles individuals.";    
$Text['desc_cashbox'] = "Efectuar ingressos i reintegraments d'efectiu. A l'inici del primer torn cal inicialitzar el balanç. L'import d'aquest compte ha de reflectir els diners disponibles reals.";
$Text['desc_stock'] = "Regularitza l'estoc dels productes.";
$Text['desc_print_orders'] = "Imprimir i descarregar comandes per a la propera setmana. Les comandes s'han de completar, imprimir i descarregar en un arxiu zip.";
$Text['nav_report_status'] = "Estadístiques";
$Text['desc_stats'] = "Descarrega un informe resumit del torn actual, incloent-hi incidents, UFs en negatiu, despesa total i productes amb estoc negatiu";
$Text['order_closed'] = "La comanda d'aquest proveïdor està tancada.";
$Text['head_ti_sales'] = "Llistat de vendes"; 
$Text['not_yet_val'] = "pendent de validar";
$Text['val_by'] = "Validat per";
$Text['purchase_details'] = "Detall de la compra de la cistella nº";
$Text['filter_uf'] = "Filtra per UF";
$Text['purchase_uf'] = "Compra de la UF";
$Text['quantity_short'] = "Qu";
$Text['incl_iva'] = "incl. IVA";
$Text['incl_revtax'] = "incl. ImpRev";
$Text['no_news_today'] = "Cap notícia és la millor notícia: avui no hi ha hagut incidents!";
$Text['nav_mng_iva'] = "tipus d'IVA";
$Text['nav_mng_money'] = "Diners";
$Text['nav_mng_admin'] = "Admin";
$Text['nav_mng_users'] = "Usuaris";
$Text['nav_mng_access_rights'] = "Permisos d'acccés";
$Text['msg_sel_account'] = "Tria un compte primer, i aleshores filtra'n els resultats!";
$Text['msg_err_nomovements'] = "Mala sort, no hi ha moviments per a aquest compte en aquesta data. Prova a ampliar el període consultat amb el botó de filtre.";
$Text['active_changed_uf'] = "Estat actiu de la UF modificat";
$Text['msg_err_mentoruf'] = "La UF no pot amfitriona de sí mateixa!";
$Text['msg_err_ufexists'] = "Ja existeix aquest nom d'UF. Tria'n un altre si us plau!";
$Text['msg_err_form_init'] = "Sembla que el formulari per a crear noves UF no s'ha inicialitzat correctament. Torna a carregar la pàgina...   ";
$Text['ti_mng_hu_members'] = "Gestiona Unitats Familiars i els seus membres"; 
$Text['list_ufs'] = "Llistat d'Unitats Familiars";
$Text['search_members'] = "Busca un membre";
$Text['member_pl'] = "Membres";
$Text['mng_members_uf'] = "Gestiona els membres de la Unitat Familiar ";
$Text['uf_name'] = "Nom";
$Text['btn_new_member'] = "Nou membre";
$Text['ti_add_member'] = "Afegir nou membre a la UF";
$Text['custom_member_ref'] = "Ref. personalitzada";
$Text['theme'] = "Tema";
$Text['member_id'] = "Id membre";
$Text['ti_mng_stock'] = "Gestiona estoc";
$Text['msg_err_no_stock'] = "sembla que aquest proveïdor no té estoc";
$Text['msg_err_qu'] = "La quantitat ha de ser numèrica i superior a 0!";
$Text['msg_correct_stock'] = "Ajustar així l'estoc hauria de ser l'excepció! El nou estoc s'hauria d'AFEGIR. Estàs segur de voler corregir l'estoc?";
$Text['btn_yes_corret'] = "Sí, fes la correcció!";
$Text['ti_mng_stock'] = "Gestiona estoc";
$Text['search_product'] = "Cerca un producte";
$Text['add_stock'] = "Afegeix estoc";
$Text['click_to_edit'] = "Prem una cel·la per a editar-la!";
$Text['no_results'] = "No hi ha resultats de la cerca.";
$Text['for'] = "per a"; //as in order FOR Aurora
$Text['date_for_order'] = "Data d'entrega";
$Text['finished_loading'] = "Càrrega acabada";
$Text['msg_err_unrevised'] = "Encara hi ha element pendents de revisar a la comanda. Si us plau, verifica que hagin arribat tots els productes!";
$Text['btn_dis_anyway'] = "Distribueix igualment";
$Text['btn_remaining'] = "Revisar els pendents";
$Text['msg_err_edit_order'] = "Aquesta comanda no està completada. Només pots desar les notes i referències quan la comanda s'hagi enviat.";
$Text['order_open'] = "La comanda està oberta";
$Text['finalize_now'] = "Finalitza ara";
$Text['msg_err_order_filter'] = "No hi ha comandes coincidents amb el filtre.";
$Text['msg_finalize'] = "Estàs a punt de finalitzar la comanda. Si ho fas, ja no podràs fer modificacions a la comanda. Estàs segur de voler continuar?";
$Text['msg_finalize_open'] = "Aquesta comanda encara està oberta. Si la finalitzes ara, l'estaràs tancant abans de la seva data límit. Estàs segur de voler continuar?";
$Text['msg_wait_tbl'] = "L'encapçalament de la taula s'està construïnt. Això pot trigar una mica en funció de la velocitat del teu navegador. Torna-ho a provar en 5 segons. ";
$Text['msg_err_invalid_id'] = "No s'ha trobat un ID de la comanda! Aquesta comanda no s'ha enviat al proveïdor!!";
$Text['msg_revise_revised'] = "Els elements de la comanda ja han estat revisats i carregats a les cistelles dels usuaris per a la data indicada. Tornar-los a revisar alteraria les modificacions ja fetes, i podria interferir amb les modificacions fetes pels propis usuaris. <br/><br/> Estàs segur de voler continuar?! <br/><br/>Si acceptes s'eliminaran els articles de les cistelles, i el procés de revisió començarà de nou.";
$Text['wait_reset'] = "Si us plau, espera mentre re-inicialitzo la comanda...";
$Text['msg_err_already_val'] = "Alguns dels elements de la comanda ja han estat validats! Em sap greu, però ja no s'hi poden fer modificacions!!";
$Text['print_several'] = "Hi ha més d'una comanda sel·leccionada. Vols imprimir-les totes?";
$Text['btn_yes_all'] = "Sí, imprimeix-les totes";
$Text['btn_just_one'] = "No, només una";
$Text['ostat_revised'] = "Revisat";
$Text['ostat_finalized'] = "Finalitzat";
$Text['set_ostat_arrived'] = "Rebut!";
$Text['set_ostat_postpone'] = "Posposat!";
$Text['set_ostat_cancel'] = "Cancel·lat!";
$Text['ostat_desc_sent'] = "La comanda s'ha enviat al proveïdor";
$Text['ostat_desc_nochanges'] = "Revisat i distribuït sense canvis";
$Text['ostat_desc_postponed'] = "La comanda s'ha posposat";
$Text['ostat_desc_cancel'] = "La comanda s'ha cancel·lat";
$Text['ostat_desc_changes'] = "Revisat amb modificacions";
$Text['ostat_desc_incomp'] = "Comanda ignorada. Falta informació anterior a la v2.5";
$Text['set_ostat_desc_arrived'] = "La majoria o tots els productes han arribat. Procedir amb la revisió i distribució de productes a les cistelles...";
$Text['set_ostat_desc_postpone'] = "La comanda no ha arribat a la data esperada, però probalement ho faci en les properes setmanes.";
$Text['set_ostat_desc_cancel'] = "Els productes no arribaran mai.";
$Text['msg_move_to_shop'] = "Els articles han estat carregats a les cistelles de la compra de les dates corresponents.";
$Text['msg_err_noselect'] = "No has seleccionat res!";
$Text['ti_revise'] = "Revisar comanda";
$Text['btn_revise'] = "Revisar comanda";
$Text['ti_order_detail'] = "Detalls de la comanda per";
$Text['ti_mng_orders'] = "Gestiona comandes";
$Text['btn_distribute'] = "Distribueix!";
$Text['distribute_desc'] = "Transfereix els articles a les cistelles";
$Text['filter_orders'] = "Filtra comandes";
$Text['btn_filter'] = "Filtra";
$Text['filter_acc_todays'] = "Moviments d'avui";
$Text['filter_recent'] = "Recents";
$Text['filter_year'] = "Darrer any";
$Text['filter_all'] = "Tots";
$Text['filter_expected'] = "Esperats per avui";
$Text['filter_next_week'] = "Propera setmana";
$Text['filter_future'] = "Totes les comandes futures";
$Text['filter_month'] = "Darrer mes";
$Text['filter_postponed'] = "Posposades";
$Text['with_sel'] = "Amb selecionats...";
$Text['dwn_zip'] = "Descarrega en zip";
$Text['closes_days'] = "Es tanca en dies";
$Text['sent_off'] = "Enviat al proveïdor";
$Text['date_for_shop'] = "Data de compra";
$Text['order_total'] = "Total comanda";
$Text['nie'] = "NIE";
$Text['total_orginal_order'] = "Comanda original";
$Text['total_after_revision'] = "Després de revisió";
$Text['delivery_ref'] = "Referència d'entrega";
$Text['payment_ref'] = "Referència de pagament";
$Text['arrived'] = "arribat"; //as in order items have arrived. this is a table heading
$Text['msg_cur_status'] = "L'estat de la comanda és";
$Text['msg_change_status'] = "Canvia l'estat de la comanda a un dels següents";
$Text['msg_confirm_move'] = "Estàs segur que vols fer aquesta comanda disponible per a la venda? Tots els productes disponibles es posaran a les cistelles de la data:";
$Text['alter_date'] = "Tria una data alternativa";
$Text['msg_err_miss_info'] = "Sembla que aquesta comanda prové d'una versió anterior de la plataforma, incompatible amb la nova funcionalitat de revisió. Em sap greu, però no es pot revisar aquesta comanda.";


$Text['order_closes'] = "La comanda es tanca el"; //as in: order closes 20 SEP 2012
$Text['left_ordering'] = " pendents per demanar."; //as in 4 days left for ordering
$Text['ostat_closed'] = "La comanda està tancada";
$Text['ostat_desc_fin_send'] = "La comanda s'ha finalitzat i enviat al proveïdor. La referència és: #";
$Text['msg_err_past'] = "Això és al passat! <br/> Massa tard per a modificar-hi coses.";
$Text['msg_err_is_deactive_p'] = "Aquest producte està desactivat. Per a obrir-lo per a una data primer l'has d'activar.";
$Text['msg_err_deactivate_p'] = "Estàs a punt de desactivar el producte. Això vol dir que les dates en que és demanable també s'eliminaran.<br/><br/>Estàs segur que vols desactivar el producte? També podries desactivar les dates en que és demanable, clicant a les caselles corresponents.";
$Text['msg_err_closing_date'] = "La data de tancament no pot ser posterior a la de la comanda!";
$Text['msg_err_sel_col'] = "La data seleccionada no té productes demanables! Has d'establir un producte demanable si vols crear una  plantilla per a aquesta data.";
$Text['msg_err_closing'] = "Per a modificar la data de tancament, hi has de posar al menys un producte demanable.";
$Text['msg_err_deactivate_sent'] = "El producte triat no pot ser (des)activat perquè la comanda corresponent ja ha estat enviada al proveïdor. No s'hi poden fer més canvis!";
$Text['view_opt'] = "Mostra opcions";
$Text['days_display'] = "Nombre de dies a mostrar";
$Text['plus_seven'] = "Mostra +7 dies";
$Text['minus_seven'] = "Mostra -7 dies";
$Text['btn_earlier'] = "Abans de"; //cómo más temprano
$Text['btn_later'] = "després de"; //más tarde... futuro

//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
$Text['pattern_intro'] = "Activa els productes i dia per als propers ";
$Text['pattern_scale'] = "mesos ";
$Text['week'] = "cada setmana";
$Text['second'] = "cada 15 dies";  //2nd 
$Text['third'] = "cada 3 setmanes";
$Text['fourth'] = "un cop al mes";
$Text['msg_pattern'] = "NOTA: Aquesta acció regenerarà els productes i dates a partir d'aquesta!";
$Text['sel_closing_date'] = "Tria una nova data de tancament";
$Text['btn_mod_date'] = "Modifica la data de tancament";
$Text['btn_repeat'] = "Repeteix el patró!";
$Text['btn_entire_row'] = "(Des)activa tota la fila";
$Text['btn_deposit'] = "Dipòsit";
$Text['btn_withdraw'] = "Reintegrament";
$Text['deposit_desc'] = "Ingrés en efectiu";
$Text['withdraw_desc'] = "Reintegrament de la caixa";
$Text['btn_set_balance'] = "Estableix el balanç";
$Text['set_bal_desc'] = "Reinicia el balanç al principi del primer torn.";
$Text['maintenance_account'] = "Manteniment";
$Text['posted_by'] = "Creat per"; //Posted by
$Text['ostat_yet_received'] = "pendent de rebre";
$Text['ostat_is_complete'] = "està complet";
$Text['ostat_postponed'] = "posposat";
$Text['ostat_canceled'] = "cancel·lat";
$Text['ostat_changes'] = "amb canvis";
$Text['filter_todays'] = "D'avui";
$Text['bill'] = "Factura";
$Text['member'] = "Membre";
$Text['cif_nif'] = "CIF/NIF"; //CIF/NIF
$Text['bill_product_name'] = "Article"; //concepte en cat... 
$Text['bill_total'] = "Total"; //Total factura 
$Text['phone_pl'] = "Telèfons";
$Text['net_amount'] = "Import net"; //importe netto 
$Text['gross_amount'] = "Import brut"; //importe brutto
$Text['add_pagebreak'] = "Prem aquí per a afegir un salt de pàgina";
$Text['remove_pagebreak'] = "Prem aquí per eliminar el salt de pàgina";

$Text['show_deactivated'] = "Mostra productes desactivats";
$Text['nav_report_sales'] = "Compres"; 
$Text['nav_help'] = "Ajuda"; 
$Text['withdraw_from'] = "Retirar diners de";  //account
$Text['withdraw_to_bank'] = "Retirar diners de banc";
$Text['withdraw_uf'] = "Retirar diners de compte UF"; 
$Text['withdraw_cuota'] = "Retirar quota de membre";
$Text['msg_err_noorder'] = "No s'ha trobat cap producte per el període seleccionat!";
$Text['primer_torn'] = "Primer Torn";
$Text['segon_torn'] = "Segon Torn";
$Text['dff_qty'] = "Dif. quantitat";
$Text['dff_price'] = "Dif. preu";
$Text['ti_mgn_stock_mov'] = "Moviments stock";
$Text['stock_acc_loss_ever'] = "Pèrdua acumulada";
$Text['closed'] = "tancat";
$Text['preorder_item'] = "Aquest producte forma part d'una comanda acumulativa";
$Text['do_preorder'] = "Des-/activar com comanda acumulativa";
$Text['do_deactivate_prod'] = "Activar/desactivar producte";
$Text['msg_make_preorder_p'] = "Aixó es una comanda acumulativa, per tant encara no te data d'entrega";
$Text['btn_ok_go'] = "Ok, endavant!";
$Text['msg_pwd_emailed'] = "La nova contrasenya s'ha enviat a l'usuari";
$Text['msg_pwd_change'] = "La nova contrasenya és: ";
$Text['msg_err_emailed'] = "Error d'enviament!";
$Text['msg_order_emailed'] = "La comanda s'ha enviat correctament!";
$Text['msg_err_responsible_uf'] = "No s'ha trobat cap responsable per aquest producte";
$Text['msg_err_finalize'] = "Ups... error al finalitzar comanda!";
$Text['msg_err_cart_sync'] = "La teva comanda no està sincronitzada amb la base de dades, doncs s'ha modificat la teva cistella mentre compraves. Torna a actualitzar la teva comanda!";
$Text['msg_err_no_deposit'] = "La darrera UF no ha realitzat cap dipòsit???!!!";
$Text['btn_load_cart'] = "Continua amb la següent UF";
$Text['btn_deposit_now'] = "Fes l'ingrés ara";
$Text['msg_err_stock_mv'] = "De moment no hi ha cap moviment d'estoc per aquest producte.";

$Text['ti_report_shop_pv'] = "Total vendes per proveidor";
$Text['filter_all_sales'] = "Totes les vendes";
$Text['filter_exact'] = "Escull dades";
$Text['total_4date'] = "Total";
$Text['total_4provider'] = "Suma";
$Text['sel_sales_dates'] = "Per quines dades vols consultar les vendes?";
$Text['sel_sales_dates_ti'] = "Escull un període"; 

$Text['instant_repeat'] = "Repeteix directament";
$Text['msg_confirm_delordereditems'] = "Ja s'ha demanat aquest producte per aquest dia. Estàs segur de desactivar-l'ho? Això esborarrà la comanda d'aquest producte de les cistelles.";
$Text['msg_confirm_instantr'] = "Vols repetir aquesta acció per a la resta de les dates activades?";
$Text['msg_err_delorerable'] = "Ja s'han demanat productes per aquesta data. No es pot desactivar!";
$Text['msg_pre2Order'] = "Converteix aquesta comanda acumulativa a comanda normal. Pots triar una data d'entrega";

$Text['msg_err_modified_order'] = "Algú ha modificat els productes a demanar per la data actual. Alguns productes que havíes demanat ja no estàn disponibles i desapareixeràn del teu carret una vegada recarregat.";
$Text['btn_confirm_del'] = "Esborrar! Estic segur";
$Text['print_new_win'] = "Nova finestra";
$Text['print_pdf'] = "Descarregar pdf";
$Text['msg_incident_emailed'] = "L'incident s'ha enviat correctament!";
$Text['upcoming_orders'] = "Pròximes comandes";

$Text['msg_confirm_del_mem'] = "Estas segur de eliminar aquest usuari de la base de dades??";
$Text['btn_del'] = "Esborrar";

$Text['btn_new_provider'] = "Nou proveïdor";
$Text['btn_new_product'] = "Afegir producte";
$Text['orderable'] = "Comanda directe"; //product type
$Text['msg_err_providershort'] = "El nom del proveïdor no pot quedar buit i ha de ser de més de 2 caràcters.";
$Text['msg_err_select_responsibleuf'] = "Qui s'encarrega? S'ha de seleccionar un responsable.";
$Text['msg_err_product_category'] = "S'ha de seleccionar una categoria de producte.";
$Text['msg_err_order_unit'] = "S'ha de seleccionar una unitat de mesura per a la comanda.";
$Text['msg_err_shop_unit'] = "S'ha de seleccionar una unitat de mesura per a la venda.";
$Text['click_row_edit'] = "Fes clic per editar.";
$Text['click_to_list'] = "Fes click per a desplegar la llista de productes.";
$Text['head_ti_provider'] = "Gestió de proveïdors i productes";
$Text['edit'] = "Editar";
$Text['ti_create_provider'] = "Afegir proveïdor";
$Text['ti_add_product'] = "Afegir producte";
$Text['order_min'] = "Quantitat mínima per la comanda.";
$Text['msg_confirm_del_product'] = "¿Segur que vols esborrar aquest producte?";
$Text['msg_err_del_product'] = "No es pot esborrar aquest producte per què hi ha entrades que depenen d'ell a la base de dades. Missatge d'error: ";
$Text['msg_err_del_member'] = "No es pot esborrar aquest usuari per que hi ha referències al mateix a la base de dades.<br/> Missatge d'error: ";
$Text['msg_confirm_del_provider'] = "¿Segur que vols esborrar aquest proveïdor?";
$Text['msg_err_del_provider'] = "No es pot esborrar aquest proveïdor. Esborra primer els seus productes i torna a provar.";
$Text['price_net'] = "Preu net";
$Text['custom_product_ref'] = "Id extern"; 
$Text['btn_back_products'] = "Tornar a productes";

$Text['copy_column'] = "Copiar columna";
$Text['paste_column'] = "Enganxar";

$Text['search_provider'] = "Buscar proveïdor";
$Text['msg_err_export'] = "Error exportant dades";
$Text['export_uf'] = "Exportar membres";
$Text['btn_export'] = "Exportar";

$Text['ti_visualization'] = "Visualitzacions";
$Text['file_name'] = "Nom de fitxer";
$Text['active_ufs'] = "Només UFs actives";
$Text['export_format'] = "Format d'exportació";
$Text['google_account'] = "Google account";
$Text['other_options'] = "Altres opcions";
$Text['export_publish'] = "Fes fitxer d'exportació públic en:";
$Text['export_options'] = "Opcions d'exportació";
$Text['correct_stock'] = "Corregeix estoc";
$Text['btn_edit_stock'] = "Edita estoc";
$Text['consult_mov_stock'] = "Consulta moviments";
$Text['add_stock_frase'] = "Estoc total = estoc actual de "; //complete frase is: total stock = current stock of X units + new stock
$Text['correct_stock_frase'] = "L'estoc actual no és de";
$Text['stock_but'] = "sinó"; //current stock is not x units but...
$Text['stock_info'] = "Nota: es poden consultar tots els canvis d'estoc (addicions, correccions, pèrdues) fent clic al nom del producte aquí baix.";
$Text['stock_info_product'] = "Nota: es poden consultar tots els canvis d'estoc (addicions, correccions, pèrdues totals) des de la secció Gestiona&gt; Productes&gt; Estoc.";


$Text['msg_confirm_prov'] = "Estàs segur que vols exportar tots els proveïdors?"; 
$Text['msg_err_upload'] = "S'ha produït un error en la càrrega de l'arxiu "; 
$Text['msg_import_matchcol'] = "Cal fer coincidir les entrades de la base de dades amb les files de la taula. Has d'assignar la columna que correspon a "; //+ here then comes the name of the matching column, e.g. custom_product_ref
$Text['msg_import_furthercol'] = "Quines altres columnes de la taula vols importar a més de la columna necessària?"; 
$Text['msg_import_success'] = "L'importació ha funcionat correctament. Vols importar un altre arxiu?"; 
$Text['btn_import_another'] = "Importar un altre"; 
$Text['btn_nothx'] = "No, gràcies"; 
$Text['import_allowed'] = "Formats compatibles"; //as in allowed file formats
$Text['import_file'] = "Arxiu d'importació"; 
$Text['public_url'] = "URL pública";
$Text['btn_load_file'] = "Afegi arxiu";
$Text['msg_uploading'] = "S'està carregant el fitxer i s'està generant la vista prèvia. Espera ...";
$Text['msg_parsing'] = "S'està llegint l'arxiu del servidor i s'està analitzant. Espera ...";
$Text['import_step1'] = "Afegir arxiu";
$Text['import_step2'] = "Vista prèvia de les dades i assignació de columnes";
$Text['import_reqcol'] = "Columna necessària";
$Text['import_auto'] = "Tinc bones notícies: gairebé totes les dades (columnes) s'han pogut reconèixer i pots intentar la importació automàtica. De tota manera, l'opció més segura és previsualitzar primer el contingut i després realitzar l'assignació de columnes de la taula manualment.";
$Text['import_qnew'] = "Què vols fer amb les dades que no existeixen a la base de dades actual?";
$Text['import_createnew'] = "Crear entrades noves";
$Text['import_update'] = "Actualitzar només les files existents";
$Text['btn_imp_direct'] = "Importar directament";
$Text['btn_import'] = "Importar";
$Text['btn_preview'] = "Vista prèvia"; 
$Text['sel_matchcol'] = "Assignar columna ..."; 
$Text['ti_import_products'] = "Importar o actualitzar els productes de "; 
$Text['ti_import_providers'] = "Importar proveïdor"; 
$Text['head_ti_import'] = "Assistent d'importació";


?>
