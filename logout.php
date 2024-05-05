<?php

session_start();
if(isset($_SESSION['beoffice_userid'])){
	 
	unset($_SESSION['beoffice_userid']);
    unset($_SESSION['beoffice_auth_token']);
}


header("Location: login.php");
die;