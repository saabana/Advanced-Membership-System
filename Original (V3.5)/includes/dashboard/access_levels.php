<?php 

$access_levels = User::get_user_levels($user->user_id);

?>

<table class="table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Created</th>
			<th>Expires</th>
			<th>Expiry Date</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($access_levels as $data) : ?>
		<tr>
			<td><?php echo User::get_level_name($data->level_id); ?></td>
			<td><?php echo datetime_to_text($data->created); ?></td>
			<td><?php echo convert_boolean_sus($data->expires); ?></td>
			<td><?php echo datetime_to_text($data->expiry_date); ?></td>
		</tr>
		<?php endforeach; ?>

		<?php if(empty($access_levels)) : ?>
		<tr>
			<td colspan="5">This account has no current access levels. Why not purchase one <a href="purchase.php">here</a>.</a></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>