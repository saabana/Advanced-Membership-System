<?php 

if(isset($_POST['send_tokens'])){
	$username = $_POST['username'];
	$tokens = $_POST['tokens'];
	User::transfer_tokens($user->user_id, $username, $tokens);
}

?>
<script type="text/javascript">
function activate_gift_card(){
	code = $("#code").val();
	if(code != ""){

		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: {page: "gift_cards", action: "activate", code: code},
			success: function(return_data){
				if(return_data == false){
					$("#code").addClass("error");
					update_msg("#activate_gift_card .message");
				} else {
					$("#activate_gift_card .modal-body").html(return_data);
					$("#activate_gift_card #activate_card").remove();
				}
			},
			beforeSend: function(){
				$("#activate_gift_card #activate_card").html("Working...");
			}
		});

	} else {
		$("#activate_gift_card .message").html("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter your gift card.</div>");
		$("#code").addClass("error");
	}
}
</script>
<div class="row">
	<div class="col-md-6">					
		<table class="settings table">
			<tbody>
				<tr>
					<td class="name">Last Login</td>
				</tr>
				<tr>
					<td class="setting"><?php echo ago_time($user->last_login)." from ".$user->last_ip; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Signed Up</td>
				</tr>
				<tr>
					<td class="setting"><?php echo ago_time($user->date_created)." from ".$user->signup_ip; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Account Lock</td>
				</tr>
				<tr>
					<td class="setting"><?php echo ($user->account_lock == 0) ? '<span style="color: #E90909;">Inactive</span>' : '<span style="color: green;">Active</span>'; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Public ID</td>
				</tr>
				<tr>
					<td class="setting"><?php echo $user->user_id; ?></td>
				</tr>
			<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES"){ ?>
				<!--  -->
				<tr>
					<td class="name">Active Invitations</td>
				</tr>
				<tr>
					<td class="setting"><?php echo Invites::count_all($user->user_id); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<!-- Right Side -->
	<div class="col-md-6">					
		<table class="settings table">
			<tbody>
				<tr>
					<td class="name">Active Tokens</td>
				</tr>
				<tr>
					<td class="setting"><?php echo number_format($user->tokens, 0, '.', ',') ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Banked Tokens</td>
				</tr>
				<tr>
					<td class="setting"><?php echo number_format($user->bank_tokens, 0, '.', ',') ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Login Count</td>
				</tr>
				<tr>
					<td class="setting"><?php echo number_format($user->login_count, 0, '.', ',') ?></td>
				</tr>
			<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES"){ ?>
				<!--  -->
				<tr>
					<td class="name">Total Users Invited</td>
				</tr>
				<tr>
					<td class="setting"><?php echo User::count_invites($user->username); ?></td>
				</tr>
			<?php } ?>
				<tr>
					<td class="name"><button class="btn btn-primary" style="padding: 4px 10px;" data-toggle="modal" href="#transfer_tokens">Send Tokens to Another User</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name"><button class="btn btn-success" style="padding: 4px 10px;" data-toggle="modal" href="#activate_gift_card">Activate Gift Card</button></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<form action="dashboard.php?page=overview" method="POST" class="modal fade" id="transfer_tokens" tabindex="-1" role="dialog" aria-labelledby="transfer_tokens" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	      <h4 class="modal-title">Transfer Tokens</h4>
	    </div>
	    <div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<strong>Warning! We are not responsible for loss of tokens if you send them to the wrong user. Please double check before clicking send.</strong><br /><br />
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<label>Recipient Username</label>
				   	<input type="text" required="required" name="username" class="form-control" value="" />
				</div>
				<div class="col-md-6">
				   	<label>Amount of Tokens to Send</label>
				   	<input type="text" required="required" name="tokens" class="form-control" value="" />
				</div>
			</div>
	    </div>
	    <div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Close</button>
			<input class="btn btn-danger" type="submit" name="send_tokens" value="Send Tokens" />
	    </div>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</form><!-- /.modal -->

<div class="modal fade" id="activate_gift_card" tabindex="-1" role="dialog" aria-labelledby="activate_gift_card" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	      <h4 class="modal-title">Activate your Gift Card</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="message"></div>
			<div class="row">
				<div class="col-md-12">
					Please enter your gift card code below.<br /><br />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label>Gift Card:</label>
				   	<input type="text" required="required" name="code" id="code" class="form-control" value="" />
				</div>
			</div>
	    </div>
	    <div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Close</button>
			<input class="btn btn-success" type="submit" name="activate_card" id="activate_card" onclick="activate_gift_card();" value="Activate Gift Card" />
	    </div>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->