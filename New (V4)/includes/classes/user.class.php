<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class User {
	
	protected static $table_name="users";
	protected static $levels_table_name="user_levels";
	protected static $invites_table_name="invites";
	protected static $db_fields = array('id', 'user_id', 'first_name', 'last_name', 'gender', 'username', 'password', 'email', 'user_level', 'primary_group', 'activated', 'suspended', 'date_created', 'last_login', 'account_lock', 'signup_ip', 'last_ip', 'country', 'whitelist', 'ip_whitelist', 'tokens', 'bank_tokens', 'invited_by', 'created', 'expires', 'expiry_date', 'level_id', 'level_name', 'auto', 'datetime', 'ip_address', 'name', 'qty', 'status', 'redirect_page', 'access_time', 'time_type', 'amount', 'created', 'timed_access', 'expiry_date', 'expires', 'package_name','salt','oauth_provider','oauth_uid','price','failed_logins','last_failed','description','count','login_count','f_count','t_count','g_count','y_count','addr_number','addr_line1','addr_line2','addr_city','addr_county','addr_postcode','addr_country','telephone');
	
	// Table: users
	
	public $id;
	public $user_id;
	public $username;
	public $password;
	public $email;
	public $user_level;
	public $primary_group;
	public $activated;
	public $suspended;
	public $first_name;
	public $last_name;
	public $gender;
	public $date_created;
	public $last_login;
	public $account_lock;
	public $signup_ip;
	public $last_ip;
	public $country;
	public $whitelist;
	public $ip_whitelist;
	public $tokens;
	public $bank_tokens;
	public $level_expiry;
	public $expiry_datetime;
	public $invited_by;
	public $salt;
	public $oauth_provider;
	public $oauth_uid;
	public $login_count;
	public $addr_number;
	public $addr_line1;
	public $addr_line2;
	public $addr_city;
	public $addr_county;
	public $addr_postcode;
	public $addr_country;
	public $telephone;
	
	// Table: levels
	
	public $level_id;
	public $level_name;
	public $auto;
	
	// Table: user_levels
	
	public $created;
	public $timed_access;
	public $expiry_date;
	public $redirect_page;
	public $access_time;
	public $time_type;
	public $amount;
	public $expires;
	
	public $package_name;
	
	public $datetime;
	public $ip_address;
	
	public $name;
	public $qty;
	public $status;
	public $price;

	public $description;
	public $count;

	public $f_count;
	public $t_count;
	public $g_count;
	public $y_count;

  	public function full_name() {
	    if(isset($this->first_name) && isset($this->last_name)) {
	      return $this->first_name . " " . $this->last_name;
	    } else {
	      return "";
	    }
  	}

	public static function authenticate($username="", $password="") {
	    global $database;
	    global $session;

	    $failed = false;
	    $allowed = false;
	    $current_ip = $_SERVER['REMOTE_ADDR'];
	    $datetime = strftime("%Y-%m-%d %H:%M:%S", time());
	   	$bruteforce = self::find_by_sql("SELECT * FROM bruteforce_watchlist WHERE ip_address = '{$current_ip}' ");

	   	if(empty($bruteforce)){
	   		$allowed = true;
	   	} else if($bruteforce[0]->count < BRUTEFORCE_LIMIT){
			$allowed = true;
	   	} else if($bruteforce[0]->count >= BRUTEFORCE_LIMIT){
			if(strtotime('now') >= strtotime("+".BRUTEFORCE_TIMEOUT." minutes", strtotime($bruteforce[0]->datetime))){
				$database->query("DELETE FROM bruteforce_watchlist WHERE ip_address = '{$current_ip}' ");
				$allowed = true;
			}
	   	}

   		if($allowed === true){
			$username = $database->escape_value($username);
			if(PUSALT == "YES"){
				$data = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE username = '{$username}' LIMIT 1");
				if($data){
					$password = $database->escape_value(encrypt_password($password,$data[0]->salt));
				} else {
					$failed = true;
				}		
			} else {
				$password = $database->escape_value(encrypt_password($password));
			}
			if($failed === false){
				$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND password = '{$password}' LIMIT 1");
			    if(!empty($result_array)){
			    	$database->query("DELETE FROM bruteforce_watchlist WHERE ip_address = '{$current_ip}' ");
			    	return array_shift($result_array);
			    } else {
			    	$failed = true;
			    }
			} else if($failed === true){
		    	if(empty($bruteforce)){
		    		$database->query("INSERT INTO bruteforce_watchlist(id,ip_address,datetime,count) VALUES('','{$current_ip}','{$datetime}','1') ");
		    	} else {
		    		$new_count = $bruteforce[0]->count + 1;
		    		$database->query("UPDATE bruteforce_watchlist SET datetime = '{$datetime}', count = '{$new_count}' WHERE id = '{$bruteforce[0]->id}' ");
		    	}
		    	unset($bruteforce);
		    	unset($data);
		    	return false;
		    }
   		} else {
   			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>You have entered incorrect login details too many times. Login has been disabled for ".BRUTEFORCE_TIMEOUT." minutes</div>");
   		}

	}
	
	public static function check_user($table, $entry) {
	    global $database;
		 $table = $database->escape_value($table);
	    $entry = $database->escape_value($entry);

	    $sql  = "SELECT * FROM ".self::$table_name." WHERE {$table} = '{$entry}' LIMIT 1";
	    $result_array = self::find_by_sql($sql);
		 return !empty($result_array) ? true : false;
	}
	
	public static function check_activation($username) {
    global $database;
	$username = $database->escape_value($username);

    // $sql  = "SELECT '{$username}' FROM users WHERE {$table} = {$entry} LIMIT 1";
	$sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND activated = '1' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_if_suspended($username) {
    global $database;
	$username = $database->escape_value($username);

    // $sql  = "SELECT '{$username}' FROM users WHERE {$table} = {$entry} LIMIT 1";
	$sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND suspended = '1' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_current_password($username, $password) {
    global $database;
	$username = $database->escape_value($username);
	$password = $database->escape_value($password);

	// $sql = "SELECT * FROM users WHERE username = '{$username}' AND password = {$password}";
	$sql = "SELECT * FROM  ".self::$table_name." WHERE username = '{$username}' AND password = '{$password}' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_whitelist($username) {
    global $database;
	$username = $database->escape_value($username);

    // $sql  = "SELECT '{$username}' FROM users WHERE {$table} = {$entry} LIMIT 1";
	$sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND whitelist = '1' LIMIT 1";
	
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_login($username, $password, $current_ip, $remember) {
		// instantiate Session Class
		$session = new Session();
		
	    // Check database to see if username/password exist.
		$found_user = self::authenticate($username, $password);

		// lets see if the users account has been activated
	    $activated = self::check_activation($username);
		// lets see if the users account has been suspended
	    $suspended = self::check_if_suspended($username);
	
		// lets see if the users account has has ip whitelist enabled
		$whitelist = self::check_whitelist($username);

		if($found_user) {
			if($activated){
			   	if(!$suspended){
					if($whitelist) {
						global $database;
						$sql = "SELECT ip_whitelist FROM users WHERE username = '{$username}'";
						$result = $database->query($sql);
						$array = $database->fetch_array($result);
						$exp = $array['ip_whitelist'];
						// print_r($exp);
						$whitelist = explode(",", $exp);
						// print_r($whitelist);
						if (in_array($current_ip, $whitelist)) {
							// echo "success";
							$session->login($found_user, $remember);
							$sql = "UPDATE ".self::$table_name." SET last_ip = '{$current_ip}' WHERE username = '{$username}' ";
							$database->query($sql);
							// redirect_to("index.php");
							// return "true";
							$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
							return User::get_login_redirect($user->primary_group);
						} else {
							// echo "failure";
							$session->message("<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>This account currently has IP Protection enabled and your IP ($current_ip) is not in the whitelist.</div>");
							return "false";
						}
					} else {
						$session->login($found_user, $remember);
						global $database;
						$sql = "UPDATE ".self::$table_name." SET last_ip = '{$current_ip}' WHERE username = '{$username}' ";
						$database->query($sql);
				      // redirect_to("index.php");
						// return "true";
						$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
						return User::get_login_redirect($user->primary_group);
					}
				 } else {
					$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Your account has been suspended, please contact support.</div>");
					return "false";
				 }
			 } else {
				$session->message("<div class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>×</button>Your account has not yet been activated, please check your email. To resend the code <a href='activate.php'>click here.</a></div>");
				return "false";
			 }
		} else {
			if(!isset($_SESSION['message'])){
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Username/Password combination incorrect.</div>");
			}
			return "false";
		}
   }

	public static function check_admin_login($username, $password) {
		// instantiate Session Class
		$session = new Session();

		// Check database to see if username/password exist.
		$found_user = self::authenticate($username, $password);

		if($found_user) {
			$_SESSION['admin_access'] = true;
			return "true";
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Username/Password combination incorrect.</div>");
			return "false";
		}
	}

	public static function get_login_redirect($primary_group){
		$data = self::find_by_sql("SELECT redirect_page FROM user_levels WHERE level_id = '{$primary_group}' LIMIT 1");
		return $data[0]->redirect_page;
  	}
	
	public function create_account($username, $password, $email, $first_name, $last_name, $signup_ip, $country, $gender, $invite_code="", $addr_number="", $addr_line1="", $addr_line2="", $addr_city="", $addr_county="", $addr_postcode="", $telephone=""){
		global $database;
		$session = new Session();
		// Genetate the users ID.
		$user_id = generate_id();
		$plain_password = $password;
		
		$sql = "SELECT * FROM ".self::$levels_table_name." WHERE auto = '1'";
		$query = $database->query($sql);
		$row = $database->fetch_array($query);
		$user_level = $row['level_id'];
		
		$flag = false;
		//until flag is false
		while ($flag == false){
			//check if the user id exists
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			//if it does try again till you find an id that does not exist
			if ($rows){
				$user_id = generate_id();
			}else{
				//if it does not exist, exit the loop
				$flag = true;
			}
		}
		if ($flag == true){
			$invited_by = current(explode('_',$invite_code));
			//insert into db the data
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			
			if(PUSALT == "YES"){
				$salt = create_salt();
				$password = encrypt_password($password, $salt);
			} else {
				$salt = "";
				$password = encrypt_password($password);
			}
			
			if(VERIFY_EMAIL == "NO"){$activated = 1;} else if(VERIFY_EMAIL == "YES"){$activated = 0;}


			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '$user_level', '$user_level', '$activated', '0', '$datetime', '', '0', '$signup_ip', '', '$country', '0', '', '0', '0', '$invited_by','$salt','','','0','{$addr_number}','{$addr_line1}','{$addr_line1}','{$addr_city}','{$addr_county}','{$addr_postcode}','{$telephone}')";
			$database->query($sql);
			
			if (ALLOW_REGISTRATIONS == "NO") {
				$sql = "DELETE FROM ".self::$invites_table_name." WHERE code = '{$invite_code}' ";
				$database->query($sql);
			}
						
			// Send and email to the user.
			if(VERIFY_EMAIL == "NO") {
				// Initialize functions.
				$email_class = new Email();

				// Email sent to the user if logged in.
				// $from = SITE_EMAIL;
				// $subject = "Welcome to ".SITE_NAME." ";
				
				// $message = $email_class->email_template('registration_success', "$plain_password", "$username", "", "");
				// $email_class->send_email($email, $from, $subject, $message);

				unset($_SESSION['email_data']);
				$_SESSION['email_data'] = array(
					"WWW" => WWW,
					"SITE_NAME" => SITE_NAME,
					"username" => $username,
					"plain_password" => $plain_password
				);
				
				$email_data = Email::email_template_data(3);		
				$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);

			} else if(VERIFY_EMAIL == "YES") {
				//$activation_hash = Activation::set_activation_link($email)
				Activation::set_activation_link($plain_password, $username, $email);
			}
			
			if(!empty($invite_code) && TOKEN_CREDIT > 0){
				$data = $database->fetch_array($database->query("SELECT user_id,tokens FROM users WHERE username = '{$invited_by}' LIMIT 1"));
				self::add_i_tokens($data['user_id'], $data['tokens'], TOKEN_CREDIT);
			}
			
			unset($_SESSION['register']);

			// Create the message that will be displayed on the login screen once the user has been redirected.
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your account has been created successfully. Please check your email for activation link.</div>");
			
			// redirect the user to the login page.
			redirect_to('signin.php');
		}
	}
	
	public function create_oauth_account($username, $email, $first_name, $last_name, $gender, $oauth_provider, $oauth_id){
		global $database;
		$session = new Session();
		// Genetate the users ID.
		$user_id = generate_id();
		
		$sql = "SELECT * FROM ".self::$levels_table_name." WHERE auto = '1'";
		$query = $database->query($sql);
		$row = $database->fetch_array($query);
		$user_level = $row['level_id'];
		
		// if(PUSALT == "YES"){
		// 	$salt = create_salt();
		// 	$password = encrypt_password($plain_password, $salt);
		// } else {
			$salt = "";
		// }
		
		$flag = false;
		//until flag is false
		while ($flag == false){
			//check if the user id exists
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			//if it does try again till you find an id that does not exist
			if ($rows){
				$user_id = generate_id();
			}else{
				//if it does not exist, exit the loop
				$flag = true;
			}
		}
		if ($flag == true){
			//insert into db the data
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$password = encrypt_password($email.$username);
			$signup_ip = $_SERVER['REMOTE_ADDR'];
			// $sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '$user_level', '$user_level', '1', '0', '$datetime', '$datetime', '0', '$signup_ip', '$signup_ip', '$country', '0', '', '0', '0', '','$salt','$oauth_provider','$oauth_id','0')"; // old one, without address fields.
			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '$user_level', '$user_level', '1', '0', '$datetime', '$datetime', '0', '$signup_ip', '$signup_ip', '$country', '0', '', '0', '0', '','$salt','$oauth_provider','$oauth_id','0','','','','','','','')";
			$database->query($sql);
						
		}
	}

	public static function find_username_by_id($user_id) {
    $result_array = self::find_by_sql("SELECT username FROM ".self::$table_name." WHERE user_id = '{$user_id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
   }
	
	public static function downgrade_access($id, $user_id, $level_id, $access_levels, $redirect="dashboard.php?page=settings"){
		global $database;
		
		$database->query("DELETE FROM levels WHERE id = '{$id}' AND user_id = '{$user_id}' ");
		
		$access_levels = explode(",", $access_levels);
		$new_access_levels = array_diff($access_levels, array($level_id));
		$new_access_levels = implode(",", $new_access_levels);
		
		if($new_access_levels == ""){
			$row = $database->fetch_array($database->query("SELECT * FROM ".self::$levels_table_name." WHERE auto = '1'"));
			$user_level = $row['level_id'];
			$database->query("UPDATE users SET user_level = '{$user_level}' WHERE user_id = '{$user_id}' ");
		} else {
			$database->query("UPDATE users SET user_level = '{$new_access_levels}' WHERE user_id = '{$user_id}' ");
		}

		redirect_to($redirect);
	}
	
	public static function purchase_access($user_id, $id){
		global $database;
		$session = new Session;
		
		$user = User::find_by_id($user_id);
		$access_package = self::find_by_sql("SELECT * FROM user_levels WHERE level_id = '{$id}' LIMIT 1");
		$package = $access_package[0];
		
		if($user->tokens < $package->amount){
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you don't have enough active tokens for this package.</div>");
		} else {
			$current_access = explode(",", $user->user_level);
			if(in_array($id, $current_access)){
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you already have this access level.</div>");
			} else {
				$new_tokens = $user->tokens - $package->amount;
				array_push($current_access, $id);
				$new_access = implode(",", $current_access);
				$database->query("UPDATE users SET user_level = '{$new_access}', tokens = '{$new_tokens}' WHERE user_id = '{$user->user_id}' ");
				$datetime = date('Y-m-d h:i:s', time());
				if($package->timed_access == 1){
					$time = "+".$package->access_time." ".User::convert_time_type($package->time_type);
					$new_date = strtotime($time, strtotime($datetime));
					$expiry_date = date( 'Y-m-d H:i:s', $new_date );
				} else {
					$expiry_date = "0000-00-00 00:00:00";
				}
				$database->query("INSERT INTO levels VALUES('','{$user_id}','{$id}','{$datetime}','{$package->timed_access}','{$expiry_date}')  ");
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Thank you! Your purchase is now active.</div>");
			}
		}
		redirect_to("dashboard.php?page=settings");
	}

	public static function add_access($user_id, $id){
		global $database;
		$session = new Session;
		
		$user = User::find_by_id($user_id);
		$access_package = self::find_by_sql("SELECT * FROM user_levels WHERE level_id = '{$id}' LIMIT 1");
		$package = $access_package[0];
		
		$current_access = explode(",", $user->user_level);
		array_push($current_access, $id);
		$new_access = implode(",", $current_access);
		$database->query("UPDATE users SET user_level = '{$new_access}' WHERE user_id = '{$user->user_id}' ");
		$datetime = date('Y-m-d h:i:s', time());

		if($package->timed_access == 1){
			$time = "+".$package->access_time." ".User::convert_time_type($package->time_type);
			$new_date = strtotime($time, strtotime($datetime));
			$expiry_date = date( 'Y-m-d H:i:s', $new_date );
		} else {
			$expiry_date = "0000-00-00 00:00:00";
		}

		$database->query("INSERT INTO levels VALUES('','{$user_id}','{$id}','{$datetime}','{$package->timed_access}','{$expiry_date}')  ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Thank you! Your purchase is now active.</div>");
		// redirect_to("dashboard.php?page=settings");
	}

	public static function add_purchase_history($user_id, $amount, $description){
		global $database;
		$datetime = date('Y-m-d H:i:s');
		$database->query("INSERT INTO purchase_history VALUES('','$user_id','$amount','$description','$datetime')");
	}
	
	public static function get_purchase_history($user_id=0) {
		return self::find_by_sql("SELECT * FROM purchase_history WHERE user_id = '{$user_id}'");
	}

	public static function get_account_history($user_id=0) {
		return self::find_by_sql("SELECT * FROM account_history WHERE user_id = '{$user_id}'");
	}

	public static function get_user_gift_cards() {
		return self::find_by_sql("SELECT * FROM gift_cards WHERE user_id = '{$user_id}'");
	}

	public static function count_invites($username){
		global $database;
		$result = $database->query("SELECT COUNT(*) FROM users WHERE invited_by = '{$username}'");
		$row = $database->fetch_array($result);
    	return array_shift($row);
	}

	public static function get_site_levels(){
		return self::find_by_sql("SELECT level_id,level_name FROM user_levels");
	}
	
	public static function get_purch_levels(){
		return self::find_by_sql("SELECT * FROM user_levels WHERE purchasable = '1' ");
	}
	
	public static function get_level_name($level_id){
		$return = self::find_by_sql("SELECT level_name FROM user_levels WHERE level_id = '{$level_id}' ");
		return $return[0]->level_name;
	}
	
	public static function get_user_levels($user_id){
		return self::find_by_sql("SELECT * FROM levels WHERE user_id = '{$user_id}' ");
	}
	
	public static function convert_time_type($type){
		if($type == 0){
			return "Days";
		} else if($type == 1){
			return "Weeks";
		} else if($type == 2){
			return "Months";
		}
	}
	
	// Tokens
	
	public static function get_token_history($user_id=0) {
		return self::find_by_sql("SELECT * FROM token_history WHERE user_id= '{$user_id}'");
	}
	
	public static function remove_tokens($user_id,$required,$message){
		global $database;
		
		$current_tokens = User::get_current_tokens($user_id);
		
		$new_tokens = $current_tokens - $required;
		
		$sql = "UPDATE ".self::$table_name." SET tokens = '{$new_tokens}' WHERE user_id = '{$user_id}' ";
		$database->query($sql);
		
		// add token history			
		$datetime = date('Y-m-d H:i:s');
		$sql = "INSERT INTO token_history VALUES('','$user_id','$required','$message','$datetime','d')";
		$database->query($sql);
	}

	public static function add_tokens($user_id, $tokens){
		global $database;
		$session = new Session();
		
		$current_tokens = User::get_current_tokens($user_id);
		
		$new_tokens = $current_tokens + $tokens;
		
		$sql = "UPDATE ".self::$table_name." SET tokens = '{$new_tokens}' WHERE user_id = '{$user_id}' ";
		$database->query($sql);
		
		// add token history			
		$datetime = date('Y-m-d H:i:s');
		// $sql = "INSERT INTO token_history VALUES('','$user_id','$tokens','Purchased Tokens','$datetime','c')";
		$sql = "INSERT INTO token_history VALUES('','$user_id','$tokens','Purchased Tokens','$datetime','c')";
		$database->query($sql);
		

		// $datetime = date('Y-m-d H:i:s');
		// $database->query("INSERT INTO token_history VALUES('','$user_id','$tokens','Purchased Tokens','$datetime')");

		// $session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>{$token_package} tokens have been successfully applied to your account.</div>");
		// redirect_to($location);
		
	}
	
	public static function add_i_tokens($user_id, $current_tokens, $token_package, $location = NULL){
		global $database;
		$session = new Session();
		
		$new_tokens = $current_tokens + $token_package;
		
		$sql = "UPDATE ".self::$table_name." SET tokens = '{$new_tokens}' WHERE user_id = '{$user_id}' ";
		$database->query($sql);
		
		// add token history			
		$datetime = date('Y-m-d H:i:s');
		$sql = "INSERT INTO token_history VALUES('','$user_id','$token_package','Invited User','$datetime','c')";
		$database->query($sql);		
	}

	
	public static function token_bank($user_id, $current_tokens, $token_bank, $tokens, $bw, $location){
		global $database;
		$session = new Session();		
		
		// $tokens = preg_replace("/[^0-9]/", '', $tokens);
				
		if($bw == "bank"){
			$active_tokens = $current_tokens - $tokens;
			$banked_tokens = $token_bank + $tokens;
			$msg = "deposited";
		} else if($bw == "withdraw"){
			$banked_tokens = $token_bank - $tokens;
			$active_tokens = $current_tokens + $tokens;
			$msg = "withdrawn";
		}
		
		$sql = "UPDATE ".self::$table_name." SET tokens = '{$active_tokens}', bank_tokens = '{$banked_tokens}' WHERE user_id = '{$user_id}' ";
		$database->query($sql);
		
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>".number_format($tokens, 0, '.', ',')." tokens have been successfully {$msg}.</div>");
		redirect_to($location);
	}
	
	public static function get_package_data($id){
		return self::find_by_sql("SELECT * FROM token_packages WHERE id = '{$id}' LIMIT 1");
	}
	
	public static function get_token_packages(){
		return self::find_by_sql("SELECT * FROM token_packages ");
	}
	
	public static function get_current_tokens($user_id){
		$return_data = self::find_by_sql("SELECT tokens FROM users WHERE user_id = '{$user_id}' ");
		return $return_data[0]->tokens;
	}
	
	public static function create_package($name,$qty,$status){
		global $database;
		$session = new Session;
		$database->query("INSERT INTO token_packages VALUES('','{$name}','{$status}','{$qty}')");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Token package has been created.</div>");
		redirect_to("tokens.php");		
	}
	
	public static function edit_token_package($id,$name,$qty,$status){
		global $database;
		$session = new Session;
		$database->query("UPDATE token_packages SET name = '{$name}', qty = '{$qty}', status = '{$status}' WHERE id = '{$id}' ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Token package has been updated.</div>");
		redirect_to("tokens.php");		
	}
	
	public static function delete_token_package($id){
		global $database;
		global $session;
		if($database->query("DELETE FROM token_packages WHERE id = '{$id}' ")){
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Token package has been deleted.</div>");
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Something went wrong, please try again.</div>");
		}
		redirect_to("tokens.php");		
	}
	
	public static function convert_token_status($status){
		if($status == 0){
			return "Hidden";
		} else if($status == 1){
			return "Active";
		}
	}
	
	public static function convert_tokens($type){
		if($type == "c"){
			return "Credit";
		} else if($type == "d"){
			return "Debit";
		}
	}
	
	public static function transfer_tokens($user_id, $username, $amount){
		global $database;
		$session = new Session();
		if($amount > User::get_current_tokens($user_id)){
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you don't have enough tokens to do that.</div>");
		} else {
			$rec_id = self::find_id_by_username($username);
			if(empty($rec_id->user_id)){
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, that user could not be found.</div>");
			} else {
				$new_tokens = User::get_current_tokens($user_id) - $amount;
				$database->query("UPDATE users SET tokens = '{$new_tokens}' WHERE user_id = '{$user_id}' ");
				$datetime = date('Y-m-d H:i:s');
				$database->query("INSERT INTO token_history VALUES('','$user_id','$amount','Token Transfer to $username','$datetime','d')");
				
				$sen_id = self::find_username_by_id($user_id);
				$new_tokens = User::get_current_tokens($rec_id->user_id) + $amount;
				$database->query("UPDATE users SET tokens = '{$new_tokens}' WHERE user_id = '{$rec_id->user_id}' ");
				$datetime = date('Y-m-d H:i:s');
				$database->query("INSERT INTO token_history VALUES('','$rec_id->user_id','$amount','Token Transfer from $sen_id->username','$datetime','c')");
				
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Tokens have been transferred.</div>");
			}
		}
		
		redirect_to("dashboard.php?page=overview");
	}
	
	public static function get_access_logs($user_id){
		return self::find_by_sql("SELECT * FROM access_logs WHERE user_id = '{$user_id}' ORDER BY datetime DESC ");
  	}
	
	public static function count_all_levels() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM user_levels";
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function update_setting($name, $value, $user_id=""){
		global $database;
		if($name == "password"){
			if(PUSALT == "YES"){
				if($user_id == ""){$user_id = $_SESSION['masdyn']['ams']['user_id'];}
				$user = User::find_by_id($user_id);
				$value = encrypt_password($value, $user->salt);
			} else {
				$value = encrypt_password($value);
			}
		}
		if($user_id == ""){
			$user_id = $_SESSION['masdyn']['ams']['user_id'];
		}
		$database->query("UPDATE users SET $name = '{$value}' WHERE user_id = '{$user_id}' ");
		return "true";
	}


	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}
  	
	public static function count_by_sql($sql) {
	  global $database;
	  // $sql = "SELECT COUNT(*) FROM user_levels";
     $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	public static function find_id_by_username($username) {
    $result_array = self::find_by_sql("SELECT user_id FROM ".self::$table_name." WHERE username = '{$username}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
   }

  	public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }
  	
	public static function find_by_username($username) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE username = '{$username}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }

  	public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
    }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".self::$table_name;
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
    	$object = new self;
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}

}

?>