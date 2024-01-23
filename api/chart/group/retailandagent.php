<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/chart.php');


$Chart=new Chart();
$result=$Chart-> getRetailAndAgentRate($_GET);

// echo "<pre>";
//     print_r($result);
// echo "</pre>";

echo json_encode($result);

?>
