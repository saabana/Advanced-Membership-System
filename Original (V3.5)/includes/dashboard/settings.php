<?php

/*****************************************************************
*    Advanced Member System                                      *
*    Copyright (c) 2013 MasDyn Studio, All Rights Reserved.      *
*****************************************************************/


?>

<script>
function edit(id){
	if(id == "gender" || id == "country" || id == "ip_protection"){
		if(id == "gender"){
			var value = '<?php echo $user->gender; ?>';
		} else if(id == "country"){
			var value = '<?php echo $user->country; ?>';
		} else if(id == "ip_protection"){
			var value = '<?php echo $user->whitelist; ?>';
		}
		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: "page=settings&get_select="+id+"&"+id+"="+value,
			success: function(data){
				$(".settings #"+id+" .setting").attr('style', 'display:none').after('<td class="setting input">'+data+'</td>');
				$(".settings #"+id+" .action").html('<button class="btn btn-link" onclick="save(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/tick.png" width="31" height="31" alt="Tick"></button> <button class="btn btn-link" onclick="cancel(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/cross.png" width="31" height="31" alt="Cross"></button>');
			}
		});
	} else {
		if(id == "password"){
			var value = "";
		} else {
			var value = $(".settings #"+id+" .setting").html();
		}
		$(".settings #"+id+" .setting").attr('style', 'display:none').after('<td class="setting input"><input type="text" class="span12" id="'+id+'" required="required" value="'+value+'"></td>');
		$(".settings #"+id+" .action").html('<button class="btn btn-link" onclick="save(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/tick.png" width="31" height="31" alt="Tick"></button> <button class="btn btn-link" onclick="cancel(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/cross.png" width="31" height="31" alt="Cross"></button>');
	}
}
function save(id){
	if(id == "gender" || id == "country" || id == "ip_protection"){
		var value = $("#"+id+" .setting.input #"+id+" option:selected ").val();
	} else {
		var value = $("#"+id+" .setting.input #"+id).val();
	}
	if(value != ""){
		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: "page=settings&name="+id+"&value="+value,
			success: function(data){
				if(data == "failure"){
					update_msg();
				} else {
					$("#"+id+" .setting").html(value);
					cancel(id);
					update_msg();
				}
			}
		});
	} else {
		$("#message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>This setting can't be left blank.</div>")
	}
}
function cancel(id){
	$(".settings #"+id+" .setting").removeAttr('style');
	$(".settings #"+id+" .setting.input").remove();
	$(".settings #"+id+" .action").html('<button class="btn btn-link" onclick="edit(\''+id+'\');">edit</button>');
}
</script>

<div class="row-fluid">
	<div class="span12">
		<table class="settings table">
			<tbody>
				<tr>
					<td class="name" colspan="2">First Name</td>
				</tr>
				<tr id="first_name">
					<td class="setting"><?php echo $user->first_name; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('first_name');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">Last Name</td>
				</tr>
				<tr id="last_name">
					<td class="setting"><?php echo $user->last_name; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('last_name');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">Username</td>
				</tr>
				<tr id="username">
					<td class="setting" colspan="2"><?php echo $user->username; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('username');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">Password</td>
				</tr>
				<tr id="password">
					<td class="setting">**********</td>
					<td class="action"><button class="btn btn-link" onclick="edit('password');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">Email Address</td>
				</tr>
				<tr id="email">
					<td class="setting"><?php echo $user->email; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('email');">edit</button></td>
				</tr>				
				<!--  -->
				<tr>
					<td class="name" colspan="2">Gender</td>
				</tr>
				<tr id="gender">
					<td class="setting"><?php echo $user->gender; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('gender');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">Country</td>
				</tr>
				<tr id="country">
					<td class="setting"><?php echo $user->country; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('country');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">IP Protection</td>
				</tr>
				<tr id="ip_protection">
					<td class="setting"><?php if($user->whitelist == 0){echo "Disabled";}else{echo "Enabled";} ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('ip_protection');">edit</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2">Whitelist <span style="font-weight: normal;font-size: 12px;">(127.0.0.1,127.0.0.2,127.0.0.3)</span></td>
				</tr>
				<tr id="ip_whitelist">
					<td class="setting"><?php echo $user->ip_whitelist; ?></td>
					<td class="action"><button class="btn btn-link" onclick="edit('ip_whitelist');">edit</button></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
