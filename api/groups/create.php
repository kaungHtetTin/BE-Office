<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/group.php');

// creating new group
if($_SERVER['REQUEST_METHOD']=="POST"){
    $Group=new Group();
    $result=$Group->create_group($_POST,$_FILES);
    echo json_encode($result);
}

?>