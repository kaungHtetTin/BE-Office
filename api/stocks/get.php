<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/stock.php');


$Stock=new Stock();
$result=$Stock->getStocks($_GET);
echo json_encode($result);


?>