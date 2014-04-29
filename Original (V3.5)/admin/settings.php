<?php 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../login.php");}

$admin = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);

$admin_class = new Admin();

$active_page = "settings";

$settings = Core_Settings::find_by_sql("SELECT * FROM core_settings");

if(isset($_POST['update_settings'])){
	
	if(DEMO_MODE == "OFF"){

		foreach($settings as $setting) {
			$array =  (array) $setting;
			$$array['name'] = $_POST[$array['name']];
			
			$database->query("UPDATE core_settings SET data = '".$$array['name']."' WHERE name = '".$array['name']."' ");
		}

		$database->query("UPDATE core_settings SET data = 'OFF' WHERE name = 'DEMO_MODE' ");

		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Settings have been successfully updated.</div>");
	} else {
		$session->message("<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>");
	}
	
	redirect_to("settings.php");
} else {
	foreach($settings as $setting) {
		$array =  (array) $setting;
		$$array['name'] = $array['data'];
	}
}

?>

<?php $page_title = "Site Settings"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<?php protect($admin->user_level,"293847","index.php"); ?>
	
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
	</div>

	<div class="row-fluid">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<form action="settings.php" method="POST">
		<div class="row-fluid">
			<div class="span6">
				<label>Site Domain</label>
		      <input type="text" name="WWW" class="span12" required="required" value="<?php echo htmlentities($WWW); ?>" />
			</div>
			<div class="span6">
				<label>Site Name</label>
	      	<input type="text" name="SITE_NAME" class="span12" required="required" value="<?php echo htmlentities($SITE_NAME); ?>" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<label>Site Description</label>
				<textarea name="SITE_DESC" class="span12" required="required" rows="3"><?php echo htmlentities($SITE_DESC); ?></textarea>
			</div>
			<div class="span6">
				<label>Site Keywords</label>
				<textarea name="SITE_KEYW" class="span12" required="required" rows="3"><?php echo htmlentities($SITE_KEYW); ?></textarea>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3">
				<label>Admin Directory</label>
				<input type="text" name="ADMINDIR" class="span12" required="required" value="<?php echo htmlentities($ADMINDIR); ?>" />
			</div>
			<div class="span3">
				<label>Site Email</label>
				<input type="text" name="SITE_EMAIL" class="span12" required="required" value="<?php echo htmlentities($SITE_EMAIL); ?>" />
			</div>
			<div class="span2">
				<label>Verify Email</label>
		      <select name="VERIFY_EMAIL" class="span12" required="required" value="<?php echo $VERIFY_EMAIL ?>">
					<option value="YES" <?php if($VERIFY_EMAIL == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($VERIFY_EMAIL == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="span2">
				<label>Allow Registrations</label>
		      <select name="ALLOW_REGISTRATIONS" class="span12" required="required" value="<?php echo $ALLOW_REGISTRATIONS ?>">
					<option value="YES" <?php if($ALLOW_REGISTRATIONS == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($ALLOW_REGISTRATIONS == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="span2">
				<label>Allow Invites</label>
		      <select name="ALLOW_INVITES" class="span12" required="required" value="<?php echo $ALLOW_INVITES ?>">
					<option value="YES" <?php if($ALLOW_INVITES == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($ALLOW_INVITES == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span2">
				<label>Max Active Invites</label>
				<input type="text" name="MAX_INVITES" class="span12" required="required" value="<?php echo htmlentities($MAX_INVITES); ?>" />
			</div>
			<div class="span2">
				<label>Token Price</label>
				<input type="text" name="TOKEN_PRICE" class="span12" required="required" value="<?php echo htmlentities($TOKEN_PRICE); ?>" />
			</div>
			<div class="span2">
				<label>Currency Code</label>
				<input type="text" name="CURRENCY_CODE" class="span12" required="required" value="<?php echo htmlentities($CURRENCY_CODE); ?>" />
			</div>
			<div class="span2">
				<label>Currency Symbol</label>
				<input type="text" name="CURRENCYSYMBOL" class="span12" required="required" value="<?php echo htmlentities($CURRENCYSYMBOL); ?>" />
			</div>
			<div class="span2">
				<label>PayPal Sandbox</label>
		      <select name="PAYPAL_SANDBOX" class="span12" required="required" value="<?php echo $PAYPAL_SANDBOX ?>">
					<option value="YES" <?php if($PAYPAL_SANDBOX == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PAYPAL_SANDBOX == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="span2">
				<label>PayPal Service Purchase</label>
				<select name="PP_SERVICE_PURCHASE" class="span12" required="required" value="<?php echo $PP_SERVICE_PURCHASE ?>">
					<option value="YES" <?php if($PP_SERVICE_PURCHASE == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PP_SERVICE_PURCHASE == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span4">
				<label>PayPal Email</label>
				<input type="text" name="PAYPAL_EMAIL" class="span12" required="required" value="<?php echo htmlentities($PAYPAL_EMAIL); ?>" />
			</div>
			<div class="span4">
				<label>Database Salt</label>
				<input type="text" name="DATABASE_SALT" class="span12" required="required" value="<?php echo htmlentities($DATABASE_SALT); ?>" />
			</div>
			<div class="span4">
				<label>Timezone</label>
		      <select name="TIMEZONE" class="span12" required="required" value="<?php echo $TIMEZONE ?>">
					<?php
					
					foreach ($timezones as $key => $value) {
						if($value == $TIMEZONE){
							$selected = ' selected="selected"';
						} else {
							$selected = '';
						}
						echo '<option value="' .$value. '" '.$selected.' >' .$key. '</option>';
					}
					
					?>
				</select>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span2">
				<label>Pagination</label>
				<input type="text" name="PAGINATION_PER_PAGE" class="span12" required="required" value="<?php echo htmlentities($PAGINATION_PER_PAGE); ?>" />
			</div>
			<div class="span2">
				<label>Personal User Salt</label>
				<select name="PUSALT" class="span12" required="required" value="<?php echo $PUSALT ?>">
					<option value="YES" <?php if($PUSALT == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PUSALT == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="span2">
				<label>OAuth</label>
				<select name="OAUTH" class="span12" required="required" value="<?php echo $OAUTH ?>">
					<option value="ON" <?php if($OAUTH == 'ON') { echo 'selected="selected"';} else { echo ''; } ?>>On</option>
					<option value="OFF" <?php if($OAUTH == 'OFF') { echo 'selected="selected"';} else { echo ''; } ?>>Off</option> 
				</select>
			</div>
			<div class="span2">
				<label>Admin Level</label>
				<input type="text" name="ADMIN_LEVEL" class="span12" required="required" value="<?php echo htmlentities($ADMIN_LEVEL); ?>" />
			</div>
			<div class="span2">
				<label>Theme Name</label>
				<input type="text" name="THEME_NAME" class="span12" required="required" value="<?php echo htmlentities($THEME_NAME); ?>" />
			</div>
			<div class="span2">
				<label>Max Failed Logins</label>
				<input type="text" name="BRUTEFORCE_LIMIT" class="span12" required="required" value="<?php echo htmlentities($BRUTEFORCE_LIMIT); ?>" />
			</div>

		</div>

		<div class="row-fluid">
			<div class="span2">
				<label>Login Timeout (Minutes)</label>
				<input type="text" name="BRUTEFORCE_TIMEOUT" class="span12" required="required" value="<?php echo htmlentities($BRUTEFORCE_TIMEOUT); ?>" />
			</div>
			<div class="span2">
				<label>Captcha</label>
				<select name="CAPTCHA" class="span12" required="required" value="<?php echo $CAPTCHA ?>">
					<option value="ON" <?php if($CAPTCHA == 'ON') { echo 'selected="selected"';} else { echo ''; } ?>>On</option>
					<option value="OFF" <?php if($CAPTCHA == 'OFF') { echo 'selected="selected"';} else { echo ''; } ?>>Off</option> 
				</select>
			</div>
			<div class="span2">
				<label>Captcha Type</label>
				<select name="CAPTCHA_TYPE" class="span12" required="required" value="<?php echo $CAPTCHA_TYPE ?>">
					<option value="0" <?php if($CAPTCHA_TYPE == '0') { echo 'selected="selected"';} else { echo ''; } ?>>Standard</option>
					<option value="1" <?php if($CAPTCHA_TYPE == '1') { echo 'selected="selected"';} else { echo ''; } ?><?php if(phpversion() < 5.3){ echo ' disabled="disabled"'; } ?>>Visual <?php if(phpversion() < 5.3){ echo '(requires PHP v5.3 or above)'; } ?></option>
					<option value="2" <?php if($CAPTCHA_TYPE == '2') { echo 'selected="selected"';} else { echo ''; } ?><?php if(RECAPTCHA_PUBLIC == ''){ echo ' disabled="disabled"'; } ?>>reCaptcha <?php if(RECAPTCHA_PUBLIC == ''){ echo '(please enter your reCaptcha access codes)'; } ?></option> 
				</select>
			</div>
		</div>
		
		<div class="form-actions" style="text-align: center;">
			<input class="btn btn-primary" type="submit" name="update_settings" value="Update Settings" />
		</div>
	</form>


<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>