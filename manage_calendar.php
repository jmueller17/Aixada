<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?></title>
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css?v=<?=aixada_js_version();?>" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <style> \<!-- a:hover{text-decoration:none;} a{text-decoration:none;} \--> </style>
<script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    <script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js?v=<?=aixada_js_version();?>" ></script>
 	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js?v=<?=aixada_js_version();?>" ></script>
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
<script>
 let idData;

 function selData(elem, torn) {

     if(idData!=null){
        document.getElementById(idData).style.color='#000000';
     }
     document.getElementById(elem.id).style.color='#7D7FF5';
     idData=elem.id;
     if(torn==0){
         printCrearTorns(idData);
     }
     else{
         printTorn(idData);
     }
 }

 /*Funció per mostrar un Torn selecionat*/
 function printTorn(dataTorn){

    let parametres = {
        oper : "printTorn",
        data : dataTorn
    };

    document.getElementById("contenidorTorn").style.display = "block";

    $.ajax({
        data: parametres,
        url: 'php/ctrl/Calendar.php',
        type: 'post',
        dataType: "JSON",
        success: function(response){
            let html = '<table>'+
                '<thead>'+
                '<tr><th colspan="5"><h1><?php echo $Text['nav_wiz'];?> ' + dataTorn + '</h1></th></tr>'+
                '<tr><th><?php echo $Text['uf_short']?></th><th><?php echo $Text['uf_long']?></th><th><?php echo $Text['assignar_torn']?></th><th><?php echo $Text['guardar']?></th><th><?php echo $Text['eliminar_uf']?></th></tr>';
            for(let i=0; i<response[0].length; i++){
                html += '<tr>'+
                        '<th>' + response[0][i].id + '</th>'+
                        '<th>' + response[0][i].name + '</th>'+
                        '<th><select id="' + i + '" name="ufs">'+
                            '<option value="" name="ufSeleccionada">...</option>';
                         for(let x=0; x<response[1].length; x++){
                            html += '<option value="'+response[1][x].id+' '+response[0][i].id+' '+response[0][i].dataTorn+'" name="ufSeleccionada">' + response[1][x].nomSelect +'</option>';
                         }
                        html += '</select></th>'+
                            '<th><button class="aix-layout-fixW150" id="btnGuardar" onclick="guardarTorn('+i+')"><?php echo $Text['guardar']?></button></th>'+
                            '<th><button class="aix-layout-fixW150" id="btnEliminar" onclick="eliminarTorn('+response[0][i].id+')"><?php echo $Text['eliminar_uf']?></button></th>'+
                            '</tr>';
            }
            html += '<th>+</th>'+
                    '<th><?php echo $Text['afegir_uf']?></th>'+
                    '<th><select id="'+response[0].length+'" name="ufs">'+
                    '<option value="" name="ufSeleccionada">...</option>';
                    for(let x=0; x<response[1].length; x++){
                       html += '<option value="'+response[1][x].id+' 0 '+response[0][0].dataTorn+'" name="ufSeleccionada">' + response[1][x].nomSelect +'</option>';
                    }
            html+= '</select></th>'+
                   '<th><button class="aix-layout-fixW150" id="btnGuardar" onclick="guardarTorn('+response[0].length+')"><?php echo $Text['afegir_uf']?></button></th>'+
                   '<th></th>'+
                   '</thead></table>'+
                   '<br><button class="aix-layout-fixW150" id="btnEliminar" onclick="eliminarTorn()"><?php echo $Text['eliminar_torn'];?></button>';
            $('#contenidorTorn').html(html);
        }
    });
 }

 /*Funció per guardar un torn*/
 function guardarTorn(elem){
    let dadesSel = document.getElementById(elem).value;
    if(dadesSel=== ""){
        $.showMsg({
            msg:"<?php echo $Text['no_uf'];?>",
		    type: 'atencio'});
    }
    else{
        let parametres = {
            oper : "guardarTorn",
            dades : dadesSel
        };
        $.ajax({
            data: parametres,
            url: 'php/ctrl/Calendar.php',
            type: 'post',
            success: function(response){
                let dataGuardar = dadesSel.split(' ');
                $('#contenidorTorn').html("<h1><?php echo $Text['torn_guardat'];?> ("+idData+")</h1>");
                }
        });
        let parametresCalendari = {
            oper : "actualitzarCalendari",
            mes : dataTorn.split("-")[1],
            any : dataTorn.split("-")[0],
        };
        $.ajax({
                data:  parametresCalendari,
                url:   'php/ctrl/Calendar.php',
                type:  'post',
                success:  function (response) {
                    $('#contenidorCalendari').html(response);
                }
        });
    }
 }

 /* Funció per imprimir crear un torn */
 function printCrearTorns(){

    dataTorn = idData.split("-").reverse().join("-");
    let parametres = {
        oper : "printCrearTorns",
        data : dataTorn
    };

    document.getElementById("contenidorTorn").style.display = "block";

    $.ajax({
        data: parametres,
        url: 'php/ctrl/Calendar.php',
        type: 'post',
        dataType: "JSON",
        success: function(response){
            let html = '<table>'+
                '<tr><th COLSPAN="2"><h1><?php echo $Text['crear_torn'];?></h1></th></tr>';
            for(let i=0;i < <?php echo get_config('ufsxTorn');?>;i++){
                html += '<tr>'+
                    '<th><?echo $Text['uf_short'];?>:</th>'+
                    '<th><select id="uf'+i+'" name="ufTorn'+i+'">';
                    for(let x=0;x<response.length;x++){
                        html += '<option value="'+response[x].id+'" name="ufSeleccionada">'+response[x].nomSelect+'</option>';
                    }
                html+='</select></th>'+
                '</tr>';
            }
            html +='</table>'+
            '<br><button class="aix-layout-fixW150" id="btnCrear" onclick="crearTorn()"><?php echo $Text['crear_torn'];?></button>'+
            '<button class="aix-layout-fixW150" id="btnRoda" onclick="crearRodaTorns()"><?php echo $Text['crear_roda'];?></button>';
            $('#contenidorTorn').html(html);
            }
    });
 }

 /* Funció per eliminar un Torn seleccionat */
 function eliminarTorn(ufTorn) {
   let dataTorn = idData.split("-").reverse().join("-");
   $.showMsg({
        msg: "<?php echo $Text['pregunta_eliminar'];?>"+idData,
        buttons: {
		"<?=$Text['btn_ok'];?>":function(){
            let parametres = {
                    oper : "eliminarTorn",
                    data : dataTorn,
                    uf : ufTorn
            };
            $.ajax({
                    data:  parametres,
                    url:   'php/ctrl/Calendar.php',
                    type:  'post',
                    success:  function (response) {
                        $('#contenidorTorn').html("<h1><?php echo $Text['torn_eliminat'];?> ("+idData+")</h1>");
                        let dataTorn = idData.split("-").reverse().join("-");
                        actualitzaCalendari(dataTorn.split("-")[1], dataTorn.split("-")[0]);
                    }
            });
			$(this).dialog("close");
		},
		"<?=$Text['btn_cancel'];?>" : function(){
			$( this ).dialog( "close" );
		}
	},
	type: 'confirm'});
 }

 /* Funció per crear un Torn */
 function crearTorn(){

    let ufsArray = [];
    for(let i=0;i< <?echo get_config('ufsxTorn');?>;i++){
        ufsArray[i] = document.getElementById("uf"+i).value;
    }

    let dataTorn = idData.split("-").reverse().join("-");
    let parametres = {
        oper : "crearTorn",
        data : dataTorn,
        ufs : JSON.stringify(ufsArray)
    };
    $.ajax({
        data: parametres,
        url: 'php/ctrl/Calendar.php',
        type: 'post',
        success:  function (response) {
           $('#contenidorTorn').html("<h1><?php echo $Text['torn_creat'];?> "+idData+"</h1>");
           let dataTorn = idData.split("-").reverse().join("-");
           actualitzaCalendari(dataTorn.split("-")[1], dataTorn.split("-")[0]);
        }
    });
 }

 /* Funció per crear roda de Torns */
 function crearRodaTorns(){
    $.showMsg({
        msg: "<?php echo $Text['pregunta_roda'];?>"+idData+"<?php echo $Text['pregunta_roda2'];?>",
        buttons: {
		"<?=$Text['btn_ok'];?>":function(){
            let dataTorn = idData.split("-").reverse().join("-");
            let parametres = {
                oper : "crearRodaTorns",
                data : dataTorn,
            };
            $.ajax({
                data: parametres,
                url: 'php/ctrl/Calendar.php',
                type: 'post',
                success:  function (response) {
                   $('#contenidorTorn').html("<h1><?php echo $Text['roda_torns_creada'];?></h1>");
                    let parametresCalendari = {
                    oper : "actualitzarCalendari",
                    mes : dataTorn.split("-")[1],
                    any : dataTorn.split("-")[0],
            };
            actualitzaCalendari(dataTorn.split("-")[1], dataTorn.split("-")[0]);
        }
            });
            $(this).dialog("close");
		},
		"<?=$Text['btn_cancel'];?>" : function(){
			$( this ).dialog( "close" );
		}
	},
	type: 'confirm'});
 }

 /*Funció per carregar mesos calendari */
 function mesCalendari(month, year, pos){
    if(pos==0){
        if(month-1==0){month=12;year--;}
        else {month = month-1;}
    }
    else if(pos==1){
        if(month+1==13){month=1;year++;}
        else {month = month+1;}
    }
    let parametres = {
            oper : "mesCalendari",
            mes : month,
            any : year,
        };

     $.ajax({
            data: parametres,
            url: 'php/ctrl/Calendar.php',
            type: 'post',
            success:  function (response) {
                let mesos = ['','Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre'];
                let html = '<tr>'+
                '<th><button class="aix-layout-fixW150" id="btnMesAnterior" onclick="mesCalendari('+month+','+year+',0)">&lt;&lt;&lt;</button></th>'+
                '<th COLSPAN="5"><h1>'+mesos[month]+' '+year+'</h1></th>'+
                '<th><button class="aix-layout-fixW150" id="btnMesPosterior" onclick="mesCalendari('+month+','+year+',1)">>>></button></th>'+
                '</tr>'+
            	'<tr>'+
        		'<th><?php echo $Text['mon'];?></th><th><?php echo $Text['tue'];?></th><th><?php echo $Text['wed'];?></th><th><?php echo $Text['thu'];?></th><th><?php echo $Text['fri'];?></th><th><?php echo $Text['sat'];?></th><th><?php echo $Text['sun'];?></th>'+
                '</tr>';
               $('#contenidorCapCalendari').html(html);
               actualitzaCalendari(month, year);
               document.getElementById("contenidorTorn").style.display = "none";
            }
        });
    idData = null;
 }

 /*Funció per actualitzar el calendari */
 function actualitzaCalendari(month, year){
    let parametres = {
        oper : "actualitzarCalendari",
        mes : month,
        any : year,
    };
    $.ajax({
        data:  parametres,
        url:   'php/ctrl/Calendar.php',
        type:  'post',
        success:  function (response) {
        $('#contenidorCalendari').html(response);
        }
    });
 }
</script>
</head>
<body>
<?php
# Definim valors inicials per al calendari

    $month=date("n");
    $year=date("Y");
    $diaActual=date("j");
?>
<div id="wrap">
        <?php include "php/lib/calendar_operations.php"?>
		<?php include "php/inc/menu.inc.php" ?>
	<!-- end of headwrap -->
	    <div id="stagewrap" class="ui-widget">
		    <div id="titlewrap">
		        <h1><?php echo $Text['head_ti_calendar']; ?></h1>
		    </div>
	    </div>
<style>
    .table { text-align: center;
                width: 100%;
              }
</style>
<table class="table">
    <thead id="contenidorCapCalendari">
        <script>mesCalendari(<?php echo $month;?>,<?php echo $year;?>,2);</script>
    </thead>
    <tbody id="contenidorCalendari">
        <?php printCalendar($month, $year);?>
    </tbody>
</table>

<div id="contenidorTorn"></div>

	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>
