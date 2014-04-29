<?php require_once("includes/inc_files.php"); 

$stylesheet = "
	<style type='text/css'>
		body {margin:0;padding:0;background: #f2f2f2;font-family: Arial, Helvetica}
		.header{background-color: #088BD7;border-color: #006CAA;box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1);}
		.header h1{color:white;font-size:19;padding:12px 10px;}
		.container {width: 700px; margin: 0px auto 0; background: rgba(0, 0, 0, 0);}
		.email_content {width: 100%; border: #ddd 1px solid; padding: 5px 15px 5px; background: #fff}
		.logo {padding-top:6px}
		.footer_text{font-size: 13px;padding-top: 7px;color: #7A7A7A;}
		a{text-decoration: none;}
	</style>
";

$header = "
	<body>
		<div class='header'>
			<div class='container'>
				<a href='".WWW."'><h1>".SITE_NAME."</h1></a>
			</div>
		</div>
		<div class='container'>
			<div class='email_content'>
";

$footer = "
			</div>
			<div class='footer_text'>
				&copy; ".date('Y')." ".SITE_NAME.", All Right Reserved.
			</div>
		</div>
	</body>";


$data = Email::find_by_id(3);
echo $data->template_content = $stylesheet.$header.email_shortcodes($data->content).$footer;


?>