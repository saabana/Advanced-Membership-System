<?php 

$invites = Invites::find_invites($current_user->user_id);
$invite_count = Invites::count_all($current_user->user_id);

$location = "dashboard.php?page=invites";

if (isset($_POST['create_invite'])) {
	Invites::create_invite($current_user->user_id, $current_user->username, $location);
}

if((!empty($_GET['delete_code']))){
	$code = $_GET['delete_code'];
	Invites::delete_invite($code, $location);
}

?>
<form action="<?php echo $location; ?>" method="post">
	<table class="table">
		<thead>
			<tr>
				<th>Invites <?php echo "(".$invite_count."/".MAX_INVITES.")" ?> - Total Users Invited: <?php echo User::count_invites($current_user->username); ?></th>
				<th style="width: 45px;">Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($invites as $invite) : ?>
			<tr>
				<td><?php echo $invite->code; ?></td>
				<td style="text-align: center;"><a href="<?php echo $location; ?>&amp;delete_code=<?php echo $invite->code; ?>"><img src="../assets/img/delete.png" alt="edit" class="edit_button" /></a></td>
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