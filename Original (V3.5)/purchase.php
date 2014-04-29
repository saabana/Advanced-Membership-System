<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("includes/inc_files.php");

if(!$session->is_logged_in()) {redirect_to("login.php");}

$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
$current_page = "purchase_access";
$location = "gateway/paypal.php";

$packages = User::get_purch_levels();

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$total_count = count($packages);
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM user_levels WHERE purchasable = '1' LIMIT {$per_page} OFFSET {$pagination->offset()}";
$packages = User::find_by_sql($sql);

if(isset($_POST['purchase'])){
	$id = $database->escape_value($_GET['purchase']);
	User::purchase_access($user->user_id, $id);
}

?>
<?php $page_title = "Purchase Access"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<?php echo output_message($message); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

	<?php if(empty($packages)){ ?>
		<strong>Sorry, no access levels could be found.</strong>
	<?php } else { ?>
	<table class="table">
	  <thead>
	    <tr>
	      <th>Name</th>
			<th>Expires</th>
	      <th>Time</th>
			<th>Package Price</th>
			<th style="width: 100px;"></th>
	    </tr>
	  </thead>
	  <tbody>
		<?php foreach($packages as $data): ?>
	    <tr>
			<td><?php echo $data->level_name; ?></td>
			<td><?php echo convert_boo($data->timed_access); ?></td>
			<td><?php echo $data->access_time." ".User::convert_time_type($data->time_type); ?></td>
			<td><?php echo $data->amount; ?> Tokens<?php if(PP_SERVICE_PURCHASE == "YES"){ echo " / ".CURRENCYSYMBOL.$data->price; } ?></td>
			<td style="text-align: center !important;"><?php if(!in_array($data->level_id, explode(",", $user->user_level))){ ?><a href="purchase.php?purchase=<?php echo $data->level_id; ?>" class="btn btn-success" style="padding: 1px 5px;">Purchase</a><?php if(PP_SERVICE_PURCHASE == "YES"){ ?><form action="<?php echo $location; ?>" method="POST" style="margin: 0;"><input type="hidden" name="id" value="<?php echo $data->level_id; ?>" /><input class="btn btn-primary" type="submit" name="purchase_service" value="PayPal" style="padding: 1px 5px;margin-top: 2px;"></form>​<?php } } else { ?><span style="color:green;font-weight:bold;">Active</span><?php } ?></td>
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
		<form action="purchase.php?purchase=<?php echo $_GET['purchase']; ?>" method="POST" id="purchase" class="modal">
		    <div class="modal-header"><a href="purchase.php" class="close" data-dismiss="modal">×</a>
		        <h3 id="myModalLabel">Purchase Access</h3>
		    </div>
		    <div class="modal-body">
		      <strong>Are you sure you want to purchase this access level?</strong>
				<input type="hidden" name="id" value="<?php echo $_GET['purchase']; ?>" />
		    </div>
		    <div class="modal-footer">
			   <a href="purchase.php" class="btn">Close</a>
			   <button class="btn btn-danger" type="submit" name="purchase">Purchase</button>
			 </div>
		</form>​
		<div class="modal-backdrop fade in"></div>
	<?php } ?>

<?php } ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>