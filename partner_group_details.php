<?php 
$page_title="Leader | Details";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/group.php');
include_once('classes/user.php');
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

if($gp_detail){
    $User = new User();
    $admin = $User->getUserProfile($gp_detail['admin_id']);
    $admin = $admin[0];
}

$isMember = $Group->isMember($user['user_id'],$group_id);

if(!$isMember){
    $gp_detail = false;
}else{
    $isMember=$isMember[0];
}

$Product = new Product();
$products = $Product->getProducts();

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
        <?php if($gp_detail){ ?>
		<div class="_215b01">
			<div class="container-fluid">			
				<div class="row">
					<div class="col-lg-12">
						<div class="section3125">							
							<div class="row justify-content-center">						
								<div class="col-xl-4 col-lg-5 col-md-6">						
									<div class="card" style="padding:7px;">
                                        <div class="center-cropped" 
                                            style="background-image: url('uploads/groups/<?php echo $gp_detail['group_image'] ?>');width:100%;height:200px;">
                                        </div>
                                    </div>
								</div>
								<div class="col-xl-8 col-lg-7 col-md-6">
									<div class="_215b03">
										<h2> <?php echo $gp_detail['group_name'] ?> </h2>
										<span class="_215b04"> <?php echo $gp_detail['group_description'] ?>.</span>
									</div>

									<div class="_215b05">										
										Join At  - <?php echo date('d M , Y', $isMember['time']) ?>
									</div>

                                    <div class="_215b05">										
										Created At - <?php echo date('d M , Y', $gp_detail['time']) ?>
									</div>

                                    <div class="_215b05">										
										Founded by   
									</div>

                                    <div style="margin-top:10px;display:flex;">
                                        <img src="uploads/profiles/<?php echo $admin['profile_image'] ?>" alt=""  style="width:35px; height:35px; border-radius:50px;">
                                        <div style="padding:10px; color:white; font-size:16px;margin-left:10px;"> <?php echo $admin['name'] ?> </div>
                                    </div>
								
                                    <?php 
                                        echo "<pre>";
                                        // print_r($gp_detail);
                                        // print_r($isMember);
                                        // print_r($admin);
                                        echo "</pre>";
                                    ?>
									
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
									<a class="nav-item nav-link" id="nav-reviews-tab" data-toggle="tab" href="#nav-order" role="tab" aria-selected="false">My Sale Requests</a>
                                    <a class="nav-item nav-link" id="nav-reviews-tab" data-toggle="tab" href="#nav-sent" role="tab" aria-selected="false">Received</a>
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
                                        <div class="col-md-12">
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
        <?php }else{?>
            <?php print_r($isMember); ?>
            <br><br><br>
            <h2>Invalid Group</h2>
            <br><br><br>
            <br><br><br>
            <br><br><br>
            <br><br><br>

        <?php }?>
        <?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->


<script>
    let group_id="<?php echo $group_id; ?>";
    let user = <?php echo json_encode($user) ?>;
    
    console.log(user);

    let setGroupMembers = (data,status)=>{
        data = JSON.parse(data);
        // console.log(data);
        var member_list = $('#member_container').html();
        var members = data.members;
        if(data.total<=data.offset) $('#btn_member_see_more').hide();
        if(members){
            data.members.map((member)=>{
            member_list +=`
                <div class="card">
                    <a href="group_detail.php" class="menu--link" style="padding:10px;">
                        <div style="position:relative;">
                            <img style="width:30px;height:30px;margin-right:20px;border-radius:50px;" src="uploads/profiles/${member.profile_image}" alt=""> 
                            <span style="position:absolute;top:8px;">${member.name}</span>
                        </div>
                    </a>
                </div>
            
            `;
            })

            $('#member_container').html(member_list);
        }else{
            $('#btn_member_see_more').hide();
        }
    }

    let setOrders = (data, status)=>{
        data = JSON.parse(data);
        var orders = data.orders;
        console.log(orders);
        if(orders){
            var order_list = $('#order_container').html();
            if(orders.length<=30) $('#btn_order_see_more').hide();
            orders.map((order)=>{ 
                var status ="Request to leader";
                if(order.seen=="1" && order.is_sold_out=="1" & order.is_received=="1"){
                    status = "Completed";
                }else if(order.seen=="1" && order.is_sold_out=="1" & order.is_received=="0"){
                    status = "Distributed by leader";
                }else if(order.seen=="1" && order.is_sold_out=="0" & order.is_received=="0"){
                    status = "Seen by leader";
                }
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
                
                var status ="Request to leader";
                if(order.seen=="1" && order.is_sold_out=="1" & order.is_received=="1"){
                    status = "Completed";
                }else if(order.seen=="1" && order.is_sold_out=="1" & order.is_received=="0"){
                    status = "Distributed by leader";
                }else if(order.seen=="1" && order.is_sold_out=="0" & order.is_received=="0"){
                    status = "Seen by leader";
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
        })


        let searched_user = null;
        $('#bt_add_member').hide();
        $('#bt_search_member').click(()=>{
            let phone = $('#search_phone').val();
            $('#search_user_result').html(setLoading());
            $('#bt_add_member').hide();
            $.get(`api/users/searchbyphone.php?phone=${phone}`,(data,status)=>{
                
                data = JSON.parse(data);
                if(data){
                    searched_user = data.result;
                    $('#search_user_result').html(
                    `
                        <br><br>
                        <img src="uploads/groups/${data.result.profile_image}" alt="" style="width:100px; height:100px; border: 1px solid #aaa;border-radius:50px;">
                        <h4>${data.result.name}</h4>
                        <br><br>
                    `
                    );
                    $('#bt_add_member').show();
                    
                }else{
                    $('#search_user_result').html(`
                        <br>No user was found<br>
                    `);
                    $('#bt_add_member').hide();
                }
                
            })
            
        })

        $('#bt_add_member').click(()=>{
            $('#search_user_result').html(setLoading());
            if(searched_user!=null) {
                let member = {};
                member.member_id = searched_user.user_id;
                member.name = searched_user.name;
                let request = {};
                request.user_id = user.user_id;
                request.auth_token = user.auth_token;
                request.group_id = group_id;
                request.members=JSON.stringify([member]);
                
                $.post("api/groups/addmembers.php", request, function(result){
                    $('#search_user_result').html('');
                    let member_page=1;
                    $('#member_container').html('');
                    
                    $('#bt_add_member').hide();
                });
            } 
        })

    })

    

    function getOrders(page,group_id,is_received,setUI){
        $.get(`api/businesses/getmyorders.php?group_id=${group_id}&page=${page}&agent_id=${user.user_id}&is_received=${is_received}`,setUI)
    }

    function setLoading(){
        return `
            <div class="spinner">
                <br><br>
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
                <br><br>
            </div>	
        `;
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
        window.location.href ="order_detail.php?voucher_id="+voucher_id;
    }

</script>
	
<script>
{
    let group_id=<?php echo $group_id; ?>;
    let user_id=<?php echo $user['user_id'] ?>;
    let auth_token ="<?php echo $user['auth_token'] ?>";
    let products = <?php echo json_encode($products['main_product']) ?>;


    $(document).ready(()=>{

        getChartData();
        
    });

    function getChartData(){
        $.get(`api/groups/gettargetplanandorderrate.php?group_id=${group_id}&member_id=${user_id}`,(data,status)=>{
            data = JSON.parse(data);
            setupChart(data);
            
        });
    }

    
    function setupChart(response){

        console.log('target plan',response);

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

</body>
</html>