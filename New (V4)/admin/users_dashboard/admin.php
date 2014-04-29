<?php 

$location = "user_dashboard.php?page=admin&user_id=$current_user->user_id";

if (isset($_POST['submit'])) { 

	$user_level = implode(",",$_POST['user_level']);
	$activated = $_POST['activated'];
	$suspended = $_POST['suspended'];
	$tokens = $_POST['tokens'];
	$bank_tokens = $_POST['bank_tokens'];
	$primary_group = $_POST['primary_group'];

	if($user_level != ""){
		Admin::update_account($current_user->user_id, $user_level, $activated, $suspended, $tokens, $bank_tokens, $primary_group);
	} else {
		// Display error message
	}

} 

if (isset($_POST['resend_code'])) {
	$email = trim($_POST['email']);
	$user_id = trim($_POST['user_id']);
	Account_Lock::check_resend_code($current_user->user_id, $current_user->email, $location);
}

if (isset($_POST['activate_lock'])) {
	Account_Lock::set_account_lock($current_user->email, $current_user->username, $current_user->user_id, $location);
}

if (isset($_POST['deactivate_lock'])) {
	Admin::check_lock_status($current_user->user_id, $location);
}

if (isset($_POST['send_new_password'])) {
	Admin::send_new_password($current_user->email, $location);
}

if(isset($_POST['delete_account'])){
	Admin::delete_account($current_user->user_id, $current_user->email);
}

if(isset($_POST['login_as_user'])){
	$session = new Session();
	$session->admin_login_as_user($current_user->user_id);
	redirect_to("../dashboard.php?page=overview");
}

if(isset($_POST['send_email'])) {
	$email = $current_user->first_name." ".$current_user->last_name." <".$current_user->email.">";
	$subject = $_POST['subject'];
	$email_message = $_POST['message'];
	if($subject != "" || $message != "") {
		Admin::email_user($email, $subject, $email_message);
	} else {
		$message = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
	}
}

?>

<style type="text/css">
	.row.bottom{margin-bottom: 15px;}
</style>

<form action="<?php echo $location; ?>" method="post" class="center" style="margin-top: 20px;">
	<?php if($current_user->account_lock == "0"){ ?>
	<input class="btn btn-small btn-primary" type="submit" name="activate_lock" value="Activate Lock" />
	<?php } else { ?>
	<input class="btn btn-small btn-primary" type="submit" name="resend_code" value="Resend Unlock Code" />
	<input class="btn btn-small btn-primary" type="submit" name="deactivate_lock" value="Deactivate Lock" />
	<?php } ?>
	<input class="btn btn-small btn-primary" type="submit" name="send_new_password" value="Send New Password" />
	<input class="btn btn-small btn-warning" type="submit" name="login_as_user" value="Login as User" />
	<button class="btn btn-small btn-primary" data-toggle="modal" href="#email_user">Email User</button>
	<button class="btn btn-small btn-danger" data-toggle="modal" href="#delete_account">Delete Account</button>
</form>

<hr />

<form action="<?php echo $location; ?>" method="post">

	<div class="row bottom">
		<div class="col-md-3">
			<label>User ID</label>
	      <input type="text" class="form-control" name="user_id" disabled="disabled" value="<?php echo $current_user->user_id; ?>" />
		</div>
		<div class="col-md-3">
	    <label>Account Lock</label>
	   	<input type="text" class="form-control" name="account_lock" disabled="disabled" value="<?php echo convert_boolean_full($current_user->account_lock); ?>" />
		</div>
		<div class="col-md-3">
	    <label>Activated</label>
			<select name="activated" class="form-control" required="required" value="<?php echo $current_user->activated ?>">
				<option value ="1" <?php if($current_user->activated == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
				<option value ="0" <?php if($current_user->activated == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
			</select>
		</div>
		<div class="col-md-3">
	    <label>Suspended</label>
			<select name="suspended" class="form-control" required="required" value="<?php echo $current_user->suspended; ?>">
				<option value ="1" <?php if($current_user->suspended == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
				<option value ="0" <?php if($current_user->suspended == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
			</select>
		</div>
	</div>

	<div class="row bottom">
		<div class="col-md-3">
	    <label>Active Tokens</label>
	   	<input type="text" class="form-control" name="tokens" value="<?php echo $current_user->tokens; ?>" />
		</div>
		<div class="col-md-3">
	    <label>Tokens in Bank</label>
	   	<input type="text" class="form-control" name="bank_tokens" value="<?php echo $current_user->bank_tokens; ?>" />
		</div>
		<div class="col-md-3">
	    <label>Invited By</label>
	   	<input type="text" class="form-control" name="invited_by" disabled="disabled" value="<?php echo $current_user->invited_by; ?>" />
		</div>
		<div class="col-md-3">
			<label>Primary Group</label>
			<select name="primary_group" class="form-control" required="required" value="<?php echo $current_user->primary_group ?>">
				<?php foreach(explode(",", $current_user->user_level) as $level){ ?>
				<option value="<?php echo $level; ?>" <?php if($current_user->primary_group == $level) { echo 'selected="selected"';} ?>><?php echo User::get_level_name($level); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>


	<div class="row bottom">
		<div class="col-md-12">
			<label>User Levels (groups)</label>
			<select data-placeholder="User Levels..." name="user_level[]" class="col-md-12 chzn-select" multiple value="<?php echo $current_user->user_level ?>">
			<?php $user_levels = explode(",", $current_user->user_level); foreach(User::get_site_levels() as $level){ ?>
				<option value="<?php echo $level->level_id; ?>"<?php if(in_array($level->level_id, $user_levels)){  echo ' selected="selected"'; } ?>><?php echo $level->level_name; ?></option>
			<?php } ?>
			</select>
		</div>
	</div>

	<div class="form-actions" style="text-align: center;">
		<input class="btn btn-primary" type="submit" name="submit" value="Update User" />
	</div>

</form>

<form action="user_dashboard.php?page=admin&amp;user_id=<?php echo $user_id ?>" method="POST" id="delete_account" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Delete Account</h3>
	</div>
	<div class="modal-body">
		<strong>Are you sure about deleting this account? This action can't be reversed.</strong>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="delete_account" value="Confirm" />
	</div>
</form>

<form action="user_dashboard.php?page=admin&amp;user_id=<?php echo $user_id ?>" method="POST" id="email_user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Email User</h3>
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

<?php if(isset($_GET['delete_level'])) {?>
	<form action="user_dashboard.php?page=admin&amp;user_id=<?php echo $user_id ?>" method="POST" id="delete_friend" class="modal">
	    <div class="modal-header"><a href="user_dashboard.php?page=admin&amp;user_id=<?php echo $user_id ?>" class="close" data-dismiss="modal">×</a>
	        <h3 id="myModalLabel">Delete Level</h3>
	    </div>
	    <div class="modal-body">
	        <label>Are you sure you want to delete <strong><?php echo $_GET['delete_level']; ?></strong> from this users levels?</label>
	    </div>
	    <div class="modal-footer">
		   <a href="user_dashboard.php?page=admin&amp;user_id=<?php echo $user_id ?>" class="btn">Close</a>
		   <button class="btn btn-danger" type="submit" name="delete_level">Confirm</button>
		 </div>
	</form>​
	<div class="modal-backdrop fade in"></div>
<?php } ?>