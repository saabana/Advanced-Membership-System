<?php require_once("includes/inc_files.php"); 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/


if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
}

$current_page = "home";

if(isset($_SESSION['oauth_message'])){
	$message = $_SESSION['oauth_message'];
	unset($_SESSION['oauth_message']);
}

?>

<?php $page_title = "Welcome"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="homepage tabs">
	<div class="container">
		<a href="">
			<div class="first box col-md-4">
				<img src="<?php echo WWW."includes/themes/".THEME_NAME."/img/homepage/"; ?>installer.png" style="float:left;margin: 3px -11px 0px -28px;">
				<div>
					<h3>Script Installer</h3>
					<p>Easy to use, step-by-installer which will get your script up and running in minutes! <br /><br /> You will also be able to upgrade from earlier versions of this script.</p>
				</div>
				<div class="clearfix"></div>
			</div>
		</a>
		<a href="">
			<div class="box col-md-4">
				<img src="<?php echo WWW."includes/themes/".THEME_NAME."/img/homepage/"; ?>support.png" style="float:left;margin: 3px -6px 0px -19px;">
				<div>
					<h3>Free Support</h3>
					<p>We offer fee support for life with all of our items! <br /><br /> Simply create a thread on our support forum, and we will do our best to help you out.</p>
				</div>
				<div class="clearfix"></div>
			</div>
		</a>
		<a href="">
			<div class="box col-md-4">
				<img src="<?php echo WWW."includes/themes/".THEME_NAME."/img/homepage/"; ?>secure.png" style="float:left;margin: 3px 0px 0px -19px;">
				<div>
					<h3>Secure Code</h3>
					<p>This script has been tested using the very latest vulrenibility testing tools, and has passed with flying colors!</p>
				</div>
				<div class="clearfix"></div>
			</div>
		</a>
	</div>
</div>


<div class="container">

<?php echo output_message($message); ?>

<div class="row center">

	<div class="col-md-12">
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin nec turpis non risus bibendum semper. Donec sit amet gravida nisl, quis tincidunt leo. Nullam ultrices felis ac augue ornare sodales. Integer pharetra nibh rhoncus, pellentesque lacus ac, malesuada dui. Nulla vitae congue mauris. Aliquam tincidunt nibh sed fringilla varius. Vestibulum elementum, turpis vel hendrerit accumsan, tortor turpis iaculis magna, at euismod orci augue hendrerit tortor. Quisque felis velit, blandit id libero et, gravida bibendum lectus. Ut volutpat ligula eget massa iaculis, id ultricies dolor tristique. Donec mattis id risus et vulputate. Quisque posuere semper mauris, vitae euismod sapien sodales eget. Duis tincidunt, purus non eleifend posuere, lorem purus dapibus elit, nec pharetra elit massa id velit. Suspendisse potenti.</p>

		<p>Vivamus egestas purus posuere, lobortis ipsum in, consectetur mi. Nulla sit amet nisl et mi egestas blandit. In quis aliquet diam. Nulla facilisi. Cras dignissim porta tellus, a faucibus eros aliquet viverra. Fusce arcu arcu, feugiat vel nunc eget, cursus tempus sem. Proin pharetra nulla mi, sed dapibus est auctor pellentesque. Ut sagittis imperdiet arcu eget porta. Donec molestie, augue eu ultrices sodales, ante massa sollicitudin felis, non malesuada nunc diam id nulla. Proin ut congue eros, eget sodales nisl. Aliquam molestie elit eget nibh volutpat, vel sagittis sapien posuere. Sed at sodales nibh. Fusce velit leo, eleifend eget orci pulvinar, feugiat volutpat ipsum. Donec quis vestibulum urna. Maecenas magna nibh, egestas et tortor non, tempus feugiat magna.</p>

		<p>Duis sit amet dui eget ante facilisis porttitor. Vivamus vulputate, massa in vestibulum viverra, est sem ullamcorper leo, iaculis eleifend eros augue in massa. Aenean dictum scelerisque leo vel hendrerit. Praesent hendrerit vitae ipsum sit amet luctus. Vestibulum in quam justo. Quisque eu aliquet leo. Sed a nisl tincidunt, pretium felis at, dignissim magna. Mauris sit amet congue dui, at ultrices turpis. Sed ut pharetra erat. Ut quis molestie purus. Nulla sit amet tempus nisl.</p>
	</div>

</div>

<br />

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>