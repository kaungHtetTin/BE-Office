<?php

$page_title="Remainders";

session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/business.php');

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

$page =1;
if(isset($_GET['page'])) $page = $_GET['page'];

$pay = 0;
if(isset($_GET['pay'])) $pay = $_GET['pay'];


$Business = new Business();
if($pay==1){
    $logs =$Business->getPayable(Array('user_id'=>$user['user_id']));
}else{
    $logs =$Business->getReceivable(Array('user_id'=>$user['user_id']));
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

            .pagination{
                padding:10px;
                border:1px solid #D79DA9;
                border-radius:50px;
                width:100px;
                margin:10px;
                text-align:center;
            }

            .p_active{
                background:#D79DA9;
                color:white;
            }

            tr{
                cursor: pointer;
            }
            tr:hover{
                background:#F6EAEC;
                color:#D79DA9;
            }
            
            .card_active {
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
						<h2 class="st_title"><i class="uil uil-apps"></i> <?php echo $page_title ?></h2>
					</div>

                    <div class="col-lg-4 col-md-4">
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-6 col-sm-6 col-xs-6">
                                <div class="card" id="btn_received">
                                    <a class="menu--link">
                                        <div style="text-align:center;padding:10px;">
                                            Receivable
                                        </div>
                                    </a>
                                </div>
                                <br>
                            </div>

                            <div class="col-lg-12 col-md-12 col-6 col-sm-6 col-xs-6">
                                
                                <div class="card" id="btn_pay">
                                    <a  class="menu--link">
                                        <div style="text-align:center;padding:10px;">
                                            Remainder Pay For
                                        </div>
                                    </a>
                                </div>
                            </div>

                        </div>
                    

                    </div>
					
                    <div class="col-lg-8 col-md-8">
                        
                        <div class="table-responsive mt-30">
                            <table class="table ucp-table">
                                <thead class="thead-s">
                                    <tr>
                                        <th class="text-center" scope="col"></th>
                                        <th class="text-center" scope="col"></th>    
                                        <th class="text-center" scope="col">Amount</th>
                                      
                                        
                                    </tr>
                                </thead>
                                <tbody id="sent_container">
                                    <?php if($logs){ 
                                        $total_amount = 0;
                                        ?>
                                        <?php foreach($logs as $log){ 
                                            $total_amount+=$log['total_amount'];
                                            ?>
                                            <tr onclick="detail(<?php echo $log['user_id']; ?>)">
                                                <td class="text-center">
                                                    <img src="uploads/profiles/<?php echo $log['profile_image']; ?>" style="width:35px; height:35px; border-radius:50px; border: 3px solid #D79DA9"/>
                                                </td>
                                                <td class="text-center"><?php echo $log['name']; ?></td>
                    
                                                <td style="<?php if($pay==1) echo'color:red' ?>" class="text-center"><?php echo $log['total_amount'] ?>  </td>
                                                
                                            </tr>
                                        <?php } ?>

                                        <tr style="background:<?php echo $pay==1? 'red':'#3AC602;' ?>; color:white;font-weight:bold;">
                                            <td colspan="2" class="text-center">Total</td>
                                            <td id="total_quantity" class="text-center"><?php echo $total_amount ?></td>
                                        
                                        </tr>


                                    <?php }else { ?>
                                        <tr>
                                            <td colspan="3" style="text-align:center">
                                                No records Here!
                                                <br><br><br><br><br><br>
                                            </td>
                                        </tr>

                                        
                                    <?php } ?>


                                </tbody>
                            </table>
                        </div>

                       
                    </div>


                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->

     <script>
        let pay = "<?php echo $pay ?>";

        function detail(u_id){
            window.location.href ="transaction_remainder_details.php?pay="+pay+"&u_id="+u_id;
        }

        
        if(pay==1){
            $('#btn_received').css({'background':'','color':''});
            $('#btn_pay').css({'background':'#F6EAEC','color':'#D79DA9'});
        }else{
            $('#btn_received').css({'background':'#F6EAEC','color':'#D79DA9'});
            $('#btn_pay').css({'background':'','color':''});
        }
          
        $(document).ready(()=>{
            $('#btn_received').click(()=>{
                $('#btn_received').css({'background':'#F6EAEC','color':'#D79DA9'});
                $('#btn_pay').css({'background':'','color':''});
                window.location.href ="transaction_remainder.php?pay=0";
            })

            $('#btn_pay').click(()=>{
                $('#btn_received').css({'background':'','color':''});
                $('#btn_pay').css({'background':'#F6EAEC','color':'#D79DA9'});
                window.location.href ="transaction_remainder.php?pay=1";
            })
        })

    </script>
    
</body>
</html>