<?php 

require_once("includes/inc_files.php"); 

/*****************************************************************
*    Advanced Member System                                      *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
} else {
	redirect_to("signin.php");
}

if(isset($_GET['page'])){
	$page = clean_value($_GET['page']);
} else {
	redirect_to("index.php");
}

$allowed = array('overview','settings','access-levels','token-history','purchase-history','token-bank','access-logs','account-history','invites','purchased-content');

if(in_array($page, $allowed)){

	switch ($page){

		case 'overview':
			$page_url = "includes/dashboard/overview.php";
			$page_title = "Account Overview";
		break;

		case 'settings':
			$page_url = "includes/dashboard/settings.php";
			$page_title = "Settings";
		break;

		case 'access-levels':
			$page_url = "includes/dashboard/access_levels.php";
			$page_title = "Access Levels";
		break;

		case 'purchased-content':
			$page_url = "includes/dashboard/purchased_content.php";
			$page_title = "Purchased Content";
		break;

		case 'token-history':
			$page_url = "includes/dashboard/token_history.php";
			$page_title = "Token History";
		break;

		case 'purchase-history':
			$page_url = "includes/dashboard/purchase_history.php";
			$page_title = "Purchase History";
		break;

		case 'token-bank':
			$page_url = "includes/dashboard/token_bank.php";
			$page_title = "Token Bank";
		break;

		case 'access-logs':
			$page_url = "includes/dashboard/access_logs.php";
			$page_title = "Access Logs";
		break;

		case 'account-history':
			$page_url = "includes/dashboard/account_history.php";
			$page_title = "Account History";
		break;

		case 'invites':
			$page_url = "includes/dashboard/invites.php";
			$page_title = "Invites";
			if(ALLOW_REGISTRATIONS == "YES" || ALLOW_INVITES == "NO"){
				redirect_to(WWW."dashboard.php?page=overview");
			}
		break;
		
		default:
			$page_url = "includes/error/404.php";
			$page_title = "404 - Page Not Found";
		break;
	}

	$current_page = $page;
	$location = WWW."dashboard.php?page=".$page;

} else {
	$page_url = "includes/error/404.php";
	$page_title = "404 - Page Not Found";
	$current_page = "error_404";
	$location = WWW."404.php";
}


if (isset($_POST['activate_lock'])) {
	Account_Lock::set_account_lock($user->email, $user->username, $user->user_id, $location);
} else {
	$unlock_code = "";
}

if (isset($_POST['deactivate_lock'])) {
	$code = trim($_POST['code']);
	if (!$code == "") {
		Account_Lock::check_lock_status($user->user_id, $code, $location);
	} else {
		$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>No unlock code entered.</div>";
	}
} 

if(isset($_POST['resend_code'])) {
	Account_Lock::check_resend_code($user->user_id, $user->email, $location);
}



?>

<?php require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<ul class="nav nav-tabs" style="margin: 15px 0px 15px;">
	<li<?php echo ($page == "overview") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=overview">Overview</a></li>
	<li<?php echo ($page == "settings") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=settings">Settings</a></li>
	<li class="dropdown<?php echo ($page == "token-history" || $page == "purchase-history" || $page == "account-history" || $page == "access-logs") ? ' active' : ''; ?>">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
		  History <span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<li<?php echo ($page == "token-history") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=token-history">Token History</a></li>
			<li<?php echo ($page == "purchase-history") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=purchase-history">Purchase History</a></li>
			<li<?php echo ($page == "account-history") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=account-history">Account History</a></li>
			<li<?php echo ($page == "access-logs") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=access-logs">Access Logs</a></li>
		</ul>
	</li>	
	<li<?php echo ($page == "access-levels") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=access-levels">Access Levels</a></li>
	<li<?php echo ($page == "purchased-content") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=purchased-content">Purchased Content</a></li>
	<li<?php echo ($page == "token-bank") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=token-bank">Token Bank</a></li>
	<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES"){ ?><li<?php echo ($page == "invites") ? ' class="active"' : ''; ?>><a href="dashboard.php?page=invites">Invites</a></li><?php } ?>
</ul>

<div id="message"><?php echo output_message($message); ?></div>

<form action="<?php echo $location; ?>" method="post" style="margin: 0px 0 20px;" role="form">
<?php if($user->account_lock == 0){ ?>
	<div class="input-group">
	  <span class="input-group-addon"><strong style="color: #E90909; font-size: 15px;">Account Lock Inactive</strong></span>
	  <span class="input-group-btn">
	    <input class="btn btn-success" type="submit" name="activate_lock" value="Activate Lock" />
	  </span>
	</div>
	<?php } else { ?>
	<div class="input-group">
	  <span class="input-group-addon"><strong style="color: green; font-size: 15px;">Account Lock Active</strong></span>
	  <input class="form-control" name="code" type="text" placeholder="Unlock Code" value="<?php echo htmlspecialchars($unlock_code); ?>">
	  <span class="input-group-btn">
		  <input class="btn btn-success" type="submit" name="deactivate_lock" value="Deactivate Lock" />
		  <input class="btn btn-primary" type="submit" name="resend_code" value="Resend Code" />
	  </span>
	</div>
<?php } ?>
</form>

<?php require_once($page_url); ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>