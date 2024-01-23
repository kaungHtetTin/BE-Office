<?php
  include ('../classes/signup.php');

  if($_SERVER['REQUEST_METHOD']=="POST"){
    $signUp=new SignUp();
    $result=$signUp->validateData($_POST);
    echo json_encode($result);
  }else{
      echo "Access denied!";
  }
  

?>