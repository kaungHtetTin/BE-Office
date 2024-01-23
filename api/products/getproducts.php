<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/product.php');
 

$Product=new Product();
$result=$Product->getProducts();



echo json_encode($result);


?>