<?php
$page_title="Create Invoice";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/product.php');
include_once('classes/stock.php');
include_once('classes/sale.php');

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

$Stock = new Stock();
$stocks = $Stock->getProductLeftByStock(Array('owner_id'=>$user['user_id']));

if(isset($_GET['phone'])){
    $Sale = new Sale();
    $phone = $_GET['phone'];
    $customer = $Sale->getDetail($phone);
    if($customer) $customer=$customer[0];
}

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
                height:100%;
                background:#eee;
                text-align:center;
                padding:5px;
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
						<h2 class="st_title"><i class="uil uil-apps"></i> Create New Invoice</h2>
					</div>
					
                     <div class="col-lg-7">
                        <br><br>
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
                                <h4  style="text-align:right; padding-top:10px;"><a href="transfer_product.php">Product Transfer</a></h4> 
                            </div>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                    
                                                <th class="text-center" scope="col">Product</th>
                                                <th class="text-center" scope="col">Quantity</th>
                                                <th class="text-center" scope="col">Left</th>
                                                <th class="text-center" scope="col">Price</th>
                                                <th class="text-center" scope="col">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach($products as $product){ ?>
                                                <tr id="row_<?php echo $product['product_id'] ?>">
                                                    <td><?php echo $product['product_name'] ?></td>
                                                    <td class="text-center">
                                                         <input id="input_<?php echo $product['product_id'] ?>" type="text" value="0" class="quantity_input"/>
                                                    </td>
                                                    <td id="left_<?php echo $product['product_id'] ?>"></td>
                                                    <td id="price_<?php echo $product['product_id'] ?>"></td>
                                                    <td id="amount_<?php echo $product['product_id'] ?>"></td>
                                                </tr>
                                            <?php }?>

                                            <tr style="background:#3AC602; color:white;font-weight:bold;">
                                                <td>Total</td>
                                                <td class="text-center" id="total_quantity"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center" id="total_amount">0</td>
                                            
                                            </tr>

                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                     </div>

                    <div class="col-lg-5">
                        <h4>Billing To</h4>
                        <div class="basic_form">
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" value="<?php if(isset($customer) && $customer ) {echo $customer['customer_name'];} ?>" type="text" name="academyname" id="input_name" required="" maxlength="64" placeholder="Name">															
                                </div>
                            </div>
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text" name="academyname" id="input_phone" required="" maxlength="64" placeholder="Phone" value="<?php if(isset($customer) && $customer) {echo $customer['customer_phone'];} ?>" >															
                                </div>
                            </div>
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text" name="academyname" id="input_address" required="" maxlength="64" placeholder="Address" value="<?php if(isset($customer) && $customer) {echo $customer['customer_address'];} ?>" >															
                                </div>
                            </div>
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text" name="academyname" id="input_delivery_fee" required="" maxlength="64" placeholder="Delivery Fee(Optional)">															
                                </div>
                            </div>

                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text" name="academyname" id="input_extra_cost" required="" maxlength="64" placeholder="My Extra Cost(Optional)">															
                                </div>
                            </div>

                            <div class="ques_item">
                                <div class="ui form">
                                    <div class="grouped fields" id="radio_group">										
                                        <div class="field fltr-radio" check>
                                            <div class="ui radio checkbox">
                                                <input value="retial"  type="radio" name="example3" tabindex="0" class="hidden" id="rb_retail" checked>
                                                <label>Retail</label>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="ui radio checkbox">
                                                <input value="wholesale"  type="radio" name="example3" tabindex="0" class="hidden" id="rb_wholesale">
                                                <label>Wholesale</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <br>
                            <div id="error_box" style="background:red;color:white;padding:5px;border-radius:3px;display:none">
                                 
                            </div>
                            <br>
                            <button id="btn_calculate" style="padding-left:20px;padding-right:20px;" class="btn_adcart" title="Create New Order">Calculate</button>

                            <button id="btn_create" style="padding-left:20px;padding-right:20px;" class="btn_adcart" title="Create New Order">Create</button>
                        
                        </div>

                     
                    </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
    </div>

    <script>
        let stocks = <?php echo json_encode($stocks) ?>;
        let selected_stock = null;
        let selected_stock_index = null;
        let products = <?php echo json_encode($products) ?>;
        console.log(products);
        let voucher_id = "<?php echo time() ?>";
        
        let user_id ="<?php echo $user['user_id']?>";
        let auth_token = "<?php echo $user['auth_token']?>";

        let order_products = null;

        
        $(document).ready(()=>{

            $('#stock_selector').on('change',()=>{
                let stock_index = parseInt($('#stock_selector').val());
                selected_stock = stocks[stock_index];
                selected_stock_index = stock_index;
                
                updateTableUI(selected_stock);
            
            })

            $('#btn_create').click(()=>{
                calculate(false);
            })

            $('#btn_calculate').click(()=>{
                 calculate(true);

            })

        })


        function updateTableUI(stock){
            let products = stock['items'];
            products.map((product)=>{
                $('#left_'+product.product_id).html(product.count);
            })

        }

        function calculate(calculate){
            let total_amount = 0;
            let total_quantity = 0;

            $('#error_box').hide();
            if(selected_stock_index==null){
                $('#error_box').html('Please select a stock');
                $('#error_box').show();
                return;
            }
            let wholesale = false;
            if($('#rb_retail').is(':checked')) { 
                 
            }else{
                wholesale = true;
            }

            let customer_name = "";
            let customer_phone = "";
            let customer_address = "";
            let delivery_fee = 0;
            let admin_extra_cost = 0;

            if($('#input_name').val()==""){
                $('#error_box').html('Pleae enter the customer name');
                $('#error_box').show();
                return;
            }else{
                customer_name = $('#input_name').val();
            }

            if($('#input_phone').val()==""){
                $('#error_box').html('Pleae enter the customer phone');
                $('#error_box').show();
                return;
            }else{
                customer_phone = $('#input_phone').val();
            }

            if($('#input_address').val()==""){
                $('#error_box').html('Pleae enter the customer address');
                $('#error_box').show();
                return;
            }else{
                customer_address = $('#input_address').val();
            }

            if($('#input_extra_cost').val()!=""){
                admin_extra_cost = $('#input_extra_cost').val();
            }

            let quantity_enter = false;

            order_products = [];

            let stock_products = selected_stock['items'];
            stock_products.map((ip,key)=>{

                let input_value = $('#input_'+ip.product_id).val();
                if(input_value=="") input_value = 0;
                input_value = parseInt(input_value);

                if(input_value>0){
                    if(input_value>ip.count){
                     $('#row_'+ip.product_id).css('background','yellow');
                    }else{
                        quantity_enter=true;
                        $('#row_'+ip.product_id).css('background',''); 
                        let price = 0;
                        if(wholesale){
                            price = selectPrice(key,input_value);
                        }else{
                            price = selectPrice(key,1);
                        }
                        $('#price_'+ip.product_id).html(price);

                        price = parseInt(price);
                        let amount = price * input_value;
                        let point = products[key].point*input_value;
                        total_amount+=amount;
                        total_quantity+=input_value;
                        $('#amount_'+ip.product_id).html(amount);

                        let order_product = {};
                        order_product.product_id = ip.product_id;
                        order_product.quantity = input_value;
                        order_product.foc = 0;
                        order_product.amount = amount;
                        order_product.price = price;
                        order_product.discount = 0;
                        order_product.point = point;

                        order_products.push(order_product);
                    }
                }
            })

            
            $('#total_quantity').html(total_quantity);
            $('#total_amount').html(total_amount);

            if(!quantity_enter){
                $('#error_box').html('Pleae enter the required quantity for a product');
                $('#error_box').show();
                return;
            }

            if(order_products==null){
                $('#error_box').html('Unexpected error! Please try again');
                $('#error_box').show();
                return;
            }

            if(calculate) return;

            let req  = {};
            req.admin_id = user_id;
            req.auth_token = auth_token;
            req.voucher_id = voucher_id;
            req.total_amount = total_amount;
            req.productJSON = JSON.stringify(order_products);
            req.stock_id = selected_stock.stock_id;
            req.extra_cost = admin_extra_cost;
            req.is_agent = wholesale ? 1 : 0;
            req.customer_name = customer_name;
            req.customer_phone = customer_phone;
            req.customer_address = customer_address;

            $.post("api/businesses/addsale.php", req, function(result){
                console.log(result);
                window.location.href = "vouchers.php";
            }); 

        }

        function selectPrice(index,quantity){
             
            let prices = products[index].prices;
            var index = prices.findIndex((price)=>{
                return price.quantity>quantity;
            })

            if(index>0) index--;
            return (prices[index].price);
        }


    </script>

	<!-- Body End -->
</body>
</html>