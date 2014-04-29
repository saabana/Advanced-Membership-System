<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("includes/inc_files.php");

if(!$session->is_logged_in()) {redirect_to("login.php");}

$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
$current_page = "buy_tokens";
$location = "gateway/paypal.php";

$token_packages = User::get_token_packages();

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$total_count = count($token_packages);
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM token_packages WHERE status = '1' LIMIT {$per_page} OFFSET {$pagination->offset()}";
$token_packages = User::find_by_sql($sql);

?>
<?php $page_title = "Buy Tokens"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<?php echo output_message($message); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

	<?php if(empty($token_packages)){ ?>
		Sorry, no token packages could be found.
	<?php } else { ?>
	<table class="table">
	  <thead>
	    <tr>
	      <th>Name</th>
	      <th>Number of Tokens</th>
			<th>Package Price</th>
			<th style="width: 100px;"></th>
	    </tr>
	  </thead>
	  <tbody>
		<?php foreach($token_packages as $data): ?>
	    <tr>
			<td><?php echo $data->name; ?></td>
			<td><?php echo $data->qty; ?></td>
			<td><?php echo CURRENCYSYMBOL . TOKEN_PRICE * $data->qty; ?></td>
			<td><a href="buy_tokens.php?purchase=<?php echo $data->id; ?>" class="btn btn-primary" style="padding: 1px 5px;">Purchase</a></td>
	    </tr>
		<?php endforeach; ?>
	  </tbody>
	</table>

	<?php
		if($pagination->total_pages() > 1) {
		echo "<div class='pagination pagination-centered'><ul>";

			for($i=1; $i <= $pagination->total_pages(); $i++) {
				if($i == $page) {
					echo " <li class='active'><a>{$i}</a></li> ";
				} else {
					echo " <li><a href=\"find.php?search={$query}&amp;filter={$_GET['filter']}&amp;page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>
	
		<?php if(isset($_GET['purchase'])) {?>
			<form action="<?php echo $location; ?>" method="POST" id="purchase" class="modal">
			    <div class="modal-header"><a href="buy_tokens.php" class="close" data-dismiss="modal">×</a>
			        <h3 id="myModalLabel">Purchase Tokens</h3>
			    </div>
			    <div class="modal-body">
			      <strong>Are you sure you want to purchase this package?</strong>
					<input type="hidden" name="id" value="<?php echo $_GET['purchase']; ?>" />
			    </div>
			    <div class="modal-footer">
				   <a href="buy_tokens.php" class="btn">Close</a>
				   <button class="btn btn-danger" type="submit" name="purchase">Purchase</button>
				 </div>
			</form>​
			<div class="modal-backdrop fade in"></div>
		<?php } ?>
	
	<?php } ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>