<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/groupchart.php');


$Chart=new Chart();
$result=$Chart->orderAndSale($_GET);
echo json_encode($result);

?>
