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

$active_page = "content_protection";

$protection = Content_Protection::find_all();

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$total_count = count($protection);
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM protected_content LIMIT {$per_page} OFFSET {$pagination->offset()}";
$protection = Content_Protection::find_by_sql($sql);

if(isset($_POST['create'])){
	$name = $_POST['name'];
	$description = $_POST['description'];
	$amount = $_POST['amount'];
	$link = $_POST['link'];
	$status = $_POST['status'];

	Content_Protection::create_protection($name,$description,$amount,$link,$status);
} else {
	$name = "";
	$description = "";
	$amount = "";
	$link = "";
	$status = "";
}

if(isset($_GET['edit'])){
	$id = trim($_GET['edit']);
	$protection_data = Content_Protection::find_by_id($id);

	if(isset($_POST['edit'])){

		$name = $_POST['name'];
		$description = $_POST['description'];
		$amount = $_POST['amount'];
		$link = $_POST['link'];
		$status = $_POST['status'];

		Content_Protection::update_protection($id,$name,$description,$amount,$link,$status);
	} else {
		$name = $protection_data->name;
		$description = $protection_data->description;
		$amount = $protection_data->amount;
		$link = $protection_data->link;
		$status = $protection_data->status;
	}
}

if(isset($_GET['delete'])){
	$id = trim($_GET['delete']);
	if(isset($_POST['delete'])){
		Content_Protection::delete_protection($id);
	}
}

$header_btn_right = '<a data-toggle="modal" href="#create" class="btn btn-default">Create Protection</a>';

?>

<?php $page_title = "Content Protection"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<?php if(empty($protection)){ ?>
		<strong>Sorry, no protection content could be found.</strong>
	<?php } else { ?>
	<table class="table table-bordered">
	  <thead>
	    <tr>
			<th>ID</th>
			<th>Name</th>
			<th>Amount</th>
			<th>Description</th>
			<th>Link</th>
			<th>Status</th>
			<th>Actions</th>
	    </tr>
	  </thead>
	  <tbody>
		<?php foreach($protection as $data): ?>
	    <tr>
			<td><?php echo $data->id; ?></td>
			<td><?php echo $data->name; ?></td>
			<td><?php echo $data->amount; ?></td>
			<td><?php echo $data->link; ?></td>
			<td><?php echo $data->description; ?></td>
			<td><?php echo ($data->status == 0) ? "Hidden" : "Visible"; ?></td>
			<td><a href="content_protection.php?edit=<?php echo $data->id; ?>">Edit</a> - <a href="content_protection.php?delete=<?php echo $data->id; ?>">Delete</a></td>
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
					echo " <li><a href=\"content_protection.php?page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>

	<?php } ?>

<form action="content_protection.php?create=<?php echo (isset($_GET['create'])) ? $_GET['create'] : "0" ; ?>" method="POST" class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Create Protection</h4>
      </div>
      <div class="modal-body">

      	<div class="row">
      		<div class="col-md-4">
				<label>Name</label>
				<input type="text" required="required" class="form-control" name="name" value="<?php echo htmlentities($name); ?>" />
      		</div>
      		<div class="col-md-4">
				<label>Price</label>
				<input type="text" required="required" class="form-control" name="amount" value="<?php echo htmlentities($amount); ?>" />
      		</div>
      		<div class="col-md-4">
				<label>Status</label>
				<select name="status" class="form-control">
					<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Hidden</option>
					<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Active</option>
				</select>
      		</div>
      	</div>
      	<br />
      	<div class="row">
      		<div class="col-md-12">
				<label>Link (exclude <?php echo WWW; ?>)</label>
				<input type="text" required="required" class="form-control" name="link" value="<?php echo htmlentities($link); ?>" />
      		</div>
      	</div>
      	<br />
      	<div class="row">
      		<div class="col-md-12">
				<label>Description</label>
				<textarea type="text" required="required" class="form-control" name="description"><?php echo htmlentities($description); ?></textarea>
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

		<form action="content_protection.php?edit=<?php echo $_GET['edit']; ?>" method="POST" class="modal" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="false" style="display: block;">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <a href="content_protection.php" class="close">&times;</a>
		        <h4 class="modal-title">Edit Protection</h4>
		      </div>
		      <div class="modal-body">
		      	<div class="row">
		      		<div class="col-md-4">
						<label>Name</label>
						<input type="text" required="required" class="form-control" name="name" value="<?php echo htmlentities($name); ?>" />
		      		</div>
		      		<div class="col-md-4">
						<label>Price</label>
						<input type="text" required="required" class="form-control" name="amount" value="<?php echo htmlentities($amount); ?>" />
		      		</div>
		      		<div class="col-md-4">
						<label>Status</label>
						<select name="status" class="form-control">
							<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Hidden</option>
							<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Active</option>
						</select>
		      		</div>
		      	</div>
		      	<br />
		      	<div class="row">
		      		<div class="col-md-12">
						<label>Link (exclude <?php echo WWW; ?>)</label>
						<input type="text" required="required" class="form-control" name="link" value="<?php echo htmlentities($link); ?>" />
		      		</div>
		      	</div>
		      	<br />
		      	<div class="row">
		      		<div class="col-md-12">
						<label>Description</label>
						<textarea type="text" required="required" name="description" class="form-control"><?php echo htmlentities($description); ?></textarea>
		      		</div>
		      	</div>
		      	<br />
		      	<div class="row">
		      		<div class="col-md-12">
						<label>Code</label>
						<pre>&lt;?php $content_<?php echo $id; ?> = Content_Protection::protect(<?php echo $id; ?>,"&lt;?php echo $user->user_level; ?&gt;"); if($content_<?php echo $id; ?> == "approved"){ ?&gt; <br /><br />  Protected Content Here <br /><br />&lt;?php } else { echo $content_<?php echo $id; ?>; } ?&gt;</pre>
		      		</div>
		      	</div>
		      </div>
		      <div class="modal-footer">
		        <a href="content_protection.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="edit">Confirm</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</form><!-- /.modal -->
		<div class="modal-backdrop fade in"></div>
	<?php } ?>
	
	<?php if(isset($_GET['delete'])) {?>
		<form action="content_protection.php?delete=<?php echo $_GET['delete']; ?>" method="POST" class="modal" id="delete" tabindex="-1" role="dialog" aria-labelledby="delete" aria-hidden="false" style="display: block;">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <a href="content_protection.php" class="close">&times;</a>
		        <h4 class="modal-title">Delete Package</h4>
		      </div>
		      <div class="modal-body">
				<strong>Are you sure you want to delete this package?</strong>
		      </div>
		      <div class="modal-footer">
		        <a href="content_protection.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="delete">Confirm</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</form><!-- /.modal -->
		<div class="modal-backdrop fade in"></div>
	<?php } ?>
	

<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>