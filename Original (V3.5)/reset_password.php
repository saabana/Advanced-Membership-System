<?php
require_once("includes/inc_files.php");

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

if($session->is_logged_in()) {
  redirect_to("index.php");
}

$current_page = "forgot_password";

if((empty($_GET['email'])) || (empty($_GET['hash']))) {
    $message = "";
    
  } else {
	$email = trim($_GET['email']);
	$hash = trim($_GET['hash']);
	
	global $database;
	
	// Escape email and hash values to help prevent sql injection.
	$email = $database->escape_value($email);
	$hash = $database->escape_value($hash);
	
	// Check if the provided information is in the database
	Reset_Password::check_confirm_link($email, $hash);
}

if (isset($_POST['submit'])) { // Form has been submitted.

	$email = trim($_POST['email']);
	$hash = trim($_POST['hash']);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
	  	if ((!empty($email)) && (!empty($hash))) {
			Reset_Password::check_confirm_link($email, $hash);

		} else {
			$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
		}
	}
  
} else { // Form has not been submitted.
	$email = "";
	$hash = "";
}

if (isset($_POST['send_code'])) { // Form has been submitted.

	$email = trim($_POST['email']);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
	  	if (!empty($email)) {
			Reset_Password::set_confirm_email_link($email);

		} else {
			$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
		}
	}
  
} else { // Form has not been submitted.
	$email = "";
}

// if (isset($_POST['resend_code'])) { // Form has been submitted.
// 
// 	$email = trim($_POST['email']);
// 	
// 	if (DEMO_MODE == 'ON') {
// 		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
// 	} else {
// 	  	if (!empty($email)) {
// 			Reset_Password::check_resend_code($email);
// 
// 		} else {
// 			$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
// 		}
// 	}
//   
// } else { // Form has not been submitted.
// 	$email = "";
// 	$hash = "";
// }

?>

<?php $page_title = "Reset your Password"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

	<?php echo output_message($message); ?>

	<?php if((!isset($_GET['email'])) && (!isset($_GET['hash'])) ) : ?>
	<div class="row-fluid">
		<form action="reset_password.php" method="post" >
			<div class="span6 center">
				<div class="span12">
			      <h3>Reset Password</h3>
				</div>
				<div class="span12">
			      <input type="text" class="span7" name="email" required="required" placeholder="Email Address" value="<?php echo htmlentities($email); ?>" />
				</div>
				<div class="span12">
			      <input type="text" class="span7" name="hash" required="required" placeholder="Confirm Code" value="<?php echo htmlentities($hash); ?>" />
				</div>
				<div class="span12">
					<input class="btn btn-primary" type="submit" name="submit" value="Reset Password" />
				</div>
			</div>
		</form>
		<form action="reset_password.php" method="post" >
			<div class="span6 center">
				<div class="span12">
			      <h3>Send Code</h3>
				</div>
				<div class="span12">
			      <input type="text" class="span7" name="email" required="required" placeholder="Email Address" value="<?php echo htmlentities($email); ?>" />
				</div>
				<div class="span12">
					<input class="btn btn-primary" type="submit" name="send_code" value="Send Confirm Code" />
				</div>
			</div>
		</form>
	</div>
	<div class="clear-fix"><!--  --></div>
	<?php endif ?>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>