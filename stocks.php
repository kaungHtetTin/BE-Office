<?php
$page_title="Stocks";
session_start();
include_once('classes/connect.php');
include_once('classes/auth.php');
include_once('classes/stock.php');

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
    die;
}

$Stock = new Stock();
if($_SERVER['REQUEST_METHOD']=="POST"){
    $name=$_POST['name'];
    $request =Array(
        'value'=>$name,
        'user_id'=>$user['user_id']
    );
    $stockAdding = $Stock->addNewStock($request);

}
$stocks = $Stock->getStocks(Array('owner_id'=>$user['user_id']));

?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('layouts/head.php'); ?>
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
						<h2 class="st_title"><i class="uil uil-apps"></i>Stocks</h2>
					</div>

                    <div class="col-lg-8">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-my-courses" role="tabpanel">
                                <div class="table-responsive mt-30">
                                    <table class="table ucp-table">
                                        <thead class="thead-s">
                                            <tr>
                                                <th width="20%" class="text-center" scope="col">No.</th>
                                                <th width="20%" class="text-center" scope="col"></th>
                                                <th scope="col">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($stocks as $key =>$stock){ ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $key+1 ?></td>
                                                    <td></td>
                                                    <td ><?php echo $stock['name']?></td>
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
                        <h4>Add new stock</h4>
                        <br>
                        <form action="" method="POST">
                            <div class="basic_form">
                                <div class="ui search focus mt-10">
                                    <div class="ui left input swdh11 swdh19">
                                        <input class="prompt srch_explore" type="text" name="name" id="id_academy" required="" maxlength="64" placeholder="Add Name">															
                                    </div>
                                </div>
                                
                                <br>
                                <button type="submit" style="padding-left:20px;padding-right:20px;cursor:pointer;" class="btn_adcart" title="Create New Course">Create</button><br><br>
                                <p>After adding a stock, it cannot be removed. <b>So be careful in adding your stock.</b></p><br>
                            </div>
                        </form>
                    </div>

                </div>
			</div>
		</div>
		<?php include('layouts/footer.php') ?>
	</div>
	<!-- Body End -->
</body>
</html>