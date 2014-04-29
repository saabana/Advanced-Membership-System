<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("../includes/inc_files.php");

if(isset($_SESSION['admin_access'])){
  redirect_to("index.php");
}

// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) { // Form has been submitted.
	
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	if(!$username == '' && !$password == '') {
		$return = User::check_admin_login($username, $password);
		if($return == "false"){
			redirect_to("login.php");
		} else {
			redirect_to("index.php");
		}
		
	} else {
		$message = "<div class='alert alert-error'>Please fill in all required fields.</div>";
	}
  
} else { // Form has not been submitted.
  $username = "";
  $password = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Lewis @ Masdyn Studio - masdyn.com - dewsbury.co">
	
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

	<div class="title">
		<h1>Admin Login</h1>
	</div>

	<?php echo output_message($message); ?>

	<form action="login.php" method="post" class="center">
	<div class="row-fluid">
		<div class="span12">
	        <input type="text" class="span12" name="username" placeholder="Username" value="<?php echo htmlentities($username); ?>" />
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
	        <input type="password" class="span12" name="password" placeholder="Password" value="<?php echo htmlentities($password); ?>" />
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<input class="btn btn-primary" type="submit" name="submit" value="Login" />
		</div>
	</div>
	
	</form>

	<!-- Footer Start -->		
			
		</div><!-- content -->
	</div><!-- container -->
	
</body>
</html>