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

if(isset($_GET['id'])){
	$email_data =  Email::email_template_data($_GET['id']);
} else {
	redirect_to("email_templates.php");
}

if(isset($_POST['update_template'])){
	$name = $_POST['name'];
	$description = $_POST['description'];
	$content = $database->escape_value(trim($_POST['content']));
	if($email_data->permanent == 0){
		$status = $_POST['status'];
	} else {
		$status = $email_data->status;
	}
	if($name == "" || $description == "" || $content == ""){
		// Error Message
	} else {
		Email::update_template($email_data->id,$name,$description,$content,$status);
	}
}

$location = WWW.ADMINDIR."template_settings.php?id=".$email_data->id;
$page_title = "Edit: ".$email_data->name;
$header_btn = "<a href='email_templates.php' class='btn btn-default btn-small' style='margin: -6px 5px 0px 0px;'>Back</a>";

?>

<?php require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>

	<?php echo output_message($message); ?>

	<form action="<?php echo $location; ?>" method="post" style="margin: 0px 0px 2px;">
		
		<div class="row">
			<div class="col-md-1">
				<label>ID</label>
				<input type="text" class="form-control" name="id" disabled="disabled" value="<?php echo $email_data->id; ?>">
			</div>
			<div class="col-md-4">
				<label>Name (Also Email Subject)</label>
				<input type="text" class="form-control" name="name" value="<?php echo $email_data->name; ?>">
			</div>
			<div class="col-md-5">
				<label>Description</label>
				<input type="text" class="form-control" name="description" value="<?php echo $email_data->description; ?>">
			</div>
			<div class="col-md-2">
				<label>Status</label>
				<select name="status" class="form-control"<?php echo ($email_data->permanent == 1) ? ' disabled="disabled"' : ''; ?>>
					<option value="0"<?php echo ($email_data->status == 0) ? ' selected="selected"' : ''; ?>>Deactivated</option>
					<option value="1"<?php echo ($email_data->status == 1) ? ' selected="selected"' : ''; ?>>Activated</option>
				</select>
			</div>
		</div>

		<div class="row" style="margin-top: 15px;">
			<div class="col-md-12">
				<label>Email Content</label>
				<textarea class="form-control" name="content" rows="10"><?php echo $email_data->content; ?></textarea>
				<label style="margin-top: 10px;">Available Shortcodes: <?php echo $email_data->allowed_shortcodes; ?></label>
			</div>
		</div>

		<div class="form-actions" style="text-align: center;margin:20px -10px 0px">
			<input type="submit" name="update_template" class="btn btn-primary" value="Update Template">
		</div>

	</form>
<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>