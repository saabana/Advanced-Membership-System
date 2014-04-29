<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("includes/inc_files.php");

if($session->is_logged_in()) {
  redirect_to("index.php");
}

// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) { // Form has been submitted.
	
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	if (!$username == '' && !$password == '') {
		$current_ip = $_SERVER['REMOTE_ADDR'];
		$remember_me = trim($_POST['remember_me']);
		$return = User::check_login($username, $password, $current_ip, $remember_me);
		if($return == "false"){
			redirect_to("login.php");
		} else {
			redirect_to($return);
		}
		
	} else {
		$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please fill in all required fields.</div>";
	}
  
} else { // Form has not been submitted.
	$username = "";
	$password = "";
}

if(isset($_SESSION['oauth_message'])){
	$message = $_SESSION['oauth_message'];
	unset($_SESSION['oauth_message']);
}

$current_page = "login";

?>
<?php $page_title = "Login to your Account"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

	<?php echo output_message($message); ?>

	<form action="login.php" method="post" class="center">
	<div class="row-fluid">
		<div class="span12">
	        <input type="text" class="span4" name="username" required="required" placeholder="Username" value="<?php echo htmlentities($username); ?>" />
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
	        <input type="password" class="span4" name="password" required="required" placeholder="Password" value="<?php echo htmlentities($password); ?>" />
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<input type="checkbox" name="remember_me" value="yes" />
			<span>Remember Me? (Uses Cookies)</span>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<a href="reset_password.php">Forgot your Password?</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<input class="btn btn-primary" type="submit" name="submit" value="Login" />
		</div>
	</div>
	
	</form>

	<?php if(OAUTH == "ON"){ ?>
	<hr />
		
	<div class="row-fluid">
		<div class="span12 center">
			<div class="span12">
				<?php if(FACEBOOK_APP_ID != ""){ ?><a href="<?php echo WWW; ?>auth/facebook" class="zocial facebook">Sign in with Facebook</a><?php } ?>
				<?php if(TWITTER_CONSUMER_KEY != ""){ ?><a href="<?php echo WWW; ?>auth/twitter" class="zocial twitter">Sign in with Twitter</a><?php } ?>
				<?php if(GOOGLE_CLIENT_ID != ""){ ?><a href="<?php echo WWW; ?>auth/google" class="zocial google">Sign in with Google</a><?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>
