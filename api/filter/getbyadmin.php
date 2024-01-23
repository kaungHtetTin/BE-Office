<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/filter.php');


$Filter=new Filter();
$result=$Filter->getOverView($_GET);

echo "<pre>";
    print_r($result);
echo "</pre>";

//echo json_encode($result);


?>