<?php
$page_title="Target Plans";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/targetplan.php');
include_once('classes/group.php');
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


$target_plan_id = $_GET['plan_id'];



$TargetPlan = new TargetPlan();

$plan_detail = $TargetPlan->getDetails(Array('user_id'=>$user['user_id'],'target_plan_id'=>$target_plan_id));



$Product = new Product();
$products = $Product->getProducts();

function createUpdateUrl($product_name,$extra1,$content_id){
    $hint = "Enter item quantity";
    $message = "Update item quantity for $product_name in target plan.";
    $key = "count";

    echo "update.php?hint=$hint?&message=$message&key=$key&extra1=$extra1&content_id=$content_id&link=4";

}

?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
        <style>
            tr{
                cursor: pointer;
            }
            tr:hover{
                background:#F6EAEC;
                color:#D79DA9;
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
						<h2 class="st_title"><i class="uil uil-apps"></i> Details</h2>
					</div>

                    <?php if($plan_detail) {?>
                    <div class="col-lg-12">
                        <span style="font-weight:bold">From : </span> <?php echo date('d M, Y',$plan_detail['main']['start_date']) ?> 
                        <span style="font-weight:bold;margin-left:20px;">To : </span> <?php echo date('d M, Y',$plan_detail['main']['end_date']) ?>
                        <br><br>
                    </div>
                    <div class="col-lg-8">
                
                        <div class="card card-default analysis_card p-0" id="user-activity">
                            <div class="row no-gutters">
                                <div class="col-xl-12">
                                    <div>	
                                        <canvas id="my_chart_layout" class="chartjs p-4" style="height: 350px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br><br>
                    </div>
                    <?php } else {?>
                            <div class="col-lg-8">
                                <br><br> 
                                No target plan for this group. You can create the new target plan now.
                            </div>
                    <?php }?>
                    <div class="col-lg-4">   
                        <h4>Change Item Quantity</h4>
                        <br>
                        <div class="row">
                            <?php foreach($products['main_product'] as $product){
                                $product_name = $product['product_name'];
                                $product_id = $product['product_id'];
                                ?>

                                    <div class=".col-lg-4 .col-md-6 col-6">
                                    <a href="<?php echo createUpdateUrl($product_name,$target_plan_id,$product_id); ?>">
                                        <div class="card" style="padding:10px;">
                                            <?php echo $product_name ?>
                                        </div>
                                    </a>
                                    </div>
                                    
                                    
                            <?php } ?>
                        </div>
                       
                    </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>

        <script>

            let user_id="<?php echo $user['user_id'] ?>";
            let auth_token ="<?php echo $user['auth_token'] ?>";
            let products = <?php echo json_encode($products['main_product']) ?>;
            let plans = <?php echo json_encode($plan_detail['plans']) ?>;
            let sales = <?php echo json_encode($plan_detail['sales']) ?>;

            console.log(plans)

            let chart_labels = [];
            let chart_dataset_plan = [];
            let chart_dataset_sale = [];
            products.map((product,key) =>{
                chart_labels[key]= product.product_name; 
                let product_id =product.product_id; 
                if(plans.hasOwnProperty(product_id)){
                    chart_dataset_plan[key]= parseInt(plans[product_id].count);
                }else{
                    chart_dataset_plan[key]=0;
                }

                if(sales.hasOwnProperty(product_id)){
                    chart_dataset_sale[key]= parseInt(sales[product_id].count)+parseInt(sales[product_id].foc);
                }else{
                    chart_dataset_sale[key]=0;
                }
            })

        

            $(document).ready(()=>{

                var dual = document.getElementById("my_chart_layout");
                if (dual !== null) {
                    var urChart = new Chart(dual, {
                    type: "line",
                    data: {
                        labels: chart_labels,
                        datasets: [
                        {
                            label: "Target",
                            pointRadius: 4,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            fill: false,
                            backgroundColor: "transparent",
                            borderWidth: 2,
                            borderColor: "#ffc136",
                            data: chart_dataset_plan
                        },
                        {
                            label: "Sale",
                            fill: false,
                            pointRadius: 4,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            backgroundColor: "transparent",
                            borderWidth: 2,
                            borderColor: "#ed2a26",
                            data: chart_dataset_sale
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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

            })

        </script>
	</div>
	<!-- Body End -->
</body>
</html>