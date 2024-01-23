<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->checkOTP($_POST);

    echo json_encode($result);
?>