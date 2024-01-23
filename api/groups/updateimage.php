<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/group.php');


// change group image
if($_SERVER['REQUEST_METHOD']=="POST"){
    $Group=new Group();
    $result=$Group->updateGroupImage($_POST,$_FILES);
    echo json_encode($result);
}


?>