<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->getUserValidData($_POST);

    echo json_encode($result);
?>