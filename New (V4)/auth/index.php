<?php

include '../includes/inc_files.php';
$session = new Session();
error_reporting(0);

if(!$session->is_logged_in()){
	
	define('CONF_FILE', dirname(__FILE__).'/'.'opauth.conf.php');
	define('OPAUTH_LIB_DIR', dirname(__FILE__).'/lib/Opauth/');

	if (!file_exists(CONF_FILE)){
		trigger_error('Config file missing at '.CONF_FILE, E_USER_ERROR);
		exit();
	}
	require CONF_FILE;

	require OPAUTH_LIB_DIR.'Opauth.php';
	
	$Opauth = new Opauth( $config );
} else {
	redirect_to(WWW."index.php");
	exit;
}
?>