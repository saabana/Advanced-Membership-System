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

$cards = Gift_Card::find_all();

$active_page = "gift_cards";

if(isset($_GET['search']) && $_GET['search'] != ""){
	$search = true;
	$query = preg_replace('#[^-a-z 0-9?!]#i', '', $_GET['search']);
	$filter = $_GET['filter'];
	if($filter == "id") {
		$sql = "SELECT * FROM gift_cards WHERE id LIKE '%$query%'";
	} else if($filter == "package_id") {
		$sql = "SELECT * FROM gift_cards WHERE package_id LIKE '%$query%'";
	} else if($filter == "code") {
		$sql = "SELECT * FROM gift_cards WHERE code LIKE '%$query%'";
	} else if($filter == "amount") {
		$sql = "SELECT * FROM gift_cards WHERE amount LIKE '%$query%'";
	} else if($filter == "from") {
		$sql = "SELECT * FROM gift_cards WHERE `gift_cards`.`from` LIKE '%$query%'";
	} else if($filter == "user_id") {
		$sql = "SELECT * FROM gift_cards WHERE user_id LIKE '%$query%'";
	} else if($filter == "status") {
		$sql = "SELECT * FROM gift_cards WHERE status LIKE '%$query%'";
	}

	$query_data = Gift_Card::find_by_sql($sql);
	
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$per_page = PAGINATION_PER_PAGE;
	$total_count = count($query_data);
	$pagination = new Pagination($page, $per_page, $total_count);
	$sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
	$query_data = Gift_Card::find_by_sql($sql);
} else {
	$search = false;
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$per_page = PAGINATION_PER_PAGE;
	$total_count = Gift_Card::count_all();
	$pagination = new Pagination($page, $per_page, $total_count);
	$sql = "SELECT * FROM gift_cards LIMIT {$per_page} OFFSET {$pagination->offset()}";
	$query_data = Gift_Card::find_by_sql($sql);
	$query = "";
	$filter = "";
}

if(isset($_GET['edit'])){
	$id = trim($_GET['edit']);
	$card_data = Gift_Card::find_by_id($id);
	if(isset($_POST['edit'])){
		$package_id = $_POST['package_id'];
		$code = $_POST['code'];
		$amount = $_POST['amount'];
		$from = $_POST['from'];
		$user_id = $_POST['user_id'];
		$status = $_POST['status'];
		$purchased = $_POST['purchased'];
		$date_used = $_POST['date_used'];
		Gift_Card::edit_card($id,$package_id,$code,$amount,$from,$user_id,$status,$purchased,$date_used);
	} else {
		$id = $card_data->id;
		$package_id = $card_data->package_id;
		$code = $card_data->code;
		$amount = $card_data->amount;
		$from = $card_data->from;
		$user_id = $card_data->user_id;
		$status = $card_data->status;
		$purchased = $card_data->purchased;
		$date_used = $card_data->date_used;
	}
}

if(isset($_GET['delete'])){
	$id = trim($_GET['delete']);
	if(isset($_POST['delete'])){
		Gift_Card::delete_card($id);
	}
}

?>

<?php protect($admin->user_level,"293847,527387","index.php"); ?>

<?php $page_title = "Gift Cards"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>
	
	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>

	<?php echo output_message($message); ?>

	<form action="gift_cards.php" method="GET" class="form-search" style="margin-bottom: 10px;">
		<div class="input-group">
			<span class="input-group-addon">Search</span>
			<input type="text" name="search" id="search" placeholder="Please enter your query..." class="form-control search" required="required" value="<?php echo $query; ?>">
			<div class="input-group-addon styled-select">
				<select name="filter">
					<option value="id"<?php if($filter == "id"){echo ' selected="selected"';} ?>>ID</option>
					<option value="package_id"<?php if($filter == "package_id"){echo ' selected="selected"';} ?>>Package ID</option>
					<option value="code"<?php if($filter == "code"){echo ' selected="selected"';} ?>>Code</option>
					<option value="amount"<?php if($filter == "amount"){echo ' selected="selected"';} ?>>Amount</option>
					<option value="from"<?php if($filter == "from"){echo ' selected="selected"';} ?>>From</option>
					<option value="user_id"<?php if($filter == "user_id"){echo ' selected="selected"';} ?>>User ID</option>
					<option value="status"<?php if($filter == "status"){echo ' selected="selected"';} ?>>Status</option>
				</select>
			</div>
			<span class="input-group-btn">
				<button class="btn btn-primary search-button" type="submit">Search</button>
			</span>
		</div>
	</form>
	
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="center">ID</th>
				<th>Package</th>
				<th>Code</th>
				<th>Amount</th>
				<th>From</th>
				<th>Purchased</th>
				<th>Date Used</th>
				<th>User ID</th>
				<th>Status</th>
				<th class="center">Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($query_data as $data) : ?>
			<tr>
				<td class="center"><?php echo $data->id; ?></td>
				<td class="center"><?php echo $data->package_id; ?></td>
				<td><?php echo $data->code; ?></td>
				<td><?php echo $data->amount; ?></td>
				<td><?php echo $data->from; ?></td>
				<td class="center"><?php echo $data->purchased; ?></td>
				<td class="center"><?php echo ($data->date_used == "0000-00-00 00:00:00") ? "Not Used" : $data->date_used; ?></td>
				<td class="center"><?php echo ($data->user_id == "0") ? "None Assigned" : $data->user_id; ?></td>
				<td class="center"><?php if($data->status == 0){echo "Unused";}else if($data->status == 1){echo "Used";}else if($data->status == 2){echo "Suspended";} ?></td>
				<td class="center"><a href="gift_cards.php?edit=<?php echo $data->id; ?>"><img src="../assets/img/pencil.png" alt="edit" class="edit_button" /></a> <a href="gift_cards.php?delete=<?php echo $data->id; ?>"><img src="../assets/img/delete.png" alt="delete" class="edit_button" /></a></td>
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
					echo " <li><a href=\"gift_cards.php?page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>
			
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
		      		<div class="col-md-3">
		      			<label>ID</label>
						<input type="text" required="required" class="form-control" name="id" value="<?php echo htmlentities($id); ?>" disabled="disabled" />
		      		</div>
		      		<div class="col-md-3">
		      			<label>Package ID</label>
						<input type="text" required="required" class="form-control" name="package_id" value="<?php echo htmlentities($package_id); ?>" />
		      		</div>
		      		<div class="col-md-3">
		      			<label>Amount</label>
						<input type="text" required="required" class="form-control" name="amount" value="<?php echo htmlentities($amount); ?>" />
		      		</div>
		      		<div class="col-md-3">
		      			<label>Status</label>
						<select name="status" class="form-control">
							<option value="0"<?php if($status == 0){echo " selected='selected'";} ?>>Unused</option>
							<option value="1"<?php if($status == 1){echo " selected='selected'";} ?>>Used</option>
							<option value="2"<?php if($status == 2){echo " selected='selected'";} ?>>Suspended</option>
						</select>
		      		</div>
		      	</div>
		      	<br />
		      	<div class="row">
		      		<div class="col-md-12">
		      			<label>Code</label>
						<input type="text" required="required" class="form-control" name="code" value="<?php echo htmlentities($code); ?>" />
		      		</div>
		      	</div>
		      	<br />
		      	<div class="row">
		      		<div class="col-md-3">
		      			<label>From</label>
						<input type="text" required="required" class="form-control" name="from" value="<?php echo htmlentities($from); ?>" />
		      		</div>
		      		<div class="col-md-3">
		      			<label>Purchased</label>
						<input type="text" required="required" class="form-control" name="purchased" value="<?php echo htmlentities($purchased); ?>" />
		      		</div>
		      		<div class="col-md-3">
		      			<label>Date Used</label>
						<input type="text" required="required" class="form-control" name="date_used" value="<?php echo htmlentities($date_used); ?>" />
		      		</div>
		      		<div class="col-md-3">
		      			<label>User ID</label>
						<input type="text" required="required" class="form-control" name="user_id" value="<?php echo htmlentities($user_id); ?>" />
		      		</div>
		      	</div>

		      </div>
		      <div class="modal-footer">
		        <a href="gift_cards.php" class="btn btn-default">Close</a>
		        <button class="btn btn-danger" type="submit" name="edit">Edit</button>
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
				<strong>Are you sure you want to delete this gift card?</strong>
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