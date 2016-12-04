<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 
require_once(__ROOT__ . "php/lib/report_orders.php");
?>
<!DOCTYPE html><html>
<head><meta charset='utf-8'></head>
<body>

<?php
echo report_order::getHtml_orders($_GET);
// $ro = new report_order('cost_amount');
// echo $ro->getHtml_orderProdUf(null, null, '2016-11-10');
// echo $ro->getHtml_orderProd(null, null, '2016-11-10');
// echo $ro->getHtml_orderUfProd(null, null, '2016-11-10');
// echo $ro->getHtml_orderMatrix(null, null, '2016-11-10');
// echo $ro->getHtml_ufOrderProd(null, null, '2016-11-10');

?>
</body>
</html>