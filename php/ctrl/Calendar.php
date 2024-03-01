<?php

    define('DS', DIRECTORY_SEPARATOR);
    define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 
    require_once(__ROOT__ . "php/inc/database.php");
    require_once(__ROOT__ . "local_config/config.php");
    include '../lib/calendar_operations.php';
  
    switch ($_POST['oper']) {      
        case 'printTorn':
            $a = explode('-',$_POST['data']);
            $db = DBWrap::get_instance();
            $rs = $db->Execute('select ufTorn from aixada_torns where dataTorn=:1q', date("Y-m-d", strtotime($a[2].'-'.$a[1].'-'.$a[0])));
            while($row = $rs->fetch_assoc()){
                $rsUf = $db->Execute('select id,name from aixada_uf where id=:1q', $row['ufTorn']);
                $results = $rsUf -> fetch_array();
                $data[] = array('id'=>$results["id"], 'name'=>$results["name"], 'dataTorn'=>date("Y-m-d", strtotime($a[2].'-'.$a[1].'-'.$a[0])));
            }
            $rsllista = $db ->Execute('select id,name FROM aixada_uf WHERE active=:1q', 1);
            while($rowuf = $rsllista->fetch_assoc()){ 
    	        if(ufsAnulada($rowuf['id'])){
                    $data2[] = array('id'=>$rowuf['id'], 'nomSelect'=>$rowuf['id']." - ".$rowuf['name']);                       
                }
            }
            echo json_encode(array($data,$data2));
            exit;

        case 'printCrearTorns':
                $db = DBWrap::get_instance();
                $rs = $db ->Execute('select id,name FROM aixada_uf WHERE active=:1q', 1);
                $rsllista = $db ->Execute('select id,name FROM aixada_uf WHERE active=:1q', 1);
                while($rowuf = $rsllista->fetch_assoc()){ 
    	            if(ufsAnulada($rowuf['id'])){
                        $data[] = array('id'=>$rowuf['id'], 'nomSelect'=>$rowuf['id']." - ".$rowuf['name']);                       
                    }
                }
                echo json_encode($data);
            exit;

        case 'crearTorn':
            $db = DBWrap::get_instance();
            $data = json_decode($_POST['ufs']);
            for($i=0;$i<count($data);$i++){
                $db->Execute('insert into aixada_torns (dataTorn, ufTorn) values (:1q,:2q)', $_POST['data'], $data[$i]);
            }
            exit;

        case 'guardarTornUsuari';
        	$dades = explode(" ", $_POST['dades'], 3);
            $db = DBWrap::get_instance();
            $db->Execute('update aixada_torns set ufTorn = :1q where ufTorn = :2q and dataTorn= :3q limit 1', $dades[0], $dades[1], $dades[2]);
            llistartorns();           
            exit;

        case 'guardarTorn';
            $ufSel = $_POST['dades'];
        	$dades = explode(" ", $ufSel, 3);
            $db = DBWrap::get_instance();
            if($dades[1]==0){
                $db->Execute('insert into aixada_torns (dataTorn, ufTorn) values (:1q,:2q)', $dades[2], $dades[0]);
            }            
            else{
                $db->Execute('update aixada_torns set ufTorn = :1q where ufTorn = :2q and dataTorn= :3q limit 1', $dades[0], $dades[1], $dades[2]);                
            }            
            exit;

        case 'crearRodaTorns':
            $db = DBWrap::get_instance();
            $rsBorrar = $db ->Execute('delete from aixada_torns where dataTorn >= :1q', $_POST['data']);
            $rs = $db ->Execute('select id FROM aixada_uf WHERE active=:1q', 1);
            $ultimaUf = get_row_query('select MAX(id) FROM aixada_uf WHERE active=1');
            $dataInici = date("Y-m-d", strtotime($_POST['data']));
            $uf=0;
            while($row = $rs->fetch_assoc()) 
            {
                if(ufsAnulada($row['id'])){
                    $ufId = $row['id'];
                    if($uf>=get_config('ufsxTorn') && $ufId != $ultimaUf[0]){
                        $dataInici = date("Y-m-d",strtotime($dataInici."+ 1 week"));
                        $db->Execute('insert into aixada_torns (dataTorn,ufTorn) values (:1q,:2q)', $dataInici, $ufId);               
                        $uf=1;
                    }
                    else{
                        $db->Execute('insert into aixada_torns (dataTorn,ufTorn) values (:1q,:2q)', $dataInici, $ufId);               
                        $uf++;
                    }
                }
            }
            exit;

        case 'eliminarTorn':
	        $db = DBWrap::get_instance();
            if(is_null($_POST['uf'])){
                $db->Execute('delete from aixada_torns where dataTorn=:1q', $_POST['data']);
            }
            else{
                $db->Execute('delete from aixada_torns where dataTorn=:1q and ufTorn=:2q', $_POST['data'], $_POST['uf']);                
            }
            $a = explode('-',$_POST['data']);
            printCalendar($a[0],$a[1]);
            exit;

        case 'mesCalendari':
            printCalendar($_POST['mes'], $_POST['any']);
            exit;

        case 'actualitzarCalendari':
            echo $_POST['mes']." ".$_POST['any'];
            printCalendar($_POST['mes'], $_POST['any']);
            exit;
            
        default:
	       throw new Exception(
					    "ctrlAccount: operation {$_REQUEST['oper']} not supported");
        }

 function ufsAnulada ($uf){

    $resultat = true;
    for( $contador = 0; $contador < count(get_config('ufAnulades')); $contador++ )
    {        
         if(get_config('ufAnulades')[$contador] == $uf) {
            $resultat = false;             
         }
    }
    return $resultat;
 }
?>

