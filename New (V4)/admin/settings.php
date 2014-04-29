<?php 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../signin.php");}

$admin = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);

$admin_class = new Admin();

$active_page = "settings";

$settings = Core_Settings::find_by_sql("SELECT * FROM core_settings");

if(isset($_POST['update_settings'])){
	

	foreach($settings as $setting) {
		$array =  (array) $setting;
		$$array['name'] = $_POST[$array['name']];
		
		$database->query("UPDATE core_settings SET data = '".$$array['name']."' WHERE name = '".$array['name']."' ");
	}

	// $database->query("UPDATE core_settings SET data = 'OFF' WHERE name = 'DEMO_MODE' ");

	$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Settings have been successfully updated.</div>");
	
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

<style type="text/css">
	.row{margin-bottom: 15px;}
</style>

	<?php echo output_message($message); ?>
	
	<form action="settings.php" method="POST">
		<div class="row">
			<div class="col-md-6">
				<label>Site Name</label>
	      		<input type="text" name="SITE_NAME" class="form-control" required="required" value="<?php echo htmlentities($SITE_NAME); ?>" />
			</div>
			<div class="col-md-6">
				<label>Site Domain</label>
		    	<input type="text" name="WWW" class="form-control" required="required" value="<?php echo htmlentities($WWW); ?>" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<label>Site Description</label>
				<textarea name="SITE_DESC" class="form-control" required="required" rows="3"><?php echo htmlentities($SITE_DESC); ?></textarea>
			</div>
			<div class="col-md-6">
				<label>Site Keywords</label>
				<textarea name="SITE_KEYW" class="form-control" required="required" rows="3"><?php echo htmlentities($SITE_KEYW); ?></textarea>
			</div>
		</div>

		<hr />

		<div class="row">
			<div class="col-md-3">
				<label>Admin Directory</label>
				<input type="text" name="ADMINDIR" class="form-control" required="required" value="<?php echo htmlentities($ADMINDIR); ?>" />
			</div>
			<div class="col-md-5">
				<label>Site Email</label>
				<input type="text" name="SITE_EMAIL" class="form-control" required="required" value="<?php echo htmlentities($SITE_EMAIL); ?>" />
			</div>
			<div class="col-md-2">
				<label>Verify Email</label>
		    	<select name="VERIFY_EMAIL" class="form-control" required="required" value="<?php echo $VERIFY_EMAIL ?>">
					<option value="YES" <?php if($VERIFY_EMAIL == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($VERIFY_EMAIL == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="col-md-2">
				<label>Allow Registrations</label>
		    	<select name="ALLOW_REGISTRATIONS" class="form-control" required="required" value="<?php echo $ALLOW_REGISTRATIONS ?>">
					<option value="YES" <?php if($ALLOW_REGISTRATIONS == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($ALLOW_REGISTRATIONS == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<label>Allow Invites</label>
		     	<select name="ALLOW_INVITES" class="form-control" required="required" value="<?php echo $ALLOW_INVITES ?>">
					<option value="YES" <?php if($ALLOW_INVITES == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($ALLOW_INVITES == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="col-md-2">
				<label>Max Active Invites</label>
				<input type="text" name="MAX_INVITES" class="form-control" required="required" value="<?php echo htmlentities($MAX_INVITES); ?>" />
			</div>
			<div class="col-md-2">
				<label>Require Address</label>
				<select name="REQ_ADDRESS" class="form-control" required="required" value="<?php echo $REQ_ADDRESS; ?>">
					<option value="YES" <?php if($REQ_ADDRESS == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($REQ_ADDRESS == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>	
			<div class="col-md-2">
				<label>Pagination</label>
				<input type="text" name="PAGINATION_PER_PAGE" class="form-control" required="required" value="<?php echo htmlentities($PAGINATION_PER_PAGE); ?>" />
			</div>
			<div class="col-md-2">
				<label>Personal User Salt</label>
				<select name="PUSALT" class="form-control" required="required" value="<?php echo $PUSALT ?>">
					<option value="YES" <?php if($PUSALT == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PUSALT == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="col-md-2">
				<label>OAuth</label>
				<select name="OAUTH" class="form-control" required="required" value="<?php echo $OAUTH ?>">
					<option value="ON" <?php if($OAUTH == 'ON') { echo 'selected="selected"';} else { echo ''; } ?>>On</option>
					<option value="OFF" <?php if($OAUTH == 'OFF') { echo 'selected="selected"';} else { echo ''; } ?>>Off</option> 
				</select>
			</div>
		</div>

		<hr />

		<div class="row">
			<div class="col-md-2">
				<label>Currency Code</label>
				<input type="text" name="CURRENCY_CODE" class="form-control" required="required" value="<?php echo htmlentities($CURRENCY_CODE); ?>" />
			</div>
			<div class="col-md-2">
				<label>Currency Symbol</label>
				<input type="text" name="CURRENCYSYMBOL" class="form-control" required="required" value="<?php echo htmlentities($CURRENCYSYMBOL); ?>" />
			</div>
			<div class="col-md-2">
				<label>PayPal Sandbox</label>
		    	<select name="PAYPAL_SANDBOX" class="form-control" required="required" value="<?php echo $PAYPAL_SANDBOX ?>">
					<option value="YES" <?php if($PAYPAL_SANDBOX == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PAYPAL_SANDBOX == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
			<div class="col-md-4">
				<label>PayPal Email</label>
				<input type="text" name="PAYPAL_EMAIL" class="form-control" required="required" value="<?php echo htmlentities($PAYPAL_EMAIL); ?>" />
			</div>
			<div class="col-md-2">
				<label>PayPal Service Purchase</label>
				<select name="PP_SERVICE_PURCHASE" class="form-control" required="required" value="<?php echo $PP_SERVICE_PURCHASE ?>">
					<option value="YES" <?php if($PP_SERVICE_PURCHASE == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PP_SERVICE_PURCHASE == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<label>Token Price</label>
				<input type="text" name="TOKEN_PRICE" class="form-control" required="required" value="<?php echo htmlentities($TOKEN_PRICE); ?>" />
			</div>
			<div class="col-md-2">
				<label>Token Credit</label>
				<input type="text" name="TOKEN_CREDIT" class="form-control" required="required" value="<?php echo htmlentities($TOKEN_CREDIT); ?>" />
			</div>
		</div>

		<hr />

		<div class="row">
			<div class="col-md-2">
				<label>Theme Name</label>
				<!-- <input type="text" name="THEME_NAME" class="form-control" required="required" value="<?php //echo htmlentities($THEME_NAME); ?>" /> -->
				<select name="THEME_NAME" class="form-control" required="required" value="<?php //echo $THEME_NAME ?>">
					<?php $themes = array_diff(scandir('../includes/themes/'), array('..', '.', 'index.html', '.DS_Store')); foreach($themes as $key => $value): if($value != "." || $value != ".."){ ?>
					<option value="<?php echo $value; ?>" <?php if($THEME_NAME == $value) { echo 'selected="selected"';} ?>><?php echo $value; ?></option>
					<?php } endforeach; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label>Database Salt</label>
				<input type="text" name="DATABASE_SALT" class="form-control" required="required" value="<?php echo htmlentities($DATABASE_SALT); ?>" />
			</div>
			<div class="col-md-4">
				<label>Timezone</label>
		      <select name="TIMEZONE" class="form-control" required="required" value="<?php echo $TIMEZONE ?>">
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
			<div class="col-md-2">
				<label>Admin Level</label>
				<input type="text" name="ADMIN_LEVEL" class="form-control" required="required" value="<?php echo htmlentities($ADMIN_LEVEL); ?>" />
			</div>
		</div>
		
		<hr />

		<div class="row">

			<div class="col-md-2">
				<label>Max Failed Logins</label>
				<input type="text" name="BRUTEFORCE_LIMIT" class="form-control" required="required" value="<?php echo htmlentities($BRUTEFORCE_LIMIT); ?>" />
			</div>
			<div class="col-md-2">
				<label>Login Timeout (Minutes)</label>
				<input type="text" name="BRUTEFORCE_TIMEOUT" class="form-control" required="required" value="<?php echo htmlentities($BRUTEFORCE_TIMEOUT); ?>" />
			</div>
			<div class="col-md-2">
				<label>Captcha</label>
				<select name="CAPTCHA" class="form-control" required="required" value="<?php echo $CAPTCHA ?>">
					<option value="ON" <?php if($CAPTCHA == 'ON') { echo 'selected="selected"';} else { echo ''; } ?>>On</option>
					<option value="OFF" <?php if($CAPTCHA == 'OFF') { echo 'selected="selected"';} else { echo ''; } ?>>Off</option> 
				</select>
			</div>
			<div class="col-md-2">
				<label>Captcha Type</label>
				<select name="CAPTCHA_TYPE" class="form-control" required="required" value="<?php echo $CAPTCHA_TYPE ?>">
					<option value="0" <?php if($CAPTCHA_TYPE == '0') { echo 'selected="selected"';} ?>>Standard</option>
					<option value="1" <?php if($CAPTCHA_TYPE == '1') { echo 'selected="selected"';} ?><?php if(phpversion() < 5.3){ echo ' disabled="disabled"'; } ?>>Visual <?php if(phpversion() < 5.3){ echo '(requires PHP v5.3 or above)'; } ?></option>
					<option value="2" <?php if($CAPTCHA_TYPE == '2') { echo 'selected="selected"';} ?><?php if(RECAPTCHA_PUBLIC == ''){ echo ' disabled="disabled"'; } ?>>reCaptcha <?php if(RECAPTCHA_PUBLIC == ''){ echo '(please add your reCaptcha access codes to your configuration file)'; } ?></option> 
				</select>
			</div>

		</div>

		<hr />

		<div class="row">

			<div class="col-md-3">
				<label>Use PHPMailer</label>
				<select name="PHPMAILER" class="form-control" required="required" value="<?php echo $PHPMAILER; ?>">
					<option value="YES" <?php if($PHPMAILER == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value="NO" <?php if($PHPMAILER == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
				</select>
			</div>	
			<div class="col-md-3">
				<label>PHPMailer SMTP Host</label>
				<input type="text" name="SMTP_HOST" class="form-control" value="<?php echo htmlentities($SMTP_HOST); ?>" />
			</div>
			<div class="col-md-3">
				<label>PHPMailer SMTP Username</label>
				<input type="text" name="SMTP_USERNAME" class="form-control" value="<?php echo htmlentities($SMTP_USERNAME); ?>" />
			</div>
			<div class="col-md-3">
				<label>PHPMailer SMTP Password</label>
				<input type="text" name="SMTP_PASSWORD" class="form-control" value="<?php echo htmlentities($SMTP_PASSWORD); ?>" />
			</div>
		</div>

		<hr />

		<div class="form-actions" style="text-align: center;">
			<input class="btn btn-primary" type="submit" name="update_settings" value="Update Settings" />
		</div>
	</form>


<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>