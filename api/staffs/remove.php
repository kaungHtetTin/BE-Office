<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/staff.php');


if($_SERVER['REQUEST_METHOD']=="POST"){
    $Staff=new Staff();
    $result=$Staff->remove($_POST['id']);
}else{
    $result['status']="fail";
    $result['error']="Request Method Error!";
}

echo json_encode($result);


?>