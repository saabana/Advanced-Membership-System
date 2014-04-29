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

$active_page = "partial_protection";

?>

<?php $page_title = "Partial Page Protection"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<?php protect($admin->user_level,"293847,527387","index.php"); ?>

	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	<?php echo output_message($message); ?>
	
	<div class="row">
		<div class="col-md-12">
			<select data-placeholder="Please select your user levels..." id="user_level" class="form-control chzn-select" multiple value="<?php echo $user_level ?>">
			<?php $user_levels = explode(",", $user->user_level); foreach(User::get_site_levels() as $level){ ?>
				<option value="<?php echo $level->level_id; ?>"<?php if(in_array($level->level_id, $user_levels)){  echo ' selected="selected"'; } ?>><?php echo $level->level_name; ?></option>
			<?php } ?>
			</select>
		</div>
	</div>
	
	<br />
	
	<div class="row">
		<div class="col-md-12 center">
			<button class="btn btn-primary" id="generate_protection">Generate Code</button>
		</div>
	</div>
	
	<br />
	
	<div id="result"></div>


<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>

<script type="text/javascript">
$(document).ready(function(){
	$("#generate_protection").click(function(){
		var levels = $("#user_level").val();
		if(!levels){
			$("#result").html("<pre>Please select as least 1 user level</pre>");
		} else {
			$("#result").html("<pre>Please paste the following code inside of the page you would like to protect: <br /><em>Note: change $user to $admin if you are going to use this code within the admin panel.</em> <br /><br />&lt;?php if(partial_protect($user->user_level,\""+levels+"\",\"index.php\")){ ?&gt; <br />     <em>Has Correct Access. Place all of the html code here.</em> <br />&lt;?php } else { ?&gt; <br />     <em>Doesn't have the Correct Access. Display error message here.</em> <br />&lt;?php } ?&gt; </pre>");
		}
	});
});
</script>