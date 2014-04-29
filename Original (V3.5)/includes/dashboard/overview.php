<?php 

if(isset($_POST['send_tokens'])){
	$username = $_POST['username'];
	$tokens = $_POST['tokens'];
	User::transfer_tokens($user->user_id, $username, $tokens);
}

?>
<div class="row-fluid">
	<div class="span6">					
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
	<div class="span6">					
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
				<!--  -->
				<tr>
					<td class="name"><button class="btn btn-primary" style="padding: 4px 10px;" data-toggle="modal" href="#transfer_tokens">Send Tokens to Another User</button></td>
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
			</tbody>
		</table>
	</div>
</div>
<form action="dashboard.php?page=overview" method="POST" id="transfer_tokens" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Send Tokens</h3>
	</div>
	<div class="modal-body">
		<div class="row-fluid">
			<div class="span12">
				<strong>Warning! We are not responsible for loss of tokens if you send them to the wrong user. Please double check before clicking send.</strong><br /><br />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<label>Recipient Username</label>
			   	<input type="text" required="required" name="username" class="span12" value="" />
			</div>
			<div class="span6">
			   	<label>Tokens to Send</label>
			   	<input type="text" required="required" name="tokens" class="span12" value="" />
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="send_tokens" value="Send Tokens" />
	</div>
</form>