<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

class Account_Lock {
	
	protected static $table_name="account_locks";
	protected static $user_table_name="users";
	protected static $db_fields = array('', 'user_id', 'code');
	
	private static function generate_code($lenth = 10) { 
	    $aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9)); 
	    $out =''; 
	    for($c=0;$c < $lenth;$c++) { 
	       $out .= $aZ09[mt_rand(0,count($aZ09)-1)]; 
	    } 
	    return $out; 
	}
	
	public static function set_account_lock($email, $username, $user_id, $location) {
		global $database;
		$session = new Session();
		// Genetate the hash.
		$code = self::generate_code();

		//insert into db the data
		$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$code')";
		$database->query($sql);
		
		// Update users table
		$sql = "UPDATE ".self::$user_table_name." SET account_lock = '1' WHERE user_id = '{$user_id}' ";
		$database->query($sql);
		
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user if logged in.
		// $from = SITE_EMAIL;
		// $subject = "Your Account's Unlock Code";
		
		// Send and email to the user.
		// $message = $email_class->email_template('account_lock', "", "$username", "", "$code");
		
		// $email_class->send_email($email, $from, $subject, $message);

		unset($_SESSION['email_data']);
		$_SESSION['email_data'] = array(
			"WWW" => WWW,
			"SITE_NAME" => SITE_NAME,
			"code" => $code
		);
		
		$email_data =  Email::email_template_data(6);		
		$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
		
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your settings have been locked, unlock code sent to {$email}.</div>");
		
		redirect_to($location);
	}
	
  	public static function check_lock_status($user_id, $code, $location) {
		global $database;
		//check if the account is locked.
		$sql = "SELECT * FROM ".self::$user_table_name." WHERE user_id = {$user_id} AND account_lock = '1'";
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		if ($rows) {
			// Check their is an unlock code associated with the account
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}' AND code = '{$code}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			if ($rows) {
				// Ok you can now deactivate the account lock.
				self::deactivate_lock($user_id, $code, $location);
			} else {
				// No unlock code found.
				$session = new Session();
				$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>The unlock code <strong>{$code}</strong> does not match the one we have in our database for your account.</div>");
				redirect_to($location);
			}
		} else {
			// Throw error back to the user.
			$session = new Session();
			$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Your account could not be locked.</div>");
			redirect_to($location);
		}
	}
	
	private static function deactivate_lock($user_id, $code, $location) {
		global $database;
		$sql = "UPDATE ".self::$user_table_name." SET account_lock = '0' WHERE user_id = '{$user_id}'";
		$database->query($sql);
		self::delete_unlock_code($user_id, $code, $location);
	}
	
	private static function delete_unlock_code($user_id, $code, $location) {
		// Delete activation link
		global $database;
		$sql = "DELETE FROM ".self::$table_name." WHERE user_id = '{$user_id}' AND code = '{$code}' ";
		$database->query($sql);
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your settings have been unlocked.</div>");
		redirect_to($location);
	}
	
	public static function check_resend_code($user_id, $email, $location) {
		global $database;
		//check if the user exists
		$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		//if it does not find anything it will try again until an account is found
		if ($rows) {
			// Check their is an activation link associated with the account
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
			$query = $database->query($sql);
			$row = $database->fetch_array($query);
			$code = $row['code'];
			$rows = $database->num_rows($query);
			if ($rows) {
				// Ok you can now activate the account.
				self::resend_code($email, $code, $location);
			} else {
				// No resend link found.
				$session = new Session();
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>No unlock code found.</div>");
				redirect_to($location);
			}
		} else {
			// Throw error back to the user.
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Their is no unlock code associated with your account. Please contact support.</div>");
			redirect_to($location);
		}
	}
	
	public static function resend_code($email, $code, $location) {
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user if logged in.
		// $from = SITE_EMAIL;
		// $subject = "Your Unlock Code";
		
		// // Send and email to the user.
		// $message = $email_class->email_template('resend_unlock_code', "", "", "", "$code");
		
		// $email_class->send_email($email, $from, $subject, $message);

		unset($_SESSION['email_data']);
		$_SESSION['email_data'] = array(
			"WWW" => WWW,
			"SITE_NAME" => SITE_NAME,
			"code" => $code
		);
		
		$email_data =  Email::email_template_data(7);		
		$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your unlock code has been resent to {$email}.</div>");
		
		redirect_to($location);
	}

	
}

?>