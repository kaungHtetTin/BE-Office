<?php
$page_title="My Customers";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/sale.php');

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

$Sale = new Sale();
$customers = $Sale->getCustomers(Array('user_id'=>$user['user_id'],'page'=>$page));

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
						<h2 class="st_title"><i class="uil uil-apps"></i>My Customers</h2>
					</div>
                    <div class="col-lg-12">            
                     

                     </div>
                     <div class="col-lg-12">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                <th class="text-center" scope="col">Name</th>
                                                <th class="text-center" scope="col">Phone</th>
                                                <th class="text-center" scope="col">Address</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php  if($customers){ foreach($customers as $customer){ ?>
                                                <tr onclick="detail(<?php echo $customer['customer_phone']; ?>)">
                                                    <td class="text-center"><?php echo $customer['customer_name'] ?></td>
                                                    <td class="text-center"><?php echo $customer['customer_phone'] ?></td>
                                                    <td class="text-center"><?php echo $customer['customer_address'] ?></td>
                                                    
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

                            <?php if($customers){ ?>
                                <div style="display:flex;">
                                    <?php if($page>1){ ?> 
                                    <a href="?page=<?php echo $page-1 ?>" class="pagination menu--link"><< Previous</a>
                                    <?php }?>
                                    <span class="pagination menu--link"><?php echo $page ?></span>
                                    <?php if($customers){ ?>
                                        <a href="?page=<?php echo $page+1 ?>" class="pagination menu--link">Next >></a>
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
    <script>
        function detail(phone){
            window.location.href ="customer_details.php?phone="+phone;
        }
    </script>
	<!-- Body End -->
</body>
</html>