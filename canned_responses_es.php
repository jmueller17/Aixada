<?php
class canned_table_manager {

  public function get_col_names_as_JSON($table)
  {
    switch($table) {
 case 'aixada_account':
     return "['id','TRANSLATION(account_id)','Cantidad','TRANSLATION(payment_method_id)','TRANSLATION(currency_id)','Descripci\u00f3n','TRANSLATION(operator_id)','TRANSLATION(ts)','Balance']";

 case 'aixada_cart':
     return "['id','Nombre','TRANSLATION(uf)','Fecha de compra','TRANSLATION(operator_id)','Validada','TRANSLATION(ts_last_saved)']";

 case 'aixada_currency':
     return "['id','Nombre','TRANSLATION(one_euro)']";

 case 'aixada_estimated_prices':
     return "['TRANSLATION(product_id)','TRANSLATION(ts)','TRANSLATION(min_estimated_price)','TRANSLATION(max_estimated_price)','TRANSLATION(true_price)']";

 case 'aixada_incident':
     return "['id','Asunto','Tipo','TRANSLATION(operator_id)','Detalles','Prioridad','UFs afectadas','TRANSLATION(commission_concerned)','Para el proveedor','TRANSLATION(ts)','Estado']";

 case 'aixada_incident_type':
     return "['id','Descripci\u00f3n','TRANSLATION(definition)']";

 case 'aixada_iva_type':
     return "['id','Nombre','Porcentaje','Descripci\u00f3n']";

 case 'aixada_member':
     return "['id','Ref. personalizada','TRANSLATION(uf)','Nombre','Direcci\u00f3n','NIF','CP','Localidad','Tel\u00e9fono1','Tel\u00e9fono2','URL','Banco o Caja','Cuenta bancario','Imagen','Observaciones','Activo','Participante','Adulto','TRANSLATION(ts)']";

 case 'aixada_order':
     return "['id','Proveedor','Fecha de entrega','TRANSLATION(ts_sent_off)','TRANSLATION(date_received)','Fecha de compra','Total','Observaciones','TRANSLATION(revision_status)','Ref. de entrega','Ref. de pago']";

 case 'aixada_order_item':
     return "['id','TRANSLATION(order_id)','TRANSLATION(unit_price_stamp)','Fecha de entrega','TRANSLATION(uf)','TRANSLATION(favorite_cart)','TRANSLATION(product)','Cantidad','TRANSLATION(ts_ordered)']";

 case 'aixada_order_to_shop':
     return "['TRANSLATION(order_item_id)','TRANSLATION(uf)','TRANSLATION(order_id)','TRANSLATION(unit_price_stamp)','TRANSLATION(product)','Cantidad','Llegado','TRANSLATION(revised)','TRANSLATION(aixada_order_to_shop_ibfk_1)','TRANSLATION(aixada_order_to_shop_ibfk_2)','TRANSLATION(aixada_order_to_shop_ibfk_3)']";

 case 'aixada_orderable_type':
     return "['id','Descripci\u00f3n']";

 case 'aixada_payment_method':
     return "['id','Descripci\u00f3n','Detalles']";

 case 'aixada_price':
     return "['TRANSLATION(product)','TRANSLATION(ts)','TRANSLATION(current_price)','TRANSLATION(operator_id)']";

 case 'aixada_product':
     return "['id','Proveedor','Nombre','Descripci\u00f3n','C\u00f3digo de barras','Id externo','Activo','Unidad familiar responsable','Tipo de producto','TRANSLATION(order_min_quantity)','Categor\u00eda','Tipo de impuesto revolucionario','Porcentaje de IVA','Precio por unidad','Unidades para el pedido','Unidades para la venta','Cantidad m\u00ednima para tener en stock','Cantidad actual en stock','Diferencia con el stock m\u00ednimo','URL de descripci\u00f3n','Imagen','TRANSLATION(ts)']";

 case 'aixada_product_category':
     return "['id','Descripci\u00f3n']";

 case 'aixada_product_orderable_for_date':
     return "['id','TRANSLATION(product_id)','Fecha de entrega','TRANSLATION(closing_date)']";

 case 'aixada_provider':
     return "['id','Nombre','Contacto','Direcci\u00f3n','NIF','CP','Localidad','Tel\u00e9fono1','Tel\u00e9fono2','Fax','Correo electr\u00f3nico','URL','Banco o Caja','Cuenta bancario','Imagen','Observaciones','Activo','Unidad familiar responsable','Tiempo de procesamiento','TRANSLATION(ts)']";

 case 'aixada_rev_tax_type':
     return "['id','Nombre','Descripci\u00f3n','TRANSLATION(rev_tax_percent)']";

 case 'aixada_shop_item':
     return "['id','TRANSLATION(cart)','TRANSLATION(order_item_id)','TRANSLATION(unit_price_stamp)','TRANSLATION(product)','Cantidad','Porcentaje de IVA','TRANSLATION(rev_tax_percent)']";

 case 'aixada_shopping_dates':
     return "['TRANSLATION(shopping_date)','TRANSLATION(available)']";

 case 'aixada_stock_movement':
     return "['id','TRANSLATION(product)','TRANSLATION(operator_id)','TRANSLATION(amount_difference)','Descripci\u00f3n','TRANSLATION(resulting_amount)','TRANSLATION(ts)']";

 case 'aixada_uf':
     return "['id','Nombre','Activo','Creado el d\u00eda','Unidad familiar anfitriona']";

 case 'aixada_unit_measure':
     return "['id','Nombre','Unidad']";

 case 'aixada_user':
     return "['id','Inicio de sesi\u00f3n','TRANSLATION(password)','Correo electr\u00f3nico','TRANSLATION(uf)','Miembro','Proveedor','TRANSLATION(language)','TRANSLATION(gui_theme)','TRANSLATION(last_login_attempt)','TRANSLATION(last_successful_login)','TRANSLATION(created_on)']";

 case 'aixada_user_role':
     return "['TRANSLATION(user_id)','TRANSLATION(role)']";

    }
  }
  public function get_col_model_as_JSON($table)
  {
    switch($table) {
 case 'aixada_account':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'account_id',index:'account_id',label:'TRANSLATION(account_id)',width:'150',xmlmap:'account_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'quantity',index:'quantity',label:'Cantidad',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'payment_method_id',index:'payment_method_id',label:'TRANSLATION(payment_method_id)',width:'150',xmlmap:'payment_method_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'currency_id',index:'currency_id',label:'TRANSLATION(currency_id)',width:'150',xmlmap:'currency_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'balance',index:'balance',label:'Balance',width:'150',xmlmap:'balance',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_cart':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'date_for_shop',index:'date_for_shop',label:'Fecha de compra',width:'300',xmlmap:'date_for_shop',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_validated',index:'ts_validated',label:'Validada',width:'300',xmlmap:'ts_validated',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_last_saved',index:'ts_last_saved',label:'TRANSLATION(ts_last_saved)',width:'300',xmlmap:'ts_last_saved',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_currency':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'50',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'one_euro',index:'one_euro',label:'TRANSLATION(one_euro)',width:'150',xmlmap:'one_euro',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_estimated_prices':
     return "[{name:'product_id',index:'product_id',label:'TRANSLATION(product_id)',width:'150',xmlmap:'product_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'min_estimated_price',index:'min_estimated_price',label:'TRANSLATION(min_estimated_price)',width:'150',xmlmap:'min_estimated_price',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'max_estimated_price',index:'max_estimated_price',label:'TRANSLATION(max_estimated_price)',width:'150',xmlmap:'max_estimated_price',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'true_price',index:'true_price',label:'TRANSLATION(true_price)',width:'150',xmlmap:'true_price',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_incident':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'subject',index:'subject',label:'Asunto',width:'255',xmlmap:'subject',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'incident_type_id',index:'incident_type',label:'TRANSLATION(incident_type_id)',width:'300',xmlmap:'incident_type',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_incident_type&field1=id&field2=description'}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'details',index:'details',label:'Detalles',width:'300',xmlmap:'details',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'priority',index:'priority',label:'Prioridad',width:'150',xmlmap:'priority',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ufs_concerned',index:'ufs_concerned',label:'UFs afectadas',width:'100',xmlmap:'ufs_concerned',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'commission_concerned',index:'commission_concerned',label:'TRANSLATION(commission_concerned)',width:'100',xmlmap:'commission_concerned',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'provider_concerned',index:'provider_concerned',label:'Para el proveedor',width:'100',xmlmap:'provider_concerned',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'status',index:'status',label:'Estado',width:'150',xmlmap:'status',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_incident_type':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'definition',index:'definition',label:'TRANSLATION(definition)',width:'300',xmlmap:'definition',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_iva_type':
     return "[{name:'id',index:'id',label:'id',width:'6',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'percent',index:'percent',label:'Porcentaje',width:'150',xmlmap:'percent',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'100',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_member':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'custom_member_ref',index:'custom_member_ref',label:'Ref. personalizada',width:'100',xmlmap:'custom_member_ref',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'address',index:'address',label:'Direcci\u00f3n',width:'255',xmlmap:'address',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'nif',index:'nif',label:'NIF',width:'15',xmlmap:'nif',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'zip',index:'zip',label:'CP',width:'150',xmlmap:'zip',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'city',index:'city',label:'Localidad',width:'255',xmlmap:'city',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'phone1',index:'phone1',label:'Tel\u00e9fono1',width:'50',xmlmap:'phone1',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'phone2',index:'phone2',label:'Tel\u00e9fono2',width:'50',xmlmap:'phone2',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'web',index:'web',label:'URL',width:'255',xmlmap:'web',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'bank_name',index:'bank_name',label:'Banco o Caja',width:'255',xmlmap:'bank_name',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'bank_account',index:'bank_account',label:'Cuenta bancario',width:'40',xmlmap:'bank_account',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'picture',index:'picture',label:'Imagen',width:'255',xmlmap:'picture',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'notes',index:'notes',label:'Observaciones',width:'300',xmlmap:'notes',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Activo',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'participant',index:'participant',label:'Participante',width:'1',xmlmap:'participant',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'adult',index:'adult',label:'Adulto',width:'1',xmlmap:'adult',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_order':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'provider_id',index:'provider',label:'TRANSLATION(provider_id)',width:'300',xmlmap:'provider',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_provider&field1=id&field2=name'}},{name:'date_for_order',index:'date_for_order',label:'Fecha de entrega',width:'300',xmlmap:'date_for_order',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_sent_off',index:'ts_sent_off',label:'TRANSLATION(ts_sent_off)',width:'300',xmlmap:'ts_sent_off',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'date_received',index:'date_received',label:'TRANSLATION(date_received)',width:'300',xmlmap:'date_received',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'date_for_shop',index:'date_for_shop',label:'Fecha de compra',width:'300',xmlmap:'date_for_shop',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'total',index:'total',label:'Total',width:'150',xmlmap:'total',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'notes',index:'notes',label:'Observaciones',width:'255',xmlmap:'notes',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'revision_status',index:'revision_status',label:'TRANSLATION(revision_status)',width:'150',xmlmap:'revision_status',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'delivery_ref',index:'delivery_ref',label:'Ref. de entrega',width:'255',xmlmap:'delivery_ref',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'payment_ref',index:'payment_ref',label:'Ref. de pago',width:'255',xmlmap:'payment_ref',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_order_item':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'order_id',index:'order_id',label:'TRANSLATION(order_id)',width:'150',xmlmap:'order_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'unit_price_stamp',index:'unit_price_stamp',label:'TRANSLATION(unit_price_stamp)',width:'150',xmlmap:'unit_price_stamp',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'date_for_order',index:'date_for_order',label:'Fecha de entrega',width:'300',xmlmap:'date_for_order',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'favorite_cart_id',index:'favorite_cart',label:'TRANSLATION(favorite_cart_id)',width:'300',xmlmap:'favorite_cart',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_cart&field1=id&field2=name'}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'quantity',index:'quantity',label:'Cantidad',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_ordered',index:'ts_ordered',label:'TRANSLATION(ts_ordered)',width:'300',xmlmap:'ts_ordered',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_order_to_shop':
     return "[{name:'order_item_id',index:'order_item_id',label:'TRANSLATION(order_item_id)',width:'150',xmlmap:'order_item_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'order_id',index:'order_id',label:'TRANSLATION(order_id)',width:'300',xmlmap:'order_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'unit_price_stamp',index:'unit_price_stamp',label:'TRANSLATION(unit_price_stamp)',width:'150',xmlmap:'unit_price_stamp',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'quantity',index:'quantity',label:'Cantidad',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'arrived',index:'arrived',label:'Llegado',width:'1',xmlmap:'arrived',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'revised',index:'revised',label:'TRANSLATION(revised)',width:'1',xmlmap:'revised',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'aixada_order_to_shop_ibfk_1',index:'aixada_order_to_shop_ibfk_1',label:'TRANSLATION(aixada_order_to_shop_ibfk_1)',width:'300',xmlmap:'aixada_order_to_shop_ibfk_1',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'aixada_order_to_shop_ibfk_2',index:'aixada_order_to_shop_ibfk_2',label:'TRANSLATION(aixada_order_to_shop_ibfk_2)',width:'300',xmlmap:'aixada_order_to_shop_ibfk_2',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'aixada_order_to_shop_ibfk_3',index:'aixada_order_to_shop_ibfk_3',label:'TRANSLATION(aixada_order_to_shop_ibfk_3)',width:'300',xmlmap:'aixada_order_to_shop_ibfk_3',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_orderable_type':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_payment_method':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'50',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'details',index:'details',label:'Detalles',width:'255',xmlmap:'details',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_price':
     return "[{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'current_price',index:'current_price',label:'TRANSLATION(current_price)',width:'150',xmlmap:'current_price',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_product':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'provider_id',index:'provider',label:'TRANSLATION(provider_id)',width:'300',xmlmap:'provider',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_provider&field1=id&field2=name'}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'300',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'barcode',index:'barcode',label:'C\u00f3digo de barras',width:'50',xmlmap:'barcode',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'custom_product_ref',index:'custom_product_ref',label:'Id externo',width:'100',xmlmap:'custom_product_ref',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Activo',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'responsible_uf_id',index:'responsible_uf',label:'TRANSLATION(responsible_uf_id)',width:'300',xmlmap:'responsible_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'orderable_type_id',index:'orderable_type',label:'TRANSLATION(orderable_type_id)',width:'300',xmlmap:'orderable_type',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_orderable_type&field1=id&field2=description'}},{name:'order_min_quantity',index:'order_min_quantity',label:'TRANSLATION(order_min_quantity)',width:'150',xmlmap:'order_min_quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'category_id',index:'category',label:'TRANSLATION(category_id)',width:'300',xmlmap:'category',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_product_category&field1=id&field2=description'}},{name:'rev_tax_type_id',index:'rev_tax_type',label:'TRANSLATION(rev_tax_type_id)',width:'300',xmlmap:'rev_tax_type',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_rev_tax_type&field1=id&field2=name'}},{name:'iva_percent_id',index:'iva_percent',label:'Typo de IVA',width:'300',xmlmap:'iva_percent',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_iva_type&field1=id&field2=name'}},{name:'unit_price',index:'unit_price',label:'Precio por unidad',width:'150',xmlmap:'unit_price',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'unit_measure_order_id',index:'unit_measure_order',label:'TRANSLATION(unit_measure_order_id)',width:'300',xmlmap:'unit_measure_order',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_unit_measure&field1=id&field2=name'}},{name:'unit_measure_shop_id',index:'unit_measure_shop',label:'TRANSLATION(unit_measure_shop_id)',width:'300',xmlmap:'unit_measure_shop',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_unit_measure&field1=id&field2=name'}},{name:'stock_min',index:'stock_min',label:'Cantidad m\u00ednima para tener en stock',width:'150',xmlmap:'stock_min',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'stock_actual',index:'stock_actual',label:'Cantidad actual en stock',width:'150',xmlmap:'stock_actual',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'delta_stock',index:'delta_stock',label:'Diferencia con el stock m\u00ednimo',width:'150',xmlmap:'delta_stock',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'description_url',index:'description_url',label:'URL de descripci\u00f3n',width:'255',xmlmap:'description_url',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'picture',index:'picture',label:'Imagen',width:'255',xmlmap:'picture',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_product_category':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_product_orderable_for_date':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product_id',label:'TRANSLATION(product_id)',width:'150',xmlmap:'product_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'date_for_order',index:'date_for_order',label:'Fecha de entrega',width:'300',xmlmap:'date_for_order',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'closing_date',index:'closing_date',label:'TRANSLATION(closing_date)',width:'300',xmlmap:'closing_date',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_provider':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'contact',index:'contact',label:'Contacto',width:'255',xmlmap:'contact',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'address',index:'address',label:'Direcci\u00f3n',width:'255',xmlmap:'address',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'nif',index:'nif',label:'NIF',width:'15',xmlmap:'nif',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'zip',index:'zip',label:'CP',width:'150',xmlmap:'zip',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'city',index:'city',label:'Localidad',width:'255',xmlmap:'city',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'phone1',index:'phone1',label:'Tel\u00e9fono1',width:'50',xmlmap:'phone1',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'phone2',index:'phone2',label:'Tel\u00e9fono2',width:'50',xmlmap:'phone2',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'fax',index:'fax',label:'Fax',width:'100',xmlmap:'fax',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'email',index:'email',label:'Correo electr\u00f3nico',width:'100',xmlmap:'email',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'web',index:'web',label:'URL',width:'255',xmlmap:'web',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'bank_name',index:'bank_name',label:'Banco o Caja',width:'255',xmlmap:'bank_name',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'bank_account',index:'bank_account',label:'Cuenta bancario',width:'40',xmlmap:'bank_account',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'picture',index:'picture',label:'Imagen',width:'255',xmlmap:'picture',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'notes',index:'notes',label:'Observaciones',width:'300',xmlmap:'notes',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Activo',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'responsible_uf_id',index:'responsible_uf',label:'TRANSLATION(responsible_uf_id)',width:'300',xmlmap:'responsible_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'offset_order_close',index:'offset_order_close',label:'Tiempo de procesamiento',width:'150',xmlmap:'offset_order_close',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_rev_tax_type':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'50',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'rev_tax_percent',index:'rev_tax_percent',label:'TRANSLATION(rev_tax_percent)',width:'150',xmlmap:'rev_tax_percent',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_shop_item':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'cart_id',index:'cart',label:'Id de la cesta',width:'300',xmlmap:'cart',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_cart&field1=id&field2=name'}},{name:'order_item_id',index:'order_item_id',label:'TRANSLATION(order_item_id)',width:'150',xmlmap:'order_item_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'unit_price_stamp',index:'unit_price_stamp',label:'TRANSLATION(unit_price_stamp)',width:'150',xmlmap:'unit_price_stamp',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'quantity',index:'quantity',label:'Cantidad',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'iva_percent',index:'iva_percent',label:'Porcentaje de IVA',width:'150',xmlmap:'iva_percent',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'rev_tax_percent',index:'rev_tax_percent',label:'TRANSLATION(rev_tax_percent)',width:'150',xmlmap:'rev_tax_percent',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_shopping_dates':
     return "[{name:'shopping_date',index:'shopping_date',label:'TRANSLATION(shopping_date)',width:'300',xmlmap:'shopping_date',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'available',index:'available',label:'TRANSLATION(available)',width:'1',xmlmap:'available',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_stock_movement':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'amount_difference',index:'amount_difference',label:'TRANSLATION(amount_difference)',width:'150',xmlmap:'amount_difference',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3n',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'resulting_amount',index:'resulting_amount',label:'TRANSLATION(resulting_amount)',width:'150',xmlmap:'resulting_amount',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_uf':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Activo',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'created',index:'created',label:'Creado el d\u00eda',width:'300',xmlmap:'created',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'mentor_uf',index:'mentor_uf',label:'Unidad familiar anfitriona',width:'150',xmlmap:'mentor_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_unit_measure':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nombre',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'unit',index:'unit',label:'Unidad',width:'50',xmlmap:'unit',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_user':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'login',index:'login',label:'Inicio de sesi\u00f3n',width:'50',xmlmap:'login',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'password',index:'password',label:'TRANSLATION(password)',width:'255',xmlmap:'password',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'password'},{name:'email',index:'email',label:'Correo electr\u00f3nico',width:'100',xmlmap:'email',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'member_id',index:'member',label:'Id del miembro',width:'300',xmlmap:'member',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_member&field1=id&field2=name'}},{name:'provider_id',index:'provider',label:'TRANSLATION(provider_id)',width:'300',xmlmap:'provider',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'php\/ctrl\/SmallQ.php?oper=getFieldOptions&table=aixada_provider&field1=id&field2=name'}},{name:'language',index:'language',label:'TRANSLATION(language)',width:'5',xmlmap:'language',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'gui_theme',index:'gui_theme',label:'TRANSLATION(gui_theme)',width:'50',xmlmap:'gui_theme',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'last_login_attempt',index:'last_login_attempt',label:'TRANSLATION(last_login_attempt)',width:'300',xmlmap:'last_login_attempt',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'last_successful_login',index:'last_successful_login',label:'TRANSLATION(last_successful_login)',width:'300',xmlmap:'last_successful_login',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'created_on',index:'created_on',label:'TRANSLATION(created_on)',width:'300',xmlmap:'created_on',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_user_role':
     return "[{name:'user_id',index:'user_id',label:'TRANSLATION(user_id)',width:'150',xmlmap:'user_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'role',index:'role',label:'TRANSLATION(role)',width:'100',xmlmap:'role',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

    }
  }
  public function get_active_fields_as_JSON($table)
  {
    switch($table) {
 case 'aixada_account':
     return "[]";

 case 'aixada_cart':
     return "[uf_id]";

 case 'aixada_currency':
     return "[]";

 case 'aixada_estimated_prices':
     return "[]";

 case 'aixada_incident':
     return "[incident_type_id]";

 case 'aixada_incident_type':
     return "[]";

 case 'aixada_iva_type':
     return "[]";

 case 'aixada_member':
     return "[uf_id]";

 case 'aixada_order':
     return "[provider_id]";

 case 'aixada_order_item':
     return "[uf_id,favorite_cart_id,product_id]";

 case 'aixada_order_to_shop':
     return "[uf_id,product_id]";

 case 'aixada_orderable_type':
     return "[]";

 case 'aixada_payment_method':
     return "[]";

 case 'aixada_price':
     return "[product_id]";

 case 'aixada_product':
     return "[provider_id,responsible_uf_id,orderable_type_id,category_id,rev_tax_type_id,iva_percent_id,unit_measure_order_id,unit_measure_shop_id]";

 case 'aixada_product_category':
     return "[]";

 case 'aixada_product_orderable_for_date':
     return "[]";

 case 'aixada_provider':
     return "[responsible_uf_id]";

 case 'aixada_rev_tax_type':
     return "[]";

 case 'aixada_shop_item':
     return "[cart_id,product_id]";

 case 'aixada_shopping_dates':
     return "[]";

 case 'aixada_stock_movement':
     return "[product_id]";

 case 'aixada_uf':
     return "[]";

 case 'aixada_unit_measure':
     return "[]";

 case 'aixada_user':
     return "[uf_id,member_id,provider_id]";

 case 'aixada_user_role':
     return "[]";

    }
  }
  public function get_list_all_queries($table, $page, $limit)
  {
    return array("SELECT COUNT(*) AS count FROM $table",
		 "SELECT * FROM $table ORDER BY active desc, id asc LIMIT $page, $limit");
  }
}
?>