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

$group_id = $_GET['group_id'];
$Group = new Group();
$gp_detail = $Group->getDetail(Array('group_id'=>$group_id));
$group_name = $gp_detail['group_name'];

$page = 1;
if(isset($_GET['page'])) $page = $_GET['page'];

$TargetPlan = new TargetPlan();
$planReq = Array(
    'user_id'=> $group_id,
    'page'=>$page
);

$planResponse = $TargetPlan->getPlans($planReq);
$plan = $planResponse['plans'];
if($plan) {
    $plan = $plan[0];
    $target_plan_id = $plan['target_plan_id'];
    $plan_detail = $TargetPlan->getDetails(Array('user_id'=>$user['user_id'],'target_plan_id'=>$target_plan_id));
}



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
						<h2 class="st_title"><i class="uil uil-apps"></i> <?php echo $group_name ?>'s Target Plan</h2>
					</div>

                    <?php if($plan) {?>
                    <div class="col-lg-12">
                        <span style="font-weight:bold">From : </span> <?php echo date('d M, Y',$plan['start_date']) ?> 
                        <span style="font-weight:bold;margin-left:20px;">To : </span> <?php echo date('d M, Y',$plan['end_date']) ?>
                        <br><br>
                    </div>
                    <div class="col-lg-8">
                        <div>
                            <canvas id="linechart" class="chartjs p-4" style="height: 350px;"></canvas>
                        </div>
                      
                        <div>
                            <br><br>
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
                        

                        <br><br>
                    </div>
                    <?php } else {?>
                            <div class="col-lg-8">
                                <br><br> 
                                No target plan for this group. You can create the new target plan now.
                            </div>
                    <?php }?>
                    <div class="col-lg-4">   
                    
                        <div class="row">
                            <div class="col-4">
                                <img src="uploads/groups/<?php echo $gp_detail['group_image'] ?>" alt="" style="width:50px; height:50px; border-radius:50px;">
                            </div>
                            <div class="col-8">
                                   <div style="font-size:16px; font-weight:bold;color:#444"><?php echo $group_name ?></div>
                             
                                   <div style = "font-size:14px; font-weight:bold; color:#555;margin-top:5px;">Target Plan</div>
                            </div>
                        </div>
                    

                        <br><br>    <br><br>
                        <h4>Update group target plan</h4>
                        <br>

                        <div class="basic_form">
                      
                            <p>Please select a period</p><br>
                            <label for="initial_date">Initial date:</label>
                            <input type="date" id="initial_date" name="initial_date"> <br><br><br>
                            
                            <label for="final_date">Final date:</label>
                            <input type="date" id="final_date" name="final_date"> <br><br>
                            
                            <p style="color:red;display:none" id="msg_err">This is error</p><br>
                           
                            <div style="display:flex">
                                <button id="btn_add" style="padding-left:20px;padding-right:20px;" class="btn_adcart" title="Create New Target Plan">Update</button>

                                <div id="loading" class="spinner" style="margin-top:10px; margin-left:30px;display:none">
                                    
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                    
                                </div>	


                            </div>
                            
                        </div>
                    </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>

        <script>

            let user_id=<?php echo $group_id ?>;
            let auth_token ="<?php echo $user['auth_token'] ?>";
            let products = <?php echo json_encode($products['main_product']) ?>;
            let plans = <?php echo json_encode($plan_detail['plans']) ?>;

            console.log(plans)

            let chart_labels = [];
            let chart_dataset = [];
            products.map((product,key) =>{
                chart_labels[key]= product.product_name; 
                let product_id =product.product_id; 
                if(plans.hasOwnProperty(product_id)){
                    chart_dataset[key]= parseInt(plans[product_id].count);
                }else{
                    chart_dataset[key]=0;
                }
            })

            console.log(chart_labels);
            console.log(chart_dataset);


            $(document).ready(()=>{

                $('#btn_add').click(()=>{
                    
                    $('#msg_err').hide();

                    let initial = $('#initial_date').val();
                    let target = $('#final_date').val();

                    if(initial==""){
                        $('#msg_err').show();
                        $('#msg_err').html("Please select the initial date");
                        return;
                    }

                    if(target==""){
                        $('#msg_err').show();
                        $('#msg_err').html("Please select the final date");
                        return;
                    }


                    let start_date = Date.parse(initial);
                    let end_date = Date.parse(target);

                    $('#loading').show();
                    
                    let req = {};
                    req.user_id = user_id;
                    req.start_date = start_date;
                    req.end_date = end_date;

                    $.post('api/targetplan/updategrouptargetplan.php', req)
                    .done(function(response) {
                        // Handle success response
                        window.location.href = "?group_id="+user_id;
                    })
                    .fail(function(xhr, status, error) {
                        // Handle error
                        console.error('Error:', error);
                        $('#loading').hide();
                    });

                });

                var ctx = document.getElementById("linechart");
                if (ctx !== null) {
                    var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: "line",

                    // The data for our dataset
                    data: {
                        labels: chart_labels,
                        datasets: [
                        {
                            label: "",
                            backgroundColor: "transparent",
                            borderColor: "rgb(237, 42, 38)",
                            data: chart_dataset,
                            lineTension: 0.3,
                            pointRadius: 5,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointHoverBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            pointHoverRadius: 8,
                            pointHoverBorderWidth: 1
                        }
                        ]
                    },

                    // Configuration options go here
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                        display: false
                        },
                        layout: {
                        padding: {
                            right: 10
                        }
                        },
                        scales: {
                        xAxes: [
                            {
                            gridLines: {
                                display: false
                            }
                            }
                        ],
                        yAxes: [
                            {
                            gridLines: {
                                display: true,
                                color: "#efefef",
                                zeroLineColor: "#efefef",
                            },
                            ticks: {
                                callback: function(value) {
                                var ranges = [
                                    { divider: 1e6, suffix: "M" },
                                    { divider: 1e4, suffix: "k" }
                                ];
                                function formatNumber(n) {
                                    for (var i = 0; i < ranges.length; i++) {
                                    if (n >= ranges[i].divider) {
                                        return (
                                        (n / ranges[i].divider).toString() + ranges[i].suffix
                                        );
                                    }
                                    }
                                    return n;
                                }
                                return formatNumber(value);
                                }
                            }
                            }
                        ]
                        },
                        tooltips: {
                        callbacks: {
                            title: function(tooltipItem, data) {
                            return data["labels"][tooltipItem[0]["index"]];
                            },
                            label: function(tooltipItem, data) {
                            return data["datasets"][0]["data"][tooltipItem["index"]];
                            }
                        },
                        responsive: true,
                        intersect: false,
                        enabled: true,
                        titleFontColor: "#333",
                        bodyFontColor: "#686f7a",
                        titleFontSize: 12,
                        bodyFontSize: 14,
                        backgroundColor: "rgba(256,256,256,0.95)",
                        xPadding: 20,
                        yPadding: 10,
                        displayColors: false,
                        borderColor: "rgba(220, 220, 220, 0.9)",
                        borderWidth: 2,
                        caretSize: 10,
                        caretPadding: 15
                        }
                    }
                    });
                }

                setupChart();
            })

            function setupChart(){
                var dual = document.getElementById("chart");
                if (dual !== null) {
                    var urChart = new Chart(dual, {
                    type: "line",
                    data: {
                        labels: ["Fri", "Sat", "Sun", "Mon", "Tue", "Wed", "Thu"],
                        datasets: [
                        {
                            label: "Old",
                            pointRadius: 4,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            fill: false,
                            backgroundColor: "transparent",
                            borderWidth: 2,
                            borderColor: "#ffc136",
                            data: [0, 4, 3, 5, 3, 7, 0]
                        },
                        {
                            label: "New",
                            fill: false,
                            pointRadius: 4,
                            pointBackgroundColor: "rgba(255,255,255,1)",
                            pointBorderWidth: 2,
                            backgroundColor: "transparent",
                            borderWidth: 2,
                            borderColor: "#ed2a26",
                            data: [0, 2, 4.3, 8, 5, 1.8, 2.2]
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
                        display: false
                        },
                        scales: {
                        xAxes: [
                            {
                            gridLines: {
                                drawBorder: false,
                                display: false
                            },
                            ticks: {
                                display: false, // hide main x-axis line
                                beginAtZero: true
                            },
                            barPercentage: 1.8,
                            categoryPercentage: 0.2
                            }
                        ],
                        yAxes: [
                            {
                            gridLines: {
                                drawBorder: false, // hide main y-axis line
                                display: false
                            },
                            ticks: {
                                display: false,
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

        </script>
	</div>
	<!-- Body End -->
</body>
</html>