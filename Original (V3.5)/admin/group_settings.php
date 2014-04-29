<?php 

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2012 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/

require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../login.php");}

$admin = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);

$admin_class = new Admin();

if(isset($_GET['group_id']) && $_GET['group_id'] != ""){
		$admin = Admin::find_by_id($_SESSION['masdyn']['ams']['user_id']);
		$group_id = $_GET['group_id'];
		$location = "group_settings.php?group_id=".$group_id."";
		
		$active_page = "groups";
		
		$sql = "SELECT * FROM user_levels WHERE level_id = {$group_id} ";
		$result = $database->query($sql);
		$group = $database->fetch_array($result);
	
} else {
	redirect_to("groups.php");
}

if (isset($_POST['submit'])) { 

	$group_name = trim($_POST['group_name']);
	$signup = trim($_POST['signup']);
	$redirect_page = trim($_POST['redirect_page']);
	$purchasable = trim($_POST['purchasable']);
	$amount = trim($_POST['amount']);
	$price = trim($_POST['price']);
	$timed_access = trim($_POST['timed_access']);
	$time_type = trim($_POST['time_type']);
	$access_time = trim($_POST['access_time']);

	if ($group_name != "" && $signup != "") {
		Admin::update_group($group_id, $group_name, $signup, $redirect_page, $purchasable, $amount, $price, $timed_access, $time_type, $access_time);
	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Please complete all required fields</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}

} else { 
	$group_name = $group['level_name'];
	$signup = $group['auto'];
	$redirect_page = $group['redirect_page'];
	$purchasable = $group['purchasable'];
	$amount = $group['amount'];
	$price = $group['price'];
	$timed_access = $group['timed_access'];
	$time_type = $group['time_type'];
	$access_time = $group['access_time'];
}

if(isset($_POST['delete_group'])){
	Admin::delete_group($group_id);
}

if(isset($_POST['send_email'])) {
	$subject = $_POST['subject'];
	$email_message = $_POST['message'];
	if($subject != "" || $message != "") {
		Admin::email_group($group_id, $subject, $email_message);
	} else {
		$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
	}
}

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = PAGINATION_PER_PAGE;
$total_count = User::count_by_sql("SELECT COUNT(*) FROM users WHERE user_level LIKE '%$group_id%' ");
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT user_id,first_name,last_name,username,primary_group,gender FROM users WHERE user_level LIKE '%$group_id%'  LIMIT {$per_page} OFFSET {$pagination->offset()}";
$users_in_group = User::find_by_sql($sql);

// $users_in_group = User::find_by_sql("SELECT user_id,first_name,last_name,username,primary_group,gender FROM users WHERE user_level LIKE '%$group_id%' ");

?>

<?php protect($admin->user_level,"293847,527387","index.php"); ?>

<?php $page_title = "Group Settings"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>
	
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div class="row-fluid">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	
	<?php echo output_message($message); ?>

	<form action="<?php echo $location; ?>" method="POST">
	
		<div class="row-fluid">
			<div class="span4">
				<label>Group Name</label>
				<input type="text" name="group_name" class="span12" required="required" value="<?php echo htmlentities($group_name); ?>" />
			</div>
			<div class="span2">
				<label>Default on Signup</label>
				<select name="signup" class="span12" required="required" value="<?php echo $signup; ?>">
					<option value="1" <?php if($signup == '1') { echo 'selected="selected"';} ?>>Yes</option>
					<option value ="0" <?php if($signup == '0') { echo 'selected="selected"';} ?>>No</option>
				</select>
			</div>
			<div class="span4">
				<label>Login Redirect to:</label>
				<input type="text" name="redirect_page" class="span12" required="required" value="<?php echo htmlentities($redirect_page); ?>" />
			</div>
			<div class="span2">
				<label>Purchasable</label>
				<select name="purchasable" class="span12" required="required" value="<?php echo $purchasable; ?>">
					<option value="0" <?php if($purchasable == '0') { echo 'selected="selected"';} ?>>No</option>
					<option value ="1" <?php if($purchasable == '1') { echo 'selected="selected"';} ?>>Yes</option>
				</select>
			</div>
		</div>
	
		<div class="row-fluid">
			<div class="span2">
				<label>Token Price</label>
				<input type="text" name="amount" class="span12" value="<?php echo htmlentities($amount); ?>" />
			</div>
			<div class="span2">
				<label><?php echo CURRENCY_CODE; ?> Price</label>
				<input type="text" name="price" class="span12" value="<?php echo htmlentities($price); ?>" />
			</div>
			<div class="span2">
				<label>Timed Access</label>
				<select name="timed_access" class="span12" value="<?php echo $timed_access; ?>">
					<option value="0" <?php if($timed_access == '0') { echo 'selected="selected"';} ?>>No</option>
					<option value ="1" <?php if($timed_access == '1') { echo 'selected="selected"';} ?>>Yes</option>
				</select>
			</div>
			<div class="span3">
				<label>Access Expires In (Time Type)</label>
				<input type="text" name="access_time" class="span12" value="<?php echo htmlentities($access_time); ?>" />
			</div>			
			<div class="span3">
				<label>Time Type</label>
				<select name="time_type" class="span12" value="<?php echo $time_type; ?>">
					<option value="0" <?php if($time_type == '0') { echo 'selected="selected"';} ?>>Day(s)</option>
					<option value ="1" <?php if($time_type == '1') { echo 'selected="selected"';} ?>>Month(s)</option>
					<option value ="2" <?php if($time_type == '2') { echo 'selected="selected"';} ?>>Year(s)</option>
				</select>
			</div>
		</div>
	
		<div class="form-actions" style="text-align: center;margin:20px -10px -20px">
			<input class="btn btn-primary" type="submit" name="submit" value="Update" /> <button class="btn btn-danger" data-toggle="modal" href="#delete">Delete</button>
		</div>
	
	</form>
	
	<hr />
	<!-- find_in_set('{$user_level}',access) -->
	<h3>Users <button class="btn btn-primary btn-small" data-toggle="modal" href="#email_group">Group Email</button></h3>	
	
	<table class="table">
		<thead>
			<tr>
				<th>Full Name</th>
				<th>Username</th>
				<th>Gender</th>
				<th>Primary Group</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($users_in_group as $data) : ?>
			<tr>
				<td><?php echo $data->first_name." ".$data->last_name; ?></td>
				<td><?php echo $data->username; ?></td>
				<td><?php echo $data->gender; ?></td>
				<td><?php echo User::get_level_name($data->primary_group); ?></td>
				<td><a href="user_dashboard.php?page=overview&amp;user_id=<?php echo $data->user_id; ?>">View User</a></td>
			</tr>
			<?php endforeach; ?>
			<?php if(empty($users_in_group)) : ?>
			<tr>
				<td colspan="5"><strong>This group contains no active users.</strong></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<?php
		if($pagination->total_pages() > 1) {
		echo "<div class='pagination pagination-centered'><ul>";

			for($i=1; $i <= $pagination->total_pages(); $i++) {
				if($i == $page) {
					echo " <li class='active'><a>{$i}</a></li> ";
				} else {
					echo " <li><a href=\"$location&page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>


<div class="clear"><!-- --></div>

<form action="<?php echo $location; ?>" method="POST" id="delete" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Delete Group</h3>
	</div>
	<div class="modal-body">
		<strong>Are you sure about deleting this group? This action can't be reversed.</strong>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="delete_group" value="Confirm" />
	</div>
</form>

<form action="<?php echo $location; ?>" method="POST" id="email_group" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Email Group</h3>
	</div>
	<div class="modal-body">
		<label>Subject</label>
	   <input type="text" required="required" style="width: 98%;" name="subject" value="" />
		<label>Message</label>
		<textarea required="required" style="width: 98%;" name="message"></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="send_email" value="Send Email" />
	</div>
</form>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>