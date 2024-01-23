<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/sale.php');


$sale=new Sale();
$result=$sale->searchCustomers($_GET);
echo json_encode($result);


?>