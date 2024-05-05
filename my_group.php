<?php
$page_title="Create Invoice";
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

if(isset($_GET['page'])) $page = $_GET['page'];
else $page = 1;

$Group = new Group();

if($_SERVER['REQUEST_METHOD']=="POST"){
    $result=$Group->create_group($_POST,$_FILES);
    print_r($result);
}

$groups = false;
$g_result = $Group->getMyGroup(Array('user_id'=>$user['user_id'],'page'=>$page));
if($g_result['status']=="success") $groups = $g_result['groups'];





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

            .pagination{
                padding:10px;
                border:1px solid #D79DA9;
                border-radius:50px;
                
                margin:10px;
                text-align:center;
            }

            .p_active{
                background:#D79DA9;
                color:white;
            }

            .gp_img{
                width:30px;
                height:30px;
                margin-right:20px;
                border-radius:3px;
            }
            .err_msg{
                display:none;
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
						<h2 class="st_title"><i class="uil uil-apps"></i> My Groups</h2>
					</div>
					
                    <div class="col-lg-6 col-md-6">

                        <?php if($groups) { foreach($groups as $group){  ?>
                            <div class="card">
                                <a href="group_detail.php?group_id=<?php echo $group['group_id'] ?>" class="menu--link" style="padding:10px;">
                                    <div style="position:relative;">
                                        <img class="gp_img" src="uploads/groups/<?php echo $group['group_image'] ?>" alt=""> 
                                        <span style="position:absolute;top:8px;"><?php echo $group['group_name'] ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php } }else { ?>
                            <div>
                                <br><br><br>
                                No group was found. You can create new group for your business now.
                            </div>
                        <?php } ?>

                        <!-- <br>
                        <div style="display:flex;">
                            <a href="" class="pagination menu--link"><<</a>
                            <a href="" class="pagination p_active menu--link">11</a>
                            <a href="" class="pagination menu--link">11</a>
                            <a href="" class="pagination menu--link">11</a>
                            <a href="" class="pagination menu--link">>></a>
                        </div> -->
                        
                      
                        <br><br>
                     </div>

                    <div class="col-lg-6 col-md-6">
                        <h4>Create New Groups</h4>
                        <br> 
                        <div class="basic_form">
                            
                            <div style="margin:10px;">
                                <a href="javascript:void(0)" class="card" title="Account" style="width:120px;height: 120px;cursor:pointer;border-radius:50%;padding:3px;">
                                    <img src="icon/mygroup.png" style="width: 114px;height: 114px; border-radius:50%" alt="" id="img_profile">
                                
                                </a>
                                <i id="pick_profile" class='uil uil-camera' style="font-size:30px;position:absolute;top:120px;left:120px;cursor:pointer"></i>
                                <form enctype="multipart/form-data">
                                    <input type="file" name="myfile" id="input_profile"  style="display:none" accept="image/*" />
                                </form>
                                <p class="err_msg" id="err_photo" style="color:red">Select a group photo.</p>
                            </div>
                            
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text" name="group_name" id="id_group_name" required="" maxlength="64" placeholder="Enter group name">	<br>
                                    													
                                </div>
                                <p class="err_msg" id="err_name" style="color:red"> Enter group name</p>
                            </div>
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text" name="group_description" id="id_group_description" required="" maxlength="64" placeholder="Enter group description">															
                                </div>
                                <p class="err_msg" id="err_description" style="color:red"> Enter group description</p>
                            </div>

                            <input type="hidden" value="<?php echo $user['user_id'] ?>" name="user_id">
                            <input type="hidden" value="<?php echo $user['auth_token'] ?>" name="auth_token">
                            <div class="spinner" id="loading" style="display:none">
                                <br><br>
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                                <br><br>
                            </div>	
                            
                            <br>
                            <button id="btn_create" style="padding-left:20px;padding-right:20px;" class="btn_adcart" title="Create New Group">Create</button>
                        </div>
                         
                    </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->

    <script>
        let user_id=<?php echo $user['user_id'] ?>;
        let auth_token ="<?php echo $user['auth_token'] ?>";
        let imageSrc="";

        $('#err_name').hide();
        $('#err_description').hide();
        $('#err_photo').hide();

        $('#pick_profile').click(()=>{
			$('#input_profile').click();
        });

        $('#img_profile').click(()=>{
			$('#input_profile').click();
        });

        $('#input_profile').change(()=>{
            var files=$('#input_profile').prop('files');
            var file=files[0];
                
            var reader = new FileReader();

            reader.onload = function (e) {
                imageSrc=e.target.result;
                $('#img_profile').attr('src', imageSrc);
                    
            };

            reader.readAsDataURL(file);
                
        });

        $('#btn_create').click(()=>{

            $('#err_name').hide();
            $('#err_description').hide();
            $('#err_photo').hide();

            let group_name = $('#id_group_name').val();
            let group_description = $('#id_group_description').val();

            //validate
            let invalid = false;
            if(group_name==""){
                invalid = true;
                $('#err_name').show();
            }
            if(group_description==""){
                invalid = true;
                $('#err_description').show();
            }
            if(imageSrc==""){
                invalid = true;
                $('#err_photo').show();
            }

            if(invalid) return;

            $('#loading').show();
            var form_data = new FormData();
            form_data.append('group_name',group_name);
            form_data.append('group_description',group_description);
            form_data.append('user_id',user_id);
            form_data.append('auth_token',auth_token);

            if(imageSrc!=""){
                var files=$('#input_profile').prop('files');
                var file=files[0];
                form_data.append('myfile',file);
            }

            var ajax=new XMLHttpRequest();
            ajax.onload =function(){
                if(ajax.status==200 || ajax.readyState==4){
                    console.log(ajax.responseText);
                    window.location.reload();
                }else{
                    console.log('Error'); 
                     $('#loading').hide();
                }
            };
            ajax.open("post",`api/groups/create.php`,true);
            ajax.send(form_data);


        })
    </script>
</body>
</html>