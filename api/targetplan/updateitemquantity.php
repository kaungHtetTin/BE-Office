<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/targetplan.php');

if($_SERVER['REQUEST_METHOD']=="POST"){
    $targetPlan=new TargetPlan();
    $result=$targetPlan->updateProductQuantity($_POST);
    echo json_encode($result);
}

?>