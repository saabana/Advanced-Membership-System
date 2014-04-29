$(document).ready(function(){
	$("#signin_link").attr("href", "#signin_modal").attr("data-toggle", "modal");
});

function login(){
	username = $("#username").val();
	password = $("#password").val();
	if($('#remember_me').is(':checked')){
		remember_me = "on";
	} else {
		remember_me = "off";
	}
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=login&action=login&username="+username+"&password="+password+"&remember_me="+remember_me,
		success: function(html){
			if(html == "false"){
				$("#username").addClass("error");
				$("#password").addClass("error");
				$("#login_btn").html("Login");
				update_login_msg();
			} else {
				// window.location.replace(html);
				location.reload();
			}
		},
		beforeSend: function(){
			$("#login_btn").html("Working...");
		}
	});
}

function update_login_msg(){
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=login&action=update_msg",
		success: function(html){
			if(html){
				$("#message").html(html);
			}
		}
	});
}

function update_msg(handle){
	if(handle == ""){
		handle = "#message";
	}
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=misc&action=update_msg",
		success: function(html){
			if(html){
				$(handle).html(html);
			}
		}
	});
}

function purchase_access(id,amount){
	$("#confirm_purchase_modal #purchase_amount").html(amount);
	$("#confirm_purchase_modal #confirm").attr('onclick','confirm_purchase_access(\''+id+'\')');
	$('#confirm_purchase_modal').modal('show');
}

function confirm_purchase_access(id){
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: {page: "global", action: "purchase_access", id: id},
		success: function(return_data){
			if(return_data != false){
				location.reload();
			} else {
				update_msg("#message");
			}
		}
	});
}

function validate_email(email){
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}