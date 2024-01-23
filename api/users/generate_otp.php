<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->generateOTP($_POST);

    echo json_encode($result);
?>