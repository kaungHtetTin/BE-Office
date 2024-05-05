<?php

session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/business.php');
include_once('classes/rank.php');

if(isset($_SESSION['beoffice_userid']) && isset($_SESSION['beoffice_auth_token']) ){
    $Auth=new Auth();
    $user = $Auth->checkAuthAndGetData($_SESSION['beoffice_userid'],$_SESSION['beoffice_auth_token']);
    if(!$user){
        header('Location:logout.php');
    }else{
        if($user['verified']==0){
            header('location:verification_fail.php');
        }
    }

}else{
    header('Location:login.php');
}

$voucher_id = $_GET['voucher_id'];
$Business = new Business();
$request = Array(
    'user_id'=>$user['user_id'],
    'voucher_id'=>$voucher_id
);

$sale_details = $Business->getSaleDetail($request);
if($sale_details){
	$customer = $sale_details['sale'];
	$items = $sale_details['details'];
}


$Rank = new Rank();
$rank = $Rank->get($user['rank_id']);

?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">		
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=9">
		<meta name="description" content="Gambolthemes">
		<meta name="author" content="Gambolthemes">
		<title>Billion Empress | Invoice</title>
		
		<!-- Favicon Icon -->
		<link rel="icon" type="image/png" href="images/fav.png">
		
		<!-- Stylesheets -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,500' rel='stylesheet'>
		<link href='vendor/unicons-2.0.1/css/unicons.css' rel='stylesheet'>
		<link href="css/vertical-responsive-menu.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/responsive.css" rel="stylesheet">
		
		<!-- Vendor Stylesheets -->
		<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
		<link href="vendor/OwlCarousel/assets/owl.carousel.css" rel="stylesheet">
		<link href="vendor/OwlCarousel/assets/owl.theme.default.min.css" rel="stylesheet">
		<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="vendor/semantic/semantic.min.css">	
		
	</head> 

<body>
	<!-- Header Start -->
	<header class="invoice_header clearfix">
		<div class="container">
			<div class="row justify-content-md-center">
				<div class="col-md-8">
					<div class="invoice_header_main">
						<div class="invoice_header_item">
							<div class="invoice_logo">
								<a  href="index.php"><img  style="width:50px; height:50px;border-radius:50px;" src="icon/logo_small.jpg" alt=""></a>
							</div>
							<p>Invoice</p>
						</div>						
					</div>						
				</div>		
			</div>
		</div>
	</header>
	<!-- Header End -->
	<?php if($sale_details){ ?>
	<!-- Body Start -->
	<div class="wrapper _bg4586 _new89 p-0">		
		<div class="container">
			<div class="row justify-content-md-center">
				<div class="col-md-8">
					<div class="invoice_body">
						<div class="invoice_date_info">
							<ul>
								<li><div class="vdt-list"><span>Date :</span><?php echo date('d M , Y', $voucher_id) ?></div></li>
								<li><div class="vdt-list"><span>Order ID :</span><?php echo $voucher_id ?></div></li>
								<li><div class="vdt-list"><span>Stock :</span><?php echo $sale_details['stock_name'] ?></div></li>
							</ul>
						</div>
						<div class="invoice_dts">
							<div class="row">
								<div class="col-md-12">
									<h2 class="invoice_title">Invoice</h2>
								</div>
								<div class="col-md-6">
									<div class="vhls140">
										<h4>To</h4>
										<ul>
											<li><div class="vdt-list"><?php echo $customer['is_agent']==1 ? "Wholesale":"Retail"; ?></div></li>
											<li><div class="vdt-list"><?php echo $customer['customer_name'] ?></div></li>
											<li><div class="vdt-list"><?php echo $customer['customer_phone'] ?></div></li>
											<li><div class="vdt-list"><?php echo $customer['customer_address'] ?></div></li>
										</ul>
									</div>		
								</div>
								<div class="col-md-6">
									<div class="vhls140">
										<h4>Billion Empress</h4>
										<ul>
											 
											<li><div class="vdt-list"><?php echo $user['name'] ?></div></li>
											<li><div class="vdt-list"><?php echo $rank['rank'] ?> </div></li>
											<li><div class="vdt-list"><?php echo $user['phone'] ?> </div></li>
											<li><div class="vdt-list"><?php echo $user['email'] ?> </div></li>
											<li><div class="vdt-list"><?php echo $user['address'] ?> </div></li>
										</ul>
									</div>		
								</div>
							</div>
						</div>
						<div class="invoice_table">
							<div class="table-responsive-md">
								<table class="table table-borderless">
									<thead>
										<tr>
										  <th scope="col">Item</th>
										  <th scope="col">Price</th>
										  <th scope="col">Qty</th>
										  <th scope="col">Total Amount</th>
										</tr>
									</thead>
									<tbody>
										
										<?php if($items){ foreach($items as $item){ ?>

											<tr>
												<th scope="row">
													<div class="user_dt_trans">
														<p><?php echo $item['product_name'] ?></p>
													</div>
												</th>
												<td>
													<div class="user_dt_trans">														
														<p><?php echo $item['price'] ?></p>
													</div>
												</td>
												<td>
													<div class="user_dt_trans">
														<p><?php echo $item['quantity'] ?></p>
													</div>
												</td>
												<td>
													<div class="user_dt_trans">														
														<p><?php echo $item['amount'] ?></p>
													</div>
												</td>												
											</tr>

										<?php }}?>

										<tr>
											<td colspan="1"></td>
											<td colspan="3">
												<div class="user_dt_trans jsk1145">														
													<div class="totalinv2">Invoice Total : <?php echo $sale_details['voucher']['total_amount'] ?></div>
													<p>Paid</p>
												</div>
											</td>												
										</tr>											
									</tbody>
								</table>														
							</div>
						</div>
					 
						
						<div class="invoice_footer">
							<p>Thanks for buying.</p>
							<div id="loading" class="spinner" style="background:white;border:'0px';display:none">
									<br><br>
									<div class="bounce1"></div>
									<div class="bounce2"></div>
									<div class="bounce3"></div>
									<br><br>
							</div>	

							<div class="leftfooter" style="display:flex">
								<button id="btn_delete" class="btn_adcart">Delete</button> 
								
							</div>
						 
							<div class="righttfooter">
								<button id="btn_print" class="btn_adcart">Print</button> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<?php }else{?>
		<h2 style="text-align:center">Invalid Invoice</h2>
	<?php }?>
	<!-- Body End -->

	

	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="vendor/OwlCarousel/owl.carousel.js"></script>
	<script src="vendor/semantic/semantic.min.js"></script>
	<script src="js/custom.js"></script>	

	<script>
		let user_id ="<?php echo $user['user_id']?>";
        let auth_token = "<?php echo $user['auth_token']?>";
		let voucher_id ="<?php echo $voucher_id ?>";


		 $(document).ready(()=>{
			$('#btn_print').click(()=>{
				$('#btn_delete').hide();
				window.print();
				 
			});

			$('#btn_delete').click(()=>{
				$('#btn_delete').html('... Deleting ...');
				$('#loading').show();
				
				let req = {};
				req.user_id = user_id;
				req.auth_token = auth_token;
				req.voucher_cancel = 1;
				req.voucher_id = voucher_id;

				$.post("api/businesses/cancelorder_admin.php", req, function(result){
                	console.log(result);
                	window.location.href = "vouchers.php";
            	}); 

			});


		})
	</script>
	
</body>
</html>