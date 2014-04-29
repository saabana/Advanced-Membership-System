<?php 

$location = WWW.ADMINDIR."user_dashboard.php?page=token-bank&user_id=".$user->user_id;

if (isset($_POST['bank_tokens'])) {
	$bank_tokens = $_POST['tokens'];
	$bank_tokens = preg_replace("/[^0-9]/", '', $bank_tokens);
	if($user->tokens < $bank_tokens){
		$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you don't have {$bank_tokens} tokens to bank.</div>");
		redirect_to($location);
	} else {
		User::token_bank($user->user_id, $user->tokens, $user->bank_tokens, $bank_tokens, "bank", $location);
	}
} else {
	$bank_tokens = "";
}

if(isset($_POST['withdraw_tokens'])) {
	$withdraw_tokens = $_POST['tokens'];
	$withdraw_tokens = preg_replace("/[^0-9]/", '', $withdraw_tokens);
	if($user->bank_tokens < $withdraw_tokens){
		$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you don't have {$withdraw_tokens} tokens to withdraw.</div>");
		redirect_to($location);
	} else {
		User::token_bank($user->user_id, $user->tokens, $user->bank_tokens, $withdraw_tokens, "withdraw", $location);
	}
} else {
	$withdraw_tokens = "";
}

?>

<form action="<?php echo $location; ?>" method="post" class="form-horizontal">
	
	<div class="row">
		<div class="col-md-3 center">
			<h3>Currently Active</h3>
			<label style="font-size: 35px;color: #2EB0E4;"><?php echo number_format($user->tokens, 0, '.', ',') ?></label>
		</div>
		<div class="col-md-3 center">
			<form action="<?php echo $location; ?>" method="post">
		        <h3>Deposit Tokens</h3>			
			    <div class="input-group">
			      <input type="text" name="tokens" class="center form-control" required="required" value="<?php echo htmlentities($bank_tokens); ?>" />
			      <span class="input-group-btn">
			        <input class="btn btn-success" type="submit" name="bank_tokens" value="Deposit" />
			      </span>
			    </div><!-- /input-group -->
			</form>
		</div>
		<div class="col-md-3 center">	
			<h3>Currently Banked</h3>
			<label style="font-size: 35px;color: #2EB0E4;"><?php echo number_format($user->bank_tokens, 0, '.', ',')?></label>
		</div>
		<div class="col-md-3 center">
			<form action="<?php echo $location; ?>" method="post">
		        <h3>Withdraw Tokens</h3>			
			    <div class="input-group">
			      <input type="text" name="tokens" class="center form-control" required="required" value="<?php echo htmlentities($withdraw_tokens); ?>" />
			      <span class="input-group-btn">
			        <input class="btn btn-danger" type="submit" name="withdraw_tokens" value="Withdraw" />
			      </span>
			    </div><!-- /input-group -->
			</form>
		</div>
	</div>

</form>