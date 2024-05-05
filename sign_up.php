<?php
session_start();
include_once('classes/signup.php');
$result="";
if($_SERVER['REQUEST_METHOD']=="POST"){
	$Signup = new SignUp();

	$_POST['fcmToken']="signupfromwebiste";
	$result = $Signup->validateData($_POST);
	if($result['register']=="success"){
		$user_id = $result['data']['user_id'];
		$auth_token = $result['data']['auth_token'];

		$_SESSION['beoffice_userid']=$user_id;
		$_SESSION['beoffice_auth_token']=$auth_token;

		header("location:index.php");
	}else{
		
	}
}


?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">		
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=9">
		<meta name="description" content="Gambolthemes">
		<meta name="author" content="Gambolthemes">
		<title>Billion Empress | Signup</title>
		
		<!-- Favicon Icon -->
		<link rel="icon" type="image/png" href="icon/logo_small.jpg">
		
		<!-- Stylesheets -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,500' rel='stylesheet'>
		<link href='vendor/unicons-2.0.1/css/unicons.css' rel='stylesheet'>
		<link href="css/vertical-responsive-menu.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/responsive.css" rel="stylesheet">
		<link href="css/night-mode.css" rel="stylesheet">
		
		<!-- Vendor Stylesheets -->
		<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
		<link href="vendor/OwlCarousel/assets/owl.carousel.css" rel="stylesheet">
		<link href="vendor/OwlCarousel/assets/owl.theme.default.min.css" rel="stylesheet">
		<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="vendor/semantic/semantic.min.css">	
		
	</head> 

<body>
	<!-- Signup Start -->
	<div class="sign_in_up_bg">
		<div class="container">
            <br><br><br>
			<div class="row justify-content-lg-center justify-content-md-center">
				 
				<div class="col-lg-6 col-md-8">
					<div class="sign_form">
						<img style="width:200px;border-radius:5px;" src="icon/billion_empress_logo.jpg" alt="">
						<h2>Welcome To Billion Empress</h2>
						<p>Sign up and Let's start the journey for success!</p>
						
						<form action="" method="POST">
                            <div class="ui search focus mt-15">
								<div class="ui left icon input swdh95">
									<input class="prompt srch_explore" type="text" name="name" value="" id="id_name" required="" maxlength="64" placeholder="Enter your name">															
									<i class="uil uil-envelope icon icon2"></i>
								</div>
							</div>
                            <div class="ui search focus mt-15">
								<div class="ui left icon input swdh95">
									<input class="prompt srch_explore" type="email" name="email" value="" id="id_address" required="" maxlength="64" placeholder="Enter Email Address">															
									<i class="uil uil-envelope icon icon2"></i>
								</div>
							</div>

							<div class="ui search focus mt-15">
								<div class="ui left icon input swdh95">
									<input class="prompt srch_explore" type="phone" name="phone" value="" id="id_phone" required="" maxlength="64" placeholder="Enter phone ">															
									<i class="uil uil-envelope icon icon2"></i>
								</div>
							</div>
							<div class="ui search focus mt-15">
								<div class="ui left icon input swdh95">
									<input class="prompt srch_explore" type="password" name="password" value="" id="id_password" required="" maxlength="64" placeholder="Enter Password">
									<i class="uil uil-key-skeleton-alt icon icon2"></i>
								</div>
							</div>
                            <div class="ui search focus mt-15">
								<div class="ui left icon input swdh95">
									<input class="prompt srch_explore" type="password" name="password" value="" id="id_confirm_password" required="" maxlength="64" placeholder="Comfirm Password">
									<i class="uil uil-key-skeleton-alt icon icon2"></i>
								</div>
							</div>
							<br><br>
							<?php if($result!="" && $result['register']=="fail"){ ?>
								<span style="color:red">
									<?php 
								 		$errors = $result['error'];
									 
										foreach($errors as $error){
											 print_r($error);
										}
									?>  
								</span>
							<?php }?>

							<button class="login-btn" type="submit">Sign Up</button>
						</form>
						
						<p class="mb-0 mt-30 hvsng145">Already have an account? <a href="login.php">Log In</a></p>
					</div>
					<div class="sign_footer"><img style="width:30px;height:30px;border-radius:50px;" src="icon/logo_small.jpg" alt="">© 2024 <strong>Billion Empress</strong>. All Rights Reserved.</div>
				</div>				
			</div>				
		</div>				
	</div>
	<!-- Signup End -->	

	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="vendor/OwlCarousel/owl.carousel.js"></script>
	<script src="vendor/semantic/semantic.min.js"></script>
	<script src="js/custom.js"></script>	
	<script src="js/night-mode.js"></script>	
	
</body>
</html>