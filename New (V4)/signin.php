<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("includes/inc_files.php");

if($session->is_logged_in()) {
  redirect_to("index.php");
}

$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
$form_names = $csrf->form_names(array('username', 'password', 'remember_me'), false);

// Remember to give your form's submit tag a name="submit" attribute!
if(isset($_POST[$form_names['username']], $_POST[$form_names['password']])){
	
	if($csrf->check_valid('post')){

		$username = trim($_POST[$form_names['username']]);
		$password = trim($_POST[$form_names['password']]);

		if ($username != '' && $password != '') {
			$current_ip = $_SERVER['REMOTE_ADDR'];
			if(isset($_POST[$form_names['remember_me']])){
				$remember_me = trim($_POST[$form_names['remember_me']]);
			} else {
				$remember_me = "off";
			}
			$return = User::check_login($username, $password, $current_ip, $remember_me);
			if($return == "false"){
				redirect_to("signin.php");
			} else {
				redirect_to($return);
			}
			
		} else {
			$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please fill in all required fields.</div>";
		}

	}

	$form_names = $csrf->form_names(array('username', 'password', 'remember_me'), false);
  
} else { // Form has not been submitted.
	$username = "";
	$password = "";
}

if(isset($_SESSION['oauth_message'])){
	$message = $_SESSION['oauth_message'];
	unset($_SESSION['oauth_message']);
}

$current_page = "signin";

?>
<?php $page_title = "Sign In to your Account"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<form action="signin.php" method="post" class="form-signin">
	<h2 class="form-signin-heading">Please sign in</h2>

	<?php echo output_message($message); ?>

	<input type="hidden" name="<?php echo $token_id; ?>" value="<?php echo $token_value; ?>" />
	<input type="text" class="form-control" name="<?php echo $form_names['username']; ?>" placeholder="Username" autofocus value="<?php echo htmlspecialchars($username); ?>">
	<input type="password" class="form-control" name="<?php echo $form_names['password']; ?>" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>">
	<label class="checkbox">
		<input type="checkbox" name="<?php echo $form_names['remember_me']; ?>"> Remember me
	</label>
	<button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" >Sign in</button>

	<br />

	<a href="reset_password.php" style="float:right">Forgot Password?</a>

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
</form>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>
