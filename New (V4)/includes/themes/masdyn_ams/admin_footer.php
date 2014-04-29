    </div> <!-- /container -->

	<div style=" padding: 10px 0px;background-color: #F8F8F8;border-top: 1px solid #DDD;margin-bottom: -9px;margin-top:15px;">
		<div class="container">
			<div style="float:left;margin-top: 3px;margin-right:15px;"><strong style="font-size: 21px;color: #6D6D6D;">Do you like this script?</strong></div> 
			<div style="float:left;margin-top: 8px;margin-right:15px;">Why not purchase it from codecanyon.</div> 
			<a href="http://codecanyon.net/item/advanced-member-system/2333683?ref=masdyn" class="btn btn-success btn-large" style="float:left">Purchase this script &raquo;</a>
		</div>
	</div>


    <footer>
      <div class="container">
        <p style="margin: 10px 0 10px">&copy; <?php echo date('Y'); ?> MASDYN Development Studio, All Rights Reserved. <span style="float:right"><a href="http://www.masdyn.com/"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/mds.png" width="107" height="29" alt="MASDYN Development Studio" style="margin: -5px 0px 0px;"></a></span></p>
      </div>
    </footer>

    
    <script type="text/javascript">var WWW = "<?php echo WWW.ADMINDIR; ?>";</script>
    <script src="<?php echo WWW; ?>includes/global/js/main.js"></script>
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/tweetable.jquery.js"></script>
    <script src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/js/jquery.bxslider.js"></script>
    <script type="text/javascript"></script>
	<script src="<?php echo WWW; ?>includes/global/js/jquery.imgareaselect.min.js"></script>
	<script src="<?php echo WWW; ?>includes/global/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
		$('#tweets').tweetable({username: 'envato',limit: 3,replies: false});
		$(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); 
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
		});
	</script>
  </body>
</html>