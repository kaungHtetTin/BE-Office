<?php
$page_title="My Investment";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/stock.php');
include_once('classes/product.php');

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
$investments = $Stock->getInvestment(Array('owner_id'=>$user['user_id']));

$Product = new Product();
$products = $Product->getProducts()['main_product'];

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
						<h2 class="st_title"><i class="uil uil-apps"></i>My Investment</h2>
					</div>
                    <div class="col-lg-12">            
                        <div class="date_selector">
                            <div class="ui selection dropdown skills-search vchrt-dropdown">
                                <input name="date" type="hidden" value="default" id="price_selector">
                                <i class="dropdown icon d-icon"></i>
                                <div class="text">Price</div>
                                <div class="menu" id="price_container">
                                    <?php foreach($products[0]['prices'] as $price) {?>
                                        <div class="item" data-value="<?php echo $price['quantity']?>">
                                            <?php
                                                if($price['quantity']==1) echo "Retail";
                                                else echo $price['quantity']." Price";
                                             ?>
                                        </div>
                                    <?php }?>
                                   
                                </div>
                            </div>
                        </div>

                     </div>
                     <div class="col-lg-12">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                    
                                                <th class="text-center" scope="col">Product</th>
                                                <th class="text-center" scope="col">Quantity</th>
                                                <th class="text-center" scope="col">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach($investments as $investment){ ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $investment['product_name'] ?></td>
                                                    <td class="text-center"><?php echo $investment['total'] ?> </td>
                                                    <td id="amount_<?php echo $investment['product_id'] ?>" class="text-center"> </td>
                                                </tr>
                                            <?php }?>

                                            <tr style="background:#3AC602; color:white;font-weight:bold;">
                                                <td class="text-center">Total</td>
                                                <td id="total_quantity" class="text-center">-</td>
                                                <td id="total_amount" class="text-center">-</td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                     </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>

    <script>
        let investments = <?php echo json_encode($investments) ?>;
        let products = <?php echo json_encode($products) ?>;
        let total_quantity, total_amount;

        $(document).ready(()=>{
             $('#price_selector').on('change',()=>{

                let quantity = parseInt($('#price_selector').val());
                
                updateUI(quantity);
            })

            updateUI(1);
        })

        

        function updateUI(quantity){
            total_quantity=0;
            total_amount=0;
            products.map((p)=>{
                let priceObj = selectPrice(p.prices,quantity);
                let price = parseInt(priceObj.price);

                investments.map((inves)=>{
                    if(p.product_id==inves.product_id){
                        let investment_quantity = parseInt(inves.total);
                        let amount = price*investment_quantity;
                        $('#amount_'+p.product_id).html(amount);

                        total_quantity+=investment_quantity;
                        total_amount+= amount;
                    }
                });

                

            })

            $('#total_quantity').html(total_quantity);
            $('#total_amount').html(total_amount);
        }

    
        function selectPrice(prices,quantity){
             
            var index = prices.findIndex((price)=>{
                return parseInt(price.quantity)>quantity;
            })

            if(index>0) index--;
            return (prices[index]);
        }

    </script>
	<!-- Body End -->
</body>
</html>