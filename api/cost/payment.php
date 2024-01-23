<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/cost.php');

$Cost=new Cost();
$result=$Cost->getPayments($_GET['id']);
echo json_encode($result);

?>