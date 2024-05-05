<?php
$page_title="Target Plans";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/targetplan.php');


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

$page = 1;
if(isset($_GET['page'])) $page = $_GET['page'];

$TargetPlan = new TargetPlan();
$planReq = Array(
    'user_id'=> $user['user_id'],
    'page'=>$page
);

$planResponse = $TargetPlan->getPlans($planReq);
$plans = $planResponse['plans'];



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
						<h2 class="st_title"><i class="uil uil-apps"></i>Target Plans</h2>
					</div>
                    <div class="col-lg-8">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                <th width="20%" class="text-center" scope="col"></th>
                                                <th class="text-center" scope="col">From</th>
                                                <th class="text-center">To</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php if($plans){ ?>
                                                <?php foreach($plans as $plan) {?>
                                                     <tr onclick="detail(<?php echo $plan['target_plan_id']; ?>)">
                                                        <td class="text-center">
                                                            <img style="width: 15px;height:15px;margin-left:20px;margin-right:10px;" src="icon/nav_target_plan.png" alt="">
                                                        </td>
                                                        <td class="text-center"><?php echo date('d M, Y',$plan['start_date']); ?></td>
                                                        <td class="text-center"><?php echo date('d M, Y',$plan['end_date']); ?></td>
                                                    </tr>
                                                <?php }?>
                                            
                                            <?php }else {?>
                                                <tr>
                                                    <td colspan="3"> No target plan</td>
                                                </tr>
                                            <?php }?>
                                            
                                             
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                          

                        </div>
                           
                    </div>
                    <div class="col-lg-4">        
                        <br><br>    
                        <h4>Add new target plan</h4>
                        <br>

                        

                        <div class="basic_form">
                      
                            <p>Please select a period</p><br>
                            <label for="initial_date">Initial date:</label>
                            <input type="date" id="initial_date" name="initial_date"> <br><br><br>
                            
                            <label for="final_date">Final date:</label>
                            <input type="date" id="final_date" name="final_date"> <br><br>
                            
                            <p style="color:red;display:none" id="msg_err">This is error</p><br>
                           
                            <div style="display:flex">
                                <button id="btn_add" style="padding-left:20px;padding-right:20px;" class="btn_adcart" title="Create New Target Plan">Add</button>

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

            let user_id=<?php echo $user['user_id'] ?>;
            let auth_token ="<?php echo $user['auth_token'] ?>";

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

                    $.post('api/targetplan/add.php', req)
                    .done(function(response) {
                        // Handle success response
                        console.log('Success:', response);
                        $('#loading').hide();
                    })
                    .fail(function(xhr, status, error) {
                        // Handle error
                        console.error('Error:', error);
                        $('#loading').hide();
                    });

                });

            })


            function detail(id){
                window.location.href = "target_plan_detail.php?plan_id="+id;
            }


        </script>
	</div>
	<!-- Body End -->
</body>
</html>