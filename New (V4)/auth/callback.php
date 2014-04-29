<?php

define('CONF_FILE', dirname(__FILE__).'/'.'opauth.conf.php');
define('OPAUTH_LIB_DIR', dirname(__FILE__).'/lib/Opauth/');

require '../includes/configuration/config.php';
require '../includes/classes/session.class.php';
require '../includes/classes/functions.class.php';

if (!file_exists(CONF_FILE)){
	trigger_error('Config file missing at '.CONF_FILE, E_USER_ERROR);
	exit();
}
require CONF_FILE;

require OPAUTH_LIB_DIR.'Opauth.php';
$Opauth = new Opauth( $config, false );

$session = new Session();

$response = null;

switch($Opauth->env['callback_transport']){	
	case 'session':
		session_start();
		$response = $_SESSION['opauth'];
		unset($_SESSION['opauth']);
		break;
	case 'post':
		$response = unserialize(base64_decode( $_POST['opauth'] ));
		break;
	case 'get':
		$response = unserialize(base64_decode( $_GET['opauth'] ));
		break;
	default:
		echo '<strong style="color: red;">Error: </strong>Unsupported callback_transport.'."<br>\n";
		break;
}

if (array_key_exists('error', $response)){
	$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, we are unable to log you in at this time. Please try again later.</div>");
	redirect_to(WWW."signin.php");
	// preprint($response);
}

else{
	if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])){
		echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.'."<br>\n";
	}
	elseif (!$Opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)){
		echo '<strong style="color: red;">Invalid auth response: </strong>'.$reason.".<br>\n";
	}
	else{
		if($response['auth']['provider'] == "Facebook"){
			$data = User::find_by_sql("SELECT * FROM users WHERE oauth_provider = '0' AND oauth_uid = '{$response['auth']['raw']['id']}' ");
			$session = new Session;
			if(empty($data)){
				if(ALLOW_REGISTRATIONS == "NO"){
					$_SESSION['oauth_message'] = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, registrations are currently closed.</div>";
					redirect_to(WWW."signin.php");
				} else {
					User::create_oauth_account($response['auth']['raw']['username'], $response['auth']['raw']['email'], $response['auth']['raw']['first_name'], $response['auth']['raw']['last_name'], $response['auth']['raw']['gender'], "0", $response['auth']['raw']['id']);
					$sql  = "SELECT * FROM users WHERE oauth_provider = '0' AND oauth_uid = '{$response['auth']['raw']['id']}' LIMIT 1";
					$result_array = User::find_by_sql($sql);
					$data = !empty($result_array) ? array_shift($result_array) : false;
					if(!empty($data) ){
							$session->logout();
							$session->login($data);
					}
					$date = strftime("%Y-%m-%d", time());
					$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
					if(!empty($data)){
						$new_count = $data[0]->f_count + 1;
						$database->query("UPDATE login_logs SET f_count = '{$new_count}' WHERE `date` = '{$date}' ");
					} else {
						$database->query("INSERT INTO login_logs VALUES ('','{$date}','0','1','0','0') ");
					}
					$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']); $new_login_count = $user->login_count + 1;
					$user_id = $_SESSION['masdyn']['ams']['user_id']; $database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$user_id}' ");
					$_SESSION['oauth_message'] = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>You have successfully registered through Facebook.</div>";
					redirect_to(WWW."index.php");
				}
			} else {
				$sql  = "SELECT * FROM users WHERE oauth_provider = '0' AND oauth_uid = '{$response['auth']['raw']['id']}' LIMIT 1";
				$result_array = User::find_by_sql($sql);
				$data = !empty($result_array) ? array_shift($result_array) : false;
				if(!empty($data) ){
						$session->logout();
						$session->login($data);
				}
				$date = strftime("%Y-%m-%d", time());
				$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
				if(!empty($data)){
					$new_count = $data[0]->f_count + 1;
					$database->query("UPDATE login_logs SET f_count = '{$new_count}' WHERE `date` = '{$date}' ");
				} else {
					$database->query("INSERT INTO login_logs VALUES ('','{$date}','0','1','0','0') ");
				}
				$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']); $new_login_count = $user->login_count + 1;
				$user_id = $_SESSION['masdyn']['ams']['user_id']; $database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$user_id}' ");
				redirect_to(WWW."signin.php");
			}
		} else if($response['auth']['provider'] == "Twitter"){
			$data = User::find_by_sql("SELECT * FROM users WHERE oauth_provider = '1' AND oauth_uid = '{$response['auth']['raw']['id']}' ");
			$session = new Session;
			if(empty($data)){
				if(ALLOW_REGISTRATIONS == "NO"){
					$_SESSION['oauth_message'] = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, registrations are currently closed.</div>";
					redirect_to(WWW."signin.php");
				} else {
					$user_names = explode(" ", $response['auth']['raw']['name']);
					User::create_oauth_account($response['auth']['raw']['screen_name'], "NONE PROVIDED", $user_names[0], $user_names[1], "Male", "1", $response['auth']['raw']['id']);
					$sql  = "SELECT * FROM users WHERE oauth_provider = '1' AND oauth_uid = '{$response['auth']['raw']['id']}' LIMIT 1";
					$result_array = User::find_by_sql($sql);
					$data = !empty($result_array) ? array_shift($result_array) : false;
					if(!empty($data) ){
							$session->logout();
							$session->login($data);
					}
					$date = strftime("%Y-%m-%d", time());
					$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
					if(!empty($data)){
						$new_count = $data[0]->t_count + 1;
						$database->query("UPDATE login_logs SET t_count = '{$new_count}' WHERE `date` = '{$date}' ");
					} else {
						$database->query("INSERT INTO login_logs VALUES ('','{$date}','0','0','1','0') ");
					}
					$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']); $new_login_count = $user->login_count + 1;
					$user_id = $_SESSION['masdyn']['ams']['user_id']; $database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$user_id}' ");
					$_SESSION['oauth_message'] = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>You have successfully registered through Twitter.</div>";
					redirect_to(WWW."index.php");
				}
			} else {
				$sql  = "SELECT * FROM users WHERE oauth_provider = '1' AND oauth_uid = '{$response['auth']['raw']['id']}' LIMIT 1";
				$result_array = User::find_by_sql($sql);
				$data = !empty($result_array) ? array_shift($result_array) : false;
				if(!empty($data) ){
						$session->logout();
						$session->login($data);
				}
				$date = strftime("%Y-%m-%d", time());
				$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
				if(!empty($data)){
					$new_count = $data[0]->t_count + 1;
					$database->query("UPDATE login_logs SET t_count = '{$new_count}' WHERE `date` = '{$date}' ");
				} else {
					$database->query("INSERT INTO login_logs VALUES ('','{$date}','0','0','1','0') ");
				}
				$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']); $new_login_count = $user->login_count + 1;
				$user_id = $_SESSION['masdyn']['ams']['user_id']; $database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$user_id}' ");
				redirect_to(WWW."signin.php");
			}
		} else if($response['auth']['provider'] == "Google"){
			$data = User::find_by_sql("SELECT * FROM users WHERE oauth_provider = '2' AND oauth_uid = '{$response['auth']['raw']['id']}' ");
			$session = new Session;
			if(empty($data)){
				if(ALLOW_REGISTRATIONS == "NO"){
					$_SESSION['oauth_message'] = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, registrations are currently closed.</div>";
					redirect_to(WWW."signin.php");
				} else {
					$user_names = explode(" ", $response['auth']['raw']['name']);
					User::create_oauth_account($response['auth']['raw']['email'], $response['auth']['raw']['email'], "", "", "Male", "2", $response['auth']['raw']['id']);
					$sql  = "SELECT * FROM users WHERE oauth_provider = '2' AND oauth_uid = '{$response['auth']['raw']['id']}' LIMIT 1";
					$result_array = User::find_by_sql($sql);
					$data = !empty($result_array) ? array_shift($result_array) : false;
					if(!empty($data) ){
							$session->logout();
							$session->login($data);
					}
					$date = strftime("%Y-%m-%d", time());
					$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
					if(!empty($data)){
						$new_count = $data[0]->g_count + 1;
						$database->query("UPDATE login_logs SET g_count = '{$new_count}' WHERE `date` = '{$date}' ");
					} else {
						$database->query("INSERT INTO login_logs VALUES ('','{$date}','0','0','0','1') ");
					}
					$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']); $new_login_count = $user->login_count + 1;
					$user_id = $_SESSION['masdyn']['ams']['user_id']; $database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$user_id}' ");
					$_SESSION['oauth_message'] = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>You have successfully registered through Google.</div>";
					redirect_to(WWW."index.php");
				}
			} else {
				$sql  = "SELECT * FROM users WHERE oauth_provider = '2' AND oauth_uid = '{$response['auth']['raw']['id']}' LIMIT 1";
				$result_array = User::find_by_sql($sql);
				$data = !empty($result_array) ? array_shift($result_array) : false;
				if(!empty($data) ){
						$session->logout();
						$session->login($data);
				}
				$date = strftime("%Y-%m-%d", time());
				$data = User::find_by_sql("SELECT * FROM login_logs WHERE `date` = '{$date}' ");
				if(!empty($data)){
					$new_count = $data[0]->g_count + 1;
					$database->query("UPDATE login_logs SET g_count = '{$new_count}' WHERE `date` = '{$date}' ");
				} else {
					$database->query("INSERT INTO login_logs VALUES ('','{$date}','0','0','0','1') ");
				}
				$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']); $new_login_count = $user->login_count + 1;
				$user_id = $_SESSION['masdyn']['ams']['user_id']; $database->query("UPDATE users SET login_count = '{$new_login_count}' WHERE `user_id` = '{$user_id}' ");
				redirect_to(WWW."signin.php");
			}
		}

	}
}