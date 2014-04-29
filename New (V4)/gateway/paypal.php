<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("../includes/inc_files.php");

$pp = new paypal(); // initiate an instance

if(PAYPAL_SANDBOX == "YES"){
	$pp->paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$pp->paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}

$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

if(isset($_POST['purchase'])) {
	$id = $database->escape_value(htmlspecialchars($_POST['id'], ENT_QUOTES));;
	$token_package = User::get_package_data($id);
	$token_package = $token_package[0];
	$pp_item_name = " Purchase ".$token_package->qty." Tokens";
	$pp_item_price = TOKEN_PRICE * $token_package->qty;
	$user_id = $_SESSION['masdyn']['ams']['user_id'];
	$tokens = $token_package->qty;
	$custom = "tokens//".$user_id."//".$tokens;
} else if(isset($_POST['purchase_service'])) {
	$id = $database->escape_value(htmlspecialchars($_POST['id'], ENT_QUOTES));
	$service = User::find_by_sql("SELECT * FROM user_levels WHERE level_id = '{$id}' LIMIT 1 ");
	$service = $service[0];
	$pp_item_name = $service->level_name;
	$pp_item_price = $service->price;
	$user_id = $_SESSION['masdyn']['ams']['user_id'];
	$custom = "service//".$user_id."//".$service->level_id."//".$service->level_name;
} else if(isset($_POST['purchase_gift_card'])) {
	$id = $database->escape_value(htmlspecialchars($_POST['id'], ENT_QUOTES));
	$from = $database->escape_value(htmlspecialchars($_POST['from'], ENT_QUOTES));
	$email = $database->escape_value(htmlspecialchars($_POST['email'], ENT_QUOTES));
	$public_id = $database->escape_value(htmlspecialchars($_POST['public_id'], ENT_QUOTES));
	$note = $database->escape_value(htmlspecialchars($_POST['note'], ENT_QUOTES));

	$card = Gift_Card::find_package_by_id($id);
	if($card->status == '1'){
		$pp_item_name = $card->name." Gift Card";
		$pp_item_price = $card->amount;
		$user_id = $_SESSION['masdyn']['ams']['user_id'];
		$custom = "gift_card//".$id."//".$card->name."//".$from."//".$email."//".$public_id."//".$note;
	} else {
		$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that gift card package has been disabled. Please choose another.</div>");
		redirect_to('../gift_cards.php');
		exit;
	}
}


// if no action variable, set 'process' as default action
if (empty($_GET['action'])) $_GET['action'] = 'process';

switch ($_GET['action']) {
	case 'process': // Process and order...
		$pp->add_field('business', PAYPAL_EMAIL);
		$pp->add_field('return', $this_script.'?action=success');
		$pp->add_field('cancel_return', $this_script.'?action=cancel');
		$pp->add_field('notify_url', $this_script.'?action=ipn');
		$pp->add_field('item_name', $pp_item_name);
		$pp->add_field('amount', $pp_item_price);
		$pp->add_field('currency_code', CURRENCY_CODE);
		$pp->add_field('custom', $custom);
		$pp->submit_paypal_post();
	break;
	case 'success': // successful order...
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Thank You, your payment has been received. Your request will be processed shortly by a member of staff.</div>");
		redirect_to('../index.php');
	break;
	case 'cancel': // Canceled Order...
		echo "<html>
		<head><title>Canceled</title></head>
		<body><h2>The order was canceled.</h2>";
		echo "</body></html>";
	break;
	case 'ipn': // For IPN validation...
		if ($pp->validate_ipn()) {
			if($pp->ipn_data['payment_status'] == "Completed"){
				$return_data = explode("//", $pp->ipn_data['custom']);
				if($return_data[0] == "tokens"){
					User::add_tokens($return_data[1], $return_data[2]);
					// user_id, amount, description
					User::add_purchase_history($return_data[1],$pp->ipn_data['mc_gross'],$return_data[2]." Tokens");
				} else if($return_data[0] == "service"){
					User::add_access($return_data[1], $return_data[2]);
					// user_id, amount, description
					User::add_purchase_history($return_data[1],$pp->ipn_data['mc_gross'],$return_data[3]);
				} else if($return_data[0] == "gift_card"){
					//id, gift_card_name, from, email, public_id, note
					Gift_Card::purchase_gift_card(1,$return_data[1],$return_data[2],$return_data[3],$return_data[4],$return_data[5],$return_data[6]);
				}
			}
		}
	break;
}

?>