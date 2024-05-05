<?php

$page_title="Billion Empress";

session_start();
include('classes/connect.php');
include('classes/auth.php');

if(isset($_SESSION['beoffice_userid']) && isset($_SESSION['beoffice_auth_token']) ){
    $Auth=new Auth();
    $user = $Auth->checkAuthAndGetData($_SESSION['beoffice_userid'],$_SESSION['beoffice_auth_token']);
    if(!$user){
        header('Location:logout.php');
    }

}else{
    header('Location:login.php');
}
    

?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
	</head>

<body>
	<!-- Header Start -->
	<?php include('layouts/header.php'); ?>
	<!-- Header End -->
	<!-- Left Sidebar Start -->
	<?php include('layouts/nav.php'); ?>
	<!-- Left Sidebar End -->

	<!-- Body Start -->
	<div class="wrapper">
		<div class="sa4d25">
			<div class="container-fluid">			
			<div style="text-align:center">
                    
                    <img style="width:100px;border-radius:5px;" src="icon/billion_empress_logo.jpg" alt="">
                    <br> <br>
                    <h1>Verification Fails</h1>
                    <br><br><br><br><br>
                    <h3>Hello, <?php echo $user['name'] ?></h3><br>
                    <p>You have not been verified by any agent yet. Please contact your group leader to add you in a group to be verifed.</p>

            </div>
            <br><br><br><br><br><br>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->
</body>
</html>