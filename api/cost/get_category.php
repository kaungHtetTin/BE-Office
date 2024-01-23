<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/cost.php');

$Cost=new Cost();
$result=$Cost->getCostCategory();
echo json_encode($result);

?>