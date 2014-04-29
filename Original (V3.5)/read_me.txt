Advanced Member System v3.5.3 Read Me
© Copyright 2013 by MasDyn Studio, All Rights Reserved.
http://www.masdyn.com/ http://www.masdyn.com/support/
For the full documentation for this script please visit: http://www.masdyn.com/support/codecanyon/product-support/advanced-member-system/
What is “Advanced Member System”?
Advanced Member System is a powerful user membership system, which gives you complete control over all of your users and your content, easily and efficiently. We handcrafted every single feature, which allows them to work seamlessly together, providing you with a reliable and affordable solution.
The Advanced Member System is been designed with security in mind; therefore we have added a large amount of ways to project your content from protection against SQL Injection to multiple user levels, whole page protection, partial page protection or even individual password salts for each user, we’ve got it covered!
This script was designed and developed exclusively for CodeCanyon and is completely object oriented PHP. AMS currently uses the standard MySQL protocols but the next update will be all MySQLi, which is more efficient. However, the installer currently uses MySQLi, so you will need to have the MySQLi extension installed on your testing/live environment.
Can I rename the admin area?
Yes, renaming the admin area is very easy to do. You just need to go to Admin Area > Settings and rename “Admin Directory” to your chosen name click on “Update Settings”. Then you just need to manually rename the directory to the name you have entered under the “Admin Directory” setting.
Upload Files
Decompress the zip file to your computer and upload all of the files within the “install” folder to your web host using a web interface or an FTP program such as FileZilla. Make sure that you upload to your public directory; this directory is most commonly called “public_html” or “www”.
Requirements
• Unix, Linux or Windows • Apache Web Server
• PHP 5.2 or Above
• MySQL5.0orAbove
• MySQLi Extension
Installation
Please change the permissions of “install” and “assets/img/profile” to 0755. Then go to http://example.com/ install/ and run the installation wizard.
Upgrade
To upgrade from 3.1.1, please copy the “Install” folder from this package to your current setup. Then go to http://example.com/install/, click on “upgrade” and follow the step-by-step wizard.
If you are upgrading from an earlier version, please go to “http://example.com/install/install_prior” and run the upgrade wizard. Once you have done that, you will need to run the main upgrade wizard above.
Maintenance Page
Please see installation.
IP Protection

As the script will have access over real money, we have added IP Projection. IP Protection allows a user to control access to their account from a specific set of IP addresses. This setting is controlled by the user, but can also be controlled through the admin panel.
Currency Bank
The currency bank is a great way for users to store their credit, without them having to worry about investing more money than they want to. This feature is controlled by the user and contains no restrictions on the amount of credit it can contain.
Disable New Registrations
By default, new users can create an account with you. You can disable this in the settings page of the admin panel.
User Invitations
This feature will only show if registrations are turned off and allow invites is turned on. By default, the maximum number of active invites per user is 10. This option can be changed within the admin panel.
Account Lock
Account lock is a great way for your users to protect their account settings from being changed without their permission. Once turned on, it can only be disabled by a randomly generated code, which has been dispatched to the email on file. The account lock can also be turned off under the users settings from within the admin panel.
Known Bugs
In some environments, the image profile image upload and crop will not work properly with large images. To fix this, please add the following line to your .htaccess file:
php_value memory_limit 124m
OAuth
This script has the capability for users to login though Facebook and Twitter. To enable OAuth, you will first need to create an app at Facebook (https://developers.facebook.com/) and Twitter (https://dev.twitter.com/ ). Please see below screenshots for the required setup.
Once you have your app codes, you will then need to add them into the configuration file (includes/ configuration/config.php) under the appropriate fields.
Now you will need to edit the AUTHPATH constant to correctly match the auth folders location. For example, if the auth folder is located in your documents root, you will need to enter:
defined("AUTHPATH") ? null : define("AUTHPATH", “/auth/");
However, if the auth folder is located elsewhere, you will need to edit it accordingly. Here is the auth path for our demo:
defined("AUTHPATH") ? null : define("AUTHPATH", "/advanced-member-system/auth/");
Captchas
This script contains a number of captchas, however, some require a site code and others require your PHP version to be greater than 5.3. This script contains a number of checks to make sure you can only display the captchas which can run inside of your environment.
reCaptcha from google, requires you to enter a private and public key, to obtain these keys, please visit their website at http://www.google.com/recaptcha. Sign in and create a site. Once you have the required codes. Please enter them in the following fields, located inside of "includes/configuration/config.php".
defined("RECAPTCHA_PUBLIC") ? null : define("RECAPTCHA_PUBLIC", "HERE"); defined("RECAPTCHA_PRIVATE") ? null : define("RECAPTCHA_PRIVATE", "HERE");
