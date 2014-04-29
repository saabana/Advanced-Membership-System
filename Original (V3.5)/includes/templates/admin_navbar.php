<div class="span12">
	<ul class="nav nav-pills" style="margin-bottom: -10px;">
		<li<?php if($active_page == "overview"){echo " class='active'";} ?>><a href="index.php">Overview</a></li>
		<li<?php if($active_page == "users"){echo " class='active'";} ?>><a href="users.php">Users</a></li>
		<li<?php if($active_page == "groups"){echo " class='active'";} ?>><a href="groups.php">Groups</a></li>
		<li<?php if($active_page == "tokens"){echo " class='active'";} ?>><a href="tokens.php">Tokens</a></li>
		<li<?php if($active_page == "page_protection"){echo " class='active'";} ?>><a href="page_protection.php">Page Protection</a></li>
		<li<?php if($active_page == "partial_protection"){echo " class='active'";} ?>><a href="partial_protection.php">Partial Protection</a></li>
		<li<?php if($active_page == "settings"){echo " class='active'";} ?>><a href="settings.php">Settings</a></li>
	</ul>
	<hr style="margin-bottom: 9px;" />
</div>