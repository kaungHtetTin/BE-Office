<?php
$page_title="Create Order";

session_start();

include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/product.php');
include_once('classes/group.php');

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
$products = $Product->getProducts()['main_product'];

$Group = new Group();
$groups = $Group->getOrderGroups(Array('user_id'=>$user['user_id']));
$groups = $groups['groups'];


?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
        <style>
            .quantity_input{
                border-radius:3px;
                border:0px;
                width:70px;
                height:40px;
                background:#eee;
                padding:5px;
                text-align:center;
            }

            .group{
                cursor:pointer
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
						<h2 class="st_title"><i class="uil uil-apps"></i> Create New Order</h2>
					</div>
					
                    <div class="col-lg-8">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                    
                                                <th class="text-center" scope="col">Product</th>
                                                <th class="text-center" scope="col">Quantity</th>
                                                <th class="text-center" scope="col">Retail</th>
                                                <th class="text-center" scope="col">Price(auto)</th>
                                                <th class="text-center" scope="col">Amount</th>
                                                <th class="text-center" scope="col">Point</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($products as $product){ ?>
                                                <tr>
                                                    <td><?php echo $product['product_name'] ?></td>
                                                    <td class="text-center">
                                                        <input type="text" value="0" class="quantity_input"/>
                                                    </td>
                                                    <td class="text-center"><?php echo $product['prices'][0]['price'] ?></td>
                                                    <td class="text-center c_price">0</td>
                                                    <td class="text-center c_amount">0</td>
                                                    <td class="text-center c_point">0</td>
                                                </tr>

                                            <?php }?>

                                            <tr style="background:#3AC602; color:white;font-weight:bold;">
                                                <td>Total</td>
                                                <td class="text-center" id="total_quantity"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center" id="total_amount">0</td>
                                                <td class="text-center" id="total_point">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <br>

                       
                    </div>

                    <div class="col-lg-4" style="padding:15px;">
                        
                        <?php if($groups){?>
                        <h4>Select a group</h4>
                        <br>
                        <?php foreach($groups as $group) {?>
                            <div class="card group menu--link" style="padding:10px;margin-bottom:10px;">
                                <div style="position:relative;">
                                    <img style="width:30px;height:30px;" src="uploads/groups/<?php echo $group['group_image']; ?>" alt="">
                                    <span style="font-weight:bold;font-size:14px;position:absolute;top:5px;margin-left:15px;">
                                        <?php echo $group['group_name']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php }?>
                        
                         
                        <br><br>
                        <p id="error" style="background:red; color:white;padding:3px;text-align:center;border-radius:3px;display:none"></p>
                        <br>
                        <button id="btn_calculate" style="float:right" class="btn_adcart">Calculate</button>
                        <button id="btn_order" class="btn_adcart">Order Now</button>
                        <?php } else {?>
                            <h4>Please contact your leader to add you in a group. You cannot send any order if you are not in any group.</h4>
                        <?php }?>
                    </div>
                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->

    <script>
        let products = <?php echo json_encode($products) ?>;
      //  console.log(products);
        let groups = <?php echo json_encode($groups); ?>;

        let agent_id = <?php echo $user['user_id'] ?>;
        let auth_token = "<?php echo $user['auth_token']?>";
        let total_amount = 0;
        let price_edited = 0;
        let group_id = null;
        let orders = null;

        $(document).ready(()=>{
            $('#btn_calculate').click(()=>{
                calculate();
            })

            $('.group').each((j,group)=>{
                $(group).click(()=>{
                    group_id = groups[j].group_id;
                    $('.group').each((i,g)=>{
                        $(g).css({"border":"","background":""});
                    })

                    $(group).css({"border":"1px solid black","background":"#A5FE82"});
                    
                })
            })

            $('#btn_order').click(()=>{
                orderNow();
            })
           

        })


        function orderNow(){
            $('#error').hide();
            calculate();

            let final_order = [];
            orders.map((order)=>{
                if(order.quantity>0){
                    final_order.push(order);
                }
            })

            if(group_id==null){
                $('#error').html('Please select a group');
                $('#error').show();
                return;
            }

            if(final_order.length==0){
                $('#error').html('Please enter a quantity for desired product');
                $('#error').show();
                return ;
            }
           
            let req = {};
            req.agent_id = agent_id;
            req.auth_token = auth_token;
            req.total_amount = total_amount;
            req.group_id = group_id;
            req.price_edit = 0;
            req.productJSON = JSON.stringify(final_order);

            $.post("api/businesses/sendorder.php", req, function(result){
                window.location.href = "orders.php?received=0";
            }); 
        }


        function calculate(){
            orders = [];
            let price_boxes = $('.c_price');
            let amount_boxes = $('.c_amount');
            let point_boxes = $('.c_point');

            let total_quantity = 0;
            total_amount = 0;
            let total_point = 0;
            products.map((product,i)=>{
               // console.log(product);
                $('.quantity_input').each((j,input)=>{
                    var input_value = parseInt($(input).val());
                    if(i==j){
                        var order = {};
                        order.quantity = input_value;
                        order.product_id = product.product_id;
                        order.price = selectPrice(i,input_value).price;
                        order.discount = product.discount;
                        order.point = product.point*order.quantity;
                        order.amount = order.price*order.quantity;
                        order.foc = 0;
                        order.input_index=j;
                        orders.push(order);

                        total_quantity+=order.quantity;
                        total_amount += order.amount;
                        total_point += order.point;

                        //set UI
                        price_boxes[j].innerHTML=order.price;
                        amount_boxes[j].innerHTML = order.amount;
                        point_boxes[j].innerHTML = order.point;

                    }
                })
            })
            $('#total_quantity').html(total_quantity);
            $('#total_amount').html(total_amount);
            $('#total_point').html(total_point);
          
        }


        function selectPrice(index,quantity){
             
            let prices = products[index].prices;
            var index = prices.findIndex((price)=>{
                return price.quantity>quantity;
            })

            if(index>0) index--;
            return (prices[index]);
        }

    </script>

</body>
</html>