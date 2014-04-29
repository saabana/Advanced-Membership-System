<?php 

$purchase_history = User::get_purchase_history($user->user_id);

?>

<table class="table">
	<thead>
		<tr>
			<th>Date Time</th>
			<th>Amount</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($purchase_history as $history) : ?>
		<tr>
			<td><?php echo datetime_to_text($history->datetime); ?></td>
			<td><?php echo CURRENCYSYMBOL.$history->amount; ?></td>
			<td><?php echo $history->description; ?></td>
		</tr>
		<?php endforeach; ?>
	
		<?php if(empty($purchase_history)) : ?>
		<tr>
			<td colspan="3"><strong>This account has not had any purchases.</strong></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>