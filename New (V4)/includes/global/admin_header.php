<?php 

if($session->is_logged_in()){
	if($admin->suspended == "1") { 
		redirect_to('logout.php?msg=suspended'); 
	} 
	check_user_access($admin->user_id);
	if(!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true){
		redirect_to(WWW.ADMINDIR.'signin.php'); 
	}
} else {
	$admin = "";
}

?>