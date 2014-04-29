<?php require_once("includes/global/header.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Lewis @ Masdyn Studio - masdyn.com - dewsbury.co">
	
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/main.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/global/css/chosen.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="https:/ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script>var WWW = "<?php echo WWW ?>";</script>
	<script src="<?php echo WWW; ?>includes/global/js/bootstrap.min.js"></script>
	<script src="<?php echo WWW ?>includes/global/js/main.js"></script>
	<script>
	$(document).ready(function() {
		$(function() {
			$('.dropdown-toggle').dropdown();
			$('.dropdown, .dropdown input, .dropdown label').click(function(e) {
				e.stopPropagation();
			});
		});
	});
	$(function(){
		$("[rel='tooltip']").tooltip();
	});
	</script>
	
	
</head>

<body>
	<div id="header-wrapper">
		<div class="container">
			<header>
				<div class="row-fluid">
					<div class="span12">
						
						<div class="navbar">
							<div class="navbar-inner">
								<div class="container">
									<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-responsive-collapse">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</a>
									<a href="<?php echo WWW; ?>" class="brand"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/logo.jpg" width="248" height="52" alt="Logo"></a>
									<div class="nav-collapse collapse navbar-responsive-collapse">
										<ul class="nav">
											<li<?php echo ($current_page == "home") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>index.php">Home</a></li>
											<?php if($session->is_logged_in()) { ?>
											<li<?php echo ($current_page == "buy_tokens") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>buy_tokens.php">Buy Tokens</a></li>
											<li<?php echo ($current_page == "purchase_access") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>purchase.php">Purchase Access</a></li>
											<?php } ?>
											<li<?php echo ($current_page == "contact") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>contact.php">Contact Us</a></li>
										</ul>
										<ul class="nav pull-right">
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
														<?php if(isset($user->user_level)){ $level_array = explode(",", $user->user_level); if(in_array("293847", $level_array) || in_array("527387", $level_array)){ echo '<li class="divider"></li><li><a href="'.ADMINDIR.'">Admin Area</a></li>'; } } ?>
														<li class="divider"></li>
														<li><a href="<?php echo WWW; ?>logout.php">Sign Out</a></li>
										          </ul>
										        </li>
											<?php } else { ?>
												<li class="dropdown">
													<a href="#" class="dropdown-toggle" data-toggle="dropdown">Login <b class="caret"></b></a>
													<ul class="dropdown-menu">
														<div id="login_form" onkeypress="if(event.keyCode == 13){login()}">
															<div id="message"></div>
															<div class="row-fluid">
																<div class="span12 center">
															        <input type="text" class="span12" id="username" required="required" placeholder="Username">
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
															        <input type="password" class="span12" id="password" required="required" placeholder="Password">
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
																	<input type="checkbox" id="remember_me" />
																	<span>Remember Me? (Uses Cookies)</span>
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
																	<a href="reset_password.php">Forgot your Password?</a>
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
																	<button class="btn btn-primary" type="submit" id="login_btn" onclick="login()" style="width: 100%">Login</button>
																</div>
															</div>

															<?php if(OAUTH == "ON"){ ?>
															<hr />
																
															<div class="row-fluid">
																<div class="span12 center">
																	<div class="span12">
																		<?php if(FACEBOOK_APP_ID != ""){ ?><a href="<?php echo WWW; ?>auth/facebook" class="zocial icon facebook">Sign in with Facebook</a><?php } ?>
																		<?php if(TWITTER_CONSUMER_KEY != ""){ ?><a href="<?php echo WWW; ?>auth/twitter" class="zocial icon twitter">Sign in with Twitter</a><?php } ?>
																		<?php if(GOOGLE_CLIENT_ID != ""){ ?><a href="<?php echo WWW; ?>auth/google" class="zocial icon google">Sign in with Google</a><?php } ?>
																	</div>
																</div>
															</div>
															<?php } ?>
															
														</div>
													</ul>
												</li>
												<li><a href="register.php">Register</a></li>
											<?php } ?>
										</ul>
									</div><!-- /.nav-collapse -->
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</header>
		</div>
	</div>
	
	<div class="container">
		<div id="content" class="settings">
			
	<!-- Header End -->