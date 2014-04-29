<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("includes/inc_files.php");

if($session->is_logged_in()) {
  redirect_to("index.php");
}

if(!empty($_GET['email']) && !empty($_GET['hash'])) {
    $message = "";
	$email = trim($_GET['email']);
	$hash = trim($_GET['hash']);
	
	global $database;
	
	// Escape email and hash values to help prevent sql injection.
	$email = $database->escape_value($email);
	$hash = $database->escape_value($hash);
	
	// Check if the provided information is in the database
	Activation::check_activation($email, $hash);
}

if(isset($_POST['submit'])) { // Form has been submitted.
	$email = trim($_POST['email']);
	$hash = trim($_POST['hash']);
	
	if ((!$email == "") && (!$hash == "")) {
		Activation::check_activation($email, $hash);
	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
} else { // Form has not been submitted.
	$email = "";
	$hash = "";
}

if(isset($_POST['resend_code'])) { // Form has been submitted.
	$email = trim($_POST['email']);
	
	if (!$email == "") {
		Activation::check_resend_code($email);
	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
} else { // Form has not been submitted.
	$email = "";
	$hash = "";
}

$current_page = "activate";

?>
<?php $page_title = "Activate your Account"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

	<?php echo output_message($message); ?>

	<?php if((!isset($_GET['email'])) && (!isset($_GET['hash'])) ) : ?>
	<div class="row">
		<form action="activate.php" method="post" >
			<div class="col-md-6 center">
				<div class="col-md-12">
			      <h3>Activate Account</h3>
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
					<input class="btn btn-primary" type="submit" name="submit" value="Activate" />
				</div>
			</div>
		</form>
		<form action="activate.php" method="post" >
			<div class="col-md-6 center">
				<div class="col-md-12">
			      <h3>Resend Activation Code</h3>
				</div>
				<div class="col-md-12">
			      <input type="text" class="form-control" name="email" required="required" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>" />
				</div>
				<br />
				<div class="col-md-12">
					<input class="btn btn-primary" type="submit" name="resend_code" value="Resend Code" />
				</div>
			</div>
		</form>
	</div>
	<?php endif ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>