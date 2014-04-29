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

$active_page = "groups";

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = PAGINATION_PER_PAGE;
$total_count = User::count_all_levels();
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM user_levels LIMIT {$per_page} OFFSET {$pagination->offset()}";
$query_data = User::find_by_sql($sql);

if(isset($_POST['create'])){
	$group_name = trim($_POST['group_name']);
	$signup = trim($_POST['signup']);
	$redirect_page = trim($_POST['redirect_page']);
	$purchasable = trim($_POST['purchasable']);
	$amount = trim($_POST['amount']);
	$price = trim($_POST['price']);
	$timed_access = trim($_POST['timed_access']);
	$time_type = trim($_POST['time_type']);
	$access_time = trim($_POST['access_time']);
	Admin::create_group($group_name, $signup, $redirect_page, $purchasable, $amount, $price, $timed_access, $time_type, $access_time);
} else {
	$group_name = "";
	$signup = "";
	$redirect_page = "index.php";
	$purchasable = "";
	$amount = "";
	$price = "";
	$timed_access = "";
	$time_type = "";
	$access_time = "";
}

$header_btn_right = '<a data-toggle="modal" href="#create" class="btn btn-default btn-small">Create Group</a>';

?>

<?php protect($admin->user_level,"293847,527387","index.php"); ?>

<?php $page_title = "Groups"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>
		
	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>

	<?php echo output_message($message); ?>
	
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="center">Group ID</th>
				<th>Group Name</th>
				<th>Default on Signup</th>
				<th>Number of Users</th>
				<th class="center">Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($query_data as $group) : ?>
			<tr>
				<td class="center"><?php echo $group->level_id; ?></td>
				<td><?php echo $group->level_name; ?></td>
				<td><?php echo convert_boolean($group->auto); ?></td>
				<td><?php echo Admin::count_all_users_in_group($group->level_id); ?></td>
				<td class="center"><a href="group_settings.php?group_id=<?php echo $group->level_id; ?>"><img src="../assets/img/pencil.png" alt="edit" class="edit_button" /></a></td>
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
					echo " <li><a href=\"groups.php?page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>


<div class="clear"><!-- --></div>

<style type="text/css">
	.row.bottom{margin-bottom: 10px;}
</style>

<form action="groups.php" method="POST" class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Create Group</h4>
      </div>
      <div class="modal-body">
		<div class="row bottom">
			<div class="col-md-6">
				<label>Group Name</label>
				<input type="text" name="group_name" class="form-control" required="required" value="<?php echo htmlentities($group_name); ?>" />
			</div>
			<div class="col-md-6">
				<label>Default on Signup</label>
				<select name="signup" class="form-control" required="required" value="<?php echo $signup; ?>">
					<option value="0">No</option>
					<option value ="1">Yes</option>
				</select>
			</div>
		</div>

		<div class="row bottom">
			<div class="col-md-6">
				<label>Login Redirect to:</label>
				<input type="text" name="redirect_page" class="form-control" required="required" value="<?php echo htmlentities($redirect_page); ?>" />
			</div>
			<div class="col-md-6">
				<label>Purchasable</label>
				<select name="purchasable" class="form-control" required="required" value="<?php echo $purchasable; ?>">
					<option value="0">No</option>
					<option value ="1">Yes</option>
				</select>
			</div>
		</div>
			

		<div class="row bottom">
			<div class="col-md-6">
				<label>Token Price</label>
				<input type="text" name="amount" class="form-control" value="<?php echo htmlentities($amount); ?>" />
			</div>
			<div class="col-md-6">
				<label><?php echo CURRENCY_CODE; ?> Price</label>
				<input type="text" name="price" class="form-control" value="<?php echo htmlentities($price); ?>" />
			</div>
		</div>

		<div class="row bottom">
			<div class="col-md-6">
				<label>Timed Access</label>
				<select name="timed_access" class="form-control" value="<?php echo $timed_access; ?>">
					<option value="0">No</option>
					<option value ="1">Yes</option>
				</select>
			</div>
			<div class="col-md-6">
				<label>Time Type</label>
				<select name="time_type" class="form-control" value="<?php echo $time_type; ?>">
					<option value="0">Day(s)</option>
					<option value ="1">Month(s)</option>
					<option value ="2">Year(s)</option>
				</select>
			</div>
		</div>

		<div class="row bottom">
			<div class="col-md-6">
				<label>Access Time</label>
				<input type="text" name="access_time" class="form-control" value="<?php echo htmlentities($access_time); ?>" />
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


<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>