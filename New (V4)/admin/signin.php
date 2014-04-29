<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("../includes/inc_files.php");

if(isset($_SESSION['admin_access'])){
  redirect_to("index.php");
}

if (isset($_POST['submit'])) {
	
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	if(!$username == '' && !$password == '') {
		$return = User::check_admin_login($username, $password);
		if($return == "false"){
			redirect_to("signin.php");
		} else {
			redirect_to("index.php");
		}
		
	} else {
		$message = "<div class='alert alert-danger'>Please complete all required fields.</div>";
	}
  
} else { 
  $username = "";
  $password = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Lewis @ MASDYN Development Studio - masdyn.com - dewsbury.co">
	
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/main.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/global/css/chosen.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script>var WWW = "<?php echo WWW ?>";</script>
	<script src="<?php echo WWW ?>includes/global/js/main.js"></script>
	<script>
	$(document).ready(function() {
		$(function() {
			$('.dropdown-toggle').dropdown();
			$('.dropdown, .dropdown input, .dropdown label').click(function(e) {
				e.stopPropagation();
			});
		});
	});
	$(function(){
		$("[rel='tooltip']").tooltip();
	});
	</script>
	
	
</head>

<body>
	
	<div class="container" style="width: 500px;">
		<div id="content" class="settings">
			
	<!-- Header End -->

	<form action="signin.php" method="post" class="form-signin">

		<h2 class="form-signin-heading">Admin Login</h2>

		<?php echo output_message($message); ?>

		<input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php echo htmlentities($username); ?>">
		<input type="password" class="form-control" name="password" id="password" placeholder="Password" value="<?php echo htmlentities($password); ?>">

		<input class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Sign In" />
	
	</form>

	<!-- Footer Start -->		
			
		</div><!-- content -->
	</div><!-- container -->
	
</body>
</html>