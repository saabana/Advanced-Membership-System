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

$users = User::find_all();

$active_page = "users";

if(isset($_GET['search']) && $_GET['search'] != ""){
	$search = true;
	$query = preg_replace('#[^a-z 0-9?!]#i', '', $_GET['search']);
	if($_GET['filter'] == "username"){
		$sql = "SELECT * FROM users WHERE username LIKE '%$query%'";
	} else if($_GET['filter'] == "first name") {
		$sql = "SELECT * FROM users WHERE first_name LIKE '%$query%'";
	} else if($_GET['filter'] == "last name") {
		$sql = "SELECT * FROM users WHERE last_name LIKE '%$query%'";
	} else if($_GET['filter'] == "full name"){
		$sql = "SELECT * FROM users WHERE CONCAT(first_name,' ',last_name) like '%$query%'";
	} else if($_GET['filter'] == "user_id"){
		$sql = "SELECT * FROM users WHERE user_id LIKE '%$query%'";
	} else if($_GET['filter'] == "country"){
		$sql = "SELECT * FROM users WHERE country LIKE '%$query%'";
	}
	$query_data = User::find_by_sql($sql);
	
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$per_page = PAGINATION_PER_PAGE;
	$total_count = count($query_data);
	$pagination = new Pagination($page, $per_page, $total_count);
	$sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
	$query_data = User::find_by_sql($sql);
} else {
	$search = true;
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$per_page = PAGINATION_PER_PAGE;
	$total_count = User::count_all();
	$pagination = new Pagination($page, $per_page, $total_count);
	$sql = "SELECT * FROM users LIMIT {$per_page} OFFSET {$pagination->offset()}";
	$query_data = User::find_by_sql($sql);
}

?>

<?php protect($admin->user_level,"293847,527387","index.php"); ?>

<?php $page_title = "Users"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>
	
	<div class="title">
		<h1><?php echo $page_title; ?> <a href="create_user.php" class="btn btn-primary btn-small">Create User</a></h1>
	</div>
	
	<div class="row-fluid">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>

	<?php echo output_message($message); ?>

	<form action="users.php" method="GET" class="form-search">
		<input type="text" placeholder="Search..." name="search" class="input-xlarge">
		<select name="filter">
			<option value="full name">Full Name</option>
			<option value="first name">First Name</option>
			<option value="last name">Last Name</option>
			<option value="username">Username</option>
			<option value="user_id">User ID</option>
			<option value="country">Country</option>
		</select>
		<button type="submit" class="btn">Search</button>
	</form>
	
	<table class="table table-condensed">
		<thead>
			<tr>
				<th>ID</th>
				<th>Username</th>
				<th>Email</th>
				<th>Activated</th>
				<th>Suspended</th>
				<th>Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($query_data as $user) : ?>
			<tr>
				<td><?php echo $user->user_id ?></td>
				<td><?php echo $user->username ?></td>
				<td><?php echo $user->email ?></td>
				<td><?php echo convert_boolean($user->activated) ?></td>
				<td><?php echo convert_boolean_sus($user->suspended) ?></td>
				<td><a href="user_dashboard.php?page=overview&amp;user_id=<?php echo $user->user_id ?>"><img src="../assets/img/pencil.png" alt="edit" class="edit_button" /></a></td>
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
					echo " <li><a href=\"users.php?page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>
	


<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>