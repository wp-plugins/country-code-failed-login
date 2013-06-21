<?php
/*
Plugin Name: Country Code Failed Login
Plugin URI: http://www.php-web-host.com/wordpress/plugins/country-code-failed-login-wordpress-plugin/
Description: Log and block IP addresses after a single failed login attempt if they are from different country to you.
Version: 1.0.3
Author: PHP-Web-Host.com
Author URI: http://www.php-web-host.com
License: GPL2
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action( 'admin_menu', 'country_code_failed_login_menu');
add_action('admin_init', 'page_init');

add_action ('wp_login_failed', 'country_code_failed_login_ban');

add_action('plugins_loaded', 'country_code_failed_login_check_for_ban', 1);
add_action('auth_cookie_bad_hash', 'country_code_failed_login_check_for_ban');
add_action('login_head', 'country_code_failed_login_check_for_ban'); 
add_action('login_init', 'country_code_failed_login_check_for_ban');
add_action('auth_cookie_bad_username', 'country_code_failed_login_check_for_ban');

add_action('admin_notices', 'country_code_failed_login_notice');

add_action('rightnow_end', 'country_code_failed_login_rightnow');

register_shutdown_function('shutdownFunction');

function shutdownFunction()
{
	
    	$error = error_get_last();

 	if ( ($error['type'] == 1) && (strstr(strtolower($error['message']), 'soapclient') ) )
	{
        	//disable the plugin

                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                deactivate_plugins("country-code-failed-login/country-code-failed-login.php");

                print "<h1 style=\"color:red;\">ERROR!</h1>Your server does not appear to have SOAP installed. SOAP is required for Country Code Failed Login plugin to work, so its been disabled!<p>Please check with your web hosting provider why SOAP is not enabled.<p><a href=\"http://www.php-web-host.com\">PHP-Web-Host.com's servers are optimised for Wordpress</a><p><a href=\"wp-login.php\">Click here to try logging in again</a>";
		exit();
    	} 

}


function country_code_failed_login_menu() {

	if(!is_admin())
	{
		print "Sorry, this functionality is only available to site admins...";
		return;
	}

	add_options_page( 'Country Code Failed Login Options', 'Country Code Failed Login', 'manage_options', 'pwh_country_code_failed_login', 'create_admin_page' );
}


function create_admin_page(){
        ?>
	<div class="wrap">
	    <?php screen_icon(); 

?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

	    <h2>Country Code Failed Login Settings</h2>	

		
	    <form method="post" action="options.php">
	        <?php
                    // This prints out all hidden setting fields
		    settings_fields('pwh_country_code_failed_login_option_group');	
		    do_settings_sections('pwh_country_code_failed_login-setting-admin');
		?>
	        <?php submit_button(); ?>
	    </form>

            <hr>
            <a style="font-size:18px; font-weight:bold;" href="http://www.php-web-host.com/brutes/ViewAttacks.php?Domain=<?php print $_SERVER["SERVER_NAME"]; ?>" target="_BLANK">Click here to view successful blocks</a> | <a style="font-size:18px; font-weight:bold;" href="http://www.php-web-host.com/brutes/ViewUserNames.php" target="_BLANK">Click here to view most commonly attacked usernames</a><p>

	<?php
	$PreBlockTotal = get_option('pwh_country_code_failed_login_country_code_preblock_total', 0);
	if(! is_numeric($PreBlockTotal) )
 	{
		$PreBlockTotal = 0;
	}

	$PreBlockMonth = get_option('pwh_country_code_failed_login_country_code_preblock_'.date("Y_m"), 0);
	if(! is_numeric($PreBlockMonth ) )
 	{
		$PreBlockMonth = 0;
	}

	?>
	IPs blocked by other sites in the network which tried to get to you!!! <b>Total: <?php print $PreBlockTotal; ?></b><br>
	IPs blocked by other sites in the network which tried to get to you this month: <b><?php print $PreBlockMonth ; ?></b><p>


	<?php
	$BlockTotal = get_option('pwh_country_code_failed_login_country_code_block_total', 0);
	if(! is_numeric($BlockTotal) )
 	{
		$BlockTotal = 0;
	}

	$BlockMonth = get_option('pwh_country_code_failed_login_country_code_block_'.date("Y_m"), 0);
	if(! is_numeric($BlockMonth ) )
 	{
		$BlockMonth = 0;
	}

	?>
	IPs blocked by your site (give yourself a pat on the back!!) <b>Total: <?php print $BlockTotal; ?></b><br>
	IPs blocked by your site this month: <b><?php print $BlockMonth ; ?></b><p>

	<?php
	$options2 = array(
	'uri' => 'https://www.php-web-host.com',
	'location' => 'https://www.php-web-host.com/API/Brutes.php',
	'trace' => 1);

	$client = new SoapClient(NULL, $options2);
	$TotalNetworkBlock = $client->TotalNetworkBlock(strtolower(get_option('pwh_country_code_failed_login_country_code')));

	$Announcements = $client->Announcements(strtolower(get_option('pwh_country_code_failed_login_country_code')));

	?>
	IPs currently blocked in the entire network (<?php print strtolower(get_option('pwh_country_code_failed_login_country_code')); ?>): <b>Total - <?php print $TotalNetworkBlock ; ?></b>


	<?php
	if(strlen($Announcements) > 0)
	{
		print "<p>";
		print "<hr>";
		print "<h2>Announcements / News</h2>";
		print $Announcements;
		print "<p>";
	}
	?>

	<hr>
	<h2>Share...</h2>
	<br>
	Sharing is good! The more sites using this plugin, the better your protection from the network, so please let everyone know!

	<p>
<div> 

<div class="fb-like" data-href="http://za24.in/AJ3" data-send="false" data-width="50" data-show-faces="false"></div>
<p>

<a style="margin-left:15px;" href="http://google.com/bookmarks/mark?op=edit&amp;bkmk=http://za24.in/AJ3&amp;title=My+Wordpress+site+has+blocked+<?php print $BlockTotal; ?>+brute+force+attacks+(protected+from+<?php print $PreBlockTotal; ?>+attacks)+with+Country+Code+Failed+Login+Plugin" target="_blank"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/googleplus.png" alt="google" width="20" height="20"></a> 

<a style="margin-left:15px;"  href="http://twitter.com/home?status=My+Wordpress+site+has+blocked+<?php print $BlockTotal; ?>+brute+force+attacks+with+Country+Code+Failed+Login+Plugin http://za24.in/AJ3"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/twitter.png" alt="twiter" width="20" height="20"></a> 

<a style="margin-left:15px;"  href="http://linkedin.com/shareArticle?mini=true&amp;url=http://za24.in/AJ3&amp;title=My+Wordpress+site+has+blocked+<?php print $BlockTotal; ?>+brute+force+attacks+(protected+from+<?php print $PreBlockTotal; ?>+attacks)+with+Country+Code+Failed+Login+Plugin" target="_blank"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/linkedin.png" alt="linkedin" width="20" height="20"></a> 

<a style="margin-left:15px;"  href="http://digg.com/submit?phase=2&amp;url=http://za24.in/AJ3&amp;bodytext=&amp;tags=&amp;title=My+Wordpress+site+has+blocked+<?php print $BlockTotal; ?>+brute+force+attacks+(protected+from+<?php print $PreBlockTotal; ?>+attacks)+with+Country+Code+Failed+Login+Plugin" target="_blank"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/digg.png" alt="linkedin" width="20" height="20"></a> 

<a style="margin-left:15px;"  href="http://www.reddit.com/submit?url=http://za24.in/AJ3&amp;title=My+Wordpress+site+has+blocked+<?php print $BlockTotal; ?>+brute+force+attacks+(protected+from+12000000333+attacks)+with+Country+Code+Failed+Login+Plugin" target="_blank"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/reddit.png" alt="linkedin" width="20" height="20"></a> 

<a style="margin-left:15px;"  href="http://www.delicious.com/post?v=2&amp;url==http://za24.in/AJ3&amp;notes=&amp;tags=&amp;title=My+Wordpress+site+has+blocked+<?php print $BlockTotal; ?>+brute+force+attacks+(protected+from+<?php print $PreBlockTotal; ?>+attacks)+with+Country+Code+Failed+Login+Plugin" target="_blank"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/delicious.png" alt="linkedin" width="20" height="20"></a> 

<a style="margin-left:15px;"  href="mailto:?subject=Wordpress Brute Force Protection Plugin&amp;body=My Wordpress site has blocked <?php print $BlockTotal; ?> brute force attacks (protected+from <?php print $PreBlockTotal; ?> attacks) with Country Code Failed Login Plugin -- http://za24.in/AJ3" target="_blank"><img src="http://www.php-web-host.com/wp-content/themes/wpboheme/images/social-icons/email.png" alt="linkedin" width="20" height="20"></a>

</div>



	</div>
	<?php
    }
	
    function page_init(){	
	
	register_setting('pwh_country_code_failed_login_option_group', 'array_key', 'check_ID');
		
        add_settings_section(
	    'setting_section_id',
	    'Your country setting',
	    'print_section_info',
	    'pwh_country_code_failed_login-setting-admin'
	);	
		
	add_settings_field(
	    'some_id', 
	    'Select your Country', 
	    'create_country_code_dropdown', 
	    'pwh_country_code_failed_login-setting-admin',
	    'setting_section_id'			
	);		
    }
	
    	function check_ID($input)
	{
	        $country_code = $input['country_code'];
	        $SiteOwnerCountry = strtolower(get_option('pwh_country_code_failed_login_country_code'));
	
		if( ($country_code != "-1") && (trim($country_code) != "") )
		{	
			if( (strlen($SiteOwnerCountry ) == 0) )
			{
				add_option('pwh_country_code_failed_login_country_code', $country_code);
				update_option('pwh_country_code_failed_login_country_code_block_total', 0);
				update_option('pwh_country_code_failed_login_country_code_preblock_total', 0);
			}
			else
			{
				update_option('pwh_country_code_failed_login_country_code', $country_code);	
			}
		}
		else
		{
			return "";
	 	}
   	}
	
    function print_section_info(){
	print 'Select the country you are in. This is the country you will usually be in when logging into your wordpress site.<p>If your country is not listed here, please mail support@php-web-host.com requesting that it be added.';
    }
	
    function create_country_code_dropdown(){

	$CurrentSetting = get_option('pwh_country_code_failed_login_country_code');
	
        ?>

	

        <select name="array_key[country_code]">
        <option value="-1">Make Selection</option>
        <option value="za" <?php if($CurrentSetting == "za") print " selected "; ?>>South Africa (ZA)</option>
        <option value="us" <?php if($CurrentSetting == "us") print " selected "; ?>>United States (US)</option>
        <option value="gb" <?php if($CurrentSetting == "gb") print " selected "; ?>>Great Brittain (GB)</option>
	</select>
        <?php
    }


	function country_code_failed_login_ban($UserName)  
	{
		$options = array(
		'uri' => 'https://www.php-web-host.com',
		'location' => 'https://www.php-web-host.com/API/Country.php',
		'trace' => 1);
		
		$client = new SoapClient(NULL, $options);
		$CountryCode = strtolower($client->GetCountryCode($_SERVER["REMOTE_ADDR"]));

		$SiteOwnerCountry = strtolower(get_option('pwh_country_code_failed_login_country_code'));
		
		if( (strlen($SiteOwnerCountry ) == 0) || ($SiteOwnerCountry == "-1") )
		{
			return;
		}

		if($CountryCode != $SiteOwnerCountry)
		{
			$options2 = array(
			'uri' => 'https://www.php-web-host.com',
			'location' => 'https://www.php-web-host.com/API/Brutes.php',
			'trace' => 1);

			$client2 = new SoapClient(NULL, $options2);
			$client2->LogBruteForce($_SERVER["REMOTE_ADDR"], $_SERVER["SERVER_NAME"], $CountryCode, $SiteOwnerCountry, $UserName);



			$BlockTotal = get_option('pwh_country_code_failed_login_country_code_block_total', 0);
			if(! is_numeric($BlockTotal) )
 			{
				$BlockTotal = 0;
			}
			$BlockTotal++;
			update_option('pwh_country_code_failed_login_country_code_block_total', $BlockTotal);


			$BlockMonth = get_option('pwh_country_code_failed_login_country_code_block_'.date("Y_m"), 0);
			if(! is_numeric($BlockMonth ) )
 			{
				$BlockMonth = 0;
			}
			$BlockMonth ++;
			update_option('pwh_country_code_failed_login_country_code_block_'.date("Y_m"), $BlockMonth);
 			
			// some house keeping
			delete_option('pwh_country_code_failed_login_country_code_block_'.date('Y_m',strtotime("-1 months")));
			delete_option('pwh_country_code_failed_login_country_code_block_'.date('Y_m',strtotime("-2 months")));

			header("HTTP/1.0 404 Not Found");
			exit();
		}

	}

    	function country_code_failed_login_check_for_ban()
	{

		// if the user is not on the wp-login.php form, then don't 
		// do the remote checks
		if( ! strstr($_SERVER["REQUEST_URI"], "wp-login.php"))
		{
			return;
		}
		

                if(isset($_REQUEST["ccfl"]))
                {
                        if($_REQUEST["ccfl"] == "off")
                        {
                                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                                deactivate_plugins("country-code-failed-login/country-code-failed-login.php");
                                return;
                        }
                }



		$options = array(
		'uri' => 'https://www.php-web-host.com',
		'location' => 'https://www.php-web-host.com/API/Country.php',
		'trace' => 1);
		
		$client = new SoapClient(NULL, $options);
		$AttackerCountryCode = strtolower($client->GetCountryCode($_SERVER["REMOTE_ADDR"]));

		$SiteOwnerCountry = strtolower(get_option('pwh_country_code_failed_login_country_code'));

		if( (strlen($SiteOwnerCountry ) == 0) || ($SiteOwnerCountry == "-1") )
		{
			return;
		}

		if($AttackerCountryCode != $SiteOwnerCountry )
		{
			$options2 = array(
			'uri' => 'https://www.php-web-host.com',
			'location' => 'https://www.php-web-host.com/API/Brutes.php',
			'trace' => 1);

			$client2 = new SoapClient(NULL, $options2);
			if($client2->CheckForBan($_SERVER["REMOTE_ADDR"]) == true)
        		{
				$PreBlockTotal = get_option('pwh_country_code_failed_login_country_code_preblock_total', 0);
				if(! is_numeric($PreBlockTotal) )
 				{
					$PreBlockTotal = 0;
				}
				$PreBlockTotal++;
				update_option('pwh_country_code_failed_login_country_code_preblock_total', $PreBlockTotal);

				$PreBlockMonth = get_option('pwh_country_code_failed_login_country_code_preblock_'.date("Y_m"), 0);
				if(! is_numeric($PreBlockMonth ) )
 				{
					$PreBlockMonth = 0;
				}
				$PreBlockMonth ++;
				update_option('pwh_country_code_failed_login_country_code_preblock_'.date("Y_m"), $PreBlockMonth);
 
				// some house keeping
				delete_option('pwh_country_code_failed_login_country_code_preblock_'.date('Y_m',strtotime("-1 months")));
				delete_option('pwh_country_code_failed_login_country_code_preblock_'.date('Y_m',strtotime("-2 months")));

				header("HTTP/1.0 404 Not Found");
				exit();
			}

		}

 	}

	function country_code_failed_login_notice($Notes="")
	{
		$SiteOwnerCountry = strtolower(get_option('pwh_country_code_failed_login_country_code'));

		if( (strlen($SiteOwnerCountry) == 0) || ($SiteOwnerCountry == "-1") )
  		{
			print "<div style=\"border: solid 1px red; width:90%; height: 50px; line-height: 130%; padding-left: 10px; padding-top: 8px; font-size:18px; background: #ffffd0; margin-top:10px;\">";
			print "You have not entered your own country in the <b><a href=\"options-general.php?page=pwh_country_code_failed_login\">Country Code Failed Login</a></b> plugin so it is not active at the moment.<br>You can edit the setting by clicking on the <b><a href=\"options-general.php?page=pwh_country_code_failed_login\">\"Settings -> Country Code Failed Login\"</a></b> menu in the left.";
			print "</div>";
		}
	}

	function country_code_failed_login_rightnow()
	{

		$PreBlocked = get_option('pwh_country_code_failed_login_country_code_preblock_'.date("Y_m"), 0);
		if(! is_numeric($PreBlocked ) )
	 	{
			$PreBlocked = 0;
		}

		$Blocked = get_option('pwh_country_code_failed_login_country_code_block_'.date("Y_m"), 0);
		if(! is_numeric($Blocked ) )
	 	{
			$Blocked = 0;
		}

		$Link=admin_url('options-general.php?page=pwh_country_code_failed_login');
		echo "<p><a style=\"font-weight:bold;\" href=\"$Link\">Country Code Failed Login</a> has prevented <font style=\"color:#FF1B55; font-weight:bold;\">".$PreBlocked."</font> attacks. Your site has blocked <font style=\"color:#FF1B55; font-weight:bold;\">".$Blocked."</font> IPs this month!";
		echo"</p>";
	}


?>