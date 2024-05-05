<?php
$page_title="Order Detail";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/business.php');
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

$voucher_id=$_GET['voucher_id'];
$user_id = $user['user_id'];
$auth_token = $user['auth_token'];

$Business = new Business ();

if($_SERVER['REQUEST_METHOD']=="POST"){
    $action = $_POST['action'];
    if($action=="cancel"){
        $Business->cancelOrder($_POST);
    }else{
        $result = $Business->revceivedOrder($_POST);
       
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
    if($order['seen']==1&&$order['is_sold_out']==1&&$order['is_received']==1){
        $status = "";
    }else if($order['seen']==1&&$order['is_sold_out']==1){
        $status = "Delivered by leader";

    }else if($order['seen']==1&&$order['is_sold_out']==0){
        $status = "Seen by leader";
    }else{
        $status ="Sent to leader";
    }

    if($agent['user_id']!=$user['user_id']) $detail = false;

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


function getUpdateUrl($voucher_id){
    $hint = "Enter extra cost";
    $message = "Update your extra cost for order of Voucher ID - $voucher_id";
    $key = "agent_extra_cost";
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
						<h2 class="st_title"><i class="uil uil-apps"></i>Sale Request Details</h2>
                        <br>
					</div>
					
                     <div class="col-lg-4">
                        
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <img src="uploads/groups/<?php echo $group['group_image'] ?>" alt="" style="width:60px; height:60px; border-radius:50px; border:3px solid #D79DA9">
                            </div>
                            <div class="col-8">
                                <h3><?php echo  $group['group_name'] ?></h3>
                                <h4> <?php echo $admin['name']; ?> </h4>
                            </div>
                        </div>

                        <br>
                        <div class="row">

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

                                Remainder for pay <br>
                                <span style="font-size:16px; font-weight:bold; color:red"><?php echo $order['remaining_amount'] ?></span>
                                <br><br>

                                Extra Cost <a href="<?php echo getUpdateUrl($voucher_id) ?>" style="font-size:14px;">( Edit )</a> <br>
                                <span style="font-size:16px; font-weight:bold;"><?php echo $order['agent_extra_cost'] ?></span>
                                <br><br>
                                <br>

                                <div style="display:flex;width:100%">
                                
                                    <?php if($order['is_sold_out']==1 && $order['is_received']==0) {?>

                                        <form action="" method="POST" style="flex:1;margin:5px;">
                                            <input type="hidden" value="reveived" name="action">
                                            <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                                            <input type="hidden" value="<?php echo $auth_token ?>" name="auth_token">
                                            <input type="hidden" value="<?php echo $voucher_id ?>" name="voucher_id">
                                            <button type="submit" class="btn_adcart my_btn" title="Create New Course">Reveived</button>
                                        </form>
                                    <?php }else if($order['is_sold_out']==0) {?>
                                        <form action="" method="POST"  style="flex:1;margin:5px;">
                                            <input type="hidden" value="cancel" name="action">
                                            <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                                            <input type="hidden" value="<?php echo $auth_token ?>" name="auth_token">
                                            <input type="hidden" value="<?php echo $voucher_id ?>" name="voucher_id">
                                            <button type="submit" class="btn_adcart my_btn" title="Create New Course">Cancel</button>
                                        </form>
                                    <?php } else { ?>
                                        <h3>Completed transaction.</h3>
                                    <?php }?>
                                </div>

                                <br><br>

                            </div>

                        </div>
                        
                     </div>

                    <div class="col-lg-8">
                        <h3>Items</h3>
                        <div class="table-responsive mt-30">
                            <table class="table ucp-table">
                                <thead class="thead-s">
                                    <tr>
                                        <th class="text-center" scope="col">Product</th>
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
                                        <tr>         
                                            <td><?php echo $product['product_name'] ?></td>
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
</body>
</html>