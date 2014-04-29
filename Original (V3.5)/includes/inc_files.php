<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

if(is_dir("install")){
	if(filesize("includes/configuration/config.php") == 0){
		echo "Please go <a href='install/'>here</a> to install this script. If you have already gone though the install process, please delete the install directory and refresh this page. ";
	    exit;
	} else {
		echo "We are currently carrying out some routine maintenance, please check back soon.";
	    exit;
	}
}

require_once("configuration/config.php");
require_once("classes/database.class.php");
require_once("classes/functions.class.php");
require_once("classes/pagination.class.php");
require_once("classes/session.class.php");
require_once("classes/user.class.php");
require_once("classes/email.class.php");
require_once("classes/activation.class.php");
require_once("classes/reset_password.class.php");
require_once("classes/account_lock.class.php");
require_once("classes/invites.class.php");
require_once("classes/paypal.class.php");

?>