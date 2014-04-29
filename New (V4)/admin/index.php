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

$active_page = "dashboard";

?>

<?php $page_title = "Dashboard"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>
	
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Direct', 'Facebook', 'Twitter', 'Google'],
          <?php foreach (Admin::get_all_login_logs() as $count) { ?>
           ['<?php if($count->date == strftime("%Y-%m-%d", time())){ echo "Today"; } else { echo date_to_text($count->date); } ?>',  <?php echo $count->count; ?>, <?php echo $count->f_count; ?>, <?php echo $count->t_count; ?>, <?php echo $count->g_count; ?>,],
          <?php } ?>
        ]);

        var options = {
          vAxis: {title: "Number of Logins"},
          hAxis: {title: "Day"},
          seriesType: "bars",
          series: {5: {type: "line"}}
        };

        var chart = new google.visualization.ComboChart(document.getElementById('graph_div'));
        chart.draw(data, options);
      }
      google.setOnLoadCallback(drawVisualization);
    </script>


	<div class="row">
		<?php require_once("../includes/global/admin_nav.php"); ?>
	</div>
	 
   <div class="center">
    <h2 style="margin-bottom: 3px;">Daily Login Count</h2>
   </div>
  
  <div id="graph_div" style="width: 100%; height: 100%;"></div>

  <hr />

  <?php $counts = Admin::find_by_sql("(SELECT COUNT(id) as count FROM users WHERE gender = 'Male') UNION ALL (SELECT COUNT(id) as count FROM users WHERE gender = 'Female') UNION ALL (SELECT COUNT(id) as count FROM users WHERE activated = '1') UNION ALL (SELECT COUNT(id) as count FROM users WHERE activated = '0') UNION ALL (SELECT COUNT(id) as count FROM users WHERE suspended = '1') UNION ALL (SELECT COUNT(id) as count FROM users) "); $user_count = Admin::find_by_sql("SELECT user_id,first_name,last_name,username,login_count FROM users ORDER BY login_count DESC LIMIT 5 "); ?>

	<div class="row">
		<div class="col-md-4">
			<h2>User Genders</h2>
			<table class="table">
			  <tbody>
			    <tr>
			      <td>Male</td>
					<td><?php echo $counts[0]->count; ?></td>
			    </tr>
				<tr>
			      <td>Female</td>
					<td><?php echo $counts[1]->count; ?></td>
			    </tr>
				<tr>
			      <td><strong>Total:</strong></td>
					<td><?php echo $counts[0]->count + $counts[1]->count; ?></td>
			    </tr>
			  </tbody>
			</table>
			<p><a class="btn" href="users.php">View All Users &raquo;</a></p>
		</div><!--/span-->
		<div class="col-md-4">
			<h2>User Account Statistics</h2>
			<table class="table">
			  <tbody>
			    <tr>
			      <td>Active</td>
					<td><?php echo $counts[2]->count; ?></td>
			    </tr>
				<tr>
			      <td>Inactive</td>
					<td><?php echo $counts[3]->count; ?></td>
			    </tr>
				<tr>
			      <td>Suspended</td>
					<td><?php echo $counts[4]->count; ?></td>
			    </tr>
				<tr>
			      <td><strong>Total:</strong></td>
					<td><?php echo $counts[5]->count; ?></td>
			    </tr>
			  </tbody>
			</table>
		</div><!--/span-->
    <div class="col-md-4">
      <h2>Top 5 Logins</h2>
      <table class="table">
        <tbody>
        <?php foreach ($user_count as $sel_user) { ?>
          <tr>
            <td><strong><a href="<?php echo WWW.ADMINDIR.'user_dashboard.php?page=overview&user_id='.$sel_user->user_id; ?>"><?php echo $sel_user->first_name." ".$sel_user->last_name; ?></a></strong> - <?php echo $sel_user->username; ?></td>
            <td><?php echo $sel_user->login_count; ?></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div><!--/span-->
	</div><!--/row-->

</div>

<?php require_once("../includes/themes/".THEME_NAME."/admin_footer.php"); ?>