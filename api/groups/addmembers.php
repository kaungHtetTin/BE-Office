<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/group.php');

 
if($_SERVER['REQUEST_METHOD']=="POST"){
    $Group=new Group();
    $result=$Group->addMembers($_POST);
    echo json_encode($result);
}


?>