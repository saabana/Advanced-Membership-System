<?php 

$access_logs = User::get_access_logs($current_user->user_id);
$location = WWW.ADMINDIR."user_dashboard.php?page=access-logs&user_id=".$user->user_id;

if(isset($_POST['delete_logs'])){
	$database->query("DELETE FROM access_logs WHERE user_id = '{$current_user->user_id}' ");
	$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>All access logs for this account have been deleted.</div>");
	redirect_to($location);
}

?>
<form action="<?php echo $location; ?>" method="post">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Date and Time</th>
				<th>IP Address</th>
			</tr>
		</thead>
		<tbody>
		<?php if(!empty($access_logs)){ foreach($access_logs as $log): ?>
			<tr>
				<td><?php echo datetime_to_text($log->datetime); ?></td>
				<td><?php echo $log->ip_address; ?></td>
			</tr>
		<?php endforeach; } else { ?>
			<td colspan="2">No access logs can be found for this account.</td>
		<?php } ?>
			<tr>
				<td colspan="2" style="text-align: center;"><input class="btn btn-danger" type="submit" name="delete_logs" style="padding: 5px 10px;" value="Delete All Logs" /></td>
			</tr>
		</tbody>
	</table>
</form>