<?php require_once("includes/inc_files.php"); 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/


if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
	if($user->account_lock == 0){$account_lock = "Inactive";} else {$account_lock = "Active";}
}

$current_page = "home";

if(isset($_SESSION['oauth_message'])){
	$message = $_SESSION['oauth_message'];
	unset($_SESSION['oauth_message']);
}

?>

<?php $page_title = "Welcome"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

<?php echo output_message($message); ?>

	<br />
	<p>This Advanced Membership System will give you complete control over all of your website users and content. We handcrafted every single feature so they would work seamlessly together providing you with the best and easiest way to manage your website users and content.</p>
	<p>With this script, you can easily protect your websites content by user levels. Each user can have multiple levels - all with separate expiration dates, or with lifetime access.</p>
	<p>This script was build using Object Oriented PHP and MySQL, this will allow you to easily customise it to suit your needs.</p>
	<br />
	<p><a href="http://codecanyon.net/item/advanced-member-system/2333683?ref=masdyn" class="btn btn-primary btn-large">Purchase this script &raquo;</a></p>
	

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>