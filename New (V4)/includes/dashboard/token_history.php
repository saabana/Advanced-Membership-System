<?php 

$token_history = User::get_token_history($user->user_id);

?>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Tokens</th>
			<th>Description</th>
			<th>Action</th>
			<th>Date Time</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($token_history as $history) : ?>
		<tr>
			<td><?php echo $history->tokens; ?></td>
			<td><?php echo $history->package_name; ?></td>
			<td><?php echo convert_token_status($history->status); ?></td>
			<td><?php echo datetime_to_text($history->datetime); ?></td>
		</tr>
		<?php endforeach; ?>
	
		<?php if(empty($token_history)) : ?>
		<tr>
			<td colspan="4"><strong>This account has not had any token transactions.</strong></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>