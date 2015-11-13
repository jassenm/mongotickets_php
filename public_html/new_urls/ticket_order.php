<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../../include/smarty_package.php');
require_once('../../lib/php/Smarty/Smarty.class.php');
require_once('../../include/host_info.inc.php');
include_once('../../include/error.php');
require_once('../../include/EventInventoryWebServices.inc.php');

if((isset($_POST['quantity']) && (($_POST['quantity'] < 100))) 
	&& (isset($_POST['ticket_id']) && (($_POST['ticket_id'] < 9999999999) && ($_POST['ticket_id'] >= 0))) 
		&& (isset($_POST['event_name']) && (strlen($_POST['event_name']) < 100))
			&& (isset($_POST['event_date']) && (strlen($_POST['event_date']) < 100))
				&& (isset($_POST['venue_name']) && (strlen($_POST['venue_name']) < 100) )) {
	$quantity = $_POST['quantity'];
	$ticketID = $_POST['ticket_id'];
	$eventName = stripslashes($_POST['event_name']);
	$eventDate = $_POST['event_date'];
	$venueName = stripslashes($_POST['venue_name']);
}
else {
        header("Location: $root_url");
}

$smarty = new Smarty;

$smarty->template_dir = '../../smarty/templates/new_urls/';
$smarty->compile_dir = '../../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../../smarty/cache/new_urls/';
$smarty->config_dir = '../../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);

#$smarty->debugging = true;

# UNESCAPE VENUE NAME

$smarty->display('header_order.tpl');
$smarty->display('top_banner_order.tpl');

echo "<div id=\"ticket_order\">";
echo "<div><h2 style='text-align: center; font-size:16px;'>$eventName<br />$eventDate, $venueName</h2></div>";

# echo "<div style='position: relative; margin: 0 0 0 40; padding: 0 0 0 60; overflow: hidden;text-align: left;'>";
echo "   <div style='overflow: hidden; margin: -70 0 0 0px; padding: 0 0 0 21px; '>";
echo "		 <iframe style='overflow: hidden; padding: 0 0 0 0; margin: 0 0 0 0px; z-index: -1;' scrolling=auto frameborder=0 src=\"https://www.ticketsnow.com/buytickets.aspx?id=$ticketID&reqqty=$quantity&client=4089\" height=\"1100\" width=\"100%\"></iframe>";
#echo "</div>";


$smarty->display('footer_no_clause.tpl');

?>
