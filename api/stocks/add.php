<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/stock.php');

if($_SERVER['REQUEST_METHOD']=="POST"){
    $Stock=new Stock();
    $result=$Stock->addNewStock($_POST);
    
    echo json_encode($result);
}

?>