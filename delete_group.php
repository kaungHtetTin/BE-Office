<?php 
$page_title="Details";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
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

$group_id = $_GET['group_id'];
$Group = new Group();
$gp_detail = $Group->getDetail(Array('group_id'=>$group_id));
if($gp_detail){
    if($gp_detail['admin_id']!=$user['user_id']) $gp_detail=false;
}



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
		<div class="_215b01" style="background:white;color:black">
			<div class="container-fluid">			
				<div class="row">
					<div class="col-lg-12">
						<div class="section3125">							
							<div class="row justify-content-center">						
								<div class="col-xl-4 col-lg-5 col-md-6">						
									<div class="card" style="padding:7px;">
                                        <img id="img_profile" src="uploads/groups/<?php echo $gp_detail['group_image'] ?>" style="width:100%;height:200px;cursor:pointer;" />

                                    </div>
								</div>
								<div class="col-xl-8 col-lg-7 col-md-6">
									<div class="_215b03">
                                        <h2 style="color:#333">Delete Group!</h2>
										<h3 style="color:#333"> <?php echo $gp_detail['group_name'] ?> </h3>
										<span class="_215b04" style="color:#333"> <?php echo $gp_detail['group_description'] ?>.</span>
									</div>
			
									<div class="_215b05" style="color:#333">										
										 Do you really want to delete this group?
									</div>

                                    <br><br>

                                    <div style="display:flex">
                                        <button id="btn_delete" class="btn_adcart">Delete</button>
                                        <div class="spinner" id="loading" style="margin-left:20px; margin-top:10px;;display:none">
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
        
        
        $(document).ready(()=>{
            $('#btn_delete').click(()=>{
                $('#loading').show();

                let req = {};
                req.user_id = user.user_id;
                req.auth_token = user.auth_token;
                req.group_id = group_id;

                $.post("api/groups/disable.php", req, function(result){
                    window.location.href="my_group.php";
                });

            })


        })
        


    </script>
	
</body>
</html>