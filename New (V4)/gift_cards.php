<?php

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

require_once("includes/inc_files.php");

if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
} else {
	// redirect_to("signin.php");
}

$location = "gateway/paypal.php";

$packages = User::find_by_sql("SELECT id,name,amount FROM gift_card_packages WHERE status = '1' ");

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$total_count = count($packages);
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT id,name,amount FROM gift_card_packages WHERE status = '1' LIMIT {$per_page} OFFSET {$pagination->offset()}";
$packages = User::find_by_sql($sql);

// preprint($packages);

$current_page = "gift_cards";

?>
<?php $page_title = "Purchase Gift Cards"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div id="message"><?php echo output_message($message); ?></div>

<?php if(empty($packages)){ ?>
	<strong>Sorry, no gift cards could be found.</strong>
<?php } else { ?>
<table class="table table-bordered">
  <thead>
    <tr>
		<th>Name</th>
		<th>Price</th>
		<th style="width: 104px;"></th>
    </tr>
  </thead>
  <tbody>
	<?php foreach($packages as $data): ?>
    <tr id="package_<?php echo $data->id; ?>">
		<td style="vertical-align: middle;" class="package_name"><?php echo $data->name; ?></td>
		<td style="vertical-align: middle;" class="package_price"><?php echo CURRENCYSYMBOL.$data->amount; ?></td>
		<td class="center">
			<button class="btn btn-primary purchase_btn" onclick="purchase('<?php echo $data->id; ?>');">Purchase</button>
		</td>
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
				echo " <li><a href=\"gift_cards.php?page={$i}\">{$i}</a></li> "; 
			}
		}

		echo "</ul>";

	}

}
?>

<script type="text/javascript">
function purchase(id){
	var package_name = $("#package_"+id+" .package_name").html();
	$("#package_"+id).after('<tr id="purchase_form_'+id+'" style="background:#F5F5F5"> <td colspan="3"> <div class="message" style="margin-bottom: 7px;"></div> <div class="row"> <div class="col-md-3"> <label>Gift Card <em class="req">*</em></label> <input name="name" id="name" class="form-control" disabled="disabled" value="'+package_name+'"> </div> <div class="col-md-3"> <label>From <em class="req">*</em></label> <input name="from" id="from" class="form-control" value="<?php if(isset($user)){echo $user->full_name();} ?>" placeholder="Please enter your name"> </div> <div class="col-md-3"> <label>Email Address <em class="req">*</em></label> <input name="email" id="email" class="form-control" value="<?php if(isset($user)){echo $user->email;} ?>" placeholder="Please enter your email address"> </div> <div class="col-md-3"> <label>Recipient Public ID (optional)</label> <input name="public_id" id="public_id" class="form-control" value=""> </div> </div> <div class="clearfix" style="margin-bottom: 10px;"></div> <div class="row"> <div class="col-md-6"> <label>Personal Note</label> <textarea name="note" id="note" class="form-control" ></textarea> </div> <div class="col-md-3"> <button class="btn btn-success" style="margin-top: 34px;" onclick="confirm_purchase(\''+id+'\')"> Purchase Gift Card </button> </div> <div class="clearfix" style="margin-bottom: 2px;"></div> </div> </td> </tr>');
	$("#package_"+id+" .purchase_btn").html('Cancel').attr('onclick','cancel_purchase('+id+')').addClass('btn-danger').removeClass('btn-primary');;
}
function cancel_purchase(id){
	$("#purchase_form_"+id).remove();
	$("#package_"+id+" .purchase_btn").html('Purchase').attr('onclick','purchase('+id+')').addClass('btn-primary').removeClass('btn-danger');
}
function confirm_purchase(id){
	var name = $("#purchase_form_"+id+" #name").val();
	var from = $("#purchase_form_"+id+" #from").val();
	var email_address = $("#purchase_form_"+id+" #email").val();
	var public_id = $("#purchase_form_"+id+" #public_id").val();
	var note = $("#purchase_form_"+id+" #note").val();
	if(from != "" && email_address != ""){
		if(validate_email(email_address)){
			var price = $("#package_"+id+" .package_price").html();
			$("#purchase_name").html($("#package_"+id+" .package_name").html());
			$(".purchase_price").html(price);
			$("#paypal_form #id").val(id);
			$("#paypal_form #from").val(from);
			$("#paypal_form #email").val(email_address);
			$("#paypal_form #public_id").val(public_id);
			$("#paypal_form #note").val(note);
			$("#purchase_gift_card").val("PayPal ("+price+")");
			$('#purchase').modal('show');
			$("#purchase_form_"+id+" #from, #purchase_form_"+id+" #email").removeClass('error');
			$("#message").html("");
			$("#purchase_gc_ac").attr('onclick','purchase_gc_ac(\''+id+'\')');
		} else {
			$("#purchase_form_"+id+" #email").addClass('error');
			$("#message").html("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter a valid email address.</div>");
		}
	} else {
		$("#purchase_form_"+id+" #from, #purchase_form_"+id+" #email").addClass('error');
		$("#message").html("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields to continue.</div>");
	}
}
<?php if($session->is_logged_in()) { ?>

function purchase_gc_ac(id){

	var name = $("#purchase_form_"+id+" #name").val();
	var from = $("#purchase_form_"+id+" #from").val();
	var email_address = $("#purchase_form_"+id+" #email").val();
	var public_id = $("#purchase_form_"+id+" #public_id").val();
	var note = $("#purchase_form_"+id+" #note").val();

	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: {page: "gift_cards", action: "purchase_ac", id: id, name: name, from: from, email_address: email_address, public_id: public_id, note: note},
		success: function(){
			location.reload();
		}
	});
}

<?php } ?>
</script>

  <!-- Modal -->
  <div class="modal fade" id="purchase" tabindex="-1" role="dialog" aria-labelledby="purchase" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Purchase Gift Card</h4>
        </div>
        <div class="modal-body">
        	Please select one of the payment methods below to purchase a <span id="purchase_name" style="font-weight: bold;"></span> gift card.<br />
        	<div class="center" style="margin-top: 5px;">
        		<?php if($session->is_logged_in()) { ?><button class="btn btn-success" id="purchase_gc_ac">Account Credit (<span class="purchase_price"></span>)</button><?php } ?>
	        	<form action="<?php echo $location; ?>" method="POST" style="margin: 0;" id="paypal_form"><input type="hidden" name="id" id="id" value="" /><input type="hidden" name="from" id="from" value="" /><input type="hidden" name="email" id="email" value="" /><input type="hidden" name="public_id" id="public_id" value="" /><input type="hidden" name="note" id="note" value="" /><input class="btn btn-primary" type="submit" name="purchase_gift_card" id="purchase_gift_card" value="PayPal" style="margin-top: 5px;"></form>
	        </div>
        </div>
        <div class="modal-footer" style="margin-top: 0px">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>