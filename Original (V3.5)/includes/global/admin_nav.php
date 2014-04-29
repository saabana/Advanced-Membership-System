<ul class="nav nav-tabs" style="margin: -15px -11px 11px;">
	<li<?php echo ($active_page == "overview") ? ' class="active"' : ''; ?>><a href="index.php">Overview</a></li>
	<li<?php echo ($active_page == "users") ? ' class="active"' : ''; ?>><a href="users.php">Users</a></li>
	<li<?php echo ($active_page == "groups") ? ' class="active"' : ''; ?>><a href="groups.php">Groups</a></li>
	<li<?php echo ($active_page == "tokens") ? ' class="active"' : ''; ?>><a href="tokens.php">Tokens</a></li>
	<li<?php echo ($active_page == "page_protection") ? ' class="active"' : ''; ?>><a href="page_protection.php">Page Protection</a></li>
	<li<?php echo ($active_page == "partial_protection") ? ' class="active"' : ''; ?>><a href="partial_protection.php">Partial Protection</a></li>
	<li<?php echo ($active_page == "email_templates") ? ' class="active"' : ''; ?>><a href="email_templates.php">Email Templates</a></li>
	<li<?php echo ($active_page == "settings") ? ' class="active"' : ''; ?>><a href="settings.php">Settings</a></li>
</ul>