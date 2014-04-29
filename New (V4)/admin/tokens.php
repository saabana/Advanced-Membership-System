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

$active_page = "tokens";

$token_packages = User::get_token_packages();

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$total_count = count($token_packages);
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM token_packages LIMIT {$per_page} OFFSET {$pagination->offset()}";
$token_packages = User::find_by_sql($sql);

if(isset($_POST['create'])){
	$name = $_POST['name'];
	$qty = $_POST['qty'];
	$status = $_POST['status'];
	User::create_package($name,$qty,$status);	
} else {
	$name = "";
	$qty = "";
	$status = "";
}

if(isset($_GET['edit'])){
	$id = trim($_GET['edit']);
	$package_data = User::get_package_data($id);
	$package_data = $package_data[0];
	if(isset($_POST['edit'])){
		$name = $_POST['name'];
		$qty = $_POST['qty'];
		$status = $_POST['status'];
		User::edit_token_package($id,$name,$qty,$status);
	} else {
		$name = $package_data->name;
		$qty = $package_data->qty;
		$status = $package_data->status;
	}
}

if(isset($_GET['delete'])){
	$id = trim($_GET['delete']);
	if(isset($_POST['delete'])){
		User::delete_token_package($id);
	}
}

$header_btn_right = '<a data-toggle="modal" href="#create" class="btn btn-default">Create Package</a>';

?>

<?php $page_title = "Token Packages"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<?php if(empty($token_packages)){ ?>
		<strong>Sorry, no token packages could be found.</strong>
	<?php } else { ?>
	<table class="table table-bordered">
	  <thead>
	    <tr>
	      <th>ID</th>
	      <th>Name</th>
	      <th>Quantity</th>
			<th>Status</th>
			<th>Actions</th>
	    </tr>
	  </thead>
	  <tbody>
		<?php foreach($token_packages as $data): ?>
	    <tr>
			<td><?php echo $data->id; ?></td>
			<td><?php echo $data->name; ?></td>
			<td><?php echo $data->qty; ?></td>
			<td><?php echo User::convert_token_status($data->status); ?></td>
			<td><a href="tokens.php?edit=<?php echo $data->id; ?>"><img src="../assets/img/pencil.png" alt="edit" class="edit_button" /></a> <a href="tokens.php?delete=<?php echo $data->id; ?>"><img src="../assets/img/delete.png" alt="delete" class="edit_button" /></a></td>
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

<form action="tokens.php?create=<?php echo (isset($_GET['create'])) ? $_GET['create'] : "0" ; ?>" method="POST" class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Create Package</h4>
      </div>
      <div class="modal-body">
		<label>Package Name</label>
		<input type="text" required="required" class="form-control" name="name" value="<?php echo htmlentities($name); ?>" />
		<br />
		<label>Number of Tokens</label>
		<input type="text" required="required" class="form-control" name="qty" value="<?php echo htmlentities($qty); ?>" />
		<br />
		<label>Status</label>
		<select name="status" class="form-control">
			<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Hidden</option>
			<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Active</option>
		</select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn btn-danger" type="submit" name="create">Create</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</form><!-- /.modal -->
		
	<?php if(isset($_GET['edit'])) {?>
		<form action="tokens.php?edit=<?php echo $_GET['edit']; ?>" method="POST" class="modal" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="false" style="display: block;">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title">Edit Package</h4>
		      </div>
		      <div class="modal-body">
				<label>Package Name</label>
				<input type="text" required="required" class="form-control" name="name" value="<?php echo htmlentities($name); ?>" />
				<br />
				<label>Number of Tokens</label>
				<input type="text" required="required" class="form-control" name="qty" value="<?php echo htmlentities($qty); ?>" />
				<br />
				<label>Status</label>
				<select name="status" class="form-control">
					<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Hidden</option>
					<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Active</option>
				</select>
		      </div>
		      <div class="modal-footer">
		        <a href="tokens.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="edit">Confirm</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</form><!-- /.modal -->
		<div class="modal-backdrop fade in"></div>
	<?php } ?>
	
	<?php if(isset($_GET['delete'])) {?>
		<form action="tokens.php?delete=<?php echo $_GET['delete']; ?>" method="POST" class="modal" id="delete" tabindex="-1" role="dialog" aria-labelledby="delete" aria-hidden="false" style="display: block;">
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
		        <a href="tokens.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="delete">Confirm</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</form><!-- /.modal -->
		<div class="modal-backdrop fade in"></div>
	<?php } ?>
	

<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>