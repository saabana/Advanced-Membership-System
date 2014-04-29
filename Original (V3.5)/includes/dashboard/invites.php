<?php 

$invites = Invites::find_invites($user->user_id);
$invite_count = Invites::count_all($user->user_id);

$location = "dashboard.php?page=invites";

if (isset($_POST['create_invite'])) {
	if($user->account_lock == 0){
		Invites::create_invite($user->user_id, $user->username, $location);
	} else {
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you can't create an invitation while your account lock is active. Please disable your account lock and try again.</div>");
		redirect_to($location);
	}
}

if((!empty($_GET['delete_code']))){
	if($user->account_lock == 0){
		$code = $_GET['delete_code'];
		Invites::delete_invite($code, $location);
	} else {
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, you can't delete an invitation while your account lock is active. Please disable your account lock and try again.</div>");
		redirect_to($location);
	}
}

?>
<form action="<?php echo $location; ?>" method="post">
	<table class="table">
		<thead>
			<tr>
				<th>Invites <?php echo "(".$invite_count."/".MAX_INVITES.")" ?> - Total Users Invited: <?php echo User::count_invites($user->username); ?></th>
				<th style="width: 45px;">Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($invites as $invite) : ?>
			<tr>
				<td><?php echo $invite->code; ?></td>
				<td style="text-align: center;"><a href="<?php echo $location; ?>&amp;delete_code=<?php echo $invite->code; ?>"><img src="assets/img/delete.png" alt="edit" class="edit_button" /></a></td>
			</tr>
			<?php endforeach; ?>
		
			<?php if($invite_count < MAX_INVITES) : ?>
			<tr>
				<td colspan="2" style="text-align: center;"><input class="btn btn-primary" type="submit" name="create_invite" style="padding: 5px 10px;" value="Create Invitation" /></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
</form>