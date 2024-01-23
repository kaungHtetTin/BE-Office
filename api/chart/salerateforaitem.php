<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/chart.php');


$Chart=new Chart();
$result=$Chart-> saleRateForAItem($_GET);

// echo "<pre>";
//     print_r($result);
// echo "</pre>";

echo json_encode($result);

?>
