<?php

$pay = $_GET['pay'];
if($pay==1) $page_title ="Pay";
else $page_title="Received";
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

$page=1;
if(isset($_GET['page'])) $page = $_GET['page'];

$u_id = $_GET['u_id'];


$order_req = Array(
    'user_id'=>$u_id,
    'payable'=>$pay,
    'page'=>$page

);
$Business = new Business();
$orders = $Business->getRemaindingVoucher($order_req);
$orders = $orders['orders'];

$User = new User();
$U = $User->getUserProfile($u_id);
$U = $U[0];

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
                    <div class="col-12">
                        <h2 class="st_title"><i class="uil uil-apps"></i> Transaction Remainder</h2>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <br><br> 
                         <img src="uploads/profiles/<?php echo $U['profile_image']; ?>" style="width:60px; height:60px; border-radius:50px; border: 3px solid #D79DA9"/>

                        <h3>
                            <?php echo $U['name'] ?>
                        </h3><br>

                        <h4>Phone - <?php echo $U['phone'] ?> </h4> <br>
                        <h4>Email - <?php echo $U['email'] ?> </h4> <br>
                        <h4>Address - <?php echo $U['address'] ?> </h4>

                     
                    </div>

					
                    <div class="col-lg- col-md-8">
                       <div>
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
                                            <?php if($orders){ foreach($orders as $order){
                                                $status = "";
                                                if($order['seen']==1 && $order['is_sold_out']==1 && $order['is_received']==1 ){
                                                    $status = "Completed";
                                                }else if($order['seen']==1 && $order['is_sold_out']==1){
                                                    $status = "Delivered by leader";
                                                }else if($order['seen']==1 && $order['is_sold_out']==0){
                                                    $status ="Seen by leader";
                                                }else{
                                                    $staus = "Sent to leader";
                                                }
                                                ?>
                                                <tr onclick="detail(<?php echo $order['voucher_id']; ?>)">
                                                        
                                                    <td class="text-center">
                                                        <img src="uploads/groups/<?php echo $order['group_image']; ?>" style="width:35px; height:35px; border-radius:50px; border: 3px solid #D79DA9"/>
                                                    </td>
                                                    <td class="text-center"><?php echo $order['group_name']; ?></td>
                                                    <td class="text-center"><?php echo $order['voucher_id']; ?></td>
                                                    <td class="text-center"><b class="course_active"> <?php echo $status ?> </b></td>
                                                    <td class="text-center"><?php echo $order['total_amount']; ?></td>
                                                    <td class="text-center"><?php echo date('d M , Y', $order['voucher_id']) ?></td>
                                                    
                                                </tr>
                                            <?php }} else{?>
                                                <tr>
                                                    <td colspan="6" style="text-align:center">
                                                        No records Here!
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php if($orders){ ?>
                                <div style="display:flex;">
                                    <?php if($page>1){ ?> 
                                    <a href="?pay=<?php echo $pay ?>&page=<?php echo $page-1 ?>&u_id=<?php echo $u_id ?>" class="pagination menu--link"><< Previous</a>
                                    <?php }?>
                                    <span class="pagination menu--link"><?php echo $page ?></span>
                                    <?php if($orders){ ?>
                                        <a href="?pay=<?php echo $pay ?>&page=<?php echo $page+1 ?>&u_id=<?php echo $u_id ?>"  class="pagination menu--link">Next >></a>
                                    <?php }?>
                                </div>
                            <?php }?>
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
        let url;
        if(pay==1){
            url = "order_detail.php?voucher_id=";
        }else{
            url = "incoming_order_detail.php?voucher_id=";
        }

        function detail(voucher_id){
            window.location.href =url+voucher_id;
        }
    </script>

</body>
</html>