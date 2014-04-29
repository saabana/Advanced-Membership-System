<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class Admin {
	
	protected static $table_name="users";
	protected static $levels_table_name="user_levels";
	protected static $account_lock_table="account_locks";
	protected static $activation_table="activation_links";
	protected static $password_table="password_links";
	protected static $invites_table="invites";
	protected static $staff_notes_table="staff_notes";
	protected static $db_fields = array('id', 'user_id', 'first_name', 'last_name', 'gender', 'username', 'password', 'email', 'user_level', 'activated', 'suspended', 'date_created', 'last_login', 'account_lock', 'signup_ip', 'last_ip', 'country', 'whitelist', 'ip_whitelist', 'tokens', 'bank_tokens', 'primary_group', 'created', 'expires', 'expiry_date', 'level_id', 'id', 'message', 'date','count','f_count','t_count','g_count','login_count');
	
	public $username;
	public $password;
	public $email;
	public $user_level;
	public $activated;
	public $suspended;
	public $first_name;
	public $last_name;
	public $gender;
	public $account_lock;
	public $country;
	public $whitelist;
	public $ip_whitelist;
	public $tokens;
	public $bank_tokens;
	public $primary_group;
	public $login_count;
	
	public $user_id;
	public $staff_name;
	public $staff_message;
	public $staff_date;
	
	public $id;
	public $level_id;
	
	public $message;
	public $date;
	
	public $created;
	public $expires;
	public $expiry_date;

	public $count;
	public $f_count;
	public $t_count;
	public $g_count;
	public $y_count;
	
	public function create_account($username, $email, $first_name, $last_name, $signup_ip, $country, $gender, $send_welcome_email){
		global $database;
		global $session;
		$user_id = generate_id();
		$password = Reset_Password::generate_password();
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
			// $invited_by = current(explode('_',$invite_code));
			//insert into db the data
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$signup_ip = "SERVER";
			
			if(PUSALT == "YES"){
				$salt = create_salt();
				$password = encrypt_password($plain_password, $salt);
			} else {
				$salt = "";
				$password = encrypt_password($plain_password);
			}

			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '$user_level', '$user_level', '1', '0', '$datetime', '', '0', '$signup_ip', '', '$country', '0', '', '', '', '','$salt','','','0')";
			$database->query($sql);

						
			// Send and email to the user.
			if($send_welcome_email == 1) {
				// Initialize functions.
				$email_class = new Email();

				unset($_SESSION['email_data']);
				$_SESSION['email_data'] = array(
					"WWW" => WWW,
					"SITE_NAME" => SITE_NAME,
					"username" => $username,
					"plain_password" => $plain_password
				);
				
				$email_data = Email::email_template_data(3);		
				$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The account has been created successfully. An email has been dispatched to the user.</div>");
			} else {
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The account has been created successfully. No welcome email has been dispatched. The password for the account is: $plain_password.</div>");
			}
			
			redirect_to('users.php');
		}
	}

	public function update_account($user_id, $user_level, $activated, $suspended, $tokens, $bank_tokens, $primary_group){
		global $database;
		global $session;

		$database->query("UPDATE users SET user_level = '{$user_level}', activated = '{$activated}', suspended = '{$suspended}', tokens = '{$tokens}', bank_tokens = '{$bank_tokens}', primary_group = '{$primary_group}' WHERE user_id = '{$user_id}' ");
		
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>User has been updated.</div>");
	
		redirect_to('user_dashboard.php?page=admin&user_id='.$user_id.'');
	}
	
	public static function delete_account($user_id, $email) {
		global $database;
		global $session;

		$database->query("DELETE FROM access_logs WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM account_locks WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM activation_links WHERE email = '{$email}' ");
		$database->query("DELETE FROM invites WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM password_links WHERE email = '{$email}' ");
		$database->query("DELETE FROM purchase_history WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM staff_notes WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM token_history WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM levels WHERE user_id = '{$user_id}' ");
		$database->query("DELETE FROM users WHERE user_id = '{$user_id}' ");

		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The user has been successfully deleted.</div>");

		redirect_to('users.php');
	}
	
	private static function deactivate_lock($user_id, $location) {
		global $database;
		$sql = "UPDATE ".self::$table_name." SET account_lock = '0' WHERE user_id = '{$user_id}'";
		$database->query($sql);
		self::delete_unlock_code($user_id, $location);
	}
	
	private static function delete_unlock_code($user_id, $location) {
		// Delete activation link
		global $database;
		$sql = "DELETE FROM ".self::$account_lock_table." WHERE user_id = '{$user_id}'";
		$database->query($sql);
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Account settings have been unlocked.</div>");
		redirect_to($location);
	}
	
	// public static function check_lock_status($user_id, $location) {
	// 	global $database;
	// 	//check if the account is locked.
	// 	$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = {$user_id} AND account_lock = '1'";
	// 	$query = $database->query($sql);
	// 	$rows = $database->num_rows($query);
	// 	if ($rows) {
	// 		// Check their is an unlock code associated with the account
	// 		$sql = "SELECT * FROM ".self::$account_lock_table." WHERE user_id = '{$user_id}'";
	// 		$query = $database->query($sql);
	// 		$rows = $database->num_rows($query);
	// 		if ($rows) {
	// 			// Ok you can now deactivate the account lock.
	// 			self::deactivate_lock($user_id, $location);
	// 		} else {
	// 			// No unlock code found.
	// 			$session = new Session();
	// 			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The unlock code <strong>{$code}</strong> does not match the one we have in our database for your account.</div>");
	// 			redirect_to($location);
	// 		}
	// 	} else {
	// 		// Throw error back to the user.
	// 		$session = new Session();
	// 		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The account could not be unlocked.</div>");
	// 		redirect_to($location);
	// 	}
	// }

  	public static function check_lock_status($user_id, $location) {
		global $database;
		//check if the account is locked.
		$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = {$user_id} AND account_lock = '1'";
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		if ($rows) {
			// Check their is an unlock code associated with the account
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}' AND account_lock = '1'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			if ($rows) {
				// Ok you can now deactivate the account lock.
				self::deactivate_lock($user_id, $location);
			} else {
				// No unlock code found.
				$session = new Session();
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>The unlock code <strong>{$code}</strong> does not match the one we have in our database for your account.</div>");
				redirect_to($location);
			}
		} else {
			// Throw error back to the user.
			$session = new Session();
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Your account could not be unlocked.</div>");
			redirect_to($location);
		}
	}
	
	public static function send_new_password($email, $location) {
		global $database;
		
		$plain_password = Reset_Password::generate_password();
		
		if(PUSALT == "YES"){
			$data = User::find_by_sql("SELECT * FROM users WHERE email = '{$email}' LIMIT 1");
			$new_password = encrypt_password($plain_password, $data[0]->salt);
		} else {
			$new_password = encrypt_password($plain_password);
		}
		
		$sql = "UPDATE ".self::$table_name." SET password = '{$new_password}' WHERE email = '{$email}'";
		$database->query($sql);
		
		// Email sent to the user.
		$email_class = new Email();

		unset($_SESSION['email_data']);
		$_SESSION['email_data'] = array(
			"WWW" => WWW,
			"SITE_NAME" => SITE_NAME,
			"username" => $username,
			"plain_password" => $plain_password
		);
		
		$email_data = Email::email_template_data(5);		
		$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>A new password has been emailed to {$email}.</div>");
		
		redirect_to($location);
	}
	
	public static function email_user($email, $subject, $message) {
		global $database;
		
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user.
		// $from = SITE_EMAIL;
		
		// Send and email to the user.
		// $content = $email_class->email_template('custom_email', "", "", "$email", "", $message);
		
		// $email_class->send_email($email, $from, $subject, $content);
		
		// unset($_SESSION['email_data']);
		// $_SESSION['email_data'] = array(
		// 	"WWW" => WWW,
		// 	"SITE_NAME" => SITE_NAME,
		// 	"username" => $username,
		// 	"plain_password" => $plain_password
		// );
		
		// $email_data = Email::email_template_data(3);		
		$email_class->send_email($email, SITE_EMAIL, $subject, $message);

		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Email successfully sent to {$email}</div>");
		redirect_to("users.php");
	}
	
	public static function email_group($group_id, $subject, $message) {
		global $database;
		
		$email_class = new Email();
		
		$group_data = self::find_by_sql("SELECT email FROM users WHERE find_in_set('{$group_id}',user_level) ");
		$mail_list = "";
		foreach ($group_data as $data) {
			$mail_list .= $data->email.", ";
		}
		$mail_list = substr($mail_list, 0, -2);

		$email_class->send_email($mail_list, SITE_EMAIL, $subject, $content);

		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Email successfully sent to all uses within this group.</div>");
		redirect_to("group_settings.php?group_id=$group_id");
	}
	

	public static function count_all_users() {
		global $database;
		$sql = "SELECT COUNT(id) FROM ".self::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function count_users($var="", $var2="") {
		global $database;
		$sql = "SELECT COUNT($var) FROM ".self::$table_name." WHERE {$var} = '{$var2}'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function count_all_users_in_group($user_level) {
		global $database;
		$sql = "SELECT COUNT(user_level) AS UserLevel FROM ".self::$table_name." WHERE user_level LIKE '%{$user_level}%'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
	
	public static function count_all_groups() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$levels_table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function find_all_groups() {
		return self::find_by_sql("SELECT * FROM ".self::$levels_table_name);
  	}
  	
	public function create_group($level_name, $auto, $redirect_page, $purchasable, $amount, $price, $timed_access, $time_type, $access_time){
		global $database;
		$session = new Session();
		// Genetate the users ID.
		$level_id = generate_id();

		if ($auto == "YES") {
			$sql = "UPDATE user_levels SET auto = '0' WHERE auto = '1' ";
			$database->query($sql);
			$auto = "1";
		} else {
			$auto = "0";
		}
		
		$flag = false;
		//until flag is false
		while ($flag == false){
			//check if the user id exists
			$sql = "SELECT * FROM ".self::$levels_table_name." WHERE level_id = '{$level_id}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			if ($rows){
				$level_id = generate_id();
			}else{
				$flag = true;
			}
		}
		if ($flag == true){
			$database->query("INSERT INTO ".self::$levels_table_name." VALUES ('', '$level_id', '$level_name', '$auto', '$redirect_page', '$purchasable', '$amount', '$price', '$timed_access', '$time_type', '$access_time')");

			// Create the message that will be displayed on the login screen once the user has been redirected.
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Group has been created.</div>");
			
			// redirect the user to the login page.
			redirect_to('groups.php');
		}
	}
	
	public static function update_group($group_id, $level_name, $signup, $redirect_page, $purchasable, $amount, $price, $timed_access, $time_type, $access_time){
			global $database;
			
			if($signup == 1){
				$database->query("UPDATE user_levels SET auto = '0' WHERE auto = '1' ");
				// $database->query("UPDATE user_levels SET auto = '1' WHERE level_id = '{$level_id}' ");
			}
			
			$database->query("UPDATE user_levels SET level_name = '{$level_name}', auto = '{$signup}', redirect_page = '{$redirect_page}', purchasable = '{$purchasable}', amount = '{$amount}', price = '{$price}', timed_access = '{$timed_access}', time_type = '{$time_type}', access_time = '{$access_time}' WHERE level_id = '{$group_id}' ");
			
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Group successfully updated.</div>");
		
			redirect_to('group_settings.php?group_id='.$group_id.'');
	}
	
	public static function find_group_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$levels_table_name." WHERE level_id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
   }
	
	public function delete_group($level_id){
		global $database;
		
		$users = self::find_by_sql("SELECT * FROM users WHERE user_level LIKE '%{$level_id}%' ");
		$default_level = self::find_by_sql("SELECT * FROM user_levels WHERE auto = '1' LIMIT 1 ");
		
		if($users != ""){
			foreach($users as $user){
				$levels = explode(",", $user->user_level);
				if(($key = array_search($level_id, $levels)) !== false) {
				    unset($levels[$key]);
				}
				// Check and Delete Users "Levels" entry
				
				// End Check
				if(empty($levels)){
					$database->query("UPDATE users SET user_level = '{$default_level[0]->level_id}' WHERE user_id = '{$user->user_id}' ");
				} else {
					$database->query("UPDATE users SET user_level = '{$levels}' WHERE user_id = '{$user->user_id}' ");
				}
			}
		}

		$database->query("DELETE FROM user_levels WHERE level_id = '{$level_id}' ");
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Group deleted successfully.</div>");
		redirect_to('groups.php');
		
	}
	
	public static function create_staff_note($user_id, $staff_username, $message) {
		global $database;
		global $session;
		$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
		
		$message = $database->escape_value($message);
		
		//insert into db the data
		$sql = "INSERT INTO ".self::$staff_notes_table." VALUES ('', '$user_id', '$staff_username', '$message', '$datetime')";
		$database->query($sql);

		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your staff note has been added to this account.</div>");
	}
	
	public static function delete_staff_note($confirm, $id, $user_id, $location) {
		global $database;
		if ($confirm == "yes") {
			$sql = "DELETE FROM ".self::$staff_notes_table." WHERE id = '{$id}' AND user_id = '{$user_id}'";
			$database->query($sql);
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Staff note has been deleted successfully.</div>");
		}
		redirect_to("$location");
	}
	
	public static function get_staff_notes($user_id=0) {
		return self::find_by_sql("SELECT * FROM staff_notes WHERE user_id= '{$user_id}'");
	}
	
	public static function get_all_login_logs(){
		return self::find_by_sql("SELECT * FROM login_logs LIMIT 10");
  	}

	// public static function get_user_levels($user_id=0) {
	// 	return self::find_by_sql("SELECT * FROM levels WHERE user_id= '{$user_id}'");
	// }
	
	// Common
	
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}
  
  	public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id={$id} LIMIT 1");
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
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}

} 	

?>