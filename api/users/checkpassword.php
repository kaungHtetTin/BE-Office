<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->checkPassword($_GET);

    echo json_encode($result);
?>