<?php 
$page_title="Details";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
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
if($gp_detail){
    if($gp_detail['admin_id']!=$user['user_id']) $gp_detail=false;
}

$group_name = $gp_detail['group_name'];

$update_group_name = getUpdateUrl("Enter your group name","Update your group name ($group_name)","group_name",$group_id);
$update_group_description =  getUpdateUrl("Enter group description","Update your group description ($group_name)","group_description",$group_id);

function getUpdateUrl($hint, $message, $key,$content_id){
    return "update.php?hint=$hint&message=$message&key=$key&content_id=$content_id&link=3";
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
        <?php if($gp_detail) {?>
		<div class="_215b01">
			<div class="container-fluid">			
				<div class="row">
					<div class="col-lg-12">
						<div class="section3125">							
							<div class="row justify-content-center">						
								<div class="col-xl-4 col-lg-5 col-md-6">						
									<div class="card" style="padding:7px;">
                                        <img id="img_profile" src="uploads/groups/<?php echo $gp_detail['group_image'] ?>" style="width:100%;height:200px;cursor:pointer;" />
                                        <i id="pick_profile" class='uil uil-camera' style="font-size:30px;position:absolute;top:150px;left:280px;cursor:pointer;background:#D79DA9;color:white;border-radius:50px"></i>
                                        <i id="profile_save" class='uil uil-check' style="font-size:30px;position:absolute;top:150px;left:280px;cursor:pointer;background:#D79DA9;color:white;border-radius:50px;display:none"></i>

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
										<h2> <?php echo $gp_detail['group_name'] ?> <a href="<?php echo $update_group_name ?>"> <i style="color:white;font-size:16px;" class='uil uil-edit'></i> </a> </h2>
										<span class="_215b04"> <?php echo $gp_detail['group_description'] ?> <a href="<?php echo $update_group_description ?>"> <i style="color:white;font-size:16px;" class='uil uil-edit'></i> </a></span>
									</div>
			
									 

									<div class="_215b05">										
										Created At - <?php echo date('d M , Y', $gp_detail['time']) ?>
									</div>
									<ul class="_215b31">										
										<li><button onclick="deleteGroup()" class="btn_adcart">Delete</button></li>
										<li><button id="btn_target_plan" class="btn_buy">Target Plan</button></li>
									</ul>
                                        
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
									<a class="nav-item nav-link" id="nav-courses-tab" data-toggle="tab" href="#nav-members" role="tab" aria-selected="false">Members</a>
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
                                        <div class="col-lg-12">
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
                                                                
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>				
								</div>
								<div class="tab-pane fade" id="nav-members" role="tabpanel">
									<div class="row">
                                        <div class="col-lg-4 col-md-4">
                                            <h4>Add Group Member</h4>
                                            <div id="search_user_result" style="text-align:center"></div>

                                            <div class="basic_form">
                                                <div class="ui search focus mt-10">
                                                    <div class="ui left input swdh11 swdh19">
                                                        <input class="prompt srch_explore" type="text" name="search_phone" id="search_phone" required="" maxlength="64" placeholder="Enter phone number">															
                                                    </div>
                                                </div>
                                                <br><br>
                                                <button id="bt_search_member" style="padding-left:20px;padding-right:20px;float:right" class="btn_adcart">Search</button>
                                                <button id="bt_add_member" style="padding-left:20px;padding-right:20px;float:right" class="btn_adcart">Add</button>
                                                
                                            </div>
                                            <br>
                                            

                                            <br><br><br><br>
                                        </div>

                                        <div class="col-lg-8 col-md-8">

                                            <div id="member_container">
                                                
                                            </div>

                                        
                                            <br>
                                            <span id="btn_member_see_more" class="menu--link btn_see_more"> 
                                                See More
                                            </span>
                                            <br>
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
                                                            <th class="text-center" scope="col"></th>
                                                            <th class="text-center" scope="col"></th>  
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
                                                            <th class="text-center" scope="col"></th>
                                                            <th class="text-center" scope="col"></th>    
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
    let group_id=<?php echo $group_id; ?>;
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
                    <a href="member_profile.php?member_id=${member.user_id}&group_id=${group_id}" class="menu--link" style="padding:10px;">
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
                var status = order.seen==1? 'Seen':'Delivered';
                order_list += `
                    <tr onclick="detail(${order.voucher_id})" >
                        <td class="text-center">
                            <img src="uploads/profiles/${order.profile_image}" style="width:30px; height:30px; border-radius:50px;"/>
                        </td>
                        <td class="text-center">${order.name}</td>
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
                        <td class="text-center">
                            <img src="uploads/profiles/${order.profile_image}" style="width:30px; height:30px; border-radius:50px;"/>
                        </td>
                        <td class="text-center">${order.name}</td>
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
        
        let member_page=1;
        getMembers(member_page,group_id);
        $('#btn_member_see_more').click(()=>{
            member_page++;
            getMembers(member_page,group_id);
        });

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
                    getMembers(member_page,group_id);
                    $('#bt_add_member').hide();
                });
            } 
        })


        $('#pick_profile').click(()=>{
            $('#input_profile').click();
        });

        $('#img_profile').click(()=>{
            $('#input_profile').click();
        });

        $('#input_profile').change(()=>{

            $('#pick_profile').hide();
            $('#profile_save').show();

            var files=$('#input_profile').prop('files');
            var file=files[0];
                
            var reader = new FileReader();

            reader.onload = function (e) {
                imageSrc=e.target.result;
                $('#img_profile').attr('src', imageSrc);
                    
            };

            reader.readAsDataURL(file);
                
        });

        $('#profile_save').click(()=>{
            var form_data = new FormData();

            form_data.append('user_id',user.user_id);
            form_data.append('auth_token',user.auth_token);
            form_data.append('content_id',group_id)

            if(imageSrc!=""){
                var files=$('#input_profile').prop('files');
                var file=files[0];
                form_data.append('myfile',file);
            }else{
                return;
            }

            var ajax=new XMLHttpRequest();
            $('#image_loading').show();
            $('#img_profile').hide();
            $('#profile_save').hide();
            ajax.onload =function(){
                if(ajax.status==200 || ajax.readyState==4){
                    console.log(ajax.responseText);
                    window.location.href="";
                }else{
                    console.log('Error');
                    $('#profile_uploading').hide();
                }
            };
            ajax.open("post",`api/groups/updateimage.php`,true);
            ajax.send(form_data);

        })

        $('#btn_target_plan').click(()=>{
            window.location.href="group_target_plan.php?group_id="+group_id;
        })

    })

    function getMembers(page,group_id){
        $.get(`api/groups/getmembers.php?group_id=${group_id}&page=${page}`,setGroupMembers)
    }

    function getOrders(page,group_id,isSoldOut,setUI){
        $.get(`api/businesses/getorders.php?group_id=${group_id}&page=${page}&user_id=${user.user_id}&is_sold_out=${isSoldOut}`,setUI)
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
    
    function deleteGroup(){
        window.location.href='delete_group.php?group_id='+group_id;
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
        $.get(`api/chart/group/orderandsale.php?group_id=${group_id}&user_id=${user_id}&start_date=${initial_time}&end_date=${final_time}`,(data,status)=>{
            data = JSON.parse(data);
            setupChart(data);
            
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
}
</script>

</body>
</html>