<?php
include('../classes/user.php');

if($_SERVER['REQUEST_METHOD']=="POST"){
    $User=new User();
    $result=$User->updatProfileImage($_POST,$_FILES);
    echo json_encode($result);
}

?>