<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/group.php');


$Group=new Group();
$result=$Group-> getAboutGroup($_GET['group_id']);

echo json_encode($result);

?>

