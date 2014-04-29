<?php

$staff_notes = Admin::get_staff_notes($current_user->user_id);

if(!empty($_GET['delete_staff_note'])){
	$confirm = $_GET['delete_staff_note'];
	$id = $_GET['id'];
	$user_id = $_GET['user_id'];
    Admin::delete_staff_note($confirm, $id, $current_user->user_id, "user_dashboard.php?page=staff-notes&user_id={$current_user->user_id}");
}

if(isset($_POST['add_note'])){
	$note = $database->escape_value(trim($_POST['new_note']));
	if($note == ""){
		$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter a note.</div>");
	} else {
		Admin::create_staff_note($current_user->user_id, $user->username, $note);
	}
	redirect_to("user_dashboard.php?page=staff-notes&user_id=$current_user->user_id");
} else {
	$note = "";
}


?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Staff Member</th>
			<th>Message</th>
			<th>Posted</th>
			<th style="text-align: center !important">Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($staff_notes as $note) : ?>
		<tr>
			<td><?php echo $note->username; ?></td>
			<td><?php echo nl2br($note->message); ?></td>
			<td><?php echo datetime_to_text($note->date); ?></td>
			<td style="text-align: center !important"><a href="user_dashboard.php?page=staff-notes&amp;user_id=<?php echo $current_user->user_id; ?>&amp;delete_staff_note=yes&amp;id=<?php echo $note->id; ?>&amp;user_id=<?php echo $current_user->user_id; ?>"><img src="../assets/img/delete.png" alt="edit" class="edit_button" /></a></td>
		</tr>
		<?php endforeach; ?>

		<?php if(empty($staff_notes)) : ?>
		<tr>
			<td colspan="4"><strong>This account does not currently have any staff notes.</strong></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<form action="user_dashboard.php?page=staff-notes&amp;user_id=<?php echo $current_user->user_id; ?>" method="post">

	<div class="row">
		<div class="col-md-12">
			<label>New Note</label>
			<textarea name="new_note" class="form-control" rows="3"></textarea>
		</div>
	</div>

	<div class="form-actions" style="text-align: center;margin:20px -10px 0px">
		<input type="submit" name="add_note" class="btn btn-primary" value="Add Note">
	</div>

</form>