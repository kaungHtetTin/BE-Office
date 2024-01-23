<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/cost.php');

$Cost=new Cost();
$result=$Cost->getCostByVoucher($_GET);
echo json_encode($result);

?>