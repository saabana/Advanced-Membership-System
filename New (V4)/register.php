<?php
require_once("includes/inc_files.php");

/*****************************************************************
*    Invest In Me                                                *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

if($session->is_logged_in()) {
  redirect_to("index.php");
}

$user = new User();

$current_page = "register";

if($session->is_logged_in()) {
  redirect_to(WWW."index.php");
}

if(phpversion() >= 5.3){ require_once('includes/captcha/visual/include.php'); }
require_once('includes/captcha/recaptcha/recaptchalib.php');
$publickey = RECAPTCHA_PUBLIC;
$privatekey = RECAPTCHA_PRIVATE;
$resp = null;
$error = null;

if(!isset($_SESSION['register'])){
	$_SESSION['register']['step'] = 1;
	$_SESSION['register']['completed'] = array();
}
if(!isset($_GET['step'])){
	$step = 1;
} else {
	$step = $_GET['step'];
}

$last_step = $_SESSION['register']['step'];

if($step > $last_step){
	header("location: register.php?step=$last_step");
}

$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
$form_names = $csrf->form_names(array('username', 'first_name', 'last_name', 'password', 'repeat_password', 'email', 'signup_ip', 'country', 'gender', 'addr_number', 'addr_line1', 'addr_line2', 'addr_city', 'addr_county', 'addr_postcode', 'telephone'), false);

if($step == 1){
	if(ALLOW_REGISTRATIONS == "NO" && !isset($_SESSION['invite_code'])){
		if(isset($_POST['check_invite'])){
			$invite_code = htmlspecialchars($_POST['invite_code']);
			if($invite_code == ""){
				$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
			} else {
				$invite_code = $_POST['invite_code'];
				$invite = Invites::check_invite_code("$invite_code");
				if($invite === true){
					$_SESSION['invite_code'] = $invite_code;
				} else {
					$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, that invite code is not valid.</div>";
				}
			}
		} else {
			$invite_code = "";
		}
	} else {
		if(isset($_POST['delete'])){
			unset($_SESSION['invite_code']);
		}
		if(isset($_POST['submit'])) {
			
			if($csrf->check_valid('post')){

				if (ALLOW_REGISTRATIONS == "NO") {
					$invite_code = $_SESSION['invite_code'];
					$_SESSION['register']['account_registration']['invite_code'] = $_SESSION['invite_code'];
					// $invite = Invites::check_invite_code("$invite_code");
				} else {
					$invite_code = null;
				}

				$username = $database->escape_value(htmlspecialchars($_POST[$form_names['username']]));
				$first_name = $database->escape_value(htmlspecialchars($_POST[$form_names['first_name']]));
				$last_name = $database->escape_value(htmlspecialchars($_POST[$form_names['last_name']]));
				$password = $database->escape_value(htmlspecialchars($_POST[$form_names['password']]));
				$repeat_password = $database->escape_value(htmlspecialchars($_POST[$form_names['repeat_password']]));
				$email = $database->escape_value(htmlspecialchars($_POST[$form_names['email']]));
				$signup_ip = $_SERVER['REMOTE_ADDR'];
				$country = $database->escape_value(htmlspecialchars($_POST[$form_names['country']]));
				$gender = $database->escape_value(htmlspecialchars($_POST[$form_names['gender']]));

				if(REQ_ADDRESS == "YES"){
					$addr_number = $database->escape_value(htmlspecialchars($_POST[$form_names['addr_number']]));
					$addr_line1 = $database->escape_value(htmlspecialchars($_POST[$form_names['addr_line1']]));
					$addr_line2 = $database->escape_value(htmlspecialchars($_POST[$form_names['addr_line2']]));
					$addr_city = $database->escape_value(htmlspecialchars($_POST[$form_names['addr_city']]));
					$addr_county = $database->escape_value(htmlspecialchars($_POST[$form_names['addr_county']]));
					$addr_postcode = $database->escape_value(htmlspecialchars($_POST[$form_names['addr_postcode']]));
					$telephone = $database->escape_value(htmlspecialchars($_POST[$form_names['telephone']]));
				}		

				$check_username = User::check_user('username', $username);
				$check_email = User::check_user('email', $email);
				

				if ($username != "" && $password != "" && $repeat_password != ""  && $first_name != "" && $last_name != "" && $email != "" && $country != "" && $gender != "") {

					if(REQ_ADDRESS == "YES"){

						if($addr_number != "" && $addr_line1 != "" && $addr_city != "" && $addr_county != "" && $addr_postcode != "" && $telephone != ""){
							$flag = true;
						} else {
							$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
							$flag = false;
						}

					} else {
						$flag = true;
					}

					if($flag === true){
					  if($password == $repeat_password){
						if(!$check_username){
							if(!$check_email){

								$ready = false;

								if(CAPTCHA == "ON"){
									if(CAPTCHA_TYPE == "0"){

										if (!empty($_REQUEST['captcha'])) {
										    if (empty($_SESSION['captcha']) || htmlspecialchars(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
										        $message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter a valid captcha code.</div>";
										    } else {
										        $ready = true;
										    }
										} else {
											$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter the captcha code.</div>";
										}

									} else if(CAPTCHA_TYPE == "1"){

										if (isset($_POST['form_submit']) && $_POST['form_submit'] == '1') {
											if (!validCaptcha('register')) {
												$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter a valid captcha code.</div>";
											} else {
												$ready = true;
											}
										}

									} else if(CAPTCHA_TYPE == "2"){

										if ($_POST["recaptcha_response_field"]) {
										        $resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);

										    if ($resp->is_valid) {
										    	$ready = true;
										    } else {
										        $error = $resp->error;
										        $message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>$error</div>";
										    }
										}

									}

								} else {
									$ready = true;
								}

								if($ready == true){
									unset($_SESSION['register']);
									$_SESSION['register']['step'] = "2";
									if(REQ_ADDRESS == "YES"){
										$_SESSION['register']['account_registration'] = array(
											"first_name" => "$first_name",
											"last_name" => "$last_name",
											"username" => "$username",
											"password" => "$password",
											"email" => "$email",
											"country" => "$country",
											"signup_ip" => "$signup_ip",
											"gender" => "$gender",
											"addr_number" => "$addr_number",
											"addr_line1" => "$addr_line1",
											"addr_line2" => "$addr_line2",
											"addr_city" => "$addr_city",
											"addr_county" => "$addr_county",
											"addr_postcode" => "$addr_postcode",
											"telephone" => "$telephone"
										);
									} else {
										$_SESSION['register']['account_registration'] = array(
											"first_name" => "$first_name",
											"last_name" => "$last_name",
											"username" => "$username",
											"password" => "$password",
											"email" => "$email",
											"country" => "$country",
											"signup_ip" => "$signup_ip",
											"gender" => "$gender"
										);
									}
									redirect_to(WWW."register.php?step=2");
								}

							} else {
								$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that email address has already been taken.</div>";
							}
						} else {
							$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that username has already been taken.</div>";
						}
					  } else {
						$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Passwords don't match.</div>";
					  }
					}
			  	} else {
					$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
				}
		  
			} else {
				error_log('failed');
			}

			$form_names = $csrf->form_names(array('username', 'first_name', 'last_name', 'password', 'repeat_password', 'email', 'signup_ip', 'country', 'gender', 'addr_number', 'addr_line1', 'addr_line2', 'addr_city', 'addr_county', 'addr_postcode', 'telephone'), false);

		} else { 
			if(isset($_SESSION['register']['account_registration'])){
				$username = $_SESSION['register']['account_registration']['username'];
				$password = $_SESSION['register']['account_registration']['password'];
				$repeat_password = $_SESSION['register']['account_registration']['password'];
				$first_name = $_SESSION['register']['account_registration']['first_name'];
				$last_name = $_SESSION['register']['account_registration']['last_name'];
				$email = $_SESSION['register']['account_registration']['email'];
				$gender = $_SESSION['register']['account_registration']['gender'];
				$country = $_SESSION['register']['account_registration']['country'];
				if(REQ_ADDRESS == "YES"){
					$addr_number = $_SESSION['register']['account_registration']['addr_number'];
					$addr_line1 = $_SESSION['register']['account_registration']['addr_line1'];
					$addr_line2 = $_SESSION['register']['account_registration']['addr_line2'];
					$addr_city = $_SESSION['register']['account_registration']['addr_city'];
					$addr_county = $_SESSION['register']['account_registration']['addr_county'];
					$addr_postcode = $_SESSION['register']['account_registration']['addr_postcode'];
					$telephone = $_SESSION['register']['account_registration']['telephone'];
				}
			} else {
				$username = "";
				$password = "";
				$repeat_password = "";
				$first_name = "";
				$last_name = "";
				$email = "";
				$gender = "";
				$country = "";
				if(REQ_ADDRESS == "YES"){
					$addr_number = "";
					$addr_line1 = "";
					$addr_line2 = "";
					$addr_city = "";
					$addr_county = "";
					$addr_postcode = "";
					$telephone = "";
				}
			}
		}
	}
} else if($step == 2){
	if(isset($_POST['submit'])){
		$username = $_SESSION['register']['account_registration']['username'];
		$password = $_SESSION['register']['account_registration']['password'];
		$repeat_password = $_SESSION['register']['account_registration']['password'];
		$first_name = $_SESSION['register']['account_registration']['first_name'];
		$last_name = $_SESSION['register']['account_registration']['last_name'];
		$email = $_SESSION['register']['account_registration']['email'];
		$gender = $_SESSION['register']['account_registration']['gender'];
		$country = $_SESSION['register']['account_registration']['country'];

		if($username != "" && $password != "" && $first_name != "" && $last_name != "" && $email != "" && $gender != "" && $country != ""){

			// $plain_password = $password;
			// $password = encrypt_password($password);
			// $user->create_account($username, $password, $email, $first_name, $last_name, $plain_password, $signup_ip, $country, $gender);
			$signup_ip = $_SERVER['REMOTE_ADDR'];
			$invite_code = $_SESSION['register']['account_registration']['invite_code'];

			if(REQ_ADDRESS == "YES"){
				$addr_number = $_SESSION['register']['account_registration']['addr_number'];
				$addr_line1 = $_SESSION['register']['account_registration']['addr_line1'];
				$addr_line2 = $_SESSION['register']['account_registration']['addr_line2'];
				$addr_city = $_SESSION['register']['account_registration']['addr_city'];
				$addr_county = $_SESSION['register']['account_registration']['addr_county'];
				$addr_postcode = $_SESSION['register']['account_registration']['addr_postcode'];
				$telephone = $_SESSION['register']['account_registration']['telephone'];
				$user->create_account($username, $password, $email, $first_name, $last_name, $signup_ip, $country, $gender, $invite_code, $addr_number, $addr_line1, $addr_line2, $addr_city, $addr_county, $addr_postcode, $telephone);
			} else {
				$user->create_account($username, $password, $email, $first_name, $last_name, $signup_ip, $country, $gender, $invite_code);
			}

		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>");
			unset($_SESSION['register']);
			$_SESSION['register']['step'] = "1";
			redirect_to(WWW."register.php?step=1");
		}

	}
} else if($step == 3){
	unset($_SESSION['register']);
}

?>

<?php $page_title = "Register"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES" && !isset($_SESSION['invite_code'])){ ?>
<div id="message"><?php echo output_message($message); ?></div>
<form action="register.php" method="POST" >

	<div class="row">
		<div class="col-md-12">
			<strong>Sorry, but we are currently closed to new registrations. However, you can still register if you have an active invitation code from an existing member.</strong>
		</div>
	</div>
	
	<br />

	<div class="row center">
		<div class="col-md-6">
			<div class="input-group">
			  <span class="input-group-addon"><strong style="font-size: 15px;">Invite Code</strong></span>
			  <input type="text" name="invite_code" class="form-control" value="<?php echo htmlentities($invite_code); ?>">
			  <span class="input-group-btn">
			    <input class="btn btn-primary" type="submit" name="check_invite" value="Check Invite" />
			  </span>
			</div>
		</div>
	</div>

</form>
<?php } else if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "NO"){ ?>
<div id="message"><?php echo output_message($message); ?></div>
	<div class="row">
		<div class="col-md-12 center">
			<strong>Sorry, but we are currently closed to new registrations.</strong>
		</div>
	</div>
<?php } else { ?>

<ul id="steps">
	<li class="<?php if($step == 1){echo "current";} ?>">Step 1<span>Enter Information</span></li>
	<li class="<?php if($step == 2){echo "current";} ?>">Step 2<span>Review Data</span></li>
	<li class="<?php if($step == 3){echo "current";} ?>">Step 3<span>Account Created</span></li>
</ul>

<hr />

<div id="message"><?php echo output_message($message); ?></div>

 <script type="text/javascript">
 var RecaptchaOptions = {
    theme : 'clean'
 };
 </script>

	<?php if($step == 1){ ?>

		<?php if(OAUTH == "ON"){ ?>		
		<div class="row">
			<div class="col-md-12 center">
				<?php if(FACEBOOK_APP_ID != ""){ ?><a href="<?php echo WWW; ?>auth/facebook" class="zocial facebook">Sign in with Facebook</a><?php } ?>
				<?php if(TWITTER_CONSUMER_KEY != ""){ ?><a href="<?php echo WWW; ?>auth/twitter" class="zocial twitter">Sign in with Twitter</a><?php } ?>
				<?php if(GOOGLE_CLIENT_ID != ""){ ?><a href="<?php echo WWW; ?>auth/google" class="zocial google">Sign in with Google</a><?php } ?>
			</div>
		</div>
		<hr />
		<?php } ?>
		
		<form action="register.php" method="POST" id="register" role="form">

			<input type="hidden" name="<?php echo $token_id; ?>" autocomplete="off" value="<?php echo $token_value; ?>" />
			
			<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES" && isset($_SESSION['invite_code'])){ ?>

			<div class="row">
				<div class="col-md-6">
					<div class="input-group">
					  <span class="input-group-addon"><strong style="font-size: 15px;">Invite Code</strong></span>
					  <input type="text" value="<?php echo $_SESSION['invite_code']; ?>" disabled="disabled" class="form-control">
					  <span class="input-group-btn">
					    <input class="btn btn-danger btn-small" type="submit" name="delete" value="Remove Code" />
					  </span>
					</div>
				</div>
			</div>
			<?php } else if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES" && !isset($_SESSION['invite_code'])){ ?>

			<?php } ?>

			<h3 class="register_title">Account Information</h3>

			<div class="row">
				<div class="col-md-7 form-inline">
				  <div class="form-group names">
				    <label for="first_name">First Name <em class="req">*</em></label>
				    <input type="text" class="form-control" name="<?php echo $form_names['first_name']; ?>" autocomplete="off" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="Enter your First Name" autofocus>
				  </div>
				  <div class="form-group names">
				    <label for="last_name">Last Name <em class="req">*</em></label>
				    <input type="text" class="form-control" name="<?php echo $form_names['last_name']; ?>" autocomplete="off" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Enter your Last Name">
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter your first and last name.</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-7">
				  <div class="form-group">
				    <label for="username">Username <em class="req">*</em></label>
				    <input type="text" class="form-control" name="<?php echo $form_names['username']; ?>" autocomplete="off" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter a Username">
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter your desired username.</p>
				</div>
			</div>


			<div class="row">
				<div class="col-md-7">
				  <div class="form-group">
				    <label for="email">Email Address <em class="req">*</em></label>
				    <input type="text" class="form-control" name="<?php echo $form_names['email']; ?>" autocomplete="off" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your Email Address">
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter your valid email address.</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-7 form-inline">
				  <div class="form-group names">
				    <label for="password">Password <em class="req">*</em></label>
				    <input type="password" class="form-control" name="<?php echo $form_names['password']; ?>" autocomplete="off" value="<?php echo htmlspecialchars($password); ?>" placeholder="Enter Password">
				  </div>
				  <div class="form-group names">
				    <label for="repeat_password">Repeat Password <em class="req">*</em></label>
				    <input type="password" class="form-control" name="<?php echo $form_names['repeat_password']; ?>" autocomplete="off" value="<?php echo htmlspecialchars($repeat_password); ?>" placeholder="Enter Password Again">
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter your desired password.</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-7 form-inline">
				  <div class="form-group names">
				    <label for="country">Country <em class="req">*</em></label>
				    <select id="country" name="<?php echo $form_names['country']; ?>" class="form-control" autocomplete="off" value="<?php echo $country ?>">
						<?php echo display_countries(); ?>
					</select>
				  </div>
				  <div class="form-group names">
				    <label for="gender">Gender <em class="req">*</em></label>
				    <select id="gender" name="<?php echo $form_names['gender']; ?>" class="form-control" autocomplete="off" value="<?php echo $gender ?>">
						<option autocomplete="off" value="Male">Male</option>
						<option autocomplete="off" value="Female">Female</option> 
					</select>
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please select your country and gender.</p>
				</div>
			</div>

			<?php if(REQ_ADDRESS == "YES"){ ?>
			<hr />

			<h3>Your Address</h3>

			<div class="row">
				<div class="col-md-2">
				  <div class="form-group">
				    <label for="addr_number">Number <em class="req">*</em></label><br />
			        <input type="text" name="<?php echo $form_names['addr_number']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($addr_number); ?>" placeholder="Number" />
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter the number of your house/flat.</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-7 form-inline">
				  <div class="form-group names">
				    <label for="addr_line1">Address Line 1 <em class="req">*</em></label>
			        <input type="text" name="<?php echo $form_names['addr_line1']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($addr_line1); ?>" placeholder="Address Line 1" />
				  </div>
				  <div class="form-group names">
				    <label for="addr_line2">Address Line 2 </label>
			        <input type="text" name="<?php echo $form_names['addr_line2']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($addr_line2); ?>" placeholder="Address Line 2" />
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter the first and second lines of your address.</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-7 form-inline">
				  <div class="form-group names">
				    <label for="addr_city">City <em class="req">*</em></label>
			        <input type="text" name="<?php echo $form_names['addr_city']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($addr_city); ?>" placeholder="City" />
				  </div>
				  <div class="form-group names">
				    <label for="addr_county">County <em class="req">*</em></label>
			        <input type="text" name="<?php echo $form_names['addr_county']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($addr_county); ?>" placeholder="County" />
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter your city and county.</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-7 form-inline">
				  <div class="form-group names">
				    <label for="addr_postcode">Postal Code <em class="req">*</em></label>
			        <input type="text" name="<?php echo $form_names['addr_postcode']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($addr_postcode); ?>" placeholder="Postal Code" />
				  </div>
				  <div class="form-group names">
				    <label for="telephone">Telephone Number <em class="req">*</em></label>
			        <input type="text" name="<?php echo $form_names['telephone']; ?>" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($telephone); ?>" placeholder="Telephone Number" />
				  </div>
				</div>
				<div class="col-md-5 description">
					<p>Please enter your postal code and telephone number.</p>
				</div>
			</div>

			<?php } ?>

		

		<?php if(CAPTCHA == "ON"){ ?>

			<?php if(CAPTCHA_TYPE == "0"){ ?>

				<img src="includes/captcha/standard/captcha.php" id="captcha" /><br/>

				<a href="#" onclick="document.getElementById('captcha').src='includes/captcha/standard/captcha.php?'+Math.random();document.getElementById('captcha-form').focus();" id="change-image">Not readable? Change text.</a><br/><br/>

				<input type="text" name="captcha" id="captcha-form" autocomplete="off" /><br/>

			<?php } else if(CAPTCHA_TYPE == "1"){ ?>

				<input type="hidden" name="form_submit" autocomplete="off" value="1" readonly="readonly" />
				<?php printCaptcha('register',$_FORM_TYPE); ?>

			<?php } else if(CAPTCHA_TYPE == "2"){ ?>

				<?php echo recaptcha_get_html($publickey, $error); ?>

			<?php } ?>

		<?php } ?>


			<div class="form-actions" style="text-align: center;">
				<input class="btn btn-success btn-large" type="submit" name="submit" autocomplete="off" value="Register" />
			</div>

		</form>

	<?php } else if($step == 2){ ?>
		<form action="register.php?step=2" method="POST" >
			<h3 class="register_title">Account Information</h3>

			<strong>First Name:</strong> <?php echo $_SESSION['register']['account_registration']['first_name']; ?><br />
			<strong>Last Name:</strong> <?php echo $_SESSION['register']['account_registration']['last_name']; ?><br />
			<strong>Username:</strong> <?php echo $_SESSION['register']['account_registration']['username']; ?><br />
			<strong>Email Address:</strong> <?php echo $_SESSION['register']['account_registration']['email']; ?><br />
			<strong>Password:</strong> <?php echo $_SESSION['register']['account_registration']['password']; ?><br />
			<strong>Country:</strong> <?php echo $_SESSION['register']['account_registration']['country']; ?><br />
			<strong>Gender:</strong> <?php echo $_SESSION['register']['account_registration']['gender']; ?><br />
			<?php if(isset($_SESSION['register']['account_registration']['invite_code'])){ ?><strong>Gender:</strong> <?php echo $_SESSION['register']['account_registration']['invite_code']; ?><br /><?php } ?>
			<?php if(REQ_ADDRESS == "YES"){ ?>

			<h3 class="register_title">Your Address</h3>
			<strong>Number:</strong> <?php echo $_SESSION['register']['account_registration']['addr_number']; ?><br />
			<strong>Address Line 1:</strong> <?php echo $_SESSION['register']['account_registration']['addr_line1']; ?><br />
			<strong>Address Line 2:</strong> <?php echo $_SESSION['register']['account_registration']['addr_line2']; ?><br />
			<strong>City:</strong> <?php echo $_SESSION['register']['account_registration']['addr_city']; ?><br />
			<strong>County:</strong> <?php echo $_SESSION['register']['account_registration']['addr_county']; ?><br />
			<strong>Postcode:</strong> <?php echo $_SESSION['register']['account_registration']['addr_postcode']; ?><br />
			<strong>Telephone Number:</strong> <?php echo $_SESSION['register']['account_registration']['telephone']; ?><br />
			<?php } ?>

			<div class="form-actions" style="text-align: center;">
				<a href="register.php?step=1" class="btn btn-primary">Back</a> <input class="btn btn-success" type="submit" name="submit" autocomplete="off" value="Create Account" />
			</div>

		</form>
	<?php } ?>

<?php } ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>