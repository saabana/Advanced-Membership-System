$(document).ready(function(){
	$("#login_link").attr("href", "#login_modal").attr("data-toggle", "modal");	
});

function login(){
	username = $("#username").val();
	password = $("#password").val();
	if($('#remember_me').attr('checked')){
		remember_me = "yes";
	} else {
		remember_me = "no";
	}
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=login&action=login&username="+username+"&password="+password+"&remember_me="+remember_me,
		success: function(html){
			if(html == "false"){
				$("#username").addClass("error");
				$("#password").addClass("error");
				$("#login_btn").html("Login");
				update_login_msg();
			} else {
				window.location.replace(html);
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
		url: "data.php",
		data: "page=login&action=update_msg",
		success: function(html){
			if(html){
				$("#message").html(html);
			}
		}
	});
}