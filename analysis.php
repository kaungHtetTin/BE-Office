<?php

$page_title="Billion Empress";

session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/product.php');

if(isset($_SESSION['beoffice_userid']) && isset($_SESSION['beoffice_auth_token']) ){
    $Auth=new Auth();
    $user = $Auth->checkAuthAndGetData($_SESSION['beoffice_userid'],$_SESSION['beoffice_auth_token']);
    if(!$user){
        header('Location:logout.php');
    }

}else{
    header('Location:login.php');
}
    
$Product = new Product();
$products = $Product->getProducts();

?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
		<style>
			.margin-td{
				margin:10px;
			}
		</style>
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
				<div class="row">
					<div class="col-lg-12">	
						<h2 class="st_title"><i class="uil uil-apps"></i> Billion Empress</h2>
					</div>

                    <?php include('charts/sale_and_order_rate.php') ?>

                    <?php include('charts/retail_and_whole_sale.php') ?>

					<?php include('charts/my_profit.php') ?>

					<?php include('charts/monthly_profit.php'); ?>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
  
	</div>
	<!-- Body End -->
</body>
</html>