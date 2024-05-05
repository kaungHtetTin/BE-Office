<?php
$page_title="Order Detail";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/business.php');
include_once('classes/product.php');
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

$voucher_id=$_GET['voucher_id'];
$user_id = $user['user_id'];
$auth_token = $user['auth_token'];

$Business = new Business ();

if($_SERVER['REQUEST_METHOD']=="POST"){
    $action = $_POST['action'];
    if($action=="cancel"){
        $Business->cancelOrderByAdmin($_POST);
    }
}

$detail = $Business->getOrderDetail(Array('voucher_id'=>$voucher_id,'user_id'=>$user_id));

if($detail){
    $items = $detail['details'];
    $order = $detail['order'];
    $group = $detail['group'];
    $admin = $detail['admin'];
    $agent = $detail['agent'];

    //determine status
    $status = "";
    $completed_transaction = false;
    $delivered = false;
    if($order['seen']==1&&$order['is_sold_out']==1&&$order['is_received']==1){
        $status = "Received by partner";
        $completed_transaction = true;
    }else if($order['seen']==1&&$order['is_sold_out']==1){
        $status = "Delivered to parnter";
        $delivered=true;
    }

    if($admin['user_id']!=$user['user_id']) $detail = false;

}

$Product = new Product();
$products = $Product->getProducts()['main_product'];

// only for item
$total_quantity=0;
$total_amount=0;
$total_point=0;

function getProduct($product_id,$products){
    for($i=0;$i<count($products);$i++){
        $product = $products[$i];
        if($product['product_id']==$product_id){
            return $product;
        }
    }
}

$Stock = new Stock();
$stocks = $Stock->getProductLeftByStock(Array('owner_id'=>$user['user_id']));

function getUpdateUrl($voucher_id){
    $hint = "Enter extra cost";
    $message = "Update your extra cost for order of Voucher ID - $voucher_id";
    $key = "admin_extra_cost";
    $content_id = "$voucher_id";
    return "update.php?hint=$hint&message=$message&key=$key&content_id=$content_id&link=2";
}

function getUpdateRemainingAmountUrl($voucher_id){
    $hint = "Enter Remaining Balance";
    $message = "Update remaining balance for order of Voucher ID - $voucher_id";
    $key = "remaining_amount";
    $content_id = "$voucher_id";
    return "update.php?hint=$hint&message=$message&key=$key&content_id=$content_id&link=2";
}

?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
        <style>
            .my_btn{        
                padding-left:20px;
                padding-right:20px;
                cursor:pointer;
                margin-right:5px;
                width:100%;
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
            <?php if($detail){ ?>		
				<div class="row">
					<div class="col-lg-12">	
						<h2 class="st_title"><i class="uil uil-apps"></i></h2>
                        <br>
					</div>
					
                    <div class="col-lg-4">
                        
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <img src="uploads/profiles/<?php echo $agent['profile_image'] ?>" alt="" style="width:60px; height:60px; border-radius:50px; border:3px solid #D79DA9">
                            </div>
                            <div class="col-8">
                                <h3> <?php echo $agent['name']; ?> </h3>
                                <h4><?php echo  $group['group_name'] ?></h4>
                            </div>
                        </div>

                        <br>
                        <div class="row">

                            <div class="col-4">
                                Phone
                            </div>
                            <div class="col-8">
                                <?php echo $agent['phone'] ?>
                            </div>

                            <div class="col-4">
                                Address
                            </div>
                            <div class="col-8">
                                <?php echo $agent['address'] ?>
                            </div>

                            <br><br>

                            <div class="col-4">
                                Date
                            </div>
                            <div class="col-8">
                                <?php echo date('d M, Y',$order['voucher_id']) ?>
                            </div>

                            <div class="col-4">
                                Status
                            </div>
                            <div class="col-8">
                                <?php echo $status ?>
                            </div>

                            <div class="col-4">
                                Voucher Id
                            </div>
                            <div class="col-8">
                                <?php echo $order['voucher_id'] ?>
                            </div>

                            <div class="col-12">
                                <br><br>
                                Total Amount <br>
                                <span style="font-size:16px; font-weight:bold; color:green"><?php echo $order['total_amount'] ?></span>
                                <br><br>

                                Remainding Amount <a href="<?php echo getUpdateRemainingAmountUrl($voucher_id) ?>" style="font-size:14px;">( Edit )</a> <br>
                                <span style="font-size:16px; font-weight:bold; color:red"><?php echo $order['remaining_amount'] ?></span>
                                <br><br>

                                Extra Cost <a href="<?php echo getUpdateUrl($voucher_id) ?>" style="font-size:14px;">( Edit )</a> <br>
                                <span style="font-size:16px; font-weight:bold;"><?php echo $order['admin_extra_cost'] ?></span>
                                <br><br>
                                <br>

                            </div>

                        </div>
                     
                         <?php if(!$completed_transaction && !$delivered){ ?>
                            <div class="row">
                                <div class="col-6">
                                    <div class="date_selector" style="margin:0">
                                        <div class="ui selection dropdown skills-search vchrt-dropdown">
                                            <input name="date" type="hidden" value="default" id="stock_selector">
                                            <i class="dropdown icon d-icon"></i>
                                            <div class="text">Select a stock</div>
                                            <div class="menu" id="price_container">
                                                <?php foreach($stocks as $key=>$stock) {?>
                                                    <div class="item" data-value="<?php echo $key ?>">
                                                        <?php echo $stock['name'] ?>
                                                    </div>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4  style="text-align:right; padding-top:10px;"><a href="">Product Transfer</a></h4> 
                                </div>
                            </div>
                         <?php }?>

                         <p style="padding:10px; color:red;" id="error_view"></p>
                        

                        <div style="display:flex;width:100%;float:right">
                            <?php if(!$completed_transaction){ ?>

                                <?php if($order['is_sold_out']==0) {?>
                                    
                                    <button id="btn_sent" class="btn_adcart my_btn" title="Sent Order" style="width:200px;">Sent</button>
                                
                                <?php }else {?>
                                    
                                    <?php if($order['is_received']==0){ ?>
                                        <form action="" method="POST"  style="flex:1;margin:5px;">
                                            <input type="hidden" value="cancel" name="action">
                                            <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                                            <input type="hidden" value="<?php echo $auth_token ?>" name="auth_token">
                                            <input type="hidden" value="<?php echo $voucher_id ?>" name="voucher_id">
                                            <button id="Cancel" class="btn_adcart my_btn" title="Cancel Order" style="width:200px;">Cancel</button>
                                            
                                        </form>
                                    <?php } ?>

                                <?php }?>
                            <?php }else {?>
                                <h3>Completed Transaction.</h3>
                            <?php }?>

                             <div class="spinner" id="loading" style="display:none">
                                <br>
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                                
                            </div>	
                        </div>

                        <br><br><br><br><br>
                    </div>

                    <div class="col-lg-8">
                        <h3>Items</h3>
                        <div class="table-responsive mt-30">
                            <table class="table ucp-table">
                                <thead class="thead-s">
                                    <tr>
                                        <th class="text-center" scope="col">Product</th>
                                        <?php if(!$completed_transaction && !$delivered){ ?>
                                            <th class="text-center" scope="col">Left</th>
                                        <?php }?>
                                        <th class="text-center" scope="col">Quantity</th>    
                                        <th class="text-center" scope="col">Retail</th>
                                        <th class="text-center" scope="col">Price</th>
                                        <th class="text-center" scope="col">Discount</th>
                                        <th class="text-center" scope="col">Amount</th>
                                        <th class="text-center" scope="col">Point</th>
                                        
                                    </tr>
                                </thead>
                                <tbody id="sent_container">
                                    <?php foreach($items as $item){ 
                                        $product = getProduct($item['product_id'],$products);
                                        $total_quantity+=$item['quantity'];
                                        $total_amount+=$item['amount'];
                                        $total_point+=$item['point'];
                                        ?>
                                        <tr id="<?php echo $product['product_id'] ?>_row">         
                                            <td class="text-center"><?php echo $product['product_name'] ?></td>
                                            <?php if(!$completed_transaction && !$delivered){ ?>
                                                <td id="<?php echo $product['product_id'] ?>_left"></td>
                                            <?php }?>                                            
                                            <td class="text-center"><?php echo $item['quantity'] ?></td>
                                            <td class="text-center"><?php echo $product['prices'][0]['price'] ?></td>
                                            <td class="text-center"><?php echo $item['price'] ?></td>
                                            <td class="text-center"><?php echo $item['discount'] ?></td>
                                            <td class="text-center"><?php echo $item['amount'] ?></td>
                                            <td class="text-center"><?php echo $item['point'] ?></td>
                                        </tr>
                                        
                                    <?php }?>
                                        <tr style="background:#3AC602; color:white;font-weight:bold;">
                                            <td class="text-center">Total</td>
                                                <?php if(!$completed_transaction && !$delivered){ ?>
                                                <td></td>
                                            <?php }?>
                                            <td class="text-center"><?php echo $total_quantity ?></td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center"><?php echo $total_amount ?></td>
                                            <td class="text-center"><?php echo $total_point ?></td>
                                        
                                        </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            <?php }else{?>
                <div>
                    <h1>Invalid Voucher</h1>
                    <br><br><br><br><br><br>
                     <br><br><br><br><br><br>
                      <br><br>
                </div>
            <?php }?>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->

    <script>
        let items = <?php echo json_encode($items) ?>;
        let stocks = <?php echo json_encode($stocks) ?>;
        let selected_stock = null;
        let voucher_id ="<?php echo $voucher_id ?>";
        let user_id ="<?php echo $user['user_id']?>";
        let auth_token = "<?php echo $user['auth_token']?>";

        console.log()
        
        let validateError = "";

        $(document).ready(()=>{
            $('#stock_selector').on('change',()=>{
                let stock_index = parseInt($('#stock_selector').val());
                selected_stock = stocks[stock_index];
                validateError="";
                updateTableUI();

            })

            $('#btn_sent').click(()=>{
                sentOrder();
            })
        })

        

        function sentOrder(){
            $('#error_view').html("");
            
            if(selected_stock==null){
                validateError="Please select a stock";
            }

            if(validateError!=""){
                $('#error_view').html(validateError);
            }else{
                $('#loading').show();
                let stock_id = selected_stock.stock_id;
                let request = {};
                request.stock_id = stock_id;
                request.user_id = user_id;
                request.auth_token = auth_token;
                request.voucher_id = voucher_id;
             



                $.post("api/businesses/soldoutorder.php", request, function(result){
                    console.log(result);
                    window.location.href="";
                });
            }

        }


        function updateTableUI(){
            let stock_products = selected_stock['items'];
            
            items.map((item)=>{
                stock_products.map((product)=>{
                    if(item.product_id==product.product_id){
                        $('#'+item.product_id+'_left').html(product.count);
                        if(parseInt(product.count)<parseInt(item.quantity)){
                            $('#'+item.product_id+'_row').css('background','yellow');
                            validateError="Not enough item left";
                        }else{
                             $('#'+item.product_id+'_row').css('background','');
                        }
                    }
                })
            })
        }


    </script>
</body>

</html>