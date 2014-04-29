<?php 

if($session->is_logged_in()) { 
	if($user->suspended == "1") { 
		redirect_to('logout.php?msg=suspended'); 
	} else if(MAINTENANCE_MODE == "ON" && !in_array(ADMIN_LEVEL, explode($user->user_level)) ){ 
		redirect_to('logout.php?msg=maintenance'); 
	}

	check_user_access($user->user_id);
 	
} else {
	$user = "";
}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo SITE_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESC; ?>">
    <meta name="author" content="MasDyn Studio - www.masdyn.com">
    <meta name="keywords" content="<?php echo SITE_KEYW; ?>">

    <!-- The styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="assets/js/google-code-prettify/prettify.css" rel="stylesheet">
	 <link href="assets/css/chosen.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/custom.js"></script>
  </head>

  <body>
		
    <div class="container">

		<div class="navbar">
			<div class="navbar-inner">
			  <div class="container">
			    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			    </a>
			    <div class="brand"><?php echo SITE_NAME; ?></div>
			    <div class="nav-collapse">
			      <ul class="nav">
					<li<?php echo ($current_page == "home") ? " class='active'" : "" ?>><a href="index.php">Home</a></li>
			        <li<?php echo ($current_page == "contact") ? " class='active'" : "" ?>><a href="contact.php">Contact Us</a></li>
					<?php if(isset($user->user_level)){ $level_array = explode(",", $user->user_level); if(in_array("293847", $level_array) || in_array("527387", $level_array)){ echo '<li><a href="'.ADMINDIR.'">Admin Area</a></li>'; } } ?>
			      </ul>
			      <ul class="nav pull-right">
					<?php if($session->is_logged_in()) { ?>
						<li class="dropdown">
				          <a href="" class="dropdown-toggle" data-toggle="dropdown"><?php echo $user->username; ?><b class="caret"></b></a>
				          <ul class="dropdown-menu">
								<li><a href="settings.php">Settings</a></li>
								<li class="divider"></li>
								<li><a href="logout.php">Sign Out</a></li>
				          </ul>
				        </li>
					<?php } else { ?>
						<!-- <li><a href="login.php">Login</a></li> -->
						<li><a href="login.php" id="login_link">Login</a></li>
			         <li class="divider-vertical"></li>
						<li><a href="register.php">Register</a></li>
					<?php } ?>
			      </ul>
			    </div><!-- /.nav-collapse -->
			  </div>
			</div><!-- /navbar-inner -->
		</div>

<!-- Header file end -->

<?php if(!$session->is_logged_in()){?>

<!-- Login Modal -->
<div id="login_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" onkeypress="if(event.keyCode == 13){login()}">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Sign In</h3>
  </div>
  <div class="modal-body">
	<div id="message"></div>
	<div class="row">
		<div class="span5 center">
			<div class="span5">
		        <input type="text" class="input-xlarge" id="username" required="require" placeholder="Username">
			</div>
			<div class="span5">
		        <input type="password" class="input-xlarge" id="password" placeholder="Password">
			</div>
			<div class="span5">
				<input type="checkbox" id="remember_me" />
				<span>Remember Me? (Uses Cookies)</span>
			</div>
			<div class="span5">
				<a href="reset_password.php">Forgot your Password?</a>
			</div>
		</div>
	</div>
	<?php if(OAUTH == "ON"){ ?>
		<hr />
		
	<div class="row">
		<div class="span5 center">
			<div class="span5">
				<a href="<?php echo WWW; ?>auth/facebook" class="zocial facebook">Sign in with Facebook</a>
				<a href="<?php echo WWW; ?>auth/twitter" class="zocial twitter">Sign in with Twitter</a>
			</div>
		</div>
	</div>
	<?php } ?>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary" id="login_btn" onclick="login()">Login</button>
  </div>
</div>
<?php } ?>

