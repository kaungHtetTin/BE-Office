<?php
    include('../../classes/user.php');
    $phone=$_GET['phone'];
    $User =new User();
    $result=$User->searchByPhone($phone);

    echo json_encode($result);
?>