<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/business.php');

 
if($_SERVER['REQUEST_METHOD']=="POST"){
    $Business=new Business();
    $result=$Business->soldOutOrder($_POST);
    echo json_encode($result);
}

?>