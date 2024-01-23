<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/cost.php');

$Cost=new Cost();
if($_SERVER['REQUEST_METHOD']=="POST"){
    $result=$Cost->addCostCategory($_POST);
    echo json_encode($result);
}

?>