<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("includes/inc_files.php");

if($session->is_logged_in()) {$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);}

$location = "contact.php";

$current_page = "contact";

if (isset($_POST['submit'])) { 
		
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$subject = trim($_POST['subject']);
	$mess = trim($_POST['mess']);
	
	if ((!$name == '') && (!$email == '') && (!$subject == '') && (!$mess == '')) {
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

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

	<?php echo output_message($message); ?>

	<form action="<?php echo $location; ?>" method="post" class="form-horizontal">
	
	<div class="row-fluid">
		<div class="span4">
			<input type="text" name="name" class="span12" required="required" placeholder="Full Name" value="<?php echo htmlentities($name); ?>" />
		</div>
		<div class="span4">
			<input type="email" name="email" class="span12" required="required" placeholder="Email" value="<?php echo htmlentities($email); ?>" />
		</div>
		<div class="span4">
			<input type="text" name="subject" class="span12" required="required" placeholder="Subject" value="<?php echo htmlentities($subject); ?>" />
		</div>
	</div>
	<br />
	<div class="row-fluid">
		<div class="span12">
			<textarea type="text" class="span12" style="height:111px;" name="mess" placeholder="Your Message" required="required"><?php echo htmlentities($mess); ?></textarea>
		</div>
	</div>

	<div class="clear"></div>
	<div class="form-actions" style="text-align: center;">
		<input class="btn btn-primary" type="submit" name="submit" value="Send Message" />
	</div>

	</form>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>