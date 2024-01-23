<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/group.php');

// disable the group
if($_SERVER['REQUEST_METHOD']=="POST"){
    $Group=new Group();
    $result=$Group->disableGroupMember($_POST);
    echo json_encode($result);
}


?>