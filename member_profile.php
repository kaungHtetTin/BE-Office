<?php 
$page_title="Member Profile";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/group.php');
include_once('classes/product.php');
include_once('classes/rank.php');

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

$member_id = $_GET['member_id'];
$group_id = $_GET['group_id'];

$Group = new Group();
$profile = $Group->getMemberProfile(Array('member_id'=>$member_id,'group_id'=>$group_id));

$info = $profile['info'];

$Product = new Product();
$products = $Product->getProducts();

$Rank = new Rank();
$ranks = $Rank->index();
$member_rank = $Rank->get($info['rank_id']);

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
        <style>
            .center-cropped {
                background-position: center center;
                background-repeat: no-repeat;
            }

            .btn_see_more{
                border:1px solid #333;
                border-radius:3px;
                text-align:center;
                padding:10px;
            }

             tr{
                cursor: pointer;
            }
            tr:hover{
                background:#F6EAEC;
                color:#D79DA9;
            }

            .rank{
                width: 100%;
                border :1px solid #D79DA9;
                border-radius:30px;
                padding:5px;
                margin-bottom:10px;
                cursor:pointer;
            }

            .rank:hover{
                background:#F6EAEC;
                color:#D79DA9;
            }

            .rank_active{
                background:#D79DA9;
                color:white;
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
	<div class="wrapper _bg4586">
      
		<div class="_215b01">
			<div class="container-fluid">			
				<div class="row">
					<div class="col-lg-12">
						<div class="section3125">							
							<div class="row justify-content-center">						
								<div class="col-xl-4 col-lg-5 col-md-6">						
									<div class="card" style="padding:7px;">
                                        <img src="uploads/profiles/<?php echo $info['profile_image'] ?>" style="width:100%;height:200px;" />
                                       

                                        <div style="width:100%;height:200px;display:none" id="image_loading">
                                            <div class="spinner" id="loading" style="margin-top:100px;">
                                                <div class="bounce1"></div>
                                                <div class="bounce2"></div>
                                                <div class="bounce3"></div>
                                            </div>	
                                        </div>
                                    
                                    </div>

                                    <form enctype="multipart/form-data">
                                        <input type="file" name="myfile" id="input_profile"  style="display:none" accept="image/*" />
                                    </form>
								</div>
								<div class="col-xl-8 col-lg-7 col-md-6">
									<div class="_215b03">
										<h2> <?php echo $info['name'] ?></h2>
										 
									</div>
			
									 <div class="_215b05">										
										Rank -  <?php echo $member_rank['rank'];  ?>
									</div>

                                    <div class="_215b05">										
										Phone -  <?php echo $info['phone'] ?>
									</div>

                                    <div class="_215b05">										
										Email -  <?php echo $info['email'] ?>
									</div>

                                    <div class="_215b05">										
										Address -  <?php echo $info['address'] ?>
									</div>

									<div class="_215b05">										
										Joined At -  <?php echo Date('d M, Y',$profile['joinDate']['time']) ?>
									</div>
                                        
								</div>							
							</div>							
						</div>							
					</div>															
				</div>
			</div>
		</div>
        
		<div class="_215b15 _byt1458">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						 
						<div class="course_tabs">
							<nav>
								<div class="nav nav-tabs tab_crse justify-content-center" id="nav-tab" role="tablist">
									<a class="nav-item nav-link active" id="nav-about-tab" data-toggle="tab" href="#nav-overview" role="tab" aria-selected="true">Overview</a>
									<a class="nav-item nav-link" id="nav-reviews-tab" data-toggle="tab" href="#nav-order" role="tab" aria-selected="false">Orders</a>
                                    <a class="nav-item nav-link" id="nav-reviews-tab" data-toggle="tab" href="#nav-sent" role="tab" aria-selected="false">Sent</a>
								</div>
							</nav>						
						</div>
					</div>
				</div>
			</div>
		</div>
        
		<div class="_215b17">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<div class="course_tab_content">
							<div class="tab-content" id="nav-tabContent">
								<div class="tab-pane fade show active" id="nav-overview" role="tabpanel">
									<div class="row">
                                         <div class="col-md-8">
                                            <!-- User activity statistics -->
                                            <div class="card card-default analysis_card p-0" id="user-activity">
                                                <div class="row no-gutters">
                                                    <div class="col-12">
                                                        <div data-scroll-height="450">	
                                                            <div class="card-header justify-content-between">
                                                                <h2 class="m-0">Target Plan and Order Rate</h2>

                                                                <div style="display:flex">
                                                                    <div class="curntusr145" style="flex:1">
                                                                        <p class="my-2">From</p>
                                                                        <span id="tv_from"> </span>
                                                                         
                                                                    </div>
                                                                    <div class="curntusr145"  style="flex:1">
                                                                        <p class="my-2">To</p>
                                                                         <span id="tv_to"> </span>

                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <canvas id="chart" class="chartjs p-4" style="height: 450px;"></canvas>
                                                            <div class="card-footer d-flex flex-wrap bg-white">
                                                                <div href="#" class="text-uppercase py-3 ovrvew-1">Total</div>

                                                                <table>
                                                                    <tr>
                                                                        <td>
                                                                            <br>
                                                                            <h5>Target Point</h5>
                                                                            <span id="target_point" style="font-size:15px;"> </span>
                                                                            <br>
                                                                        </td>
                                                                        <td>
                                                                            <br>
                                                                            <h5>Rewarded Point</h5>
                                                                            <span id="rewarded_point" style="font-size:15px;"> </span>
                                                                            <br>
                                                                        </td>
                                                                    </tr>
                                                                </table>

                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="card card-default analysis_card p-0" id="user-activity">
                                                <div class="row no-gutters">
                                                    <div class="col-12">
                                                        <div data-scroll-height="450">	
                                                            <div class="card-header justify-content-between">
                                                                <h2 class="m-0">Sale Rate <span style="font-size:14px;">Filtering</span></h2>

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

                                                            <canvas id="chart2" class="chartjs p-4" style="height: 450px;"></canvas>
                                                            <!-- <div class="card-footer d-flex flex-wrap bg-white">
                                                                <a href="#" class="text-uppercase py-3 ovrvew-1">Audience Overview</a>
                                                            </div> -->
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <!-- filtering order rate -->

                                        <div class="col-md-4">
                                            <div class="card card-default analysis_card p-0" id="user-activity">
                                                <div style="padding:10px;">
                                                    <h4>Promote this member</h4>
                                                    <br>
                                                   

                                                    <?php foreach ($ranks as $rank){ ?>
                                                        <?php if($user['rank_id']>$rank['id']){ ?>

                                                        
                                                            <?php if($member_rank['id']==$rank['id']) {?>
                                                                <div class="rank rank_active">
                                                                    <?php echo $rank['rank'] ?>
                                                                </div>
                                                            <?php }else {?>
                                                                <div class="rank">
                                                                    <?php echo $rank['rank'] ?>
                                                                </div>
                                                            <?php }?>
                                                        <?php }?>
                                                        
                                                    <?php }?>

                                                    <div id="err_promote" style="color:red;text-align:center;padding:5px;display:none">
                                                        This is error 
                                                    </div>
                                                    
                                                    <br>
                                                    <div class="spinner" id="loading_promote" style="display:none">
                                                        <div class="bounce1"></div>
                                                        <div class="bounce2"></div>
                                                        <div class="bounce3"></div>
                                                    </div>	
                                                    <br>
                                                    <div style="text-align:right">
                                                        <button id="btn_promote" class="btn" style="background:#16AD16;color:white;">
                                                            Promote Now
                                                        </button>
                                                    </div>
                                                                
                                                    <hr>

                                                </div>
                                                
                                               
                                            </div>
                                           
                                        </div>


                                    </div>				
								</div>
							 
								<div class="tab-pane fade" id="nav-order" role="tabpanel">
									<div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                            <div class="table-responsive mt-30">
                                                <table class="table ucp-table">
                                                    <thead class="thead-s">
                                                        <tr>
                                                            <th class="text-center" scope="col">Id</th>
                                                            <th class="text-center" scope="col">Status</th>
                                                            <th class="text-center" scope="col">Amount</th>
                                                            <th class="text-center" scope="col">Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="order_container">
                                                       
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                    <span id="btn_order_see_more" class="menu--link btn_see_more"> 
                                        See More
                                    </span>
								</div>
                                <div class="tab-pane fade" id="nav-sent" role="tabpanel">
									<div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                            <div class="table-responsive mt-30">
                                                <table class="table ucp-table">
                                                    <thead class="thead-s">
                                                        <tr>
                                                          
                                                            <th class="text-center" scope="col">Id</th>
                                                            <th class="text-center" scope="col">Status</th>
                                                            <th class="text-center" scope="col">Amount</th>
                                                            <th class="text-center" scope="col">Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="sent_container">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                    <span id="btn_sent_see_more" class="menu--link btn_see_more"> 
                                        See More
                                    </span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
 
        <?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->

    <script>
        let group_id=<?php echo $group_id; ?>;
        let user = <?php echo json_encode($user) ?>;
        let member_id = <?php echo $member_id ?>;
        let ranks = <?php echo json_encode($ranks) ?>;

        let setOrders = (data, status)=>{
            data = JSON.parse(data);
            var orders = data.orders;
            console.log(orders);
            if(orders){
                var order_list = $('#order_container').html();
                if(orders.length<=30) $('#btn_order_see_more').hide();
                orders.map((order)=>{ 
                    var status = order.seen==1? 'Seen':'Delivered';
                    order_list += `
                        <tr onclick="detail(${order.voucher_id})" >
                            <td class="text-center">${order.voucher_id}</td>
                            <td class="text-center"><b class="course_active">${status}</b></td>
                            <td class="text-center">${order.total_amount}</td>
                            <td class="text-center">${formatDateTime(order.voucher_id*1000)}</td>
                        </tr>
                    `;
                })
            }else{
                    $('#btn_order_see_more').hide();
            }

            $('#order_container').html(order_list);
        }

        let setSents = (data, status)=>{
            data = JSON.parse(data);
            var orders = data.orders;
            // console.log(orders);
            var sent_list = $('#sent_container').html();
            if(orders){ 
                if(orders.length<=30) $('#btn_sent_see_more').hide();
                orders.map((order)=>{ 
                    
                    var status='';
                    if( order.is_received==0){
                        status = order.seen==1? 'Seen':'Delivered';
                    }else{
                        status = 'Received';
                    }
                        
                    sent_list += `
                        <tr onclick="detail(${order.voucher_id})" >
                            <td class="text-center">${order.voucher_id}</td>
                            <td class="text-center"><b class="course_active">${status}</b></td>
                            <td class="text-center">${order.total_amount}</td>
                            <td class="text-center">${formatDateTime(order.voucher_id*1000)}</td>
                        </tr>
                    `;
                })
            }else{
                    $('#btn_sent_see_more').hide();
            }

            $('#sent_container').html(sent_list);
        }


        $(document).ready(()=>{
            let order_page=1;
            getOrders(order_page,group_id,0,setOrders);
            $('#btn_order_see_more').click(()=>{
                order_page++;
                getOrders(order_page,group_id,0,setOrders);
            })

            let sent_page = 1;
            getOrders(sent_page,group_id,1,setSents);
            $('#btn_sent_see_more').click(()=>{
                sent_page++;
                getOrders(sent_page,group_id,1,setSents);
            });



            let selected_rank_id=null;
            $('#btn_promote').click(()=>{
                $('#err_promote').hide();
                $('#loading_promote').hide();
                if(selected_rank_id==null){
                    $('#err_promote').show();
                    $('#err_promote').html('Please select a rank');
                    return;
                }

                $('#loading_promote').show();

                let req = {};
                req.user_id = member_id;
                req.rank_id = selected_rank_id;

                $.post('api/users/promote.php', req)
                .done(function(response) {
                    // Handle success response
                    $('#loading_promote').hide();
                    window.location.href="?member_id="+member_id+"&group_id="+group_id;
                })
                .fail(function(xhr, status, error) {
                    // Handle error
                    console.error('Error:', error);
                    $('#loading').hide();
                });

            })
            
            $('.rank').each((j,rank)=>{
                $(rank).click(()=>{
                    selected_rank_id = ranks[j].id;
                    $('.rank').each((i,r)=>{
                        $(r).css({"border":"","background":""});
                    })

                    $(rank).css({"border":"1px solid black","background":"#A5FE82","color":"black"});
                    
                    //console.log(ranks);
                    console.log(selected_rank_id);
                })
            })

        });

        function getOrders(page,group_id,isSoldOut,setUI){
            $.get(`api/businesses/getorderbyagent.php?group_id=${group_id}&page=${page}&user_id=${user.user_id}&is_sold_out=${isSoldOut}&agent_id=${member_id}`,setUI)
        }

        function formatDateTime(cmtTime){

            var currentTime = Date.now();
            var min=60;
            var h=min*60;
            var day=h*24;

            var diff =currentTime-cmtTime
            diff=diff/1000;
            
            if(diff<day*3){
                if(diff<min){
                    return "a few second ago";
                }else if(diff>=min&&diff<h){
                    return Math.floor(diff/min)+'min ago';
                }else if(diff>=h&&diff<day){
                    return Math.floor(diff/h)+'h ago';
                }else{
                    return Math.floor(diff/day)+'d ago';
                }
            }else{
                var date = new Date(Number(cmtTime));
                return date.toLocaleDateString("en-GB");
            }
        }

        function detail(voucher_id){
            window.location.href ="incoming_order_detail.php?voucher_id="+voucher_id;
        }

    </script>


    <script>
    {
        let group_id=<?php echo $group_id; ?>;
        let user_id=<?php echo $user['user_id'] ?>;
        let auth_token ="<?php echo $user['auth_token'] ?>";
        let products = <?php echo json_encode($products['main_product']) ?>;
        let member_id = <?php echo $member_id ?>;

       

        $(document).ready(()=>{

            getChartData();
            
        });

        function getChartData(){
            $.get(`api/groups/gettargetplanandorderrate.php?group_id=${group_id}&member_id=${member_id}`,(data,status)=>{
                data = JSON.parse(data);
                setupChart(data);
                
            });
        }

        
        function setupChart(response){


            let target_plan = response.target_plan;
            let months =  ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

            const start_date = new Date(parseFloat(target_plan.start_date)*1000);
            const end_date = new Date(parseFloat(target_plan.end_date)*1000);

            console.log('start date',start_date);

            $('#tv_from').html(start_date.getDate()+' '+months[start_date.getMonth()]+', '+start_date.getFullYear());
            $('#tv_to').html(end_date.getDate()+' '+months[end_date.getMonth()]+', '+end_date.getFullYear());

        

            let chart_labels = [];
            let chart_dataset_target = [];
            let chart_dataset_sale = [];

            let total_target_point =0;
            let total_rewarded_point = 0;

            products.map((product,key) =>{
                chart_labels[key]= product.product_name; 

                let product_id =product.product_id; 


                if(response.hasOwnProperty("sale_detail")){
                    if(response.sale_detail.hasOwnProperty(product_id)){
                        let count = parseInt(response.sale_detail[product_id].count);
                        let point = product.point*count
                        chart_dataset_sale[key] = count;
                        total_rewarded_point+=point;
                    }else{
                        chart_dataset_sale[key]=0;
                    }
                }else{
                    chart_dataset_sale[key]=0;
                }

                
                if(response.hasOwnProperty("target_plan_detail")){
                    if(response.target_plan_detail.hasOwnProperty(product_id)){
                        let count = parseInt(response.target_plan_detail[product_id].count);
                        let point =  product.point*count;
                        chart_dataset_target[key] = count;
                        total_target_point+=point;
                    }else{
                        chart_dataset_target[key]=0;
                    }
                }else{
                    chart_dataset_target[key]=0;
                }

            })

        
            $('#target_point').html(total_target_point);
            $('#rewarded_point').html(total_rewarded_point);

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
                        label: "Target Plan",
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: "rgba(255,255,255,1)",
                        pointBorderWidth: 2,
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        borderColor: "#ed2a26",
                        data: chart_dataset_target
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
    }
    </script>



    <script>
    {
        let user_id=<?php echo $user['user_id'] ?>;
        let auth_token ="<?php echo $user['auth_token'] ?>";
        let member_id = <?php echo $member_id ?>;
        let group_id = <?php echo $group_id ?>;
        let products = <?php echo json_encode($products['main_product']) ?>;

        const currentDate = new Date();
        const firstDayofMonth = new Date(currentDate.getFullYear(),currentDate.getMonth(),2,0,0,1);
      
        
        
        let initial_default = firstDayofMonth.toISOString().split('T')[0];
        let target_default = currentDate.toISOString().split('T')[0];

        let initial_time = 0;
        let final_time = 0;

        $(document).ready(()=>{
            $('#initial_date').val(initial_default);
            $('#final_date').val(target_default);

            initial_time = Date.parse(initial_default);
            final_time = Date.parse(target_default);
            final_time = final_time + (60*60*24*1000);

            $('#initial_date').on('change',()=>{
                initial_time= Date.parse($('#initial_date').val());

                console.log($('#initial_date').val());

                getChartData();
            })

            $('#final_date').on('change',()=>{
                final_time = Date.parse($('#final_date').val());
                final_time = final_time + (60*60*24*1000);
                console.log($('#final_date').val());
                getChartData();
            })

            getChartData();
            
        });

        function getChartData(){
            $.get(`api/chart/group/filter_order_rate.php?group_id=${group_id}&member_id=${member_id}&start_date=${initial_time}&end_date=${final_time}`,(data,status)=>{
                data = JSON.parse(data);
             
                setupChart(data);
                
            });
        }

        
        function setupChart(response){

            console.log(response);

            let chart_labels = [];
            let chart_dataset_order = [];
    
            products.map((product,key) =>{
                chart_labels[key]= product.product_name; 

                let product_id =product.product_id; 
                
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

        

            var dual = document.getElementById("chart2");
            dual.innerHTML="";
            if (dual !== null) {
                var urChart = new Chart(dual, {
                type: "line",
                data: {
                    labels: chart_labels ,
                    datasets: [
                    {
                        label: "Order Rate",
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
    }
    </script>

</body>
</html>