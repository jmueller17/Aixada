<?php
class canned_table_manager {

  public function get_col_names_as_JSON($table)
  {
    switch($table) {
 case 'aixada_account':
     return "['id','TRANSLATION(account_id)','Quantitat','TRANSLATION(payment_method)','TRANSLATION(currency)','Descripci\u00f3','TRANSLATION(operator_id)','TRANSLATION(ts)','Balan\u00e7']";

 case 'aixada_account_balance':
     return "['TRANSLATION(account_id)','Balan\u00e7','TRANSLATION(last_update)']";

 case 'aixada_currency':
     return "['id','Nom','TRANSLATION(one_euro)']";

 case 'aixada_distributor':
     return "['id','Nom','Contacte','Adre\u00e7a','TRANSLATION(nif)','CP','Localitat','Tel\u00e8fon1','Tel\u00e8fon2','Fax','Correu-e','URL','Observacions','Actiu','Unitat familiar responsable']";

 case 'aixada_favorite_order_cart':
     return "['id','TRANSLATION(uf)','Nom']";

 case 'aixada_favorite_order_item':
     return "['id','TRANSLATION(favorite_order_cart)','TRANSLATION(uf)','TRANSLATION(product)','Quantitat','TRANSLATION(ts_ordered)']";

 case 'aixada_incident':
     return "['id','Assumpte','Tipus','TRANSLATION(operator_id)','TRANSLATION(details)','Prioritat','UFs afectades','TRANSLATION(commission_concerned)','Per al prove\u00efdor','TRANSLATION(ts)','Estat']";

 case 'aixada_incident_type':
     return "['id','Descripci\u00f3','TRANSLATION(definition)']";

 case 'aixada_member':
     return "['id','TRANSLATION(uf)','Nom','Adre\u00e7a','CP','Localitat','Tel\u00e8fon1','Tel\u00e8fon2','URL','TRANSLATION(picture)','Observacions','Actiu','Participant','TRANSLATION(adult)']";

 case 'aixada_order_item':
     return "['id','TRANSLATION(date_for_order)','TRANSLATION(uf)','TRANSLATION(product)','Quantitat','TRANSLATION(ts_ordered)']";

 case 'aixada_orderable_type':
     return "['id','Descripci\u00f3']";

 case 'aixada_payment_method':
     return "['id','Descripci\u00f3','TRANSLATION(details)']";

 case 'aixada_product':
     return "['id','Prove\u00efdor','Nom','Descripci\u00f3','Codi de barres','Actiu','Unitat familiar responsable','Com fer-ne la comanda','Categoria','Tipus d_impost revolucionari','Preu per unitat','Percentatge d_IVA','Unitat de comanda','Unitat de venda','Quantitat m\u00ednima per tindre en estoc','Quantitat actual en estoc','Difer\u00e8ncia amb l_estoc m\u00ednim','URL de descripci\u00f3']";

 case 'aixada_product_category':
     return "['id','Descripci\u00f3']";

 case 'aixada_product_orderable_for_date':
     return "['id','TRANSLATION(product)','TRANSLATION(date_for_order)']";

 case 'aixada_provider':
     return "['id','Nom','Contacte','Adre\u00e7a','TRANSLATION(nif)','CP','Localitat','Tel\u00e8fon1','Tel\u00e8fon2','Fax','Correu-e','URL','Observacions','Actiu','Unitat familiar responsable']";

 case 'aixada_providers_of_distributor':
     return "['id','TRANSLATION(distributor)','Prove\u00efdor']";

 case 'aixada_rev_tax_type':
     return "['id','Descripci\u00f3','TRANSLATION(rev_tax_percent)']";

 case 'aixada_shop_item':
     return "['id','TRANSLATION(uf)','TRANSLATION(date_for_shop)','TRANSLATION(product)','Quantitat','TRANSLATION(ts_validated)','TRANSLATION(operator_id)']";

 case 'aixada_shopping_dates':
     return "['TRANSLATION(shopping_date)','TRANSLATION(available)']";

 case 'aixada_stock_movement':
     return "['id','TRANSLATION(product)','TRANSLATION(operator_id)','TRANSLATION(amount_difference)','Descripci\u00f3','TRANSLATION(resulting_amount)','TRANSLATION(ts)']";

 case 'aixada_uf':
     return "['id','Nom','Actiu','Creat el dia','Unitat familiar amfitriona']";

 case 'aixada_unit_measure':
     return "['id','Unitat']";

 case 'aixada_user':
     return "['id','Inici de sessi\u00f3','TRANSLATION(password)','Correu-e','TRANSLATION(uf)','TRANSLATION(member)','Prove\u00efdor','TRANSLATION(language)','TRANSLATION(color_scheme_id)','TRANSLATION(last_login_attempt)','TRANSLATION(last_successful_login)','TRANSLATION(created_on)']";

 case 'aixada_user_role':
     return "['TRANSLATION(user_id)','TRANSLATION(role)']";

    }
  }
  public function get_col_model_as_JSON($table)
  {
    switch($table) {
 case 'aixada_account':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'account_id',index:'account_id',label:'TRANSLATION(account_id)',width:'150',xmlmap:'account_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'quantity',index:'quantity',label:'Quantitat',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'payment_method_id',index:'payment_method',label:'TRANSLATION(payment_method_id)',width:'300',xmlmap:'payment_method',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_payment_method&field1=id&field2=description'}},{name:'currency_id',index:'currency',label:'TRANSLATION(currency_id)',width:'300',xmlmap:'currency',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_currency&field1=id&field2=name'}},{name:'description',index:'description',label:'Descripci\u00f3',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'balance',index:'balance',label:'Balan\u00e7',width:'150',xmlmap:'balance',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_account_balance':
     return "[{name:'account_id',index:'account_id',label:'TRANSLATION(account_id)',width:'150',xmlmap:'account_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'balance',index:'balance',label:'Balan\u00e7',width:'150',xmlmap:'balance',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'last_update',index:'last_update',label:'TRANSLATION(last_update)',width:'300',xmlmap:'last_update',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_currency':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nom',width:'50',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'one_euro',index:'one_euro',label:'TRANSLATION(one_euro)',width:'150',xmlmap:'one_euro',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_distributor':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nom',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'contact',index:'contact',label:'Contacte',width:'255',xmlmap:'contact',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'address',index:'address',label:'Adre\u00e7a',width:'255',xmlmap:'address',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'nif',index:'nif',label:'TRANSLATION(nif)',width:'15',xmlmap:'nif',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'zip',index:'zip',label:'CP',width:'150',xmlmap:'zip',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'city',index:'city',label:'Localitat',width:'255',xmlmap:'city',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'phone1',index:'phone1',label:'Tel\u00e8fon1',width:'50',xmlmap:'phone1',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'phone2',index:'phone2',label:'Tel\u00e8fon2',width:'50',xmlmap:'phone2',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'fax',index:'fax',label:'Fax',width:'100',xmlmap:'fax',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'email',index:'email',label:'Correu-e',width:'100',xmlmap:'email',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'web',index:'web',label:'URL',width:'255',xmlmap:'web',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'notes',index:'notes',label:'Observacions',width:'300',xmlmap:'notes',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Actiu',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'responsible_uf_id',index:'responsible_uf',label:'TRANSLATION(responsible_uf_id)',width:'300',xmlmap:'responsible_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}}]";

 case 'aixada_favorite_order_cart':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'name',index:'name',label:'Nom',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_favorite_order_item':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'favorite_order_cart_id',index:'favorite_order_cart',label:'TRANSLATION(favorite_order_cart_id)',width:'300',xmlmap:'favorite_order_cart',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_favorite_order_cart&field1=id&field2=name'}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'quantity',index:'quantity',label:'Quantitat',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_ordered',index:'ts_ordered',label:'TRANSLATION(ts_ordered)',width:'300',xmlmap:'ts_ordered',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_incident':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'subject',index:'subject',label:'Assumpte',width:'255',xmlmap:'subject',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'incident_type_id',index:'incident_type',label:'TRANSLATION(incident_type_id)',width:'300',xmlmap:'incident_type',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_incident_type&field1=id&field2=description'}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'details',index:'details',label:'TRANSLATION(details)',width:'300',xmlmap:'details',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'priority',index:'priority',label:'Prioritat',width:'150',xmlmap:'priority',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ufs_concerned',index:'ufs_concerned',label:'UFs afectades',width:'100',xmlmap:'ufs_concerned',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'commission_concerned',index:'commission_concerned',label:'TRANSLATION(commission_concerned)',width:'100',xmlmap:'commission_concerned',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'provider_concerned',index:'provider_concerned',label:'Per al prove\u00efdor',width:'100',xmlmap:'provider_concerned',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'status',index:'status',label:'Estat',width:'150',xmlmap:'status',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_incident_type':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'definition',index:'definition',label:'TRANSLATION(definition)',width:'300',xmlmap:'definition',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_member':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'name',index:'name',label:'Nom',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'address',index:'address',label:'Adre\u00e7a',width:'255',xmlmap:'address',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'zip',index:'zip',label:'CP',width:'150',xmlmap:'zip',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'city',index:'city',label:'Localitat',width:'255',xmlmap:'city',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'phone1',index:'phone1',label:'Tel\u00e8fon1',width:'50',xmlmap:'phone1',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'phone2',index:'phone2',label:'Tel\u00e8fon2',width:'50',xmlmap:'phone2',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'web',index:'web',label:'URL',width:'255',xmlmap:'web',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'picture',index:'picture',label:'TRANSLATION(picture)',width:'255',xmlmap:'picture',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'notes',index:'notes',label:'Observacions',width:'300',xmlmap:'notes',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Actiu',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'participant',index:'participant',label:'Participant',width:'1',xmlmap:'participant',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'adult',index:'adult',label:'TRANSLATION(adult)',width:'1',xmlmap:'adult',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_order_item':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'date_for_order',index:'date_for_order',label:'TRANSLATION(date_for_order)',width:'300',xmlmap:'date_for_order',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'quantity',index:'quantity',label:'Quantitat',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_ordered',index:'ts_ordered',label:'TRANSLATION(ts_ordered)',width:'300',xmlmap:'ts_ordered',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_orderable_type':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_payment_method':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'50',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'details',index:'details',label:'TRANSLATION(details)',width:'255',xmlmap:'details',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_product':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'provider_id',index:'provider',label:'TRANSLATION(provider_id)',width:'300',xmlmap:'provider',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_provider&field1=id&field2=name'}},{name:'name',index:'name',label:'Nom',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'300',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'barcode',index:'barcode',label:'Codi de barres',width:'50',xmlmap:'barcode',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Actiu',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'responsible_uf_id',index:'responsible_uf',label:'TRANSLATION(responsible_uf_id)',width:'300',xmlmap:'responsible_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'orderable_type_id',index:'orderable_type',label:'TRANSLATION(orderable_type_id)',width:'300',xmlmap:'orderable_type',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_orderable_type&field1=id&field2=description'}},{name:'category_id',index:'category',label:'TRANSLATION(category_id)',width:'300',xmlmap:'category',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_product_category&field1=id&field2=description'}},{name:'rev_tax_type_id',index:'rev_tax_type',label:'TRANSLATION(rev_tax_type_id)',width:'300',xmlmap:'rev_tax_type',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_rev_tax_type&field1=id&field2=description'}},{name:'unit_price',index:'unit_price',label:'Preu per unitat',width:'150',xmlmap:'unit_price',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'iva_percent',index:'iva_percent',label:'Percentatge d_IVA',width:'150',xmlmap:'iva_percent',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'unit_measure_order_id',index:'unit_measure_order',label:'TRANSLATION(unit_measure_order_id)',width:'300',xmlmap:'unit_measure_order',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_unit_measure&field1=id&field2=unit'}},{name:'unit_measure_shop_id',index:'unit_measure_shop',label:'TRANSLATION(unit_measure_shop_id)',width:'300',xmlmap:'unit_measure_shop',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_unit_measure&field1=id&field2=unit'}},{name:'stock_min',index:'stock_min',label:'Quantitat m\u00ednima per tindre en estoc',width:'150',xmlmap:'stock_min',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'stock_actual',index:'stock_actual',label:'Quantitat actual en estoc',width:'150',xmlmap:'stock_actual',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'delta_stock',index:'delta_stock',label:'Difer\u00e8ncia amb l_estoc m\u00ednim',width:'150',xmlmap:'delta_stock',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'description_url',index:'description_url',label:'URL de descripci\u00f3',width:'255',xmlmap:'description_url',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_product_category':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_product_orderable_for_date':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'date_for_order',index:'date_for_order',label:'TRANSLATION(date_for_order)',width:'300',xmlmap:'date_for_order',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_provider':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nom',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'contact',index:'contact',label:'Contacte',width:'255',xmlmap:'contact',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'address',index:'address',label:'Adre\u00e7a',width:'255',xmlmap:'address',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'nif',index:'nif',label:'TRANSLATION(nif)',width:'15',xmlmap:'nif',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'zip',index:'zip',label:'CP',width:'150',xmlmap:'zip',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'city',index:'city',label:'Localitat',width:'255',xmlmap:'city',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'phone1',index:'phone1',label:'Tel\u00e8fon1',width:'50',xmlmap:'phone1',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'phone2',index:'phone2',label:'Tel\u00e8fon2',width:'50',xmlmap:'phone2',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'fax',index:'fax',label:'Fax',width:'100',xmlmap:'fax',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'email',index:'email',label:'Correu-e',width:'100',xmlmap:'email',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'web',index:'web',label:'URL',width:'255',xmlmap:'web',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'notes',index:'notes',label:'Observacions',width:'300',xmlmap:'notes',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Actiu',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'responsible_uf_id',index:'responsible_uf',label:'TRANSLATION(responsible_uf_id)',width:'300',xmlmap:'responsible_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}}]";

 case 'aixada_providers_of_distributor':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'distributor_id',index:'distributor',label:'TRANSLATION(distributor_id)',width:'300',xmlmap:'distributor',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_distributor&field1=id&field2=name'}},{name:'provider_id',index:'provider',label:'TRANSLATION(provider_id)',width:'300',xmlmap:'provider',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_provider&field1=id&field2=name'}}]";

 case 'aixada_rev_tax_type':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'50',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'rev_tax_percent',index:'rev_tax_percent',label:'TRANSLATION(rev_tax_percent)',width:'150',xmlmap:'rev_tax_percent',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_shop_item':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'date_for_shop',index:'date_for_shop',label:'TRANSLATION(date_for_shop)',width:'300',xmlmap:'date_for_shop',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'quantity',index:'quantity',label:'Quantitat',width:'150',xmlmap:'quantity',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts_validated',index:'ts_validated',label:'TRANSLATION(ts_validated)',width:'300',xmlmap:'ts_validated',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_shopping_dates':
     return "[{name:'shopping_date',index:'shopping_date',label:'TRANSLATION(shopping_date)',width:'300',xmlmap:'shopping_date',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'available',index:'available',label:'TRANSLATION(available)',width:'1',xmlmap:'available',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_stock_movement':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'product_id',index:'product',label:'TRANSLATION(product_id)',width:'300',xmlmap:'product',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_product&field1=id&field2=name'}},{name:'operator_id',index:'operator_id',label:'TRANSLATION(operator_id)',width:'150',xmlmap:'operator_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'amount_difference',index:'amount_difference',label:'TRANSLATION(amount_difference)',width:'150',xmlmap:'amount_difference',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'description',index:'description',label:'Descripci\u00f3',width:'255',xmlmap:'description',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'resulting_amount',index:'resulting_amount',label:'TRANSLATION(resulting_amount)',width:'150',xmlmap:'resulting_amount',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'ts',index:'ts',label:'TRANSLATION(ts)',width:'300',xmlmap:'ts',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_uf':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'name',index:'name',label:'Nom',width:'255',xmlmap:'name',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'active',index:'active',label:'Actiu',width:'150',xmlmap:'active',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true},edittype:'checkbox',editoptions:{value:'1:0'}},{name:'created',index:'created',label:'Creat el dia',width:'300',xmlmap:'created',editable:false,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'mentor_uf',index:'mentor_uf',label:'Unitat familiar amfitriona',width:'150',xmlmap:'mentor_uf',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_unit_measure':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'unit',index:'unit',label:'Unitat',width:'50',xmlmap:'unit',editable:true,hidden:false,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_user':
     return "[{name:'id',index:'id',label:'id',width:'150',xmlmap:'id',editable:false,hidden:false,editrules:{edithidden:true,searchhidden:true}},{name:'login',index:'login',label:'Inici de sessi\u00f3',width:'50',xmlmap:'login',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'password',index:'password',label:'TRANSLATION(password)',width:'255',xmlmap:'password',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'password'},{name:'email',index:'email',label:'Correu-e',width:'100',xmlmap:'email',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'uf_id',index:'uf',label:'TRANSLATION(uf_id)',width:'300',xmlmap:'uf',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_uf&field1=id&field2=name'}},{name:'member_id',index:'member',label:'TRANSLATION(member_id)',width:'300',xmlmap:'member',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_member&field1=id&field2=name'}},{name:'provider_id',index:'provider',label:'TRANSLATION(provider_id)',width:'300',xmlmap:'provider',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true},edittype:'select',editoptions:{dataUrl:'ctrlSmallQ.php?oper=getFieldOptions&table=aixada_provider&field1=id&field2=name'}},{name:'language',index:'language',label:'TRANSLATION(language)',width:'5',xmlmap:'language',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'color_scheme_id',index:'color_scheme_id',label:'TRANSLATION(color_scheme_id)',width:'150',xmlmap:'color_scheme_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'last_login_attempt',index:'last_login_attempt',label:'TRANSLATION(last_login_attempt)',width:'300',xmlmap:'last_login_attempt',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'last_successful_login',index:'last_successful_login',label:'TRANSLATION(last_successful_login)',width:'300',xmlmap:'last_successful_login',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'created_on',index:'created_on',label:'TRANSLATION(created_on)',width:'300',xmlmap:'created_on',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

 case 'aixada_user_role':
     return "[{name:'user_id',index:'user_id',label:'TRANSLATION(user_id)',width:'150',xmlmap:'user_id',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}},{name:'role',index:'role',label:'TRANSLATION(role)',width:'100',xmlmap:'role',editable:true,hidden:true,editrules:{edithidden:true,searchhidden:true}}]";

    }
  }
  public function get_active_fields_as_JSON($table)
  {
    switch($table) {
 case 'aixada_account':
     return "[payment_method_id,currency_id]";

 case 'aixada_account_balance':
     return "[]";

 case 'aixada_currency':
     return "[]";

 case 'aixada_distributor':
     return "[responsible_uf_id]";

 case 'aixada_favorite_order_cart':
     return "[uf_id]";

 case 'aixada_favorite_order_item':
     return "[favorite_order_cart_id,uf_id,product_id]";

 case 'aixada_incident':
     return "[incident_type_id]";

 case 'aixada_incident_type':
     return "[]";

 case 'aixada_member':
     return "[uf_id]";

 case 'aixada_order_item':
     return "[uf_id,product_id]";

 case 'aixada_orderable_type':
     return "[]";

 case 'aixada_payment_method':
     return "[]";

 case 'aixada_product':
     return "[provider_id,responsible_uf_id,orderable_type_id,category_id,rev_tax_type_id,unit_measure_order_id,unit_measure_shop_id]";

 case 'aixada_product_category':
     return "[]";

 case 'aixada_product_orderable_for_date':
     return "[product_id]";

 case 'aixada_provider':
     return "[responsible_uf_id]";

 case 'aixada_providers_of_distributor':
     return "[distributor_id,provider_id]";

 case 'aixada_rev_tax_type':
     return "[]";

 case 'aixada_shop_item':
     return "[uf_id,product_id]";

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