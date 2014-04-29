<?php require_once("includes/inc_files.php"); 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
	if($user->account_lock == 0){$account_lock = "Inactive";} else {$account_lock = "Active";}
} else {
	redirect_to("login.php");
}

$current_page = "settings";

$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
$invites = Invites::find_invites($user->user_id);
$invite_count = Invites::count_all($user->user_id);
$token_history = User::get_token_history($user->user_id);
$access_levels = User::get_user_levels($user->user_id);

$location = "settings.php";

if (isset($_POST['submit'])) {

	$username = $user->username;
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$repeat_password = trim($_POST['repeat_password']);
	$email = trim($_POST['email']);
	$gender = $_POST['gender'];
	$country = $_POST['country'];
	$whitelist = $_POST['whitelist'];
	$ip_whitelist = $_POST['ip_whitelist'];
	// $ip_whitelist = implode(",",array_slice(explode(",", $_POST['ip_whitelist']), 0, 2));
	
	// $staff_username = $admin->username;
	
	$check_email = User::check_user('email', $email);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		if($user->account_lock == 0){
			if ($first_name != "" && $last_name != "" && $email != "") {
				if ($password != "" && $repeat_password != "") {
					// if new password fields are not empty, check to see if they match.
					if ($password == $repeat_password) {
						// new password match
						$new_password = encrypt_password($password);
						$user->update_account('1', $first_name, $last_name, $new_password, $email, $password, $country, $gender, $whitelist, $ip_whitelist);
					} else {
						$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but your new passwords don't match.</div>";
					}
				} else {
					// if new password fields are empty
					$user->update_account('2', $first_name, $last_name, $password, $email, $password, $country, $gender, $whitelist, $ip_whitelist);
				}
			} else {
				$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
			}
		} else {
			$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but we can't change your settings while your account lock is enabled.</div>";
			$username = $user->username;
			$password = "";
			$current_password = "";
			$repeat_password = "";
			$first_name = $user->first_name;
			$last_name = $user->last_name;
			$email = $user->email;
			$lock_status = "";
			$lock_status_message = "";
			$code = "";
			$whitelist = $user->whitelist;
			$ip_whitelist = $user->ip_whitelist;
		}
	}
	
} else { // Form has not been submitted.
	$username = $user->username;
	$password = "";
	$current_password = "";
	$repeat_password = "";
	$first_name = $user->first_name;
	$last_name = $user->last_name;
	$email = $user->email;
	$lock_status = "";
	$lock_status_message = "";
	$code = "";
	$whitelist = $user->whitelist;
	$ip_whitelist = $user->ip_whitelist;
}

if (isset($_POST['activate_lock'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
		Account_Lock::set_account_lock($email, $username, $user->user_id, $location);
	}
}

if (isset($_POST['deactivate_lock'])) {
	$code = trim($_POST['code']);
	if (!$code == "") {
		if (DEMO_MODE == 'ON') {
			$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
		} else {
			Account_Lock::check_lock_status($user->user_id, $code, $location);
		}
	} else {
		$message = "<div class='notification-box warning-notification-box'><p>No unlock code entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
} 

if (isset($_POST['resend_code'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
		Account_Lock::check_resend_code($user->user_id, $user->email, $location);
	}
}

if (isset($_POST['create_invite'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
		Invites::create_invite($user->user_id, $user->username, $location);
	}
}

if((!empty($_GET['delete_code']))){
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
		$code = $_GET['delete_code'];
	    Invites::delete_invite($code, "settings.php");
	}
}


?>

<?php $page_title = "Account Settings"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

	<?php echo output_message($message); ?>

	<div class="row-fluid">
		<form action="settings.php" method="post">
		<?php if($user->account_lock == 0){ ?>
			<div class="span2">
				<strong style="color: #E90909; font-size: 15px; position: absolute; margin: 4px 0 0 0;">Account Lock Inactive</strong>
			</div>
			<div class="span5">
				<input class="btn" type="submit" name="activate_lock" value="Activate Lock" />
			</div>
			<?php } else { ?>
			<div class="span2">
				<strong style="color: green; font-size: 15px; position: absolute; margin: 4px 0 0 0;">Account Lock Active</strong>
			</div>
			<div class="span5">
				<div class="input-append">
				  <input class="span5" name="code" type="text" placeholder="Unlock Code" value="<?php echo htmlentities($code); ?>">
				  <input class="btn" type="submit" name="deactivate_lock" value="Deactivate Lock" />
				  <input class="btn" type="submit" name="resend_code" value="Resend Code" />
				</div>
			</div>
		<?php } ?>
		</form>
	</div>

	<hr />

	<form action="settings.php" method="post">
	
		<h3>General Settings</h3>
	
		<div class="row-fluid">
			<div class="span3">
				<label>First Name</label>
		      <input type="text" class="span12" <?php echo $lock_status ?> name="first_name" required="required" value="<?php echo htmlentities($first_name); ?>" />
			</div>
			<div class="span3">
				<label>Last Name</label>
	      	<input type="text" class="span12" <?php echo $lock_status ?> name="last_name" required="required" value="<?php echo htmlentities($last_name); ?>" />
			</div>
			<div class="span3">
				<label>Gender</label>
				<select name="gender" class="span12 chzn-select" required="required" value="<?php echo $gender ?>">
					<option value="Male" <?php if($user->gender == 'Male') { echo 'selected="selected"';} else { echo ''; } ?>>Male</option>
					<option value="Female" <?php if($user->gender == 'Female') { echo 'selected="selected"';} else { echo ''; } ?>>Female</option> 
				</select>
			</div>
			<div class="span3">
				<label>Country</label>
				<select name="country" data-placeholder="Choose a Country..." class="span12 chzn-select" tabindex="2" value="<?php echo $country ?>">
					<option value="<?php echo $user->country ?>" selected="selected"><?php echo $user->country ?></option> 
					<option value="United States">United States</option> 
					<option value="United Kingdom">United Kingdom</option> 
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
		      <input type="email" class="span12" <?php echo $lock_status ?> name="email" required="required" value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span3">
				<label>Username</label>
	      	<input type="text" class="span12" name="username" disabled="disabled" required="required" value="<?php echo htmlentities($username); ?>" />
			</div>
			<div class="span3">
				<label>New Password</label>
		      <input type="password" class="span12" <?php echo $lock_status ?> name="password" value="<?php echo htmlentities($password); ?>" />
			</div>
			<div class="span3">
				<label>Repeat New Password</label>
	      	<input type="password" class="span12" <?php echo $lock_status ?> name="repeat_password" value="<?php echo htmlentities($repeat_password); ?>" />
			</div>
		</div>
	
		<div class="row-fluid">
			<div class="span3">
				<label>IP Protection</label>
		      <select name="whitelist" class="span12 chzn-select" value="<?php echo $whitelist ?>">
					<option value="1" <?php if($user->whitelist == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Enabled</option>
					<option value="0" <?php if($user->whitelist == '0') { echo 'selected="selected"';} else { echo ''; } ?>>Disabled</option> 
				</select>
			</div>
			<div class="span9">
				<label>IP Whitelist (127.0.0.1,127.0.0.2,127.0.0.3)</label>
	      	<input type="text" class="span12" <?php echo $lock_status ?> name="ip_whitelist" value="<?php echo htmlentities($ip_whitelist); ?>" />
			</div>
		</div>
	
		<?php if(ALLOW_REGISTRATIONS == "NO") { if (ALLOW_INVITES == "YES") :?>
		
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
					<td><a href="settings.php?delete_code=<?php echo $invite->code; ?>"><img src="assets/img/delete.png" alt="edit" class="edit_button" /></a></td>
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
	
		<hr />
	
		<h3>Tokens</h3>	
	
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Currently Active</th>
					<th>Currently Banked</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php echo number_format($user->tokens, 0, '.', ',') ?></strong> Tokens</td>
					<td><strong><?php echo number_format($user->bank_tokens, 0, '.', ',')?></strong> Tokens</td>
					<td><a href="buy_tokens.php" class="button">Buy Tokens</a> - <a href="token_bank.php" class="button">Deposit / Withdraw</a> - <a href="purchase.php" class="button">Purchase Access</a></td>
				</tr>
			</tbody>
		</table>
		
		<hr />
		
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
		
		<hr />
		
		<h3>Access Levels</h3>	
	
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Created</th>
					<th>Expires</th>
					<th>Expiry Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($access_levels as $data) : ?>
				<tr>
					<td><?php echo User::get_level_name($data->level_id); ?></td>
					<td><?php echo datetime_to_text($data->created); ?></td>
					<td><?php echo convert_boolean_sus($data->expires); ?></td>
					<td><?php echo datetime_to_text($data->expiry_date); ?></td>
				</tr>
				<?php endforeach; ?>

				<?php if(empty($access_levels)) : ?>
				<tr>
					<td colspan="5"><strong>This account has not got any current access levels. Purchase some <a href="purchase.php">here</a>.</a></strong></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	
		<div class="form-actions" style="text-align: center;">
			<input class="btn btn-primary" type="submit" name="submit" value="Update Settings" />
		</div>
	
	</form>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>