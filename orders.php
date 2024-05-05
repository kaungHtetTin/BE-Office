<?php
$received = $_GET['received'];
if($received==1) $page_title ="Received";
else $page_title="Sales Request";
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

$page=1;

if(isset($_GET['page'])) $page = $_GET['page'];

$order_req = Array(
    'page'=>$page,
    'is_received'=>$received,
    'agent_id'=>$user['user_id']

);
$Business = new Business();
$orders = $Business->getMyOrders($order_req);
$orders = $orders['orders'];
 
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
                        <h2 class="st_title"><i class="uil uil-apps"></i> 
                        <?php echo $page_title;
                        if($received==0){?>
                              <a style="font-size:14px;" href="create_order.php">Request new</a>
                        <?php }?></h2>
                    </div>
                    <div class="col-lg-2 col-md-4">

                        <br>
                        <br>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-4 col-sm-4 col-xs-4">
                                <div class="card">
                                    <a href="orders.php?received=0" class="menu--link <?php if($received==0) echo "card_active" ?>">
                                        <div style="text-align:center;padding:10px;">
                                            <img style="width:40px;height:40px;" src="icon/orders.png" alt=""><br>
                                            Sales Request
                                        </div>
                                    </a>
                                </div>
                            </div>
                       
                            <div class="col-lg-12 col-md-12 col-4  col-sm-4 col-xs-4">
                                <div class="card">
                                    <a href="orders.php?received=1" class="menu--link  <?php if($received==1) echo "card_active" ?>">
                                        <div class="" style="text-align:center;padding:10px;">
                                            <img style="width:40px;height:40px;" src="icon/sents.png" alt=""><br>
                                            Received
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <br>

                        <h4>Distribution</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-4 col-sm-4 col-xs-4">
                                <div class="card">
                                    <a href="my_business.php?is_sold_out=0" class="menu--link">
                                        <div style="text-align:center;padding:10px;">
                                            <img style="width:40px;height:40px;" src="icon/orders.png" alt=""><br>
                                            Orders
                                        </div>
                                    </a>
                                </div>
                            </div>
                       
                            <div class="col-lg-12 col-md-12 col-4  col-sm-4 col-xs-4">
                                <div class="card">
                                    <a href="my_business.php?is_sold_out=1" class="menu--link">
                                        <div class="" style="text-align:center;padding:10px;">
                                            <img style="width:40px;height:40px;" src="icon/sents.png" alt=""><br>
                                            Delivered to
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-4  col-sm-4 col-xs-4">
                                <div class="card">
                                    <a href="vouchers.php" class="menu--link">
                                        <div style="text-align:center;padding:10px;">
                                            <img style="width:40px;height:40px;" src="icon/voucherlist.png" alt=""><br>
                                            Voucher
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <br>

                   
                    </div>

					
                    <div class="col-lg-10 col-md-8">
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
                                    <a href="?received=<?php echo $received ?>&page=<?php echo $page-1 ?>" class="pagination menu--link"><< Previous</a>
                                    <?php }?>
                                    <span class="pagination menu--link"><?php echo $page ?></span>
                                    <?php if($orders){ ?>
                                        <a href="?received=<?php echo $received ?>&page=<?php echo $page+1 ?>" class="pagination menu--link">Next >></a>
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
        function detail(voucher_id){
            window.location.href ="order_detail.php?voucher_id="+voucher_id;
        }
    </script>

</body>
</html>