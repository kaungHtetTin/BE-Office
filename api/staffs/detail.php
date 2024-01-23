<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/staff.php');


$Staff=new Staff();
$result=$Staff->detail($_GET['id']);

echo json_encode($result);


?>