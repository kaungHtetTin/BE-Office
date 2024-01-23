<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/group.php');

$Group=new Group();
$result=$Group->getTargetPlanAndOrderRate($_GET);
echo json_encode($result);


?>