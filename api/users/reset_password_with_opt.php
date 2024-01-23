<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->resetPasswordByOTP($_POST);

    echo json_encode($result);
?>