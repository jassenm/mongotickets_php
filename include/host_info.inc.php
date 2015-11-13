<?php

#$domain = "www.mongotickets.com";
#$root_url = "http://$domain";
$domain = "";
#$root_url = "";
$root_url = "http://mongotickets.com";
#$root_url = "https://174.120.238.25:8443/sitepreview/http/mongotickets.com";
if (!defined('SITE_DOMAIN')) {

	define('SITE_DOMAIN', $root_url);
}

?>
