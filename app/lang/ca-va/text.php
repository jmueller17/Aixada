<?php
// Traducció al Català per a l'aixada 
// contribute by Cristóbal Cabeza-Cáceres; Jordi Losantos
// Email: cristobal.cabeza@gmail.com; jordi@losantos.name

require_once(asset('app/storage/config.php'));

return array(

  'ca-va' => "Català",
  'charset' => "utf-8",
  'text_dir' => "ltr", // ('ltr' significa d'esquerra a dreta, 'rtl' per aescriure de dreta a esquerra)


/** 
 *  		Elements globals
 */
  'coop_name' => configuration_vars::get_instance()->coop_name,
  'currency_sign' => configuration_vars::get_instance()->currency_sign,
  'currency_desc' => "Euros",
  'please_select' => "Seleccioneu...",
  'loading' => "Si us plau, espere mentre es carreguen les dades...",
  'search' => "Cerca",
  'id' => "id",
  'uf_short' => "UF",
  'uf_long' => "Unitat familiar",


/** 
 *  		miscelànea
 */
  'date_from' => "de",
  'date_to' => "a",
  'mon' => "Dilluns",
  'tue' => "Dimarts",
  'wed' => "Dimecres",
  'thu' => "Dijous",
  'fri' => "Divendres",
  'sat' => "Dissabte",
  'sun' => "Diumenge",

/**
 * 				seleccions
 */
  'sel_provider' => "Seleccioneu un proveïdor...",
  'sel_product' => "Seleccioneu un producte...",
  'sel_user' => "Seleccioneu un usuari...",
  'sel_category' => "Seleccioneu una categoria de productes...",
  'sel_uf' => "Seleccioneu una unitat familiar...",
  'sel_uf_or_account' => "Seleccioneu unitat familiar o compte...",
  'sel_account' => "Seleccioneu un compte...",
  'sel_none' => "Cap",

/**
 * 				tabs
 */
  'by_provider' => "Per proveïdor",
  'by_category' => "Per categoria de productes",
  'special_offer' => "Comanda acumulativa",
  'search_add' => "Cerca i afegeix un altre article",
  'validate' => "Valida",


/**
 *  			titles for header <title></title>
 */
  'global_title' => configuration_vars::get_instance()->coop_name,
  'head_ti_validate' => "Valida",

  'head_ti_active_products' => "Activa/Desactiva Productes demanables",
  'head_ti_active_roles' => "Rols actius",
  'head_ti_account' => "Comptes",
  'head_ti_manage_orders' => "Gestiona les comandes",
//  'head_ti_manage_dates' => "Estableix dates de comanda",
  'head_ti_manage' => "Gestiona",
  'head_ti_manage_uf' => "Unitats familiars/Membres",
  'head_ti_incidents' => "Incidents",
  'head_ti_stats' => "Estadístiques diàries",
  'head_ti_prev_orders' => "Les meves compres anteriors", 
  'head_ti_cashbox' => "Control de caixa", 



/**
 *  			capçaleres de les pàgines principals <h1></h1>
 */
  'ti_mng_activate_products' => "Les UFs podran demanar els següents productes PER EL dia ",
//  'ti_mng_arrived_products' => "Els següents productes han arribat el dia ",
  'ti_mng_activate_roles' => "Gestiona els rols d'usuari ",
  'ti_mng_activate_users' => "Activa usuaris com a ",
//  'ti_mng_move_orders' => "Canvia una comanda",
  'ti_mng_activate_preorders' => "Converteix una comanda acumulativa en comanda",
  'ti_mng_members' => "Gestiona els membres",
//  'ti_mng_ufs' => "Gestiona les unitats familiars",
//  'ti_mng_dates' => "De-/activar individualment dates per fer comandes",
//  'ti_mng_dates_pattern' => "Bulk de-/activar dates per fer comandes",
  'ti_mng_db' => "Còpia de seguretat de la base de dades", 
  'ti_order' => "Fes la comanda per a ",
  'ti_shop' => "Compra articles ",
//  'ti_report_report' => "Resum de comandes per a ", 
  'ti_report_account' => "Informe de tots els comptes", 
//  'ti_report_my_account' => "Informe del meu compte ", 
  'ti_report_preorder' => "Resum de comandes acumulatives", 
  'ti_report_incidents' => "Incidents d'avui",
  'ti_incidents' => "Incidents",
  'ti_validate' => "Valida la compra de la UF",
  'ti_stats' => "Estadístiques totals",
  'ti_my_account' => "Configuració",
  'ti_my_account_money' => "Diners",
//  'ti_my_prev_sales' => "Les meves compres anteriors",
  'ti_all_sales' => "Totes les compres anteriors de la UF",
  'ti_login_news' => "Inici de sessió i notícies",
  'ti_timeline' => "Informe línia de temps",
  'ti_report_torn' => "Resum del torn d'aviu",
//  'ti_mng_cashbox' => "Caixa",




/**
 * 				roles
 */
  'Consumer' => "Consumidor",
  'Checkout' => "Caixa",
  'Consumer Commission' => "Comissió de consum",
  'Econo-Legal Commission' => "Comissió econolegal",
  'Logistic Commission' => "Comissió de logística",
  'Hacker Commission' => "Comissió d'informàtica",
  'Fifth Column Commission' => "La cinquena",
  'Producer' => "Productor",


/**
 * 				Manage Products / Roles
 */
//  'mo_inact_prod' => "No podran demanar:",
//  'mo_act_prod' => "Si podran demanar:",
//  'mo_notarr_prod' => "Productes que no han arribat:",
//  'mo_arr_prod' => "Productes que si han arribat:",
  'mo_inact_role' => "Rols inactius",
//  'mo_act_role' => "Rols actius",
  'mo_inact_user' => "Usuaris inactius",
  'mo_act_user' => "Usuaris actius",
//  'msg_no_report' => "No s'han trobat productors/productes per a la data!",


/**
 * 				uf member manage
 */
  'search_memberuf' => "Cerca un nom o inici de sessió",
  'browse_memberuf' => "Navega",
  'assign_members' => "Assigna membres",
  'login' => "Inici de sessió",
  'create_uf' => "Nova unitat familiar",
  'name_person' => "Nom",
  'address' => "Adreça",
  'zip' => "CP",
  'city' => "Localitat",
  'phone1' => "Telèfon1",
  'phone2' => "Telèfon2",
  'email' => "Correu-e",
  'web' => "URL",
  'last_logon' => "Últim accés",
  'created' => "Creat el dia",
  'active' => "Actiu",
  'participant' => "Participant",
  'roles' => "Rols",
  'active_roles' => "Rols actius",
  'products_cared_for' => "Productes de què sóc responsable",
  'providers_cared_for' => "Proveïdors de què sóc responsable",
  'notes' => "Observacions",
  'edit_uf' => "Edita UF",
//  'members_uf' => "Membres de la unitat familiar",
  'mentor_uf' => "Unitat familiar amfitriona",
  'unassigned_members' => "Membres no assignats",
  'edit_my_settings' => "Edita la meva configuració",

  'nif' => "NIF",
  'bank_name' => "Banc o Caixa",
  'bank_account' => "Compte bancari",
  'picture' => "Imatge",
  'offset_order_close' => "Temps de procés",
  'iva_percent_id' => "Tipus d'IVA",
  'percent' => "Percentatge",
  'adult' => "Adult",


/**
 *  			wiz stuff
 */
  'deposit_cashbox' => "Ingrés a caixa",
  'widthdraw_cashbox' => "Retirar diners de la caixa",
  'current_balance' => "Balanç actual",
  'deposit_type' => "Concepte d'ingrés",
  'deposit_by_uf' => "Ingrés per la UF",
  'deposit_other' => "Altres tipus de ingressos...",
  'make_deposit_4HU' => "Ingrés de la UF",
  'short_desc' => "Breu descripció",
  'withdraw_type' => "Tipus de reintegrament",
  'withdraw_for_provider' => "Fer un reintegrament a un proveïdor",
  'withdraw_other' => "Altres reintegraments...",
  'withdraw_provider' => "Proveïdor del reintegrament",
  'btn_make_withdrawal' => "Reintegrament",
  'correct_balance' => "Corregir balanç",
  'set_balance' => "Actualitzar balanç de la caixa",
  'name_cash_account' => "Caixa",




/**
 *				validar
 */
  'set_date' => "Defineix una data",
  'get_cart_4_uf' => "Recupera la compra de la unitat familiar",
  'make_deposit' => "Ingrés de la unitat familiar",
  'success_deposit' => "L'ingrés s'ha fet correctament",
  'amount' => "Quantitat",
  'comment' => "Comentari",
//  'deposit_other_uf' => "Fes un ingrés per a una altra unitat familiar o compte",
  'latest_movements' => "Darrers moviments",
  'time' => "Hora",
  'account' => "Compte",
  'consum_account' => "Compte de Consum",
  'operator' => "Usuari",
  'balance' => "Balanç",
  'dailyStats' => "Estadístiques diàries",
  'totalIncome' => "Ingressos totals",
  'totalSpending' => "Despeses totals",
  'negativeUfs' => "Unitats familiars en negatiu",
  'lastUpdate' => "Última actualització",
  'negativeStock' => "Productes amb estoc negatiu",
  'curStock' => "Estoc actual",
  'minStock' => "Estoc mínim",
  'stock' => "Estoc",



/**
 *              Shop and order
 */

  'info' => "Info",
  'quantity' => "Quantitat",
  'unit' => "Unitat",
  'price' => "Preu",
  'name_item' => "Nom",
  'revtax_abbrev' => "ImpRev",
//  'cur_stock' => "Estoc actual",
  'date_for_shop' => "Data de compra",
  'ts_validated' => "Validada",

/**
 * 		Logon Screen
 */ 
  'welcome_logon' => "Benvinguts/des a " . configuration_vars::get_instance()->coop_name . "!",
  'logon' => "Usuari",
  'pwd' => "Contrasenya",
  'old_pwd' => "Contrasenya antiga",
  'retype_pwd' => "Torneu a escriure la contrasenya",
  'lang' => "Llengua",
  'msg_err_incorrectLogon' => "Accés incorrecte",
  'msg_err_noUfAssignedYet' => "Encara no estàs assignat a cap UF. Si us plau, demana a algú que et registri.",


//  'msg_reg_success' => "Us heu registrat correctament, però el vostre usuari encara no s'ha aprovat. Registreu la resta de membres de la vostra UF i contacteu amb un responsable per finalitzar el registre.",
//  'register' => "Registre",
  'required_fields' => " són camps obligatoris",


/**
 *			Navigation
 */
  'nav_home' => "Inici",
  'nav_wiz' => "Torn",
//    'nav_wiz_arrived' => "Productes que no han arribat",
	  'nav_wiz_validate' => "Valida",
//    'nav_wiz_open' => "Obre",
//    'nav_wiz_close' => "Tanca",
    'nav_wiz_torn' => "Resum torn",
	  'nav_wiz_cashbox' => "Caixa",
  'nav_shop' => "Compra avui",
  'nav_order' => "Propera comanda",
  'nav_mng' => "Gestiona",
	//  'nav_mng_uf' => "Unitats familiars",
	  'nav_mng_member' => "Membres",
	  'nav_mng_providers' => "Proveïdors",
	  'nav_mng_products' => "Productes",
		  'nav_mng_deactivate' => "Activa/desactiva productes",
		  'nav_mng_stock' => "Estoc",
		  'nav_mng_units' => "Unitats",
	  'nav_mng_orders' => "Comandes",
		//  'nav_mng_setorderable' => "Establiu dates ordenables",
		//  'nav_mng_move' => "Canvia la data de la comanda",
		//  'nav_mng_orders_overview' => "Gestiona comandes",
		  'nav_mng_preorder' => "Converteix la comanda acumulativa en comanda",
	  'nav_mng_db' => "Backup bd",
	  'nav_mng_roles' => "Rols",
  'nav_report' => "Informes",
//  'nav_report_order' => "Comanda actual",
  'nav_report_account' => "Comptes",
  'nav_report_timelines' => "Evolució " . configuration_vars::get_instance()->coop_name,
  'nav_report_timelines_uf' => "Per UFs",
  'nav_report_timelines_provider' => "Per proveïdors",
  'nav_report_timelines_product' => "Per productes",
  'nav_report_daystats' => "Estadístiques diàries",
  'nav_report_preorder' => "Comandes acumulatives",
  'nav_report_incidents' => "Incidents d'avui",
  'nav_report_shop_hu' => "Per UF",
  'nav_report_shop_pv' => "Per Proveïdor",

  'nav_incidents' => "Incidents",
	  'nav_browse' => "Navega / afegeix",
  'nav_myaccount' => "El meu compte",
	  'nav_myaccount_settings' => "Configuració",
	  'nav_myaccount_account' => "Diners",
	  'nav_changepwd' => "Canvia la contrasenya", 
	  'nav_prev_orders' => "Compres anteriors",

  'nav_logout' => "Surt/Ix",
  'nav_signedIn' => "Heu accedit com a ",
  'nav_can_checkout' => "Podeu validar compres.",
  'nav_try_to_checkout' => "Comença a validar",
  'nav_stop_checkout' => "Deixa de validar",



/**
 *			Buttons
 */
  'btn_login' => "Inicia la sessió",
  'btn_submit' => "Envia",
  'btn_save' => "Desa",
  'btn_reset' => "Esborra",
  'btn_cancel' => "Cancel·la",
  'btn_activate' => "Activa",
  'btn_deactivate' => "Desactiva",
//  'btn_arrived' => "Ha arribat",
//  'btn_notarrived' => "No ha arribat",
//  'btn_move' => "Canvia de data",
  'btn_ok' => "D'acord",
  'btn_assign' => "Assigna",
  'btn_create' => "Crea",
  'btn_close' => "Tanca",
  'btn_make_deposit' => "Ingressa",
  'btn_new_incident' => "Incident nou",
  'btn_reset_pwd' => "Reestableix contrasenya", 
  'btn_view_cart' => "Cistella", 
  'btn_view_cart_lng' => "Només veure la cistella",
  'btn_view_list' => "Productes",
  'btn_view_list_lng' => "Només veure els productes",
  'btn_view_both' => "Tot",
  'btn_view_both_lng' => "Veure tant productes com cistella",
  'btn_repeat' => "Entesos, repetim això!",
  'btn_repeat_single' => "No, únicament aquest", 
  'btn_repeat_all' => "Sí a tots", 




/**
 * Incidents
 */
  'create_incident' => "Crea un incident",
  'overview' => "Resum",
  'subject' => "Assumpte",
  'message' => "Missatge",
  'priority' => "Prioritat",
  'status' => "Estat",
  'incident_type' => "Tipus",
  'status_open' => "Obert",
  'status_closed' => "Tancat",
  'ufs_concerned' => "UFs afectades",
  'provider_concerned' => "Per al proveïdor",
  'comi_concerned' => "Per a la comissió",
  'created_by' => "Creat per",
  'edit_incident' => "Edita l'incident",

/**
 *  Reports
 */
  'provider_name' => "Proveïdor",
  'product_name' => "Producte",
//  'qty' => "Quantitat",
  'total_qty' => "Quantitat total",
  'total_price' => "Preu total",
  'total_amount' => "Suma total",
//  'select_order' => "Mostra les comandes per a la data següent:",
//  'move_success' => "Les comandes de la llista estan actives per a: ",
//  'show_compact' => "Mostra la llista reduïda",
//  'show_all_providers' => "Expandeix els productes",
//  'show_all_print' => "Expandeix les impressions",
  'nr_ufs' => "UFs total",
  'printout' => "Imprimeix",
  'summarized_orders' => "Resum de la comanda",
  'detailed_orders' => "Detalls de la comanda",


/**
 * 		Error / Warning Messages
 */
  'msg_err_incorrectLogon' => "L'usuari o la contrasenya són incorrectes. Intenteu-ho de nou!",
  'msg_err_pwdctrl' => "Les contrasenyes no coincideixen. Escriviu-les de nou!",
  'msg_err_usershort' => "El nom d'usuari és massa curt. Ha de tindre un mínim de tres caràcters",
  'msg_err_userexists' => "El nom d'usuari ja està agafat. Trieu-ne un altre.",
  'msg_err_passshort' => "La contrasenya és massa curta. Ha de tenir entre 4 i 15 caràcters",
  'msg_err_notempty' => " no pot estar buit!", 
  'msg_err_namelength' => "El nom i cognom no poden estar buits i no poden tenir més de 255 caràcters!", 
  'msg_err_only_num' => " només accepta xifres i no pot estar buit!", 
  'msg_err_email' => "El format del correu-e no és correcte. Ha de ser del tipus nom@domini.com o semblant a això.",
//  'msg_err_select_uf' => "Per assignar un membre nou a una UF primer heu de seleccionar la UF fent-hi clic! Si voleu crear una UF nova, feu-ho fent clic en + Nova UF.",
//  'msg_err_select_non_member' => "Per assignar un membre nou a una UF, primer heu de seleccionar-lo de la llista de no membres que hi ha a la dreta!", 
//  'msg_err_insufficient_stock' => "No hi ha prou estoc de ",


  'msg_edit_success' => "Les dades editades s'han desat correctament!",
//  'msg_edit_mysettings_success' => "La nova configuració s'ha desat correctament!",
  'msg_pwd_changed_success' => "La contrasenya s'ha canviat correctament!", 
  'msg_confirm_del' => "Segur que voleu eliminar aquest membre?",
  'msg_enter_deposit_amount' => "El camp de quantitat de l'ingrés només accepta xifres i no pot estar buit!",
  'msg_please_set_ufid_deposit' => "No s'ha definit l'ID de la UF. Heu de triar una cistella o seleccionar una altra UF per fer el dipòsit!",
//  'msg_error_deposit' => "S'ha produït un error en fer l'ingrés. Intenteu-ho de nou. Els ingressos que s'han fet correctament apareixen en la llista de comptes. <br/>L'error ha sigut: ",
  'msg_deposit_success' => "El dipòsit s'ha fet correctament!",
  'msg_withdrawal_success' => "El pagament s'ha fet correctament!",
  'msg_select_cart_first' => "Per afegir articles per validar abans heu de seleccionar una UF o una cistella!",
//  'msg_err_move_date' => "S'ha produït un error mentre es canviava la data de la comanda. Intenteu-ho de nou.",
  'msg_no_active_products' => "En aquests moments no hi ha productes actius per fer la comanda!",
//  'msg_no_movements' => "No hi ha moviments per al compte i la data seleccionats!", 
  'msg_delete_incident' => "Segur que voleu eliminar aquest incident?",
//  'msg_err_selectFirstUF' => "No hi ha UF seleccionada. Selecciona una primer i després les seves compres.", //ADDED JAN 2012


/**
 *  Product categories
 */
  'SET_ME' => "SET_ME",

  'prdcat_vegies' => "Verdures",
  'prdcat_fruit' => "Fruita",
  'prdcat_mushrooms' => "Bolets",
  'prdcat_dairy' => "Llet i iogurts", 			//llet fresca, iogurt
  'prdcat_meat' => "Carn",							//pollastre, vedella, xai/corder, etc.
  'prdcat_bakery' => "Productes de forn i farina",						//pa, pastes, farina
  'prdcat_cheese' => "Formatge",
  'prdcat_sausages' => "Embotits",					//pernil, botifarres, etc.
  'prdcat_infant' => "Nutrició infantil",
  'prdcat_cereals_pasta' => "Cereals i pasta",	//cereals i pasta
  'prdcat_canned' => "Conserves",
  'prdcat_cleaning' => "Neteja",					//neteja de la llar, detergents, etc.
  'prdcat_body' => "Productes corporals",
  'prdcat_seasoning' => "Adobs i algues",
  'prdcat_sweets' => "Mel i dolços",		//melmelada, mel, sucre, xocolata
  'prdcat_drinks_alcohol' => "Begudes alcohòliques",			//vi, cervesa, etc.
  'prdcat_drinks_soft' => "Begudes no alcohòliques",			//suc, begudes vegetals
  'prdcat_drinks_hot' => "Cafè i te",
  'prdcat_driedstuff' => "Coses per a picar i fruits secs",
  'prdcat_paper' => "Cel·lulosa i paper",		//mocadors, paper del vàter, paper de cuina 
  'prdcat_health' => "Salut",		//paper del vàter, paper de cuina 1
  'prdcat_misc' => "Tota la resta..." ,





/**
 *  Field names in database
 */

  'name' => "Nom",
  'contact' => "Contacte",
  'fax' => "Fax",
  'responsible_mem_name' => "Membre responsable",
  'responsible_uf' => "Unitat familiar responsable",
  'provider' => "Proveïdor",
  'description' => "Descripció",
  'barcode' => "Codi de barres",
  'orderable_type' => "Tipus de producte",
  'category' => "Categoria",
  'rev_tax_type' => "Tipus d'impost revolucionari",
  'unit_price' => "Preu per unitat",
  'iva_percent' => "Percentatge d'IVA",
  'unit_measure_order' => "Unitat de comanda",
  'unit_measure_shop' => "Unitat de venda",
  'stock_min' => "Quantitat mínima per tindre en estoc",
  'stock_actual' => "Quantitat actual en estoc",
  'delta_stock' => "Diferència amb l'estoc mínim",
  'description_url' => "URL de descripció",


/**
 * afegits després 14.5
 */
  'msg_err_validate_self' => "No pots validar-te a tu mateix",
  'msg_err_preorder' => "La comanda acumulativa ha de ser amb data futura!",
  'msg_preorder_success' => "La comanda acumulativa s'ha activat correctament per a la data:",
//  'msg_can_be_ordered' => "Es pot fer comanda en aquesta data",
//  'msg_has_ordered_items' => "Hi ha comandes per aquest dia. No es poden esborrar, només moure de data",
//  'msg_today' => "Avui",
//  'msg_default_day' => "Dies sense comanda encara",
//  'activate_for_date' => "Activa per al ",
//  'start_date' => "Mostra els registres començant pel ",


//  'Download zip' => "Baixar fitxer amb totes les comandes",
  'product_singular' => "producte",
  'product_plural' => "productes",
  'confirm_db_backup' => "Segur que vols copiar tota la base de dades? Això trigarà una estona...",
  'show_date_field' => "Prem aquí per a obrir el calendari i triar una data que no sigui la d'avui.",


  'purchase_current' => "Les meves compres",
//  'items_bought' => "Compres anteriors",
  'purchase_future' => "Les meves comandes",
//  'purchase_prev' => "Compres anteriors",
  'icon_order' => "La propera comanda",
  'icon_purchase' => "Fes la teva compra",
  'icon_incidents' => "Fes una incidencia",
  'purchase_date' => "Data de la compra",
//  'purchase_validated' => "Data de la validació",
//  'ordered_for' => "Comanda pel",
  'not_validated' => "sense validar",






/* Novetats */

  'download_db_zipped' => "Descarrega base de dades comprimida",
  'backup' => "Entesos, copia la base de dades!",
  'filter_incidents' => "Filtra incidents",
  'todays' => "d'avui",
  'recent_ones' => "Recents",
  'last_year' => "Últim any",
  'details' => "Detalls",
  'actions' => "Accions",
  'incident_details' => "Detalls de l'incident",
  'distribution_level' => "Nivell de distribució",
  'internal_private' => "Intern (privat)",
  'internal_email_private' => "Intern + email (privat)",
  'internal_post' => "Intern + envia al portal (públic)",
  'internal_email_post' => "Intern + email + envia (públic)",

  'date' => "Data",
  'iva' => "IVA",
  'expected' => "Esperat",
  'not_yet_sent' => "Pendent d'enviar",
  'ordered_for' => "Demanat per",
  'my_orders' => "Les meves comandes",
  'my_purchases' => "Les meves compres",
  'loading_status_info' => "Carregant informació d'estat...",
  'previous' => "Anterior",
  'next' => "Següent",
  'date_of_purchase' => "Data de compra",
  'validated' => "Validat",
  'total' => "Total",
  'ordered' => "Demanat",
  'delivered' => "Entregat",
  'price' => "Preu",
  'qu' => "Qu",
  'msg_err_deactivatedUser' => "El teu compte d'usuari ha estat desactivat!",
  'order' => "Comanda",
  'order_pl' => "Comandes",
  'msg_already_validated' => "La cistella sel·leccionada ja està validada. Vols veure els productes que hi ha?",
  'validated_at' => "Validat en data ", //refers to a date/hour


  'nothing_to_val' => "Res a validar per a la UF",
  'cart_id' => "Id cistella",
  'msg_several_carts' => "La UF seleccionada té més d'una cistella pendent de validar. Tria'n una si us plau:",
  'transfer_type' => "Tipus",
  'todays_carts' => "Cistelles d'avui",
  'head_ti_torn' => "Resum del torn", 
  'btn_validate' => "Validar",
  'desc_validate' => "Validar cistelles actuals i antigues de les UF. Ingressar efectiu.",
  'nav_wiz_revise_order' => "Revisar",
  'desc_revise' => "Revisar comandes individuals; verificar si han arribat els productes i ajustar les quantitats si cal. Distribuir la comanda en cistelles individuals.",    
  'desc_cashbox' => "Efectuar ingressos i reintegraments d'efectiu. A l'inici del primer torn cal inicialitzar el balanç. L'import d'aquest compte ha de reflectir els diners disponibles reals.",
  'desc_stock' => "Regularitza l'estoc dels productes.",
  'desc_print_orders' => "Imprimir i descarregar comandes per a la propera setmana. Les comandes s'han de completar, imprimir i descarregar en un arxiu zip.",
  'nav_report_status' => "Estadístiques",
  'desc_stats' => "Descarrega un informe resumit del torn actual, incloent-hi incidents, UFs en negatiu, despesa total i productes amb estoc negatiu",
  'order_closed' => "La comanda d'aquest proveïdor està tancada.",
  'head_ti_sales' => "Llistat de vendes", 
  'not_yet_val' => "pendent de validar",
  'val_by' => "Validat per",
  'purchase_details' => "Detall de la compra de la cistella nº",
  'filter_uf' => "Filtra per UF",
  'purchase_uf' => "Compra de la UF",
  'quantity_short' => "Qu",
  'incl_iva' => "incl. IVA",
  'incl_revtax' => "incl. ImpRev",
  'no_news_today' => "Cap notícia és la millor notícia: avui no hi ha hagut incidents!",
  'nav_mng_iva' => "tipus d'IVA",
  'nav_mng_money' => "Diners",
  'nav_mng_admin' => "Admin",
  'nav_mng_users' => "Usuaris",
  'nav_mng_access_rights' => "Permisos d'acccés",
  'msg_sel_account' => "Tria un compte primer, i aleshores filtra'n els resultats!",
  'msg_err_nomovements' => "Mala sort, no hi ha moviments per a aquest compte en aquesta data. Prova a ampliar el període consultat amb el botó de filtre.",
  'active_changed_uf' => "Estat actiu de la UF modificat",
  'msg_err_mentoruf' => "La UF no pot amfitriona de sí mateixa!",
  'msg_err_ufexists' => "Ja existeix aquest nom d'UF. Tria'n un altre si us plau!",
  'msg_err_form_init' => "Sembla que el formulari per a crear noves UF no s'ha inicialitzat correctament. Torna a carregar la pàgina...   ",
  'ti_mng_hu_members' => "Gestiona Unitats Familiars i els seus membres", 
  'list_ufs' => "Llistat d'Unitats Familiars",
  'search_members' => "Busca un membre",
  'member_pl' => "Membres",
  'mng_members_uf' => "Gestiona els membres de la Unitat Familiar ",
  'uf_name' => "Nom",
  'btn_new_member' => "Nou membre",
  'ti_add_member' => "Afegir nou membre a la UF",
  'custom_member_ref' => "Ref. personalitzada",
  'theme' => "Tema",
  'member_id' => "Id membre",
  'ti_mng_stock' => "Gestiona estoc",
  'msg_err_no_stock' => "sembla que aquest proveïdor no té estoc",
  'msg_err_qu' => "La quantitat ha de ser numèrica i superior a 0!",
  'msg_correct_stock' => "Ajustar així l'estoc hauria de ser l'excepció! El nou estoc s'hauria d'AFEGIR. Estàs segur de voler corregir l'estoc?",
  'btn_yes_corret' => "Sí, fes la correcció!",
  'ti_mng_stock' => "Gestiona estoc",
  'search_product' => "Cerca un producte",
  'add_stock' => "Afegeix estoc",
  'click_to_edit' => "Prem una cel·la per a editar-la!",
  'no_results' => "No hi ha resultats de la cerca.",
  'for' => "per a", //as in order FOR Aurora
  'date_for_order' => "Data d'entrega",
  'finished_loading' => "Càrrega acabada",
  'msg_err_unrevised' => "Encara hi ha element pendents de revisar a la comanda. Si us plau, verifica que hagin arribat tots els productes!",
  'btn_dis_anyway' => "Distribueix igualment",
  'btn_remaining' => "Revisar els pendents",
  'msg_err_edit_order' => "Aquesta comanda no està completada. Només pots desar les notes i referències quan la comanda s'hagi enviat.",
  'order_open' => "La comanda està oberta",
  'finalize_now' => "Finalitza ara",
  'msg_err_order_filter' => "No hi ha comandes coincidents amb el filtre.",
  'msg_finalize' => "Estàs a punt de finalitzar la comanda. Si ho fas, ja no podràs fer modificacions a la comanda. Estàs segur de voler continuar?",
  'msg_finalize_open' => "Aquesta comanda encara està oberta. Si la finalitzes ara, l'estaràs tancant abans de la seva data límit. Estàs segur de voler continuar?",
  'msg_wait_tbl' => "L'encapçalament de la taula s'està construïnt. Això pot trigar una mica en funció de la velocitat del teu navegador. Torna-ho a provar en 5 segons. ",
  'msg_err_invalid_id' => "No s'ha trobat un ID de la comanda! Aquesta comanda no s'ha enviat al proveïdor!!",
  'msg_revise_revised' => "Els elements de la comanda ja han estat revisats i carregats a les cistelles dels usuaris per a la data indicada. Tornar-los a revisar alteraria les modificacions ja fetes, i podria interferir amb les modificacions fetes pels propis usuaris. <br/><br/> Estàs segur de voler continuar?! <br/><br/>Si acceptes s'eliminaran els articles de les cistelles, i el procés de revisió començarà de nou.",
  'wait_reset' => "Si us plau, espera mentre re-inicialitzo la comanda...",
  'msg_err_already_val' => "Alguns dels elements de la comanda ja han estat validats! Em sap greu, però ja no s'hi poden fer modificacions!!",
  'print_several' => "Hi ha més d'una comanda sel·leccionada. Vols imprimir-les totes?",
  'btn_yes_all' => "Sí, imprimeix-les totes",
  'btn_just_one' => "No, només una",
  'ostat_revised' => "Revisat",
  'ostat_finalized' => "Finalitzat",
  'set_ostat_arrived' => "Rebut!",
  'set_ostat_postpone' => "Posposat!",
  'set_ostat_cancel' => "Cancel·lat!",
  'ostat_desc_sent' => "La comanda s'ha enviat al proveïdor",
  'ostat_desc_nochanges' => "Revisat i distribuït sense canvis",
  'ostat_desc_postponed' => "La comanda s'ha posposat",
  'ostat_desc_cancel' => "La comanda s'ha cancel·lat",
  'ostat_desc_changes' => "Revisat amb modificacions",
  'ostat_desc_incomp' => "Comanda ignorada. Falta informació anterior a la v2.5",
  'set_ostat_desc_arrived' => "La majoria o tots els productes han arribat. Procedir amb la revisió i distribució de productes a les cistelles...",
  'set_ostat_desc_postpone' => "La comanda no ha arribat a la data esperada, però probalement ho faci en les properes setmanes.",
  'set_ostat_desc_cancel' => "Els productes no arribaran mai.",
  'msg_move_to_shop' => "Els articles han estat carregats a les cistelles de la compra de les dates corresponents.",
  'msg_err_noselect' => "No has seleccionat res!",
  'ti_revise' => "Revisar comanda",
  'btn_revise' => "Revisar comanda",
  'ti_order_detail' => "Detalls de la comanda per",
  'ti_mng_orders' => "Gestiona comandes",
  'btn_distribute' => "Distribueix!",
  'distribute_desc' => "Transfereix els articles a les cistelles",
  'filter_orders' => "Filtra comandes",
  'btn_filter' => "Filtra",
  'filter_acc_todays' => "Moviments d'avui",
  'filter_recent' => "Recents",
  'filter_year' => "Darrer any",
  'filter_all' => "Tots",
  'filter_expected' => "Esperats per avui",
  'filter_next_week' => "Propera setmana",
  'filter_future' => "Totes les comandes futures",
  'filter_month' => "Darrer mes",
  'filter_postponed' => "Posposades",
  'with_sel' => "Amb selecionats...",
  'dwn_zip' => "Descarrega en zip",
  'closes_days' => "Es tanca en dies",
  'sent_off' => "Enviat al proveïdor",
  'date_for_shop' => "Data de compra",
  'order_total' => "Total comanda",
  'nie' => "NIE",
  'total_orginal_order' => "Comanda original",
  'total_after_revision' => "Després de revisió",
  'delivery_ref' => "Referència d'entrega",
  'payment_ref' => "Referència de pagament",
  'arrived' => "arribat", //as in order items have arrived. this is a table heading
  'msg_cur_status' => "L'estat de la comanda és",
  'msg_change_status' => "Canvia l'estat de la comanda a un dels següents",
  'msg_confirm_move' => "Estàs segur que vols fer aquesta comanda disponible per a la venda? Tots els productes disponibles es posaran a les cistelles de la data:",
  'alter_date' => "Tria una data alternativa",
  'msg_err_miss_info' => "Sembla que aquesta comanda prové d'una versió anterior de la plataforma, incompatible amb la nova funcionalitat de revisió. Em sap greu, però no es pot revisar aquesta comanda.",


  'order_closes' => "La comanda es tanca el", //as in: order closes 20 SEP 2012
  'left_ordering' => " pendents per demanar.", //as in 4 days left for ordering
  'ostat_closed' => "La comanda està tancada",
  'ostat_desc_fin_send' => "La comanda s'ha finalitzat i enviat al proveïdor. La referència és: #",
  'msg_err_past' => "Això és al passat! <br/> Massa tard per a modificar-hi coses.",
  'msg_err_is_deactive_p' => "Aquest producte està desactivat. Per a obrir-lo per a una data primer l'has d'activar.",
  'msg_err_deactivate_p' => "Estàs a punt de desactivar el producte. Això vol dir que les dates en que és demanable també s'eliminaran.<br/><br/>Estàs segur que vols desactivar el producte? També podries desactivar les dates en que és demanable, clicant a les caselles corresponents.",
  'msg_err_closing_date' => "La data de tancament no pot ser posterior a la de la comanda!",
  'msg_err_sel_col' => "La data seleccionada no té productes demanables! Has d'establir un producte demanable si vols crear una  plantilla per a aquesta data.",
  'msg_err_closing' => "Per a modificar la data de tancament, hi has de posar al menys un producte demanable.",
  'msg_err_deactivate_sent' => "El producte triat no pot ser (des)activat perquè la comanda corresponent ja ha estat enviada al proveïdor. No s'hi poden fer més canvis!",
  'view_opt' => "Mostra opcions",
  'days_display' => "Nombre de dies a mostrar",
  'plus_seven' => "Mostra +7 dies",
  'minus_seven' => "Mostra -7 dies",
  'btn_earlier' => "Abans de", //cómo más temprano
  'btn_later' => "després de", //más tarde... futuro

//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
  'pattern_intro' => "Activa els productes i dia per als propers ",
  'pattern_scale' => "mesos ",
  'week' => "cada setmana",
  'second' => "cada 15 dies",  //2nd 
  'third' => "cada 3 setmanes",
  'fourth' => "un cop al mes",
  'msg_pattern' => "NOTA: Aquesta acció regenerarà els productes i dates a partir d'aquesta!",
  'sel_closing_date' => "Tria una nova data de tancament",
  'btn_mod_date' => "Modifica la data de tancament",
  'btn_repeat' => "Repeteix el patró!",
  'btn_entire_row' => "(Des)activa tota la fila",
  'btn_deposit' => "Dipòsit",
  'btn_withdraw' => "Reintegrament",
  'deposit_desc' => "Ingrés en efectiu",
  'withdraw_desc' => "Reintegrament de la caixa",
  'btn_set_balance' => "Estableix el balanç",
  'set_bal_desc' => "Reinicia el balanç al principi del primer torn.",
  'maintenance_account' => "Manteniment",
  'posted_by' => "Creat per", //Posted by
  'ostat_yet_received' => "pendent de rebre",
  'ostat_is_complete' => "està complet",
  'ostat_postponed' => "posposat",
  'ostat_canceled' => "cancel·lat",
  'ostat_changes' => "amb canvis",
  'filter_todays' => "D'avui",
  'bill' => "Factura",
  'member' => "Membre",
  'cif_nif' => "CIF/NIF", //CIF/NIF
  'bill_product_name' => "Article", //concepte en cat... 
  'bill_total' => "Total", //Total factura 
  'phone_pl' => "Telèfons",
  'net_amount' => "Import net", //importe netto 
  'gross_amount' => "Import brut", //importe brutto
  'add_pagebreak' => "Prem aquí per a afegir un salt de pàgina",
  'remove_pagebreak' => "Prem aquí per eliminar el salt de pàgina",

  'show_deactivated' => "Mostra productes desactivats",
  'nav_report_sales' => "Compres", 
  'nav_help' => "Ajuda", 
  'withdraw_from' => "Retirar diners de",  //account
  'withdraw_to_bank' => "Retirar diners de banc",
  'withdraw_uf' => "Retirar diners de compte UF", 
  'withdraw_cuota' => "Retirar quota de membre",
  'msg_err_noorder' => "No s'ha trobat cap producte per el període seleccionat!",
  'primer_torn' => "Primer Torn",
  'segon_torn' => "Segon Torn",
  'dff_qty' => "Dif. quantitat",
  'dff_price' => "Dif. preu",
  'ti_mgn_stock_mov' => "Moviments stock",
  'stock_acc_loss_ever' => "Pèrdua acumulada",
  'closed' => "tancat",
  'preorder_item' => "Aquest producte forma part d'una comanda acumulativa",
  'do_preorder' => "Des-/activar com comanda acumulativa",
  'do_deactivate_prod' => "Activar/desactivar producte",
  'msg_make_preorder_p' => "Aixó es una comanda acumulativa, per tant encara no te data d'entrega",
  'btn_ok_go' => "Ok, endavant!",
  'msg_pwd_emailed' => "La nova contrasenya s'ha enviat a l'usuari",
  'msg_pwd_change' => "La nova contrasenya és: ",
  'msg_err_emailed' => "Error d'enviament!",
  'msg_order_emailed' => "La comanda s'ha enviat correctament!",
  'msg_err_responsible_uf' => "No s'ha trobat cap responsable per aquest producte",
  'msg_err_finalize' => "Ups... error al finalitzar comanda!",
  'msg_err_cart_sync' => "La teva comanda no està sincronitzada amb la base de dades, doncs s'ha modificat la teva cistella mentre compraves. Torna a actualitzar la teva comanda!",
  'msg_err_no_deposit' => "La darrera UF no ha realitzat cap dipòsit???!!!",
  'btn_load_cart' => "Continua amb la següent UF",
  'btn_deposit_now' => "Fes l'ingrés ara",
  'msg_err_stock_mv' => "De moment no hi ha cap moviment d'estoc per aquest producte.",

  'ti_report_shop_pv' => "Total vendes per proveidor",
  'filter_all_sales' => "Totes les vendes",
  'filter_exact' => "Escull dades",
  'total_4date' => "Total",
  'total_4provider' => "Suma",
  'sel_sales_dates' => "Per quines dades vols consultar les vendes?",
  'sel_sales_dates_ti' => "Escull un període", 

  'instant_repeat' => "Repeteix directament",
  'msg_confirm_delordereditems' => "Ja s'ha demanat aquest producte per aquest dia. Estàs segur de desactivar-l'ho? Això esborarrà la comanda d'aquest producte de les cistelles.",
  'msg_confirm_instantr' => "Vols repetir aquesta acció per a la resta de les dates activades?",
  'msg_err_delorerable' => "Ja s'han demanat productes per aquesta data. No es pot desactivar!",
  'msg_pre2Order' => "Converteix aquesta comanda acumulativa a comanda normal. Pots triar una data d'entrega",

  'msg_err_modified_order' => "Algú ha modificat els productes a demanar per la data actual. Alguns productes que havíes demanat ja no estàn disponibles i desapareixeràn del teu carret una vegada recarregat.",
  'btn_confirm_del' => "Esborrar! Estic segur",
  'print_new_win' => "Nova finestra",
  'print_pdf' => "Descarregar pdf",
  'msg_incident_emailed' => "L'incident s'ha enviat correctament!",
  'upcoming_orders' => "Pròximes comandes",

  'msg_confirm_del_mem' => "Estas segur de eliminar aquest usuari de la base de dades??",
  'btn_del' => "Esborrar",

  'btn_new_provider' => "Nou proveïdor",
  'btn_new_product' => "Afegir producte",
  'orderable' => "Comanda directe", //product type
  'msg_err_providershort' => "El nom del proveïdor no pot quedar buit i ha de ser de més de 2 caràcters.",
  'msg_err_select_responsibleuf' => "Qui s'encarrega? S'ha de seleccionar un responsable.",
  'msg_err_product_category' => "S'ha de seleccionar una categoria de producte.",
  'msg_err_order_unit' => "S'ha de seleccionar una unitat de mesura per a la comanda.",
  'msg_err_shop_unit' => "S'ha de seleccionar una unitat de mesura per a la venda.",
  'click_row_edit' => "Fes clic per editar.",
  'click_to_list' => "Fes click per a desplegar la llista de productes.",
  'head_ti_provider' => "Gestió de proveïdors i productes",
  'edit' => "Editar",
  'ti_create_provider' => "Afegir proveïdor",
  'ti_add_product' => "Afegir producte",
  'order_min' => "Quantitat mínima per la comanda.",
  'msg_confirm_del_product' => "¿Segur que vols esborrar aquest producte?",
  'msg_err_del_product' => "No es pot esborrar aquest producte per què hi ha entrades que depenen d'ell a la base de dades. Missatge d'error: ",
  'msg_err_del_member' => "No es pot esborrar aquest usuari per que hi ha referències al mateix a la base de dades.<br/> Missatge d'error: ",
  'msg_confirm_del_provider' => "¿Segur que vols esborrar aquest proveïdor?",
  'msg_err_del_provider' => "No es pot esborrar aquest proveïdor. Esborra primer els seus productes i torna a provar.",
  'price_net' => "Preu net",
  'custom_product_ref' => "Id extern", 
  'btn_back_products' => "Tornar a productes",

  'copy_column' => "Copiar columna",
  'paste_column' => "Enganxar",

  'search_provider' => "Buscar proveïdor",
  'msg_err_export' => "Error exportant dades",
  'export_uf' => "Exportar membres",
  'btn_export' => "Exportar",

  'ti_visualization' => "Visualitzacions",
  'file_name' => "Nom de fitxer",
  'active_ufs' => "Només UFs actives",
  'export_format' => "Format d'exportació",
  'google_account' => "Google account",
  'other_options' => "Altres opcions",
  'export_publish' => "Fes fitxer d'exportació públic en:",
  'export_options' => "Opcions d'exportació",
  'correct_stock' => "Corregeix estoc",
  'btn_edit_stock' => "Edita estoc",
  'consult_mov_stock' => "Consulta moviments",
  'add_stock_frase' => "Estoc total = estoc actual de ", //complete frase is: total stock = current stock of X units + new stock
  'correct_stock_frase' => "L'estoc actual no és de",
  'stock_but' => "sinó", //current stock is not x units but...
  'stock_info' => "Nota: es poden consultar tots els canvis d'estoc (addicions, correccions, pèrdues) fent clic al nom del producte aquí baix.",
  'stock_info_product' => "Nota: es poden consultar tots els canvis d'estoc (addicions, correccions, pèrdues totals) des de la secció Informes &gt; Estoc.",


  'msg_confirm_prov' => "Estàs segur que vols exportar tots els proveïdors?", 
  'msg_err_upload' => "S'ha produït un error en la càrrega de l'arxiu ", 
  'msg_import_matchcol' => "Cal fer coincidir les entrades de la base de dades amb les files de la taula. Has d'assignar la columna que correspon a ", //+ here then comes the name of the matching column, e.g. custom_product_ref
  'msg_import_furthercol' => "Quines altres columnes de la taula vols importar a més de la columna necessària?", 
  'msg_import_success' => "L'importació ha funcionat correctament. Vols importar un altre arxiu?", 
  'btn_import_another' => "Importar un altre", 
  'btn_nothx' => "No, gràcies", 
  'import_allowed' => "Formats compatibles", //as in allowed file formats
  'import_file' => "Arxiu d'importació", 
  'public_url' => "URL pública",
  'btn_load_file' => "Afegi arxiu",
  'msg_uploading' => "S'està carregant el fitxer i s'està generant la vista prèvia. Espera ...",
  'msg_parsing' => "S'està llegint l'arxiu del servidor i s'està analitzant. Espera ...",
  'import_step1' => "Afegir arxiu",
  'import_step2' => "Vista prèvia de les dades i assignació de columnes",
  'import_reqcol' => "Columna necessària",
  'import_auto' => "Tinc bones notícies: gairebé totes les dades (columnes) s'han pogut reconèixer i pots intentar la importació automàtica. De tota manera, l'opció més segura és previsualitzar primer el contingut i després realitzar l'assignació de columnes de la taula manualment.",
  'import_qnew' => "Què vols fer amb les dades que no existeixen a la base de dades actual?",
  'import_createnew' => "Crear entrades noves",
  'import_update' => "Actualitzar només les files existents",
  'btn_imp_direct' => "Importar directament",
  'btn_import' => "Importar",
  'btn_preview' => "Vista prèvia", 
  'sel_matchcol' => "Assignar columna ...", 
  'ti_import_products' => "Importar o actualitzar els productes de ", 
  'ti_import_providers' => "Importar proveïdor", 
  'head_ti_import' => "Assistent d'importació",

  'withdraw_desc_banc' => "Withdraw money from account or make transfer for provider payment.",
  'deposit_desc_banc' => "Register all incoming money to consum account.",
  'deposit_banc' => "Deposit to consume account",
  'withdraw_banc' => "Withdraw from consume account",
  'deposit_sales_cash' => "Deposit sales cash",
  'ti_stock_report' => "Stock report for ", 
  'netto_stock' => "Netto stock value", 
  'brutto_stock' => "Brutto stock value", 
  'total_netto_stock' => "Total netto stock value", 
  'total_brutto_stock' => "Total brutto stock value", 
  'sales_total_pv' => "Sales total for provider ",
  'dates_breakdown' => "Dates break down", 
  'price_brutto' => "Price brutto", 
  'total_brutto' => "Brutto total",
  'total_netto' => "Netto total",
  'msg_err_oldPwdWrong' => "Sorry, but you got your old password wrong. Please try again. ", 
  'msg_err_adminStuff' => "Insufficient access priviledges. Only Admin can do that!",
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
