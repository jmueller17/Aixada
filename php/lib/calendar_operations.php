<?php

require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "local_config/config.php");
   
//Funció per imprimir el calendari
 function printCalendar($month, $year){
    $diaSemana=date("w",mktime(0,0,0,$month,1,$year))+7;
    $ultimDiaMes=date("d",(mktime(0,0,0,$month+1,1,$year)-1));      
    $last_cell=$diaSemana+$ultimDiaMes;
	echo "<tr>";
		for($i=1;$i<=42;$i++){
			if($i==$diaSemana){
				$day=1;
			}
			if($i<$diaSemana || $i>=$last_cell){echo "<td></td>";}
            else{     
                comprovarTorn($day."-".$month."-".$year);
			    $day++;
			}
			if($i%7==0){echo "</tr><tr>";}
		}   
  }

//Funció comprovar Torn
function comprovarTorn($data){

     $dataTorns = array();

     $time = strtotime($data);
     $dataFormatada = date('Y-m-d',$time);
     $dia = date("d", $time);
     $dataAvui = date("Y-m-d");
     $db = DBWrap::get_instance();
     $rs = $db->Execute('select * from aixada_torns where dataTorn=:1q', $dataFormatada);
     $row_cnt = $rs->num_rows; 
     if ($row_cnt>0) {
        array_push($dataTorns, $data);?>
        <td id="<?php echo $data?>" onclick="selData(this, 1)" style="background-color:red"><?php if($dataAvui==$dataFormatada){ echo "<b>".$dia."</b>";} else {echo $dia;}?></td>
     <?php
     }
     else{?>
        <td id="<?php echo $data?>" onclick="selData(this, 0)" style="background-color:white"><?php if($dataAvui==$dataFormatada){ echo "<b>".$dia."</b>";} else {echo $dia;}?></td>
     <?php
     }
 }

//Funció llistar Torns
 function llistarTorns(){
  
    $i=0;
    $dataActual = date("Y/m/d");
    $db = DBWrap::get_instance();
    $rs = $db ->Execute('select * from aixada_torns where dataTorn >=:1q order by dataTorn asc', $dataActual);

    $mateixTorn = '1900-00-00';
    while($row = $rs->fetch_assoc()) 
	    {    				
        $ufId = $row['ufTorn'];
	    $rsUfs = $db ->Execute('select name from aixada_uf where id=:1q', $ufId);
		while($rowufs = $rsUfs->fetch_assoc()){  
	        $nomUf=$rowufs['name'];
	    }
        if($row['dataTorn']!=$mateixTorn){?>
            <tr style="height:10px"></tr>
            <?php $mateixTorn = $row['dataTorn'];
        }?>
        <tr>
        <th><b><?php echo date("d-m-Y", strtotime($row['dataTorn']))?></b></th>
        <th><?php echo $row['ufTorn']?></th>
        <th><?php echo $nomUf ?></th>
        <?php presentarUfs($row['ufTorn'],$row['dataTorn'], $i)?>
        </tr>
        <?php
        $i++;                      
    }

 }

 //Funció per presentar les uf's en el select
function presentarUfs($idOriginal, $dataOriginal, $i){
	?>
    <th>
	<select id="<?php echo $i;?>" name="ufs">
    <option value="" name="ufSeleccionada">...</option>
	<?php
    $db = DBWrap::get_instance();
    $rsllista = $db ->Execute('select id,name from aixada_uf where active=:1q', 1);
    while($rowuf = $rsllista->fetch_assoc()){ 
    	if(ufAnulada($rowuf['id'])){
            $nomSelect=$rowuf['id']." - ".$rowuf['name'];?>
            <option value="<?php echo $rowuf['id']." ".$idOriginal." ".$dataOriginal?>" name="ufSeleccionada"><?php echo $nomSelect ?></option>
            <?php
       	}
    }
    ?>    
    </select>
    </th>
    <th><button class="aix-layout-fixW150" id="btnGuardar" onclick="guardarTorn(<?php echo $i;?>)">Guardar</button></th>
<?php
 }

 //Funció per presentar les uf's com a llistat
 function llistatUfs(){

    $db = DBWrap::get_instance();
    $rsllista = $db ->Execute('select id,name from aixada_uf where active=:1q', 1);
    while($rowuf = $rsllista->fetch_assoc()){ 
    	if(ufAnulada($rowuf['id'])){
            $rsProveidors = $db ->Execute('select name,active from aixada_provider where responsible_uf_id=:1q', $rowuf['id']);
            $proveidors = " ";
            while($rowProveidor = $rsProveidors->fetch_assoc()){
                if($rowProveidor['active']==1){
                    $proveidors = $proveidors." - ".$rowProveidor['name'];
                }
            }
            echo "<tr><th>".$rowuf['id']."</th><th>".$rowuf['name']."<th>".$proveidors."</tr>";
       	}
    }
 }

//Funció per comprovar si la Uf esta llistada o no

 function ufAnulada ($uf){

    $resultat = true;
    $ufAnulades = get_config('ufAnulades', array(1)); // second parameter is default value when is not defined in config.php
    for( $contador = 0; $contador < count($ufAnulades); $contador++ )
    {        
         if($ufAnulades[$contador] == $uf) {
            $resultat = false;             
         }
    }
    return $resultat;
 }
?>


