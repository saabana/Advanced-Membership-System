<?php 

$account_history = User::get_account_history($user->user_id);

?>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Date Time</th>
			<th>Amount</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>	
		<?php if(empty($account_history)){ ?>
		<tr>
			<td colspan="3"><strong>This account has not had any purchases.</strong></td>
		</tr>
		<?php } else { ?>
			<?php foreach($account_history as $history) : ?>
			<tr>
				<td><?php echo datetime_to_text($history->datetime); ?></td>
				<td><?php echo CURRENCYSYMBOL.$history->amount; ?></td>
				<td><?php echo $history->description; ?></td>
			</tr>
			<?php endforeach; ?>
		<?php } ?>
	</tbody>
</table>