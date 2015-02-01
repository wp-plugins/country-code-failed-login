=== Country Code Failed Login ===
Contributors: jsmcm
Tags: brute force, login, failed login, country ban, country block, attack, security, country code failed login
Requires at least: 3.5.1
Tested up to: 3.6.0
Stable tag: 2.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Log and block IP addresses after a single failed login attempt if they are from a different country to you.

== Description ==

Site: [phpwebhost.co.za - Country Code Failed Login Attempts, Wordpress plugin](http://www.phpwebhost.co.za/wordpress/plugins/country-code-failed-login-wordpress-plugin/ "Country Code Failed Login Attempts,")

This plugin checks the two letter country code of anyone trying to login to wp-login.php. If the country code is different to your country code which you selected in the setup and the login fails a single attempt, it lists the IP address to a database hosted with phpwebhost.co.za. 

Each time a login attempt is made from a country code outside of your own, the IP address is checked against this database and if the IP address is in the block list, the wp-login.php form is not displayed. 

IP's listed in our database are automatically expired after a week of inactivity.

Why a single login failure for countries outside of your own, but no protection for login attempts from your own country?

Most malicious login attempts will come from a range of countries. Its "safe" to block those on a single failure as you will not end up blocking yourself, unless you try to login from a different country (ie, you're travelling) or you use a proxy in a different country AND your login fails.

This plugin will stop those brute force attempts by logging and blocking IPs from your own site. However, the real power comes in from the fact that other Wordpress sites in your country using this plugin all contribute to the globally available block list. If another wordpress site in a different part of your country lists an IP address in the block list and that IP address then comes to your site, it will be blocked from the wp-login.php (ie, it won't even have a single attempt!)

We don't block IP's from your own country because our blocking is too strict, ie after a single failure the IP is listed and blocked. This would mean that if you accidentally type in an incorrect login once, you would be blocked. So, we ignore your own country code's login failures. To catch those, we recommend a plugin like "Limit Login Attempts"

== Installation ==
 There are three options listed below...
 
FTP Installation

1. Unzip country-code-failed-login.zip
2. Upload the `country-code-failed-login` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to the plugin's setting page and select your own country.


Wordpress File Upload Installation

1. Click on Plugins->Add New->Upload and browse for the zip file
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the plugin's setting page and select your own country.


Wordpress Install (easiest)

1. Click on Plugins->Add New then search for "country code failed login"
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the plugin's setting page and select your own country.


== Frequently Asked Questions ==

= After installing this plugin my wp-login.php form crashed (500 server error or similar) =

This may happen if your web hosting company has not configured their server to handle SOAP. The first thing you should do is ask them why! Then to access your login form go to your wp-login.php form and add the ccfl=off parameter, eg, http://yoursite.com/wp-login.php?ccfl=off
If this seems confusing, please go to http://www.phpwebhost.co.za/wordpress/country-code-failed-login/ccfl.php for more info and we'll help you get in!


= We have users from multiple countries logging in, can we use this plugin? =

If your blog allows registrations and logins from more than a single country, then no, this plugin will not be suitable as your users outside of your country may be blocked.

= I have multiple users, can I use this plugin? =

That depends on where your multiple users are. This is really more of a multiple country question. If your blog allows registrations and logins from more than a single country, then no, this plugin will not be suitable as your users outside of your country may be blocked.

= What happens if I accidentally block myself and I can't log in? =

This shouldn't really happen, provided that you've set your own country in the settings screen. This plugin will not block the country you select there


= Why so strict against "other" countries? =

As server admins we see huge numbers of attacks on the wordpress login screen. These brute force attacks are a real threat to your wordpress site. We've seen up to 2000 attacks from almost as many different IPs on a single site per hour recently. This is not about being "nasty" to other countries, but lets face it, if your in a particular country do you really need to provide access to your login form to other countries?

= Up to 2000 attacks per hour from almost as many IPs, will this plugin help? =

Its true that if you were the only person using this plugin, it would not help much. However, as this plugin is used by more and more sites its very likely that bad IPs will be in the block list before they even get to your site, in which case they will not be able to access the wp-login.php form, so yes, it helps.


= What happens if I don't enter my country? =

Nothing... The plugin won't log bad IPs and it won't block IPs either. Its essentially the same as deactivating the plugin

== Screenshots ==

1. Look for "Country Code Failed Login" in the Plugins screen and click on the "Activate" link.
2. Once activated a new menu item will appear under the "Settings" menu called "Country Code Failed Login where you manage settings
3. In the settings screen you select which country you are from. You can also view info about the number of attacks blocked
4. When logging in you can see a brief overview of attacks blocked in the Dashboard screen

== Changelog ==

= 2.2.0 =
* Country Code lookups are stored in a local table now to prevent multiple lookups via the web. They expire after 365 days
* IPs which are not listed are cached for that session and good health IPs cause the script to return without expensive lookups.

= 2.1.1 =
* Some minor errors were introduced when making the plugin translation ready.

= 2.1.0 =
* Changed lookups to new dedicated server at api.phpwebhost.co.za
* Changed all links to www.phpwebhost.co.za
* Added support for language translations

= 2.0.4 =
* Update remote URL in plugin file

= 2.0.3 =
* Tiny bug fix with an undeclared variable which causes a warning message on some sites

= 2.0.2 =
* Checks if the debug file exists before checking its size 

= 2.0.1 =
* Does not require deactivation / reactivation to create custom db to store local ips

= 2.0.0 =
* Plugin stores banned IPs locally now to minimise external connections (particularly during an active attack!!)

= 1.0.8 =
* Bug fix. prevents accidental automatic disabling of the plugin!

= 1.0.7 =
* Bug fix. First check if the debug file exists before attempting to delete it!

= 1.0.6 =
* Added logging capabilities.

= 1.0.5 =
* All external links now use https
* Added all country codes to an include file which is installed with the plugin so that remote lookups are not needed (negates v1.0.4)

= 1.0.4 =
* Added remote lookups of the list of country codes to use in the settings page. If a new country code(s) is added to the remote server, they are automatically updated locally.

= 1.0.3 =
* Added the register_shutdown_function to check if a SOAP not found error has occured and automatically disables this plugin and display a useful message to the user. This version negates v1.0.2

= 1.0.2 =
* Added ccfl argument to wp-login.php?ccfl=off if your host is not correctly setup and the plugin causes your login form to crash.

= 1.0.1 =
* Added a message loader function to import announcements / news and update messages from the main server into the settings screen.
* Added a message dialog in the dashboard's right now box to show stats
* Added new stats in the settings screens. Blocked by you this month and Attacks against you blocked by the network this month
* Added a check in check_ban routine to see if the request page was the wp-login.php form. If not it returns. This saves on external lookups to the phpwebhost.co.za server 

= 1.0.0 =
* This is the first version :)



== Upgrade Notice ==

= 2.2.0 =
Continuing on from version 2.0.0, not only are banned IPs cached locally now but also IP address's corresponding country codes. These are stored locally for 365 days. This dramatically reduces the number of lookups over the internet. Not only that, but a "clean" ip address is cached too in a session variable to prevent multiple lookups during a single session

= 2.0.0 =
In previous versions, an external server hosted by www.phpwebhost.co.za was solely responsible for banned IP lookups. During an active attack, this could cause very high loads on your server as it has to constantly connect to our server for the lookup. Version 2.0.0 now stores info about banned IPs locally to eliminate constant external lookups

= 1.0.6 =
Added logging capability which allows the user to view run time logs, or click a link to automatically upload those logs to phpwebhost.co.za's servers for inspection.

= 1.0.1 =
The test to see if the user is actually on the wp-login.php screen before doing remote ban lookups is important for page load speeds
