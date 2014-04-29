<?php 

$purchased = Content_Protection::get_purchased_content($user->user_id);

?>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Price</th>
			<th>Description</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($purchased as $data) : ?>
		<tr>
			<td><?php echo '<a href="'.WWW.$data->link.'">'.$data->name.'</a>'; ?></td>
			<td><?php echo $data->amount; ?></td>
			<td><?php echo $data->description; ?></td>
			<td><?php echo ($data->status == 0) ? "Inactive" : "Active"; ?></td>
		</tr>
		<?php endforeach; ?>

		<?php if(empty($purchased)) : ?>
		<tr>
			<td colspan="5">You have not purchased any content.</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>