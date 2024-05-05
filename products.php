<?php
$page_title="Products";
session_start();

include_once('classes/product.php');
include_once('classes/auth.php');
include_once('classes/business.php');

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

$Product = new Product();
$products = $Product->getProducts();

$main_products = $products['main_product'];

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
				<div class="row">
					<div class="col-lg-12">	
						<h2 class="st_title"><i class="uil uil-apps"></i>Products</h2>
					</div>

                     <div class="col-lg-12">
                        <div class="table-responsive mt-30">
							<table class="table ucp-table earning__table">
								<thead class="thead-s">
									<tr>
										<th scope="col">Product</th>

                                        <?php 
                                            $prices = $main_products[0]['prices'];
                                            foreach($prices as $price){
                                        ?>
                                            <th scope="col">
                                                <?php 
                                                    if($price['quantity']==1) echo "Retail";
                                                    else echo $price['quantity']." Price" ;
                                                ?>
                                            </th>
                                        <?php }?>

									</tr>
								</thead>
								<tbody>

                                    <?php foreach($main_products as $product){ 
                                        $prices = $product['prices'];
                                        ?>
                                        <tr>										
                                            <td><?php echo $product['product_name'] ?></td>	
                                            <?php foreach($prices as $price){?>
                                                <td><?php echo $price['price'] ?></td>
                                            <?php }?>
                                            
                                        </tr>
                                    <?php }?>
									
								</tbody>
							</table>
						</div> 
                    </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->
</body>
</html>