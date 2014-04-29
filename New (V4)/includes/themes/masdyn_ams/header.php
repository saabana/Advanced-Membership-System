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

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/html5shiv.js"></script>
      <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/respond.min.js"></script>
    <![endif]-->

    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
      $('.bxslider').bxSlider({pager:false});
      $('#chzn-select').chosen();
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
          <div class="navbar-brand"><?php echo SITE_NAME; ?></div>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?php echo ($current_page == "home") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>index.php">Home</a></li>
            <?php if($session->is_logged_in()) { ?>
            <li<?php echo ($current_page == "buy_tokens") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>buy_tokens.php">Buy Tokens</a></li>
            <li<?php echo ($current_page == "purchase_access") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>purchase.php">Purchase Access</a></li>
            <?php } ?>
            <li<?php echo ($current_page == "gift_cards") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>gift_cards.php">Gift Cards</a></li>
            <li<?php echo ($current_page == "contact") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>contact.php">Contact Us</a></li>
          </ul>
          <ul class="nav navbar-nav pull-right">
          <?php if($session->is_logged_in()) { ?>
          <li class="dropdown">
            <a href="" class="dropdown-toggle" data-toggle="dropdown"><?php echo $user->username; ?><b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo WWW; ?>dashboard.php?page=overview">Dashboard</a></li>
              <li><a href="<?php echo WWW; ?>dashboard.php?page=settings">Settings</a></li>
              <li><a href="<?php echo WWW; ?>dashboard.php?page=access-levels">Access Levels</a></li>
              <li><a href="<?php echo WWW; ?>dashboard.php?page=token-history">Token History</a></li>
              <li><a href="<?php echo WWW; ?>dashboard.php?page=purchase-history">Purchase History</a></li>
              <li><a href="<?php echo WWW; ?>dashboard.php?page=token-bank">Token Bank</a></li>
              <li><a href="<?php echo WWW; ?>dashboard.php?page=access-logs">Access Logs</a></li>
              <?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES"){ ?><li><a href="<?php echo WWW; ?>dashboard.php?page=invites">Invites</a></li><?php } ?>
              <?php if(isset($user->user_level)){ $level_array = explode(",", $user->user_level); if(in_array("293847", $level_array) || in_array("527387", $level_array)){ echo '<li class="divider"></li><li><a href="'.ADMINDIR.'">Admin Panel</a></li>'; } } ?>
              <li class="divider"></li>
              <li><a href="<?php echo WWW; ?>logout.php">Sign Out</a></li>
            </ul>
          </li>
          <?php } else { ?>
            <li<?php echo ($current_page == "signin") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>signin.php" id="signin_link">Sign In</a></li>
            <li<?php echo ($current_page == "register") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>register.php">Register</a></li>
          <?php } ?>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <?php if($current_page != "home"){ ?>
    <div class="jumbotron page_title">
      <div class="container">
        <h1><?php echo $page_title; ?></h1>
      </div>
    </div>

    <div class="container">

    <?php } ?>