<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("includes/inc_files.php"); 

if(isset($_SESSION['masdyn']['ams']['user_id'])){
	$user_id = $_SESSION['masdyn']['ams']['user_id'];
} else {
	$user_id = "";
}

if(isset($_POST['page'])){

	$page = $database->escape_value(htmlspecialchars($_POST['page'], ENT_QUOTES));

	if($page == "login"){
		if(isset($_POST['action'])){
			if($_POST['action'] == "login"){
				if(isset($_POST['username']) && isset($_POST['password'])){
					$username = $database->escape_value(htmlspecialchars($_POST['username'], ENT_QUOTES));
					$password = $database->escape_value(htmlspecialchars($_POST['password'], ENT_QUOTES));
					$remember_me = $database->escape_value(htmlspecialchars($_POST['remember_me'], ENT_QUOTES));
					$current_ip = $_SERVER['REMOTE_ADDR'];
					$return = User::check_login($username, $password, $current_ip, $remember_me);
					if($return == "false"){
						echo "false";
					} else {
						echo $return;
					}
				} else {
					echo "false";
				}
			} else if($database->escape_value(htmlspecialchars($_POST['action'], ENT_QUOTES)) == "update_msg"){
				echo output_message($message);
			}
		}
	} else if($page == "settings"){
		if(isset($_POST['name'])){
			$name = $database->escape_value(htmlspecialchars($_POST['name'], ENT_QUOTES));
			$value = $database->escape_value(htmlspecialchars($_POST['value'], ENT_QUOTES));
			$allowed = array('first_name','last_name','gender','username','password', 'email','country','ip_protection','ip_whitelist');
			if(in_array($name, $allowed)){
				$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
				if($user->account_lock == 0){
					if($name == "username" || $name == "email"){
						$user_id = $_SESSION['masdyn']['ams']['user_id'];
						$data = User::find_by_sql("SELECT ".$name." FROM users WHERE ".$name." = '{$value}' LIMIT 1 ");
						if(!empty($data)){
							$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, that ".$name." has already been taken. Please choose another.</div>");
							echo false;
							$flag = false;
						} else {
							$flag = true;
						}
					} else if($name == "ip_protection"){
						$user_id = $_SESSION['masdyn']['ams']['user_id'];
						if(empty($user->ip_whitelist)){
							$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, we can't enable IP Protection on this account while your whitelist is empty.</div>");
							echo false;
							$flag = false;
						} else {
							$flag = true;
						}
					} else {
						$flag = true;
					}
					if($flag == true){
						$clean_name = str_replace("_", " ", $name);
						if($name == "ip_protection"){
							$name = "whitelist";
							if($value == "Disabled"){
								$value = "0";
							} else {
								$value = "1";
							}
						}
						User::update_setting($name,$value);
						$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your ".$clean_name." has successfully updated.</div>");
						echo "success";
					}
				} else {
					$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, we are unable to change your settings while your account lock is active. Please deactivate your account lock and try again.</div>");
					echo false;
				}
			}
		} if(isset($_POST['password'])){
			$password = $database->escape_value(htmlspecialchars($_POST['password'], ENT_QUOTES));
			User::update_setting("password",$password);
		} else if(isset($_POST['get_select'])){
			$select = $database->escape_value(htmlspecialchars($_POST['get_select'], ENT_QUOTES));
			if($select == "ip_protection"){
				$ip_protection = $database->escape_value(htmlspecialchars($_POST['ip_protection'], ENT_QUOTES));
				echo '<div class="styled-select"><select name="ip_protection" id="ip_protection" class="form-control chzn-select" required="required" value="<?php echo $ip_protection ?>"><option value="Disabled" ';
					if($ip_protection == '0') { echo 'selected="selected"';} else { echo ''; }
				echo '>Disabled</option><option value="Enabled"';
					if($ip_protection == '1') { echo 'selected="selected"';} else { echo ''; }
				echo '>Enabled</option></select></div>';
			} else if($select == "gender"){
				$gender = clean_value($_POST['gender']);
				echo '<select name="gender" id="gender" class="form-control chzn-select" required="required" value="<?php echo $gender ?>"><option value="Male" ';
					if($gender == 'Male') { echo 'selected="selected"';} else { echo ''; }
				echo '>Male</option><option value="Female"';
					if($gender == 'Female') { echo 'selected="selected"';} else { echo ''; }
				echo '>Female</option></select>';
			} else if($select == "country"){
				$country = clean_value($_POST['country']);
				echo '<select name="country" id="country" data-placeholder="Choose a Country..." class="form-control chzn-select" tabindex="2" value="<?php echo $country ?>">
					<option value="'.$country.'" selected="selected">'.$country.'</option>
					'.display_countries().'
				</select>';
			}
		}
	} else if($page == "misc"){
		if($database->escape_value(htmlspecialchars($_POST['action'], ENT_QUOTES)) == "update_msg"){
			echo output_message($message);
		}
	} else if($page == "gift_cards"){
		if(isset($_POST['action'])){
			$action = $database->escape_value(htmlspecialchars($_POST['action'], ENT_QUOTES));

			if($action == "purchase_ac"){

				$id = $database->escape_value(htmlspecialchars($_POST['id'], ENT_QUOTES));
				$name = $database->escape_value(htmlspecialchars($_POST['name'], ENT_QUOTES));
				$from = $database->escape_value(htmlspecialchars($_POST['from'], ENT_QUOTES));
				$email_address = $database->escape_value(htmlspecialchars($_POST['email_address'], ENT_QUOTES));
				$public_id = $database->escape_value(htmlspecialchars($_POST['public_id'], ENT_QUOTES));
				$note = $database->escape_value(htmlspecialchars($_POST['note'], ENT_QUOTES));

				// error_log($id.", ".$name.", ".$from.", ".$email_address.", ".$public_id.", ".$note);
				echo Gift_Card::purchase_gift_card(0,$id,$name,$from,$email_address,$public_id,$note);

			} else if($action == "activate"){
				$code = $database->escape_value(htmlspecialchars($_POST['code'], ENT_QUOTES));
				echo Gift_Card::activate_gift_card($user_id,$code);

			}

		}
		
	} else if($page == "global"){
		if(isset($_POST['action'])){
			$action = $database->escape_value(htmlspecialchars($_POST['action'], ENT_QUOTES));

			if($action == "purchase_access"){

				$id = $database->escape_value(htmlspecialchars($_POST['id'], ENT_QUOTES));

				echo Content_Protection::purchase_access($id);

			}

		}
		
	} else {
		echo "Page ($page) Not Found";
	}
}

?>