<?php
require_once("includes/inc_files.php");

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

if($session->is_logged_in()) {
  redirect_to("index.php");
}

$current_page = "forgot_password";

if(empty($_GET['email']) || empty($_GET['hash'])) {
    $message = "";
    
} else {
	
  	$email = $database->escape_value(htmlspecialchars($_GET['email'], ENT_QUOTES));
	$hash = $database->escape_value(htmlspecialchars($_GET['hash'], ENT_QUOTES));
		
	Reset_Password::check_confirm_link($email, $hash);
}

if (isset($_POST['submit'])) {

  	$email = $database->escape_value(htmlspecialchars($_POST['email'], ENT_QUOTES));
	$hash = $database->escape_value(htmlspecialchars($_POST['hash'], ENT_QUOTES));
	
  	if (!empty($email) && !empty($hash)) {
		Reset_Password::check_confirm_link($email, $hash);

	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
  
} else { 
	$email = "";
	$hash = "";
}

if (isset($_POST['send_code'])) { 

	$email = $database->escape_value(htmlspecialchars($_POST['email'], ENT_QUOTES));
	
  	if (!empty($email)) {
		Reset_Password::set_confirm_email_link($email);

	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
  
} else {
	$email = "";
}

?>

<?php $page_title = "Reset your Password"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

	<?php echo output_message($message); ?>

	<?php if((!isset($_GET['email'])) && (!isset($_GET['hash'])) ) : ?>
	<div class="row">
		<form action="reset_password.php" method="post" >
			<div class="col-md-6 center">
				<div class="col-md-12">
			      <h3>Reset Password</h3>
				</div>
				<div class="col-md-12">
			      <input type="text" class="form-control" name="email" required="required" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>" />
				</div>
				<br />
				<div class="col-md-12">
			      <input type="text" class="form-control" name="hash" required="required" placeholder="Confirm Code" value="<?php echo htmlspecialchars($hash); ?>" />
				</div>
				<br />
				<div class="col-md-12">
					<input class="btn btn-primary" type="submit" name="submit" value="Reset Password" />
				</div>
			</div>
		</form>
		<form action="reset_password.php" method="post" >
			<div class="col-md-6 center">
				<div class="col-md-12">
			      <h3>Send Code</h3>
				</div>
				<div class="col-md-12">
			      <input type="text" class="form-control" name="email" required="required" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>" />
				</div>
				<br />
				<div class="col-md-12">
					<input class="btn btn-primary" type="submit" name="send_code" value="Send Confirm Code" />
				</div>
			</div>
		</form>
	</div>
	<div class="clear-fix"><!--  --></div>
	<?php endif ?>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>