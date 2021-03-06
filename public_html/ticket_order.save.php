<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
require('../include/host_info.inc.php');
include('../include/error.php');
require_once('../include/EventInventoryWebServices.inc.php');

print_r($_POST);


if((isset($_POST['quantity']) && (($_POST['quantity'] < 100) && ($_POST['quantity'] > 0))) 
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

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);

#$smarty->debugging = true;

# UNESCAPE VENUE NAME

$smarty->display('header.tpl');

echo "<div id=\"ticket_order\">";
echo "<div><h2 style='text-align: center; font-size:16px;'>$eventName<br />$eventDate, $venueName</h2></div>";

echo "<div style='position: relative; overflow: hidden;text-align: left;'>";
echo "   <div style='overflow: hidden; margin-top: -70; z-index: -1;'>";
echo "		 <iframe style='overflow: hidden; z-index: -1;' scrolling=auto frameborder=0 src='https://www.ticketsnow.com/buytickets.aspx?id=$ticketID&reqqty=$quantity&client=4089' height='1000' width='1024'></iframe>";
#echo "</div>";


$smarty->display('footer_no_clause.tpl');

?>
