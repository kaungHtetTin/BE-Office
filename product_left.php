<?php
$page_title="Product Left";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/stock.php');

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

$Stock = new Stock();
$stocks = $Stock->getProductLeftByStock(Array('owner_id'=>$user['user_id']));

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
						<h2 class="st_title">  
                            <i class="uil uil-apps"></i>Product Left
                            <a style="margin-left:10px; font-size:14px;" href="transfer_product.php">(Transfer Product)</a>
                        </h2>
					</div>
					
                     <div class="col-lg-12">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                    
                                                <th scope="col">Products</th>
                                                <?php 
                                                    foreach($stocks as $stock){
                                                ?>
                                                    <th class="text-center" scope="col"><?php echo $stock['name'] ?></th>
                                                <?php }?>
                                                 <th style="background:#3AC602;color:white;font-weight:bold;" class="text-center" scope="col">Total</th>
                                        
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php foreach($stocks[0]['items'] as $key=>$product){
                                                $total =0;
                                                ?>
                                                <tr>
                                                    <td><?php echo $product['product_name'] ?></td>
                                                    
                                                    <?php foreach($stocks as $stock){
                                                            $quantity = $stock['items'][$key]['count'];
                                                            $total+=$quantity;
                                                        ?>
                                                        <td class="text-center"><?php echo $quantity ?></td>
                                                    <?php }?>

                                                     <td style="background:#3AC602;color:white;font-weight:bold;" class="text-center"><?php echo $total ?></td>
                                                </tr>

                                            <?php }?>
                                            
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                     </div>
                     <div class="col-lg-12" style="float:right">	
                        <br><br>
						
					</div>
                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->
</body>
</html>