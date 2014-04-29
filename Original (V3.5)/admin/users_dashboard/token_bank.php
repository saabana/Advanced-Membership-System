<?php 

$location = WWW.ADMINDIR."user_dashboard.php?page=token-bank&user_id=".$user->user_id;

if (isset($_POST['bank_tokens'])) {
	$bank_tokens = $_POST['bank_tokens'];
	$bank_tokens = preg_replace("/[^0-9]/", '', $bank_tokens);
	if($user->tokens < $bank_tokens){
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you don't have {$bank_tokens} tokens to bank.</div>");
		redirect_to($location);
	} else {
		User::token_bank($user->user_id, $user->tokens, $user->bank_tokens, $bank_tokens, "bank", $location);
	}
} else {
	$bank_tokens = "";
}

if(isset($_POST['withdraw_tokens'])) {
	$withdraw_tokens = $_POST['withdraw_tokens'];
	$withdraw_tokens = preg_replace("/[^0-9]/", '', $withdraw_tokens);
	if($user->bank_tokens < $withdraw_tokens){
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you don't have {$withdraw_tokens} tokens to withdraw.</div>");
		redirect_to($location);
	} else {
		User::token_bank($user->user_id, $user->tokens, $user->bank_tokens, $withdraw_tokens, "withdraw", $location);
	}
} else {
	$withdraw_tokens = "";
}

?>

<form action="<?php echo $location; ?>" method="post" class="form-horizontal">
	
	<div class="row-fluid">
		<div class="span3 center">
			<h3>Currently Active</h3>
			<label style="font-size: 35px;color: #2EB0E4;"><?php echo number_format($current_user->tokens, 0, '.', ',') ?></label>
		</div>
		<div class="span3 center">
			<form action="<?php echo $location; ?>" method="post">
	         <h3>Deposit Tokens</h3>
				<input type="text" name="bank_tokens" class="center" required="required" value="<?php echo htmlentities($bank_tokens); ?>" />
				<input class="btn btn-primary" style="margin-top: 15px;" type="submit" name="submit" value="Deposit" />
			</form>
		</div>
		<div class="span3 center">	
			<h3>Currently Banked</h3>
			<label style="font-size: 35px;color: #2EB0E4;"><?php echo number_format($current_user->bank_tokens, 0, '.', ',')?></label>
		</div>
		<div class="span3 center">
			<form action="<?php echo $location; ?>" method="post">
	         <h3>Withdraw Tokens</h3>
				<input type="text" id="withdraw_tokens" name="withdraw_tokens" class="center" required="required" value="<?php echo htmlentities($withdraw_tokens); ?>" />
				<input class="btn btn-danger" style="margin-top: 15px;" type="submit" name="submit" value="Withdraw" />
			</form>
		</div>
	</div>

</form>