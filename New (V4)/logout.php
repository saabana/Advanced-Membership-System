<?php require_once("includes/inc_files.php"); 
/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/
?>
<?php	

    $session->logout();
	
	if(isset($_GET['msg'])){

		$msg = $_GET['msg'];

		if ($msg == "suspended") {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Your account has been suspended, please contact support.</div>");
		} else if ($msg == "not_found") {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but we are unable to find your account, please contact support.</div>");
		} else if ($msg == "maintenance") {
			$session->message("<div class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but we are currently doing some maintenance work.</div>");
		}
	} else {
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>You have successfully been logged out.</div>");
	}

	redirect_to("signin.php");
?>