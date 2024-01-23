<?php
    include('../classes/login.php');

    $user_id=$_POST['user_id'];
    $auth_token=$_POST['auth_token'];

    $login=new Login();
    $result=$login-> authRefresh($user_id,$auth_token);
    echo json_encode($result);

?>