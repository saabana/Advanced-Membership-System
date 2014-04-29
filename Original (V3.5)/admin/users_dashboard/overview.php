<div class="row-fluid">
	<div class="span6">					
		<table class="settings table">
			<tbody>
				<tr>
					<td class="name">Last Login</td>
				</tr>
				<tr>
					<td class="setting"><?php echo ago_time($current_user->last_login)." from ".$current_user->last_ip; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Signed Up</td>
				</tr>
				<tr>
					<td class="setting"><?php echo ago_time($current_user->date_created)." from ".$current_user->signup_ip; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Account Lock</td>
				</tr>
				<tr>
					<td class="setting"><?php echo ($current_user->account_lock == 0) ? '<span style="color: #E90909;">Inactive</span>' : '<span style="color: green;">Active</span>'; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Public ID</td>
				</tr>
				<tr>
					<td class="setting"><?php echo $current_user->user_id; ?></td>
				</tr>
			<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES"){ ?>
				<!--  -->
				<tr>
					<td class="name">Active Invitations</td>
				</tr>
				<tr>
					<td class="setting"><?php echo Invites::count_all($current_user->user_id); ?></td>
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
					<td class="setting"><?php echo number_format($current_user->tokens, 0, '.', ',') ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Banked Tokens</td>
				</tr>
				<tr>
					<td class="setting"><?php echo number_format($current_user->bank_tokens, 0, '.', ',') ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name">Login Count</td>
				</tr>
				<tr>
					<td class="setting"><?php echo number_format($current_user->login_count, 0, '.', ',') ?></td>
				</tr>
			<?php if(ALLOW_REGISTRATIONS == "NO" && ALLOW_INVITES == "YES"){ ?>
				<!--  -->
				<tr>
					<td class="name">Total Users Invited</td>
				</tr>
				<tr>
					<td class="setting"><?php echo User::count_invites($current_user->username); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>