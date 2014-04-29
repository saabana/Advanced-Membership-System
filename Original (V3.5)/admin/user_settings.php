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
$user_id = $_GET['user_id'];
$location = "user_settings.php?user_id=".$user_id;
$active_page = "users";

$access_logs = User::get_access_logs($user_id);

if(empty($_GET['user_id'])) {
	$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No User ID was provided.</div>";
}

if(empty($_GET['user_id'])){
    $user_id = "";
	$user = "";
	$admin_user = "";
    
  } else {
	$user_id = $_GET['user_id'];
	$user = User::find_by_id($user_id);
	$admin_user = Admin::find_by_id($_SESSION['masdyn']['ams']['user_id']);
	if(!$user) {
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>User could not be found.</div>");
		redirect_to('users.php');
	}
}

if (isset($_POST['submit'])) { 

	$username = $user->username;
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$repeat_password = trim($_POST['repeat_password']);
	$email = trim($_POST['email']);
	$gender = $_POST['gender'];
	$country = $_POST['country'];
	$user_level = implode(",",$_POST['user_level']);
	$activated = $_POST['activated'];
	$suspended = $_POST['suspended'];
	$staff_note = $_POST['staff_note'];
	$whitelist = $_POST['whitelist'];
	$ip_whitelist = $_POST['ip_whitelist'];
	$tokens = $_POST['tokens'];
	$bank_tokens = $_POST['bank_tokens'];
	$primary_group = $_POST['primary_group'];
	
	$staff_username = $admin->username;
	
	$check_email = User::check_user('email', $email);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		if ($first_name != "" && $last_name != "" && $email != "") {
		
			if ($password != "" && $repeat_password != "") {
				// if new password fields are not empty, check to see if they match.
				if ($password == $repeat_password) {
					// new password match
					
					// $new_password = encrypt_password($password);
					
					if(PUSALT == "YES"){
						$new_password = encrypt_password($password, $user->salt);
					} else {
						$new_password = encrypt_password($password);
					}
					
					$admin_user->update_account('1', $user_id, $username, $first_name, $last_name, $new_password, $email, $password, $country, $gender, $user_level, $activated, $suspended, $staff_note, $whitelist, $ip_whitelist, $staff_username, $tokens, $bank_tokens, $primary_group);
				} else {
					$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Passwords don't match.</div>";
				}
			} else {
				// if new password fields are empty
				$admin_user->update_account('2', $user_id, $username, $first_name, $last_name, '', $email, $password, $country, $gender, $user_level, $activated, $suspended, $staff_note, $whitelist, $ip_whitelist, $staff_username, $tokens, $bank_tokens, $primary_group);
				// $message = "Settings 2 Updated";
			}

		} else {
			$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
		}
	}
	
} else { // Form has not been submitted.
	if (!$user_id == ""){
		$username = $user->username;
		$password = "";
		$current_password = "";
		$repeat_password = "";
		$first_name = $user->first_name;
		$last_name = $user->last_name;
		$email = $user->email;
		$whitelist = $user->whitelist;
		$ip_whitelist = $user->ip_whitelist;
		$user_level = $user->user_level;
		$tokens = $user->tokens;
		$bank_tokens = $user->bank_tokens;
		$level_expiry = $user->level_expiry;
		$expiry_datetime = $user->expiry_datetime;
		$staff_note = "";
		$primary_group = $user->primary_group;
		
		$invites = Invites::find_invites($user->user_id);
		$invite_count = Invites::count_all($user->user_id);
		$token_history = User::get_token_history($user->user_id);
		$staff_notes = Admin::get_staff_notes($user->user_id);
		$access_levels = User::get_user_levels($user->user_id);
	}
}

if (isset($_POST['resend_code'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		$email = trim($_POST['email']);
		$user_id = trim($_POST['user_id']);
		Account_Lock::check_resend_code($user->user_id, $user->email, $location);
	}
}

if (isset($_POST['activate_lock'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Account_Lock::set_account_lock($email, $username, $user->user_id, $location);
	}
}

if (isset($_POST['deactivate_lock'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Admin::check_lock_status($user_id, $location);
	}
}

if (isset($_POST['send_new_password'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Admin::send_new_password($email, $location);
	}
}

if (isset($_POST['delete_account'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Admin::delete_account($user_id, $email);
	}
}

if (isset($_POST['create_invite'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Invites::create_invite($user->user_id, $user->username, $location);
	}
}

if((!empty($_GET['delete_code']))){
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		$code = $_GET['delete_code'];
		$user_id = $_GET['user_id'];
	    Invites::delete_invite($code, "user_settings.php?user_id={$user_id}");
	}
}

if((!empty($_GET['delete_staff_note']))){
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		$confirm = $_GET['delete_staff_note'];
		$id = $_GET['id'];
		$user_id = $_GET['user_id'];
	    Admin::delete_staff_note($confirm, $id, $user_id, "user_settings.php?user_id={$user_id}");
	}
}

if(isset($_POST['login_as_user'])){
	$session = new Session();
	$session->admin_login_as_user($user->user_id);
	redirect_to("../settings.php");
}

if(isset($_POST['send_email'])) {
	$email = $user->first_name." ".$user->last_name." <".$user->email.">";
	$subject = $_POST['subject'];
	$email_message = $_POST['message'];
	if(($subject != "") || ($message != "")) {
		if (DEMO_MODE == 'ON') {
			$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
		} else {
			Admin::email_user($email, $subject, $email_message);
		}
	} else {
		$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
	}
}

if((!empty($_GET['delete_level']))){
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		$id = $_GET['id'];
		$level_id = $_GET['delete_level'];
		User::downgrade_access($id,$user->user_id, $level_id, $user->user_level, "user_settings.php?user_id=$user_id");
	}
}

?>

<?php $page_title = "User Settings"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>
	
<?php protect($admin->user_level,"293847,527387","index.php"); ?>
	
	<div class="row-fluid">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<?php

	if($user_id == "") {
		echo "<a href='users.php' class='button'>Back to users</a>";
	} else { ?>
	
		<div class="title">
			<h1><?php echo $page_title; ?></h1>
		</div>

	<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="post" class="center">
		<input class="btn btn-small btn-primary" type="submit" name="activate_lock" value="Activate Lock" />
		<input class="btn btn-small btn-primary" type="submit" name="resend_code" value="Resend Unlock Code" />
		<input class="btn btn-small btn-primary" type="submit" name="deactivate_lock" value="Deactivate Lock" />
		<input class="btn btn-small btn-primary" type="submit" name="send_new_password" value="Send New Password" />
		<input class="btn btn-small btn-warning" type="submit" name="login_as_user" value="Login as User" />
		<button class="btn btn-small btn-primary" data-toggle="modal" href="#access_logs" >Access Logs</button>
		<button class="btn btn-small btn-primary" data-toggle="modal" href="#email_user">Email User</button>
		<button class="btn btn-small btn-danger" data-toggle="modal" href="#delete_account">Delete Account</button>
	</form>
	<hr />

	<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="post">
		
		<h3>Account Information</h3>	
		
		<div class="row-fluid">
			<div class="span3">
				<label>First Name</label>
		      <input type="text" class="span12" name="first_name" required="required" value="<?php echo htmlentities($first_name); ?>" />
			</div>
			<div class="span3">
				<label>Last Name</label>
	      	<input type="text" class="span12" name="last_name" required="required" value="<?php echo htmlentities($last_name); ?>" />
			</div>
			<div class="span3">
				<label>Gender</label>
				<select name="gender" class="span12" required="required" value="<?php echo $gender ?>">
					<option value="Male" <?php if($user->gender == 'Male') { echo 'selected="selected"';} else { echo ''; } ?>>Male</option>
					<option value="Female" <?php if($user->gender == 'Female') { echo 'selected="selected"';} else { echo ''; } ?>>Female</option> 
				</select>
			</div>
			<div class="span3">
				<label>Country</label>
				<select name="country" class="span12" required="required" value="<?php echo $country ?>">
					<option value="<?php echo $user->country ?>" selected="selected"><?php echo $user->country ?></option> 
					<option value="Afghanistan">Afghanistan</option> 
					<option value="Albania">Albania</option> 
					<option value="Algeria">Algeria</option> 
					<option value="American Samoa">American Samoa</option> 
					<option value="Andorra">Andorra</option> 
					<option value="Angola">Angola</option> 
					<option value="Anguilla">Anguilla</option> 
					<option value="Antarctica">Antarctica</option> 
					<option value="Antigua and Barbuda">Antigua and Barbuda</option> 
					<option value="Argentina">Argentina</option> 
					<option value="Armenia">Armenia</option> 
					<option value="Aruba">Aruba</option> 
					<option value="Australia">Australia</option> 
					<option value="Austria">Austria</option> 
					<option value="Azerbaijan">Azerbaijan</option> 
					<option value="Bahamas">Bahamas</option> 
					<option value="Bahrain">Bahrain</option> 
					<option value="Bangladesh">Bangladesh</option> 
					<option value="Barbados">Barbados</option> 
					<option value="Belarus">Belarus</option> 
					<option value="Belgium">Belgium</option> 
					<option value="Belize">Belize</option> 
					<option value="Benin">Benin</option> 
					<option value="Bermuda">Bermuda</option> 
					<option value="Bhutan">Bhutan</option> 
					<option value="Bolivia">Bolivia</option> 
					<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
					<option value="Botswana">Botswana</option> 
					<option value="Bouvet Island">Bouvet Island</option> 
					<option value="Brazil">Brazil</option> 
					<option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
					<option value="Brunei Darussalam">Brunei Darussalam</option> 
					<option value="Bulgaria">Bulgaria</option> 
					<option value="Burkina Faso">Burkina Faso</option> 
					<option value="Burundi">Burundi</option> 
					<option value="Cambodia">Cambodia</option> 
					<option value="Cameroon">Cameroon</option> 
					<option value="Canada">Canada</option> 
					<option value="Cape Verde">Cape Verde</option> 
					<option value="Cayman Islands">Cayman Islands</option> 
					<option value="Central African Republic">Central African Republic</option> 
					<option value="Chad">Chad</option> 
					<option value="Chile">Chile</option> 
					<option value="China">China</option> 
					<option value="Christmas Island">Christmas Island</option> 
					<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
					<option value="Colombia">Colombia</option> 
					<option value="Comoros">Comoros</option> 
					<option value="Congo">Congo</option> 
					<option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
					<option value="Cook Islands">Cook Islands</option> 
					<option value="Costa Rica">Costa Rica</option> 
					<option value="Cote D'ivoire">Cote D'ivoire</option> 
					<option value="Croatia">Croatia</option> 
					<option value="Cuba">Cuba</option> 
					<option value="Cyprus">Cyprus</option> 
					<option value="Czech Republic">Czech Republic</option> 
					<option value="Denmark">Denmark</option> 
					<option value="Djibouti">Djibouti</option> 
					<option value="Dominica">Dominica</option> 
					<option value="Dominican Republic">Dominican Republic</option> 
					<option value="Ecuador">Ecuador</option> 
					<option value="Egypt">Egypt</option> 
					<option value="El Salvador">El Salvador</option> 
					<option value="Equatorial Guinea">Equatorial Guinea</option> 
					<option value="Eritrea">Eritrea</option> 
					<option value="Estonia">Estonia</option> 
					<option value="Ethiopia">Ethiopia</option> 
					<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
					<option value="Faroe Islands">Faroe Islands</option> 
					<option value="Fiji">Fiji</option> 
					<option value="Finland">Finland</option> 
					<option value="France">France</option> 
					<option value="French Guiana">French Guiana</option> 
					<option value="French Polynesia">French Polynesia</option> 
					<option value="French Southern Territories">French Southern Territories</option> 
					<option value="Gabon">Gabon</option> 
					<option value="Gambia">Gambia</option> 
					<option value="Georgia">Georgia</option> 
					<option value="Germany">Germany</option> 
					<option value="Ghana">Ghana</option> 
					<option value="Gibraltar">Gibraltar</option> 
					<option value="Greece">Greece</option> 
					<option value="Greenland">Greenland</option> 
					<option value="Grenada">Grenada</option> 
					<option value="Guadeloupe">Guadeloupe</option> 
					<option value="Guam">Guam</option> 
					<option value="Guatemala">Guatemala</option> 
					<option value="Guinea">Guinea</option> 
					<option value="Guinea-bissau">Guinea-bissau</option> 
					<option value="Guyana">Guyana</option> 
					<option value="Haiti">Haiti</option> 
					<option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
					<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
					<option value="Honduras">Honduras</option> 
					<option value="Hong Kong">Hong Kong</option> 
					<option value="Hungary">Hungary</option> 
					<option value="Iceland">Iceland</option> 
					<option value="India">India</option> 
					<option value="Indonesia">Indonesia</option> 
					<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
					<option value="Iraq">Iraq</option> 
					<option value="Ireland">Ireland</option> 
					<option value="Israel">Israel</option> 
					<option value="Italy">Italy</option> 
					<option value="Jamaica">Jamaica</option> 
					<option value="Japan">Japan</option> 
					<option value="Jordan">Jordan</option> 
					<option value="Kazakhstan">Kazakhstan</option> 
					<option value="Kenya">Kenya</option> 
					<option value="Kiribati">Kiribati</option> 
					<option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option> 
					<option value="Korea, Republic of">Korea, Republic of</option> 
					<option value="Kuwait">Kuwait</option> 
					<option value="Kyrgyzstan">Kyrgyzstan</option> 
					<option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option> 
					<option value="Latvia">Latvia</option> 
					<option value="Lebanon">Lebanon</option> 
					<option value="Lesotho">Lesotho</option> 
					<option value="Liberia">Liberia</option> 
					<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
					<option value="Liechtenstein">Liechtenstein</option> 
					<option value="Lithuania">Lithuania</option> 
					<option value="Luxembourg">Luxembourg</option> 
					<option value="Macao">Macao</option> 
					<option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
					<option value="Madagascar">Madagascar</option> 
					<option value="Malawi">Malawi</option> 
					<option value="Malaysia">Malaysia</option> 
					<option value="Maldives">Maldives</option> 
					<option value="Mali">Mali</option> 
					<option value="Malta">Malta</option> 
					<option value="Marshall Islands">Marshall Islands</option> 
					<option value="Martinique">Martinique</option> 
					<option value="Mauritania">Mauritania</option> 
					<option value="Mauritius">Mauritius</option> 
					<option value="Mayotte">Mayotte</option> 
					<option value="Mexico">Mexico</option> 
					<option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
					<option value="Moldova, Republic of">Moldova, Republic of</option> 
					<option value="Monaco">Monaco</option> 
					<option value="Mongolia">Mongolia</option> 
					<option value="Montserrat">Montserrat</option> 
					<option value="Morocco">Morocco</option> 
					<option value="Mozambique">Mozambique</option> 
					<option value="Myanmar">Myanmar</option> 
					<option value="Namibia">Namibia</option> 
					<option value="Nauru">Nauru</option> 
					<option value="Nepal">Nepal</option> 
					<option value="Netherlands">Netherlands</option> 
					<option value="Netherlands Antilles">Netherlands Antilles</option> 
					<option value="New Caledonia">New Caledonia</option> 
					<option value="New Zealand">New Zealand</option> 
					<option value="Nicaragua">Nicaragua</option> 
					<option value="Niger">Niger</option> 
					<option value="Nigeria">Nigeria</option> 
					<option value="Niue">Niue</option> 
					<option value="Norfolk Island">Norfolk Island</option> 
					<option value="Northern Mariana Islands">Northern Mariana Islands</option> 
					<option value="Norway">Norway</option> 
					<option value="Oman">Oman</option> 
					<option value="Pakistan">Pakistan</option> 
					<option value="Palau">Palau</option> 
					<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
					<option value="Panama">Panama</option> 
					<option value="Papua New Guinea">Papua New Guinea</option> 
					<option value="Paraguay">Paraguay</option> 
					<option value="Peru">Peru</option> 
					<option value="Philippines">Philippines</option> 
					<option value="Pitcairn">Pitcairn</option> 
					<option value="Poland">Poland</option> 
					<option value="Portugal">Portugal</option> 
					<option value="Puerto Rico">Puerto Rico</option> 
					<option value="Qatar">Qatar</option> 
					<option value="Reunion">Reunion</option> 
					<option value="Romania">Romania</option> 
					<option value="Russian Federation">Russian Federation</option> 
					<option value="Rwanda">Rwanda</option> 
					<option value="Saint Helena">Saint Helena</option> 
					<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
					<option value="Saint Lucia">Saint Lucia</option> 
					<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
					<option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
					<option value="Samoa">Samoa</option> 
					<option value="San Marino">San Marino</option> 
					<option value="Sao Tome and Principe">Sao Tome and Principe</option> 
					<option value="Saudi Arabia">Saudi Arabia</option> 
					<option value="Senegal">Senegal</option> 
					<option value="Serbia and Montenegro">Serbia and Montenegro</option> 
					<option value="Seychelles">Seychelles</option> 
					<option value="Sierra Leone">Sierra Leone</option> 
					<option value="Singapore">Singapore</option> 
					<option value="Slovakia">Slovakia</option> 
					<option value="Slovenia">Slovenia</option> 
					<option value="Solomon Islands">Solomon Islands</option> 
					<option value="Somalia">Somalia</option> 
					<option value="South Africa">South Africa</option> 
					<option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
					<option value="Spain">Spain</option> 
					<option value="Sri Lanka">Sri Lanka</option> 
					<option value="Sudan">Sudan</option> 
					<option value="Suriname">Suriname</option> 
					<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
					<option value="Swaziland">Swaziland</option> 
					<option value="Sweden">Sweden</option> 
					<option value="Switzerland">Switzerland</option> 
					<option value="Syrian Arab Republic">Syrian Arab Republic</option> 
					<option value="Taiwan, Province of China">Taiwan, Province of China</option> 
					<option value="Tajikistan">Tajikistan</option> 
					<option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
					<option value="Thailand">Thailand</option> 
					<option value="Timor-leste">Timor-leste</option> 
					<option value="Togo">Togo</option> 
					<option value="Tokelau">Tokelau</option> 
					<option value="Tonga">Tonga</option> 
					<option value="Trinidad and Tobago">Trinidad and Tobago</option> 
					<option value="Tunisia">Tunisia</option> 
					<option value="Turkey">Turkey</option> 
					<option value="Turkmenistan">Turkmenistan</option> 
					<option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
					<option value="Tuvalu">Tuvalu</option> 
					<option value="Uganda">Uganda</option> 
					<option value="Ukraine">Ukraine</option> 
					<option value="United Arab Emirates">United Arab Emirates</option> 
					<option value="United Kingdom">United Kingdom</option> 
					<option value="United States">United States</option> 
					<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
					<option value="Uruguay">Uruguay</option> 
					<option value="Uzbekistan">Uzbekistan</option> 
					<option value="Vanuatu">Vanuatu</option> 
					<option value="Venezuela">Venezuela</option> 
					<option value="Viet Nam">Viet Nam</option> 
					<option value="Virgin Islands, British">Virgin Islands, British</option> 
					<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
					<option value="Wallis and Futuna">Wallis and Futuna</option> 
					<option value="Western Sahara">Western Sahara</option> 
					<option value="Yemen">Yemen</option> 
					<option value="Zambia">Zambia</option> 
					<option value="Zimbabwe">Zimbabwe</option>
				</select>		
			</div>
		</div>	

		<div class="row-fluid">
			<div class="span3">
				<label>Email Address</label>
		      <input type="email" class="span12" name="email" required="required" value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span3">
				<label>Username</label>
	      	<input type="text" class="span12" name="username" disabled="disabled" required="required" value="<?php echo htmlentities($username); ?>" />
			</div>
			<div class="span3">
				<label>New Password</label>
		      <input type="password" class="span12" name="password" value="<?php echo htmlentities($password); ?>" />
			</div>
			<div class="span3">
				<label>Repeat New Password</label>
	      	<input type="password" class="span12" name="repeat_password" value="<?php echo htmlentities($repeat_password); ?>" />
			</div>
		</div>

		<div class="row-fluid">
			<div class="span3">
				<label>Account Lock</label>
	      	<input type="text" class="span12" name="username" disabled="disabled" required="required" value="<?php echo htmlentities($username); ?>" />
			</div>
			<div class="span3">
				<label>IP Protection</label>
		      <select name="whitelist" class="span12" value="<?php echo $whitelist ?>">
					<option value="1" <?php if($user->whitelist == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Enabled</option>
					<option value="0" <?php if($user->whitelist == '0') { echo 'selected="selected"';} else { echo ''; } ?>>Disabled</option> 
				</select>
			</div>
			<div class="span6">
				<label>IP Whitelist (127.0.0.1,127.0.0.2,127.0.0.3)</label>
	      	<input type="text" class="span12" name="ip_whitelist" value="<?php echo htmlentities($ip_whitelist); ?>" />
			</div>
		</div>
		
		<h3>Account Settings and Statistics</h3>
		
		<div class="row-fluid">
			<div class="span3">
				<label>Signup Date</label>
		      <input type="text" class="span12" name="signup_date" disabled="disabled" value="<?php echo $user->date_created; ?>" />
			</div>
			<div class="span3">
            <label>Last Login</label>
           	<input type="text" class="span12" name="last_login" disabled="disabled" value="<?php echo $user->last_login; ?>" />
			</div>
			<div class="span3">
            <label>Signup IP</label>
            <input type="text" class="span12" name="signup_ip" disabled="disabled" value="<?php echo $user->signup_ip; ?>" />
			</div>
			<div class="span3">
            <label>Last IP</label>
            <input type="text" class="span12" name="last_ip" disabled="disabled" value="<?php echo $user->last_ip; ?>" />
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span3">
				<label>User ID</label>
		      <input type="text" class="span12" name="user_id" disabled="disabled" value="<?php echo $user->user_id; ?>" />
			</div>
			<div class="span3">
            <label>Account Lock</label>
           	<input type="text" class="span12" name="account_lock" disabled="disabled" value="<?php echo convert_boolean_full($user->account_lock); ?>" />
			</div>
			<div class="span3">
            <label>Activated</label>
				<select name="activated" class="span12" required="required" value="<?php echo $activated ?>">
					<option value ="1" <?php if($user->activated == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value ="0" <?php if($user->activated == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
				</select>
			</div>
			<div class="span3">
            <label>Suspended</label>
				<select name="suspended" class="span12" required="required" value="<?php echo $suspended; ?>">
					<option value ="1" <?php if($user->suspended == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value ="0" <?php if($user->suspended == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
				</select>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span3">
            <label>Active Tokens</label>
           	<input type="text" class="span12" name="tokens" value="<?php echo $user->tokens; ?>" />
			</div>
			<div class="span3">
            <label>Tokens in Bank</label>
           	<input type="text" class="span12" name="bank_tokens" value="<?php echo $user->bank_tokens; ?>" />
			</div>
			<div class="span3">
            <label>Invited By</label>
           	<input type="text" class="span12" name="invited_by" disabled="disabled" value="<?php echo $user->invited_by; ?>" />
			</div>
			<div class="span3">
				<label>Primary Group</label>
				<select name="primary_group" class="span12" required="required" value="<?php echo $primary_group ?>">
					<?php foreach(explode(",", $user->user_level) as $level){ ?>
					<option value="<?php echo $level; ?>" <?php if($user->primary_group == $level) { echo 'selected="selected"';} ?>><?php echo User::get_level_name($level); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span12">
				<label>User Levels (groups)</label>
				<select data-placeholder="User Levels..." name="user_level[]" class="span12 chzn-select" multiple value="<?php echo $user_level ?>">
				<?php $user_levels = explode(",", $user->user_level); foreach(User::get_site_levels() as $level){ ?>
					<option value="<?php echo $level->level_id; ?>"<?php if(in_array($level->level_id, $user_levels)){  echo ' selected="selected"'; } ?>><?php echo $level->level_name; ?></option>
				<?php } ?>
				</select>
			</div>
		</div>
		
		<h3>User Levels</h3>	
		
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Level ID</th>
					<th>Level Name</th>
					<th>Created</th>
					<th>Expires</th>
					<th>Expiry Date</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($access_levels as $data) : ?>
				<tr>
					<td><?php echo $data->level_id; ?></td>
					<td><?php echo User::get_level_name($data->level_id); ?></td>
					<td><?php echo $data->created; ?></td>
					<td><?php echo convert_boolean($data->expires); ?></td>
					<td><?php echo $data->expiry_date; ?></td>
					<td><a href="user_settings.php?user_id=<?php echo $user_id ?>&amp;id=<?php echo $data->id; ?>&amp;delete_level=<?php echo $data->level_id; ?>">Delete</a></td>
				</tr>
				<?php endforeach; ?>

				<?php if(empty($access_levels)) : ?>
				<tr>
					<td colspan="6"><strong>This account has not got any current access levels.</strong></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<?php if(ALLOW_REGISTRATIONS == "NO") { if(ALLOW_INVITES == "YES") : ?>

		<h3>Invites</h3>	

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Invite Code <?php echo "(".$invite_count."/".MAX_INVITES.")" ?> - Total Users Invited: <?php echo User::count_invites($username); ?></th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($invites as $invite) : ?>
				<tr>
					<td><?php echo $invite->code; ?></td>
					<td><a href="user_settings.php?user_id=<?php echo $user_id ?>&amp;delete_code=<?php echo $invite->code; ?>"><img src="../assets/img/delete.png" alt="edit" class="edit_button" /></a></td>
				</tr>
				<?php endforeach; ?>

				<?php if($invite_count < MAX_INVITES) : ?>
				<tr>
					<td colspan="2"><input class="btn btn-primary" type="submit" name="create_invite" value="Create Invite" /></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php endif; } ?>

		<h3>Token History</h3>	

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Tokens</th>
					<th>Package Name</th>
					<th>Action</th>
					<th>Date Time</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($token_history as $history) : ?>
				<tr>
					<td><?php echo $history->tokens; ?></td>
					<td><?php echo $history->package_name; ?></td>
					<td><?php echo convert_token_status($history->status); ?></td>
					<td><?php echo datetime_to_text($history->datetime); ?></td>
				</tr>
				<?php endforeach; ?>

				<?php if(empty($token_history)) : ?>
				<tr>
					<td colspan="4"><strong>This account has not had any token transactions.</strong></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		
		<h3>Staff Notes</h3>	

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Staff Member</th>
					<th>Message</th>
					<th>Posted</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($staff_notes as $note) : ?>
				<tr>
					<td><?php echo $note->username; ?></td>
					<td><?php echo nl2br($note->message); ?></td>
					<td><?php echo datetime_to_text($note->date); ?></td>
					<td><a href="user_settings.php?delete_staff_note=yes&amp;id=<?php echo $note->id; ?>&amp;user_id=<?php echo $user->user_id; ?>"><img src="../assets/img/delete.png" alt="edit" class="edit_button" /></a></td>
				</tr>
				<?php endforeach; ?>

				<?php if(empty($staff_notes)) : ?>
				<tr>
					<td colspan="4"><strong>This account does not currently have any staff notes.</strong></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		<div class="row-fluid">
			<div class="span12">
				<label>Add Account Note:</label>
		      <textarea type="text" class="staff_notes span12" name="staff_note"><?php echo $staff_note; ?></textarea>
			</div>
		</div>
		
		<div class="form-actions" style="text-align: center;">
			<input class="btn btn-primary" type="submit" name="submit" value="Update Settings" />
		</div>

	</form>

<div id="access_logs" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Access Logs</h3>
	</div>
	<div class="modal-body">
		<?php if(!empty($access_logs)){ ?>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Date and Time</th>
					<th>IP Address</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($access_logs as $log): ?>
				<tr>
					<td><?php echo datetime_to_text($log->datetime); ?></td>
					<td><?php if(DEMO_MODE == "ON"){echo "--Hidden In Demo--";}else{echo $log->ip_address;} ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php } else { ?>
		<strong>This user has not yet logged in.</strong>
		<?php } ?>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
	</div>
</div>

<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="POST" id="delete_account" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Delete Account</h3>
	</div>
	<div class="modal-body">
		<strong>Are you sure about deleting this account? This action can't be reversed.</strong>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="delete_account" value="Confirm" />
	</div>
</form>

<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="POST" id="email_user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Email User</h3>
	</div>
	<div class="modal-body">
		<label>Subject</label>
	   <input type="text" required="required" style="width: 98%;" name="subject" value="" />
		<label>Message</label>
		<textarea required="required" style="width: 98%;" name="message"></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="send_email" value="Send Email" />
	</div>
</form>

<?php if(isset($_GET['delete_level'])) {?>
	<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="POST" id="delete_friend" class="modal">
	    <div class="modal-header"><a href="user_settings.php?user_id=<?php echo $user_id ?>" class="close" data-dismiss="modal">×</a>
	        <h3 id="myModalLabel">Delete Level</h3>
	    </div>
	    <div class="modal-body">
	        <label>Are you sure you want to delete <strong><?php echo $_GET['delete_level']; ?></strong> from this users levels?</label>
	    </div>
	    <div class="modal-footer">
		   <a href="user_settings.php?user_id=<?php echo $user_id ?>" class="btn">Close</a>
		   <button class="btn btn-danger" type="submit" name="delete_level">Confirm</button>
		 </div>
	</form>​
	<div class="modal-backdrop fade in"></div>
<?php } // delete friend check ?>

<?php } ?>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>