<?php
include('../classes/user.php');

if($_SERVER['REQUEST_METHOD']=="POST"){
    $User=new User();
    $result=$User->updatProfileData($_POST);
    echo json_encode($result);
}

?>