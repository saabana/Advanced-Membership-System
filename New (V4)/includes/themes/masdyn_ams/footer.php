    </div> <!-- /container -->

	<div style=" padding: 10px 0px;background-color: #F8F8F8;border-top: 1px solid #DDD;margin-bottom: -9px;margin-top:15px;">
		<div class="container">
			<div style="float:left;margin-top: 3px;margin-right:15px;"><strong style="font-size: 21px;color: #6D6D6D;">Do you like this script?</strong></div> 
			<div style="float:left;margin-top: 8px;margin-right:15px;">Why not purchase it from codecanyon.</div> 
			<a href="http://codecanyon.net/item/advanced-member-system/2333683?ref=masdyn" class="btn btn-success btn-large" style="float:left">Purchase this script &raquo;</a>
		</div>
	</div>


    <footer>
      <div class="above_footer">
        <div class="container">
			<div class="row">
			  <div class="col-md-3">
			  	<h4>About Us</h4>
			  	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus leo lectus, ultrices ut tortor eget, commodo consequat ligula. Vivamus dignissim libero a auctor iaculis. Praesent placerat justo at erat pellentesque dignissim. Curabitur commodo diam in pretium condimentum.
			  </div>
			  <div class="col-md-3">
			  	<h4>Twitter Feed</h4>
			  	<div id="tweets"></div>
			  </div>
			  <div class="col-md-3">
			  	<h4>Useful Links</h4>
			  	<ul class="links">
			  		<li><a href="http://www.masdyn.com/">MASDYN Website</a></li>
			  		<li><a href="http://codecanyon.net/user/MASDYN?ref=masdyn">Our CodeCanyon Portfolio</a></li>
			  		<li><a href="#">AMS Script Documentation</a></li>
			  		<li><a href="http://www.masdyn.com/forum/">Support Forum</a></li>
			  	</ul>
			  </div>
			  <div class="col-md-3">
			  	<h4>Contact Us</h4>
			  	101 Some Street<br />
			  	Liverpool<br />
			  	United Kingdom<br />
			  	UK12 SOME.

			  	<br /><br />
			  	Tel: (+44) 0151 000 0000<br />
			  	Fax: (+44) 0151 000 0001<br />
			  	Email: info@some-domain.ext<br />
			  </div>
			</div>
        </div>
      </div>
      <div class="container">
        <p>&copy; <?php echo date('Y'); ?> MASDYN Development Studio, All Rights Reserved. <span style="float:right"><a href="http://www.masdyn.com/"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/mds.png" width="107" height="29" alt="MASDYN Development Studio" style="margin: -5px 0px 0px;"></a></span></p>
      </div>
    </footer>

    
    <script type="text/javascript">var WWW = "<?php echo WWW; ?>";</script>
    <script src="<?php echo WWW; ?>includes/global/js/main.js"></script>
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/tweetable.jquery.js"></script>
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/jquery.bxslider.js"></script>
    <script type="text/javascript"></script>
	<script src="<?php echo WWW; ?>includes/global/js/jquery.imgareaselect.min.js"></script>
	<script src="<?php echo WWW; ?>includes/global/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
		$('#tweets').tweetable({username: 'envato',limit: 2,replies: false});
		$(".chzn-select").chosen();
		$(document).ready(function() {
			$(function() {
				$('.dropdown-toggle').dropdown();
				$('.dropdown, .dropdown input, .dropdown label').click(function(e) {
					e.stopPropagation();
				});
			});
		});
		$(function(){
			$("[rel='tooltip']").tooltip();
			$("[rel='popover']").popover();
		});
	</script>
  </body>
</html>

<?php if(!$session->is_logged_in()) { ?>

  <!-- Sign In Modal -->
  <div class="modal fade" id="signin_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Sign In</h4>
        </div>
        <div id="login_form" onkeypress="if(event.keyCode == 13){login()}" class="modal-body form-signin">
			<h2 class="form-signin-heading">Please sign in</h2>

			<div id="message"></div>

			<input type="text" class="form-control" name="username" id="username" placeholder="Username">
			<input type="password" class="form-control" name="password" id="password" placeholder="Password">
			<label class="checkbox">
				<input type="checkbox" name="remember_me" id="remember_me"> Remember me
			</label>
			<button class="btn btn-lg btn-primary btn-block" type="submit" name="login_btn" id="login_btn" onclick="login()">Sign in</button>

			<br />

			<a href="reset_password.php" style="float:right">Forgot Password?</a>
			<div class="clearfix"></div>

			<?php if(OAUTH == "ON"){ ?>
			<hr />
				
			<div class="row-fluid">
				<div class="span12 center">
					<div class="span12">
						<?php if(FACEBOOK_APP_ID != ""){ ?><a href="<?php echo WWW; ?>auth/facebook" class="zocial facebook">Sign in with Facebook</a><?php } ?>
						<?php if(TWITTER_CONSUMER_KEY != ""){ ?><a href="<?php echo WWW; ?>auth/twitter" class="zocial twitter">Sign in with Twitter</a><?php } ?>
						<?php if(GOOGLE_CLIENT_ID != ""){ ?><a href="<?php echo WWW; ?>auth/google" class="zocial google">Sign in with Google</a><?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

 <?php } else { ?>

  <!-- Confirm Purchase Modal -->
  <div class="modal fade" id="confirm_purchase_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Confirm Purchase</h4>
        </div>
        <div class="modal-body">
        	<div class="row">
        		<div class="col-md-12">
        			<strong>Area you sure about purchasing access to this page?</strong>
        		</div>
        	</div>
        	<br />
        	<div class="row">
        		<div class="col-md-12">
        			<strong>Once you have clicked "Confirm", <span id="purchase_amount">NUM</span> tokens will be deducted from your account and you will be able to see the content right away.</strong>
        		</div>
        	</div>
        </div>
	    <div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Close</button>
			<input class="btn btn-success" type="submit" id="confirm" value="Confirm" />
	    </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


 <?php } ?>