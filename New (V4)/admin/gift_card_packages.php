<?php 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../signin.php");}

$admin = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);

$admin_class = new Admin();

$active_page = "gift_cards";

$gift_card_packages = Gift_Card::find_all_packages();

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$total_count = count($gift_card_packages);
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM gift_card_packages LIMIT {$per_page} OFFSET {$pagination->offset()}";
$gift_card_packages = Gift_Card::find_by_sql($sql);

if(isset($_POST['create'])){
	$name = $_POST['name'];
	$amount = $_POST['amount'];
	$status = $_POST['status'];
	Gift_Card::create_package($name,$amount,$status);	
} else {
	$name = "";
	$amount = "";
	$status = "";
}

if(isset($_GET['edit'])){
	$id = trim($_GET['edit']);
	$package_data = Gift_Card::find_package_by_id($id);
	// $package_data = $package_data[0];
	if(isset($_POST['edit'])){
		$name = $_POST['name'];
		$amount = $_POST['amount'];
		$status = $_POST['status'];
		Gift_Card::edit_package($id,$name,$amount,$status);
	} else {
		$name = $package_data->name;
		$amount = $package_data->amount;
		$status = $package_data->status;
	}
}

if(isset($_GET['delete'])){
	$id = trim($_GET['delete']);
	if(isset($_POST['delete'])){
		Gift_Card::delete_package($id);
	}
}

$header_btn_right = '<a data-toggle="modal" href="#create" class="btn btn-default">Create Package</a>';

?>

<?php $page_title = "Gift Card Packages"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<?php if(empty($gift_card_packages)){ ?>
		<strong>Sorry, no token packages could be found.</strong>
	<?php } else { ?>
	<table class="table table-bordered">
	  <thead>
	    <tr>
			<th>ID</th>
			<th>Name</th>
			<th>Amount</th>
			<th>Status</th>
			<th>Actions</th>
	    </tr>
	  </thead>
	  <tbody>
		<?php foreach($gift_card_packages as $data): ?>
	    <tr>
			<td><?php echo $data->id; ?></td>
			<td><?php echo $data->name; ?></td>
			<td><?php echo $data->amount; ?></td>
			<td><?php echo User::convert_token_status($data->status); ?></td>
			<td><a href="gift_cards.php?edit=<?php echo $data->id; ?>">Edit</a> - <a href="gift_cards.php?delete=<?php echo $data->id; ?>">Delete</a></td>
	    </tr>
		<?php endforeach; ?>
	  </tbody>
	</table>

	<?php
		if($pagination->total_pages() > 1) {
		echo "<ul class=\"pagination\">";

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

	<?php } ?>

<form action="gift_cards.php?create=<?php echo (isset($_GET['create'])) ? $_GET['create'] : "0" ; ?>" method="POST" class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Create Package</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
      		<div class="col-md-12">
				<label>Package Name</label>
				<input type="text" required="required" class="form-control" name="name" value="<?php echo htmlentities($name); ?>" />
      		</div>
      	</div>
      	<br />
      	<div class="row">
      		<div class="col-md-6">
				<label>Amount</label>
				<input type="text" required="required" class="form-control" name="amount" value="<?php echo htmlentities($amount); ?>" />
      		</div>
      		<div class="col-md-6">
      			<label>Status</label>
				<select name="status" class="form-control">
					<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Hidden</option>
					<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Active</option>
				</select>
      		</div>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn btn-danger" type="submit" name="create">Create</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</form><!-- /.modal -->
		
	<?php if(isset($_GET['edit'])) {?>
		<form action="gift_cards.php?edit=<?php echo $_GET['edit']; ?>" method="POST" class="modal" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="false" style="display: block;">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title">Edit Package</h4>
		      </div>
		      <div class="modal-body">
		      	<div class="row">
		      		<div class="col-md-12">
						<label>Package Name</label>
						<input type="text" required="required" class="form-control" name="name" value="<?php echo htmlentities($name); ?>" />
		      		</div>
		      	</div>
		      	<br />
		      	<div class="row">
		      		<div class="col-md-6">
						<label>Amount</label>
						<input type="text" required="required" class="form-control" name="amount" value="<?php echo htmlentities($amount); ?>" />
		      		</div>
		      		<div class="col-md-6">
		      			<label>Status</label>
						<select name="status" class="form-control">
							<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Hidden</option>
							<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Active</option>
						</select>
		      		</div>
		      	</div>
		      </div>
		      <div class="modal-footer">
		        <a href="gift_cards.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="edit">Confirm</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</form><!-- /.modal -->
		<div class="modal-backdrop fade in"></div>
	<?php } ?>
	
	<?php if(isset($_GET['delete'])) {?>
		<form action="gift_cards.php?delete=<?php echo $_GET['delete']; ?>" method="POST" class="modal" id="delete" tabindex="-1" role="dialog" aria-labelledby="delete" aria-hidden="false" style="display: block;">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title">Delete Package</h4>
		      </div>
		      <div class="modal-body">
				<strong>Are you sure you want to delete this package?</strong>
		      </div>
		      <div class="modal-footer">
		        <a href="gift_cards.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="delete">Confirm</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</form><!-- /.modal -->
		<div class="modal-backdrop fade in"></div>
	<?php } ?>
	

<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>