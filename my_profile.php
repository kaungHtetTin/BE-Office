<?php

$page_title= "My Profile";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/business.php');
include_once('classes/user.php');


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

function getUpdateUrl($hint, $message, $key){
    return "update.php?hint=$hint&message=$message&key=$key&link=1";
}




?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            .center-cropped {
                background-position: center center;
                background-repeat: no-repeat;
            }

           
            .edit{
                font-size:12px;
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
                    <div class="col-12">
                        <h2 class="st_title"><i class="uil uil-apps"></i> Profile Setting</h2>
                    </div>
                    <div class="col-lg-12" style="padding-left:50px;">
                        <br><br> 
                        <div>
                            <a href="javascript:void(0)" class="card" title="Account" style="width:120px;height: 120px;cursor:pointer;border-radius:50%;padding:3px;">
                                <img src="uploads/profiles/<?php echo $user['profile_image']; ?>" style="width: 114px;height: 114px; border-radius:50%;" alt="" id="img_profile">
                                <div style="width: 114px;height: 114px; border-radius:50%;display:none" alt="" id="image_loading">
                                    <br><br><br>
                                    <div class="spinner" id="loading">
                                        <div class="bounce1"></div>
                                        <div class="bounce2"></div>
                                        <div class="bounce3"></div>
                                    </div>	
                                </div>
                            </a>
                            
                            <i id="pick_profile" class='uil uil-camera' style="font-size:30px;position:absolute;top:120px;left:180px;cursor:pointer;"></i>
                            <i id="profile_save" class='uil uil-check' style="font-size:30px;position:absolute;top:120px;left:180px;cursor:pointer;background:#D79DA9;color:white;border-radius:50px;display:none"></i>
                            
                            <form enctype="multipart/form-data">
                                <input type="file" name="myfile" id="input_profile"  style="display:none" accept="image/*" />
                            </form>
                           
                        </div>

                        <br><br>

                        <h3>
                            <?php echo $user['name'] ?>
                        </h3><br>


                        <h4>Email - <?php echo $user['email'] ?>  </h4> <br>
                        <h4>Name - <?php echo $user['name'] ?> <a class="edit" href="<?php echo getUpdateUrl('Update your name','We recommend to enter your name in English','name') ?>"> ( Edit )</a> </h4> <br>
                        <h4>Address - <?php echo $user['address'] ?> <a class="edit" href='<?php echo getUpdateUrl("Update your address","Add your address so that your partner can contact you easily.We recommend to type your address in English.","address") ?>'> ( Edit )</a> </h4><br>
                        <h4>Phone - <?php echo $user['phone'] ?> <a class="edit" href='<?php echo getUpdateUrl("Update your phone number","Add your phone number so that your partner can contact you easily.","phone") ?>'> ( Edit )</a> </h4> <br>

                     
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

            form_data.append('user_id',user_id);
			form_data.append('auth_token',auth_token);

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
            ajax.onload =function(){
                if(ajax.status==200 || ajax.readyState==4){
                    console.log(ajax.responseText);
                    window.location.href="";
                }else{
                    console.log('Error');
                    $('#profile_uploading').hide();
                }
            };
            ajax.open("post",`api/updateprofileimage.php`,true);
            ajax.send(form_data);

        })

    </script>

</body>
</html>