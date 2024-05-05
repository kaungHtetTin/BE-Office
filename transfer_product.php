<?php
$page_title="Product Transfer";
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
						<h2 class="st_title"><i class="uil uil-apps"></i>Product Transfering</h2>
					</div>
					
                    <div class="col-lg-12">
                        <br><br>
                        <div class="row">
                            <div class="col-6">
                                <h3>From <span id="from" style="font-size:14px;">  </span></h3>

                                <div class="date_selector" style="margin-top:10px;" id="initial_stock_container">
                                    <div class="ui selection dropdown skills-search vchrt-dropdown">
                                        <input name="date" type="hidden" value="default" id="initial_stock">
                                        <i class="dropdown icon d-icon"></i>
                                        <div style="padding-right:10px;" class="text">Select an initial stock </div>
                                        <div class="menu" id="price_container">
                                            <?php foreach($stocks as $key=>$stock){ ?>
                                                <div class="item" data-value="<?php echo $key ?>">
                                                    <?php echo $stock['name']; ?>
                                                </div>
                                            <?php } ?> 
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <h3>To <span id="to" style="font-size:14px;"> </span></h3>
                                <div class="date_selector" style="margin-top:10px;" id="target_stock_container">
                                    <div class="ui selection dropdown skills-search vchrt-dropdown">
                                        <input name="date" type="hidden" value="default" id="target_stock">
                                        <i class="dropdown icon d-icon"></i>
                                        <div  style="padding-right:10px;" class="text">Select a target stock </div>
                                        <div class="menu" id="price_container">
                                            <?php foreach($stocks as $key=>$stock){ ?>
                                                <div class="item" data-value="<?php echo $key ?>">
                                                    <?php echo $stock['name']; ?>
                                                </div>
                                            <?php } ?> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>

                                                <th scope="col">Products</th>
                                                <th scope="col">Initial</th>
                                                <th scope="col">target</th>
                                                <th class="text-center" scope="col">Amount</th>
                                        
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($stocks[0]['items'] as $product) {?>
                                                <tr id="row_<?php echo $product['product_id'] ?>">
                                                    <td><?php echo $product['product_name'] ?></td>
                                                    <td id="initial_<?php echo $product['product_id'] ?>"></td>
                                                    <td id="target_<?php echo $product['product_id'] ?>"></td>
                                                    <td class="text-center">
                                                        <input id="input_<?php echo $product['product_id'] ?>" type="text" value="0" class="quantity_input"/>
                                                    </td>
                                                </tr>
                                           <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div id="error_box" style="display:none; background:red;padding:5px; text-align:center; border-radius: 3px;color:white">
                                 
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <button id="btn_calculate" style="text-align:center" class="btn_adcart" title="Create New Course">Calculate</button>
                                 <button id="btn_reset" style="text-align:center;display:none" class="btn_adcart" title="Create New Course">Reset</button>
                            </div>
                            <div class="col-4">
                               
                            </div>
                            <div class="col-4" style="text-align:right">
                                <button id="btn_transfer" style="text-align:center;display:none" class="btn_adcart" title="Create New Course">Transfer Now</button>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-2">
                         
						
                    </div>
                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->
    <script>
        let stocks = <?php echo json_encode($stocks) ?>;
        let initial_stock = null;
        let target_stock = null;
        let initial_index = null;
        let target_index = null;

        console.log(stocks);

        let user_id ="<?php echo $user['user_id']?>";
        let auth_token = "<?php echo $user['auth_token']?>";

        $(document).ready(()=>{

            $('#initial_stock').on('change',()=>{
                let stock_index = parseInt($('#initial_stock').val());
                initial_stock = stocks[stock_index];
                initial_index = stock_index;
                $('#from').html('( '+initial_stock.name+' )');
                updateTableUI(initial_stock,'initial');
               $('#initial_stock_container').hide();

            })

            $('#target_stock').on('change',()=>{
                let stock_index = parseInt($('#target_stock').val());
                target_stock = stocks[stock_index];
                target_index = stock_index;
                updateTableUI(target_stock,'target');
                $('#to').html('( '+target_stock.name+' )');
                $('#target_stock_container').hide();

            })

            $('#btn_calculate').click(()=>{

                $('#error_box').hide();

                if(initial_stock == null || target_stock == null){
                    
                    $('#error_box').html("Please select initial and target stocks");
                    $('#error_box').show();
                    return;
                }

                if(initial_index==target_index){
                     
                    $('#btn_reset').show();
                    $('#btn_calculate').hide();
                    $('#error_box').html("Initial and target stocks cannot be the same.");
                    $('#error_box').show();
                    return;
                }

                let result  = calculate();
                if(result!=""){
                    $('#btn_reset').show();
                    $('#btn_calculate').hide();
                    $('#error_box').html(result);
                    $('#error_box').show();

                }else{
                    $('#btn_transfer').show();
                    $('#btn_reset').show();
                }
                $('#btn_calculate').hide();

            })

            $('#btn_reset').click(()=>{
                window.location.href="";
            })

            $('#btn_transfer').click(()=>{
                transferNow();
            })

        })

        function updateTableUI(stock,id_key){
            let products = stock['items'];
            products.map((product)=>{
                $('#'+id_key+'_'+product.product_id).html(product.count);
            })

        }

        function calculate(){

            let error = "";


            let initial_products = initial_stock['items'];
            let target_products = target_stock['items'];

            let valid_input_amount = true;
            initial_products.map((ip,key)=>{

                let input_value = $('#input_'+ip.product_id).val();
                if(input_value=="") input_value = 0;
                input_value = parseInt(input_value);
                if(input_value>ip.count){
                     $('#row_'+ip.product_id).css('background','yellow');
                     valid_input_amount = false;
                }else{
                    $('#row_'+ip.product_id).css('background','');
                    initial_products[key].count = initial_products[key].count-input_value
                    target_products[key].count = parseInt(target_products[key].count)+input_value
                }
                
            })

            updateTableUI(initial_stock,'initial'); 
            updateTableUI(target_stock,'target'); 

            if(!valid_input_amount){
                error = "Invalid Product Amount. Please reset the calculator";
            }
            return error;

        }

        function transferNow(){
            let request = {};
            request.user_id = user_id;
            request.auth_token = auth_token;
            request.initial_stock_id = initial_stock.stock_id;
            request.target_stock_id = target_stock.stock_id;
            request.initial_json = JSON.stringify(initial_stock['items']);
            request.target_json = JSON.stringify(target_stock['items']);

             $.post("api/stocks/transferproduct.php", request, function(result){
                window.location.href = "";
            }); 
        }


    </script>
</body>
</html>