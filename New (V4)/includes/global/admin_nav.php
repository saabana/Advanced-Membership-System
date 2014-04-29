<li<?php echo ($active_page == "dashboard") ? ' class="active"' : ''; ?>><a href="index.php">Dashboard</a></li>
<li class="dropdown">
	<a href="" class="dropdown-toggle" data-toggle="dropdown">Users <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li<?php echo ($active_page == "users") ? ' class="active"' : ''; ?>><a href="users.php">All Users</a></li>
		<li<?php echo ($active_page == "groups") ? ' class="active"' : ''; ?>><a href="groups.php">All Groups</a></li>
	</ul>
</li>
<li class="dropdown">
	<a href="" class="dropdown-toggle" data-toggle="dropdown">Wizards <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li<?php echo ($active_page == "page_protection") ? ' class="active"' : ''; ?>><a href="page_protection.php">Page Protection</a></li>
		<li<?php echo ($active_page == "partial_protection") ? ' class="active"' : ''; ?>><a href="partial_protection.php">Partial Page Protection</a></li>
		<li<?php echo ($active_page == "content_protection") ? ' class="active"' : ''; ?>><a href="content_protection.php">Content Protection</a></li>
	</ul>
</li>
<li class="dropdown">
	<a href="" class="dropdown-toggle" data-toggle="dropdown">Packages <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li<?php echo ($active_page == "tokens") ? ' class="active"' : ''; ?>><a href="tokens.php">Tokens</a></li>
		<li<?php echo ($active_page == "gift") ? ' class="active"' : ''; ?>><a href="gift_card_packages.php">Gift Cards</a></li>
	</ul>
</li>
<li<?php echo ($active_page == "gift_cards") ? ' class="active"' : ''; ?>><a href="gift_cards.php">Gift Cards</a></li>
<li class="dropdown">
	<a href="" class="dropdown-toggle" data-toggle="dropdown">Settings <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li<?php echo ($active_page == "settings") ? ' class="active"' : ''; ?>><a href="settings.php">General Settings</a></li>
		<li<?php echo ($active_page == "email_templates") ? ' class="active"' : ''; ?>><a href="email_templates.php">Email Templates</a></li>
	</ul>
</li>