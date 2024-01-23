<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/staff.php');


$Staff=new Staff();
$result=$Staff->get();

echo json_encode($result);


?>