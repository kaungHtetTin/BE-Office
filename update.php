<?php

$page_title= "Update";
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

$valid = true;
if(isset($_GET['link'])){
    $link = $_GET['link'];
    if($link==1) $url = "api/updateprofiledata.php"; //update profile data
    if($link==2) $url = "api/businesses/updateextracost.php";  // update my extra cost on a voucher
    if($link==3) $url = "api/groups/update.php"; // update group data
    if($link==4) $url = "api/targetplan/updateitemquantity.php"; // update target plan item count

} else {
    $valid = false;
}

if(isset($_GET['hint'])) $hint = $_GET['hint'];
else $valid = false;

if(isset($_GET['message'])) $message = $_GET['message'];
else $valid = false;

if(isset($_GET['key'])) $key = $_GET['key'];
else $valid = false;


if(isset($_GET['content_id'])){
    $content_id = $_GET['content_id'];
}else{
    $content_id = "";
}

if(isset($_GET['extra1'])){
    $extra1 = $_GET['extra1'];
}else{
    $extra1 = "";
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
                        <h2 class="st_title"><i class="uil uil-apps"></i> Update</h2>
                    </div>

                    <?php if($valid){ ?>

                        <div class="col-lg-8" style="padding-left:50px;">
                            
                            <div class="ui search focus mt-10">
                                <div class="ui left input swdh11 swdh19">
                                    <input class="prompt srch_explore" type="text"id="input_box" required="" maxlength="64" placeholder="<?php echo $hint ?>">	<br>
                                                                                        
                                </div>
                                <p class="err_msg" id="err_msg" style="color:red;"></p>

                                <br><br>
                                <p id="msg" ><?php echo $message ?></p>
                            </div>
                            <br><br>
                            <button id="btn_update" style="padding-left:20px;padding-right:20px;" class="btn_adcart" title="Create New Group">Update</button>
                        </div>
                    <?php } else {?>
                        <br><br>
                        <h3>Invalid Update</h3>
                        <br><br><br><br><br><br>
                    <?php }?>

                  

                </div>
			</div>
		</div>
          <br><br>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->

    <script>
        let user_id=<?php echo $user['user_id'] ?>;
        let auth_token ="<?php echo $user['auth_token'] ?>";
        let key = "<?php echo $key ?>";
        let content_id ="<?php echo $content_id  ?>";
        let extra1 = "<?php echo $extra1 ?>";
        let value;

        let url = "<?php echo $url ?>";

        $(document).ready(()=>{
           


            $('#btn_update').click(()=>{


                $('#err_msg').hide();

                if($('#input_box').val()==""){
                    $('#err_msg').show();
                    $('#err_msg').html('Please enter the value');
                    return;
                }

                let req = {};
                req.user_id = user_id;
                req.auth_token = auth_token;
                req.key = key;
                req.content_id = content_id;
                req.extra1 = extra1;
                req.value =  $('#input_box').val();

                console.log(req);

                $.post(url, req, function(result){
                    console.log(result);
                    history.back();
                });

            })
        });




    </script>

</body>
</html>