<?php
   include("../classes/connect.php");
   $email=$_GET['email'];

   $DB=new Database();
   $query ="select email from users where email='$email' limit 1";

   
   $result=$DB->read($query);
   if(!$result){
        $response['email_exist']="false";
       echo json_encode($response);
   }else{
        $response['email_exist']="true";
        echo json_encode($response);
   }    

?>