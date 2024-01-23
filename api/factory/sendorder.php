<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/factory.php');

 
if($_SERVER['REQUEST_METHOD']=="POST"){
    $Factory=new Factory();
    $result=$Factory->sentOrder($_POST);
    echo json_encode($result);
}


?>