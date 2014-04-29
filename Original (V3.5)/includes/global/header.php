<?php 

if($session->is_logged_in()){
	if($user->suspended == "1") { 
		redirect_to('logout.php?msg=suspended'); 
	} 
	
	check_user_access($user->user_id);
 	
} else {
	$user = "";
}

?>