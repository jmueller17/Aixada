<?php 
require_once "../inc/header.inc.php";
require_once "torns_report__include.php";
    
// Se usa el parametro ?date=aaaa-mm-dd o ?from_date=aaaa-mm-dd
$from_date = get_param_date('date');
$from_date = $from_date ? $from_date : get_param_date('from_date');

// Si no existe nos vamos dos semanas atràs
if ( !$from_date ) {
    $aux = new DateTime("-2 weeks");
    $from_date = $aux->format('Y-m-d');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['torns_des_de'] . ' ' . $from_date;?></title>
	<link rel="stylesheet" type="text/css"               href="css/reports_paper.css"/>
    <link rel="stylesheet" type="text/css"               href="css/reports_layout.css"/>
  	<link rel="stylesheet" type="text/css" media="print" href="css/reports-print.css"/>
</head>
<body>
    <div class="page-A4p">
    <h2 class="start"><?php echo $Text['torns_des_de'] . ': ' . $from_date; ?></h2>
<?php echo write_turn_fromDate($from_date); ?>
    </div>
</body>
</html>
