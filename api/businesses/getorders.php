<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/business.php');


$Business=new Business();
$result=$Business->getOrders($_GET);
echo json_encode($result);

?>