<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path.'/beoffice/classes/notificationpusher.php');


$pusher =new NotificationPusher();
$result=$pusher->pushNotificationToSingleUser("fV0UXJ_URx-yd93nO7G402:APA91bEHuh2I2Vq8A00s9sWfPGVoqq7xgadJCzjtuLkdKWt2h_DxRrO-Sy5kHLdAKBNLeQGF6l_h8-W0S5j6hjYZVCjuxKVeXtR8IaSm1GhemJsXbk0q2rkuVoKyxmEwNGoDb-G_7SSQ"
,"New Order!","Your group received a new order.");
echo json_encode($result);


?>