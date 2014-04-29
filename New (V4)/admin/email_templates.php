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

$active_page = "email_templates";

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = PAGINATION_PER_PAGE;
$total_count = Email::count_all();
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM email_templates LIMIT {$per_page} OFFSET {$pagination->offset()}";
$query_data = Email::find_by_sql($sql);


?>

<?php $page_title = "Email Templates"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="center">ID</th>
				<th>Name</th>
				<th>Description</th>
				<th class="center">Status</th>
				<th class="center">Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($query_data as $template) : ?>
			<tr>
				<td class="center"><?php echo $template->id; ?></td>
				<td><?php echo $template->name; ?></td>
				<td><?php echo $template->description; ?></td>
				<td class="center"><?php echo convert_boolean_full($template->status); ?></td>
				<td class="center"><a href="template_settings.php?id=<?php echo $template->id ?>"><img src="../assets/img/pencil.png" alt="edit" class="edit_button" /></a></td>
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
					echo " <li><a href=\"template_settings.php?page={$i}\">{$i}</a></li> "; 
				}
			}

		}

		echo "</ul>";
	?>

<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>