<?php

$config = array(

	'path' => AUTHPATH,

	'callback_url' => '{path}callback.php',

	'security_salt' => AUTHSALT,

	'Strategy' => array(
		
		'Facebook' => array(
			'app_id' => FACEBOOK_APP_ID,
			'app_secret' => FACEBOOK_APP_SECRET,
			'scope' => 'email'
		),

		'Twitter' => array(
			'key' => TWITTER_CONSUMER_KEY,
			'secret' => TWITTER_CONSUMER_SECRET
		),

		'Google' => array(
			'client_id' => GOOGLE_CLIENT_ID,
			'client_secret' => GOOGLE_CLIENT_SECRET
		)
				
	),
);