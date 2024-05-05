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
					
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- User activity statistics -->
                                <div class="card card-default analysis_card p-0" id="user-activity">
                                    <div class="row no-gutters">
                                        <div class="col-12">
                                            <div data-scroll-height="450">	
                                                <div class="card-header justify-content-between">
                                                    <h2 class="m-0">Sale Rate</h2>

                                                    <div style="display:flex">
                                                        <div class="curntusr145" style="flex:1">
                                                            <p class="my-2">From</p>
                                                            <input style="" type="date" id="initial_date" name="initial_date">
                                                        </div>
                                                        <div class="curntusr145"  style="flex:1">
                                                            <p class="my-2">To</p>
                                                            <input style="" type="date" id="final_date" name="final_date">
                                                        </div>
                                                    </div>

                                                </div>

                                                <canvas id="chart" class="chartjs p-4" style="height: 450px;"></canvas>
                                                <!-- <div class="card-footer d-flex flex-wrap bg-white">
                                                    <a href="#" class="text-uppercase py-3 ovrvew-1">Audience Overview</a>
                                                </div> -->
                                            </div>
                                            <button onclick="goAnalysis()" class="btn_adcart" style="width:150px;float:right;margin:10px;">See More </button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-lg-4">
                        <br><br>
                        <div class="row">
                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                                <a href="my_business.php?is_sold_out=0" class="menu--link card" style="padding:10px;">
                                    <div style="text-align:center">
                                        <img style="width:70px;height:70px;" src="icon/mybusiness.png" alt="">
                                        <br>
                                        My Business
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 ">
                                <a href="create_order.php" class="menu--link card"  style="padding:10px;">
                                    <div class="" style="text-align:center">
                                        <img style="width:70px;height:70px;" src="icon/createorder.png" alt=""><br>
                                        Create Order
                                    </div>
                                </a>
                            </div>
                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                                <a href="create_invoice.php" class="menu--link card"  style="padding:10px;">
                                    <div style="text-align:center">
                                        <img style="width:70px;height:70px;" src="icon/voucherlist.png" alt=""><br>
                                        Voucher
                                    </div>
                                </a>
                            </div>

                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                                <a href="product_left.php" class="menu--link card"  style="padding:10px;">
                                    <div class=""  style="text-align:center">
                                        <img style="width:70px;height:70px;" src="icon/products.png" alt=""><br>
                                        Product Left
                                    </div>
                                </a>
                            </div>

                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 ">
                                <a href="my_group.php"  class="menu--link card" style="padding:10px;">
                                    <div class=""  style="text-align:center">
                                        <img style="width:70px;height:70px;" src="icon/mygroup.png" alt=""> <br>
                                        My Group
                                    </div>
                                </a>
                            </div>

                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 ">
                                <a href="partner_group.php"  class="menu--link card" style="padding:10px;">
                                    <div class=""  style="text-align:center">
                                        <img style="width:70px;height:70px;" src="icon/partnergroup.png" alt="">
                                        <br>
                                        My Leader's Group
                                    </div>
                                </a>
                            </div>
                        </div>
                     </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
        <script>
            let user_id=<?php echo $user['user_id'] ?>;
            let auth_token ="<?php echo $user['auth_token'] ?>";
            let products = <?php echo json_encode($products['main_product']) ?>;

            const currentDate = new Date();
            const firstDayofMonth = new Date(currentDate.getFullYear(),currentDate.getMonth(),1);
            
            let initial_default = firstDayofMonth.toISOString().split('T')[0];
            let target_default = currentDate.toISOString().split('T')[0];

            let initial_time = 0;
            let final_time = 0;

            $(document).ready(()=>{
                $('#initial_date').val(initial_default);
                $('#final_date').val(target_default);

                initial_time = Date.parse(initial_default);
                final_time = Date.parse(target_default);

                $('#initial_date').on('change',()=>{
                    initial_time= Date.parse($('#initial_date').val());
                    getChartData();
                })

                 $('#final_date').on('change',()=>{
                    final_time = Date.parse($('#final_date').val());
                    getChartData();
                })

                getChartData();
               
            });

            function getChartData(){
                $.get(`api/chart/orderandsale.php?user_id=${user_id}&start_date=${initial_time}&end_date=${final_time}`,(data,status)=>{
                    data = JSON.parse(data);
                    setupChart(data);
                    console.log(data);
                });
            }

           
            function setupChart(response){

                let chart_labels = [];
                let chart_dataset_order = [];
                let chart_dataset_sale = [];
                products.map((product,key) =>{
                    chart_labels[key]= product.product_name; 

                    let product_id =product.product_id; 


                    if(response.hasOwnProperty("sales")){
                        if(response.sales.hasOwnProperty(product_id)){
                            chart_dataset_sale[key] = parseInt(response.sales[product_id].count);
                        }else{
                            chart_dataset_sale[key]=0;
                        }
                    }else{
                        chart_dataset_sale[key]=0;
                    }


                    
                    if(response.hasOwnProperty("orders")){
                        if(response.orders.hasOwnProperty(product_id)){
                            chart_dataset_order[key] = parseInt(response.orders[product_id].count);
                        }else{
                            chart_dataset_order[key]=0;
                        }
                    }else{
                        chart_dataset_order[key]=0;
                    }

                })

                console.log(chart_labels,chart_dataset_order,chart_dataset_sale)


                var dual = document.getElementById("chart");
                dual.innerHTML="";
                if (dual !== null) {
                    var urChart = new Chart(dual, {
                    type: "line",
                    data: {
                        labels: chart_labels ,
                        datasets: [
                        {
                            label: "Sale Rate",
                            pointRadius: 4,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            fill: false,
                            backgroundColor: "transparent",
                            borderWidth: 2,
                            borderColor: "#ffc136",
                            data: chart_dataset_sale
                        },
                        {
                            label: "Investment",
                            fill: false,
                            pointRadius: 4,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            backgroundColor: "transparent",
                            borderWidth: 2,
                            borderColor: "#ed2a26",
                            data: chart_dataset_order
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        layout: {
                        padding: {
                            right: 10
                        }
                        },

                        legend: {
                        display: true
                        },
                        scales: {
                        xAxes: [
                            {
                            gridLines: {
                                drawBorder: true,
                                display: true
                            },
                            ticks: {
                                display: true, // hide main x-axis line
                                beginAtZero: true
                            },
                            barPercentage: 1.8,
                            categoryPercentage: 0.2
                            }
                        ],
                        yAxes: [
                            {
                            gridLines: {
                                drawBorder: true, // hide main y-axis line
                                display: true
                            },
                            ticks: {
                                display: true,
                                beginAtZero: true
                            }
                            }
                        ]
                        },
                        tooltips: {
                        titleFontColor: "#333",
                        bodyFontColor: "#686f7a",
                        titleFontSize: 12,
                        bodyFontSize: 14,
                        backgroundColor: "rgba(256,256,256,0.95)",
                        displayColors: true,
                        borderColor: "rgba(220, 220, 220, 0.9)",
                        borderWidth: 2
                        }
                    }
                    });
                }
            }


            function goAnalysis(){
                window.location.href = "analysis.php";
            }


        </script>
	</div>
	<!-- Body End -->
</body>
</html>