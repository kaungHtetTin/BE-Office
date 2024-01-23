<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/login.php');
    
 
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $email=$_POST['email'];
        $password=$_POST['password'];
        
        $login=new Login();
 
        $result=$login->loginUser($email,$password);
        echo json_encode($result);
    }else{
        echo "Access denied!";
    }

 
?>