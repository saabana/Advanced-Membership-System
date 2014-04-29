<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

include 'user.class.php';

class Session {
	
	private $logged_in=false;

	public $id;
	public $user_id;
	public $message;
	public $data;
	public $last_login;
	
	public $primary_group;

	public $login_count;
	
	function __construct() {
		$this->check_message();
		$this->check_login();
	}
	
  public function is_logged_in() {
    return $this->logged_in;
  }

	public function login($user, $remember="") {
    if($user){
		global $database;
	    $this->user_id = $_SESSION['masdyn']['ams']['user_id'] = $user->user_id;
	  	$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
	  	$database->query("UPDATE users SET last_login = '{$datetime}' WHERE user_id = '{$this->user_id}' ");
		$current_ip = $_SERVER['REMOTE_ADDR'];
		$database->query("INSERT INTO access_logs VALUES('','{$this->user_id}','{$current_ip}','{$datetime}') ");
	    $this->logged_in = true;
		$_SESSION['username'] = $user->username;
		if($remember == "yes"){
			setcookie("auth", $user->user_id."/".md5($user->salt.$user->user_id.$user->username.$user->password.$user->salt), time()+60*60*24*30, "/");
		}
		if($user->oauth_uid == ""){
			$date = strftime("%Y-%m-%d", time());
			$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
			if(!empty($data)){
				$new_count = $data[0]->count + 1;
				$database->query("UPDATE login_logs SET count = '{$new_count}' WHERE `date` = '{$date}' ");
			} else {
				$database->query("INSERT INTO login_logs VALUES ('','{$date}','1','0','0','0') ");
			}
			$new_login_count = $user->login_count + 1;
			$database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$this->user_id}' ");
		}
    }
  }

  	public function admin_login_as_user($user_id){
		$sql  = "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1";
		$result_array = User::find_by_sql($sql);
		$data = !empty($result_array) ? array_shift($result_array) : false;
		if(!empty($data) ){
			self::logout();
			self::login($data);
		}
	}

	public function logout() {
		unset($_SESSION['masdyn']['ams']['user_id']);
		unset($_SESSION['admin_access']);
		unset($this->user_id);
		setcookie("auth", "", time()-60*60*24*30, "/");
		$this->logged_in = false;
	}

	public function message($msg="") {
		if(!empty($msg)) {
			$_SESSION['message'] = $msg;
		} else {
			return $this->message;
		}
	}


	private function check_login() {
	  	if(isset($_SESSION['masdyn']['ams']['user_id'])){
	  		$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
	  		if(empty($user)){
	  			$flag = false;
	  		} else {
				$flag = true;
	  		}
	    } else {
			$flag = false;
	    }
	    if($flag === true){
	    	$this->user_id = $_SESSION['masdyn']['ams']['user_id'];
			$this->logged_in = true;
	    } else {
	    	if(isset($_COOKIE['auth'])){
				self::cookie_login();
			} else {
				unset($this->user_id);
			    $this->logged_in = false;
			}
	    }
  	}

	public function cookie_login(){
		$cookie_data = explode("/", $_COOKIE['auth']);
		$user_id = $cookie_data[0];
		$result_array = User::find_by_sql("SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1");
		$data = !empty($result_array) ? array_shift($result_array) : false;
		if(!empty($data) && $cookie_data[1] == md5($data->salt.$data->user_id.$data->username.$data->password.$data->salt)){
			self::login($data);
			unset($data);
		} else {
			unset($data);
		}
	}
  
	private function check_message() {
		// Is there a message stored in the session?
		if(isset($_SESSION['message'])) {
			// Add it as an attribute and erase the stored version
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		} else {
			$this->message = "";
		}
	}

}

$session = new Session();
$message = $session->message();

?>