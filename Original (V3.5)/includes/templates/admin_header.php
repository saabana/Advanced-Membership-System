<?php if($session->is_logged_in()) { 
	$admin = User::find_by_id($_SESSION['user_id']);
	
	if($admin->suspended == "1") { 
		redirect_to('logout.php?msg=suspended'); 
	} else if(MAINTENANCE_MODE == "ON" && $admin->user_level != "293847") { 
		redirect_to('logout.php?msg=maintenance'); 
	}
} else {
	$is_staff = "";
}

$current_page = "admin";
$sidebar_tab = "settings";

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo SITE_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESC ?>">
    <meta name="author" content="MasDyn Studio">
    <meta name="keywords" content="<?php echo SITE_KEYW ?>">

    <!-- The styles -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
	 <link href="../assets/css/chosen.css" rel="stylesheet">

    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- the fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">

	<script src="../assets/js/jquery.js"></script>
	<script src="../assets/js/custom.js"></script>
	<script src="../assets/js/jquery.jcarousel.min.js"></script>
	<script src="../assets/js/jquery.pikachoose.js"></script>

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
		        <li<?php echo ($current_page == "home") ? " class='active'" : "" ?>><a href="../index.php">Visit Site</a></li>
				<li class='active'><a href="<?php echo WWW.ADMINDIR; ?>">Admin Area</a></li>
		      </ul>
		      <ul class="nav pull-right">
					<li class="dropdown">
			          <a href="" class="dropdown-toggle" data-toggle="dropdown"><?php echo $admin->username; ?><b class="caret"></b></a>
			          <ul class="dropdown-menu">
							<li><a href="../settings.php">Settings</a></li>
							<li class="divider"></li>
							<li><a href="../logout.php">Sign Out</a></li>
			          </ul>
			      </li>
		      </ul>
		    </div><!-- /.nav-collapse -->
		  </div>
		</div><!-- /navbar-inner -->
	</div>

<!-- Header file end -->