<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("includes/inc_files.php");

if($session->is_logged_in()) {$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);}

$location = "contact.php";

$current_page = "contact";

$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
$form_names = $csrf->form_names(array('name', 'email', 'subject', 'mess'), false);

// if(isset($_POST['submit'])) { 
if(isset($_POST[$form_names['name']], $_POST[$form_names['email']], $_POST[$form_names['subject']], $_POST[$form_names['mess']])){

	if($csrf->check_valid('post')){

		$name = trim(htmlspecialchars($_POST[$form_names['name']]));
		$email = trim(htmlspecialchars($_POST[$form_names['email']]));
		$subject = trim(htmlspecialchars($_POST[$form_names['subject']]));
		$mess = trim(htmlspecialchars($_POST[$form_names['mess']]));
		
		if ($name == '' && $email == '' && $subject == '' && $mess == ''){
			// $message = "Success";
			
			$headers = "From: {$email}\r\n".
			"Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			$current_ip = $_SERVER['REMOTE_ADDR'];
			
			$html_message = nl2br($mess);
			
			$sub = "CONTACT FORM: ".$subject;
			
			//send email
			$to = SITE_EMAIL;
			$the_mess = "IP: ".$current_ip." <br />
					FROM: ".$email."<br />
					MESSAGE: <p />"."$html_message";
						
			mail($to, $sub, $the_mess, $headers);
		
			$message = "<div class='notification-box success-notification-box'><p>Thank you, your message has been sent successfully.</p><a href='#' class='notification-close success-notification-close'>x</a></div><!--.notification-box .notification-box-success end-->";		
			
		} else {
			$message = "<div class='notification-box error-notification-box'><p>Please complete all required fields.</p><a href='#' class='notification-close error-notification-close'>x</a></div><!--.notification-box .notification-box-error end-->";
		}

	}

	$form_names = $csrf->form_names(array('name', 'email', 'subject', 'mess'), false);
  
} else {
	if(isset($user)){
	    $name = $user->first_name." ".$user->last_name;
	    $email = $user->email;
	} else {
	    $name = "";
	    $email = "";
	}
	$subject = "";
	$mess = "";
	$message = "";
}

?>

<?php $page_title = "Contact Us"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>


	<?php echo output_message($message); ?>

	<form action="<?php echo $location; ?>" method="post" class="form-horizontal">

	<input type="hidden" name="<?php echo $token_id; ?>" value="<?php echo $token_value; ?>" />
	
	<div class="row">
		<div class="col-md-4">
			<input type="text" name="<?php echo $form_names['name']; ?>" class="form-control" required="required" placeholder="Full Name" value="<?php echo htmlspecialchars($name); ?>" />
		</div>
		<div class="col-md-4">
			<input type="email" name="<?php echo $form_names['email']; ?>" class="form-control" required="required" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" />
		</div>
		<div class="col-md-4">
			<input type="text" name="<?php echo $form_names['subject']; ?>" class="form-control" required="required" placeholder="Subject" value="<?php echo htmlspecialchars($subject); ?>" />
		</div>
	</div>
	<br />
	<div class="row">
		<div class="col-md-12">
			<textarea type="text" class="form-control" style="height:111px;" name="<?php echo $form_names['mess']; ?>" placeholder="Your Message" required="required"><?php echo htmlspecialchars($mess); ?></textarea>
		</div>
	</div>

	<div class="form-actions" style="text-align: center;margin-top: 15px;">
		<input class="btn btn-primary" type="submit" name="submit" value="Send Message" />
	</div>

	</form>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>