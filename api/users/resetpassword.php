<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->resetPassword($_POST);

    echo json_encode($result);
?>