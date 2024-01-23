<?php
include ('../classes/user.php');
$user_id=$_GET['user_id'];
$User =new User();

$profile_data=$User-> getUserProfile($user_id);

echo json_encode($profile_data[0]);


?>