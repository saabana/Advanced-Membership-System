<?php require_once("../includes/global/admin_header.php"); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Lewis @ Masdyn Studio - masdyn.com - dewsbury.co">
    <link rel="shortcut icon" href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/ico/favicon.png">

    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/main.css?v=7" rel="stylesheet">
    <link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/jquery.bxslider.css" rel="stylesheet">
    <link href="<?php echo WWW; ?>includes/global/css/chosen.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/html5shiv.js"></script>
      <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/respond.min.js"></script>
    <![endif]-->

    <!-- <script src="<?php //echo WWW; ?>includes/themes/<?php //echo THEME_NAME; ?>/js/jquery-2.0.3.min.js"></script>-->
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
      $('.bxslider').bxSlider({pager:false});
    });
    </script>
  </head>

  <body>

    <div class="navbar navbar-main navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="navbar-brand">Admin Panel</div>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <?php require_once("../includes/global/admin_nav.php"); ?>
          </ul>
          <ul class="nav navbar-nav pull-right">
	          <li><a href="<?php echo WWW; ?>index.php">Frontend</a></li>
	          <li class="dropdown">
	            <a href="" class="dropdown-toggle" data-toggle="dropdown"><?php echo $admin->username; ?><b class="caret"></b></a>
	            <ul class="dropdown-menu">
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=overview">Dashboard</a></li>
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=settings">Settings</a></li>
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=access-levels">Access Levels</a></li>
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=token-history">Token History</a></li>
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=purchase-history">Purchase History</a></li>
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=token-bank">Token Bank</a></li>
	              <li><a href="<?php echo WWW; ?>dashboard.php?page=access-logs">Access Logs</a></li>
	              <li class="divider"></li>
	              <li><a href="<?php echo WWW; ?>logout.php">Sign Out</a></li>
	            </ul>
	          </li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <div class="jumbotron page_title">
      <div class="container">
        <h1><?php if(isset($header_btn)){echo $header_btn." ";} echo $page_title; if(isset($header_btn_right)){echo " ".$header_btn_right;} ?></h1>
      </div>
    </div>

    <div class="container">