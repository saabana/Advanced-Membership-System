<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class Reset_Password {
	
	protected static $table_name="password_links";
	protected static $user_table_name="users";
	protected static $db_fields = array('', 'email', 'hash');
	
	public static function generate_password($lenth = 8) { 
	    $aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9)); 
	    $out =''; 
	    for($c=0;$c < $lenth;$c++) { 
	       $out .= $aZ09[mt_rand(0,count($aZ09)-1)]; 
	    } 
	    return $out; 
	}

	public static function set_confirm_email_link($email) {
		global $database;
		global $session;
		
		// Initialize functions.
		$email_class = new Email();
	
		// Email sent to the user if logged in.
		$from = SITE_EMAIL;
		$subject = "New Password Request";
		
		if (!$email) {
			$session = new Session();
			// Genetate the hash.
			$hash = self::generate_password();

			//insert into db the data
			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$email', '$hash')";
			$database->query($sql);
		
			// Send and email to the user.
			// $message = $email_class->email_template('reset_password', "", "", "$email", "$hash");
		
			// $email_class->send_email($email, $from, $subject, $message);

			unset($_SESSION['email_data']);
			$_SESSION['email_data'] = array(
				"WWW" => WWW,
				"SITE_NAME" => SITE_NAME,
				"email" => $email,
				"code" => $hash
			);
			
			$email_data =  Email::email_template_data(4);		
			$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
		
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Confirm code sent to {$email}.</div>");
			redirect_to('signin.php');
		} else {
			$sql = "DELETE FROM ".self::$table_name." WHERE email = '{$email}' ";
			$database->query($sql);
			
			// Genetate the hash.
			$hash = self::generate_password();

			//insert into db the data
			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$email', '$hash')";
			$database->query($sql);
		
			// Send and email to the user.
			// $message = $email_class->email_template('reset_password', "", "", "$email", "$hash");
		
			// $email_class->send_email($email, $from, $subject, $message);

			unset($_SESSION['email_data']);
			$_SESSION['email_data'] = array(
				"WWW" => WWW,
				"SITE_NAME" => SITE_NAME,
				"email" => $email,
				"code" => $hash
			);
			
			$email_data =  Email::email_template_data(4);		
			$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
			
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Confirm code sent to {$email}.</div>");
			redirect_to('signin.php');
			
		}
	
	}
	
	public static function check_confirm_link($email, $hash) {
		global $database;
		//check if the user exists
		$sql = "SELECT * FROM users WHERE email = '{$email}'";
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		//if it does try again till you find an id that does not exist
		if ($rows) {
			// Check their is an activation link associated with the account
			$sql = "SELECT * FROM ".self::$table_name." WHERE email = '{$email}' AND hash = '{$hash}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			if ($rows) {
				// Ok you can now set the password for the account.
				self::set_password($email, $hash);
			} else {
				// No activation link found.
				$session = new Session();
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but no password link was found.</div>");
				redirect_to('signin.php');
			}
		} else {
			// Throw error back to the user.
			$session = new Session();
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that account was not found.</div>");
			redirect_to('signin.php');
		}
	}
	
	public static function check_resend_code($email) {
		global $database;
		//check if the user exists
		$sql = "SELECT * FROM ".self::$table_name." WHERE email = '{$email}'";
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		//if it does try again till you find an id that does not exist
		if ($rows) {
			// Check their is an activation link associated with the account
			$sql = "SELECT * FROM ".self::$table_name." WHERE email = '{$email}'";
			$query = $database->query($sql);
			$row = $database->fetch_array($query);
			$hash = $row[2];
			$rows = $database->num_rows($query);
			if ($rows) {
				// Ok you can now activate the account.
				self::resend_code($email, $hash);
			} else {
				// No resend link found.
				$session = new Session();
				$session->message("<div class='notification-box error-notification-box'><p>No password link found.</p><a href='#' class='notification-close error-notification-close'>x</a></div><!--.notification-box .notification-box-error end-->
				");
				redirect_to('signin.php');
			}
		} else {
			// Throw error back to the user.
			$session = new Session();
			$session->message("<div class='notification-box error-notification-box'><p>That code does not exist in our database.</p><a href='#' class='notification-close error-notification-close'>x</a></div><!--.notification-box .notification-box-error end-->
			");
			redirect_to('signin.php');
		}
	}
	
	public static function resend_code($email, $hash) {
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user if logged in.
		// $from = SITE_EMAIL;
		// $subject = "Your Reset Password Code";
		
		// Send and email to the user.
		// $content = $email_class->email_template('resend_password_reset_code', "", "", "$email", "$hash");
		
		// $email_class->send_email($email, $from, $subject, $content);

		unset($_SESSION['email_data']);
		$_SESSION['email_data'] = array(
			"WWW" => WWW,
			"SITE_NAME" => SITE_NAME,
			"email" => $email,
			"code" => $hash
		);
		
		$email_data =  Email::email_template_data(4);		
		$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your confirm code has been resent to {$email}.</div>");
		
		redirect_to('signin.php');
	}
	
	public static function set_password($email, $hash) {
		global $database;
		global $session;
		
		$plain_password = self::generate_password();
		if(PUSALT == "YES"){
			$data = User::find_by_sql("SELECT * FROM users WHERE email = '{$email}' LIMIT 1");
			$new_password = encrypt_password($plain_password, $data[0]->salt);
		} else {
			$new_password = encrypt_password($plain_password);
		}
		
		$sql = "UPDATE ".self::$user_table_name." SET password = '{$new_password}' WHERE email = '{$email}'";
		$database->query($sql);
		
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user.
		// $from = SITE_EMAIL;
		// $subject = "Your New Password";
		
		// // Send and email to the user.
		// $content = $email_class->email_template('new_password', "$plain_password", "", "$email", "");
		
		// $email_class->send_email($email, $from, $subject, $content);
		
		unset($_SESSION['email_data']);
		$_SESSION['email_data'] = array(
			"WWW" => WWW,
			"SITE_NAME" => SITE_NAME,
			"plain_password" => $plain_password
		);
		
		$email_data =  Email::email_template_data(5);		
		$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);

		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your new password has been emailed to {$email}.</div>");
		
		self::delete_confirm_email_link($email, $hash);
		
		redirect_to('signin.php');
	}
	
	public static function delete_confirm_email_link($email, $hash) {
		// Delete activation link
		global $database;
		$sql = "DELETE FROM ".self::$table_name." WHERE email = '{$email}' AND hash = '{$hash}' ";
		$database->query($sql);
	}
}

?>