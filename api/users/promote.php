<?php
    include('../../classes/user.php');

    $User =new User();
    $result=$User->promoteRank($_POST);

    echo json_encode($result);
?>