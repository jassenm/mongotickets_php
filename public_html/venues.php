<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
require('../include/breadcrumbs.inc.php');
require_once('../include/url_factory.inc.php');



if((isset($_REQUEST['vid']) && ($_REQUEST['vid'] < 10000)) && (isset($_REQUEST['vname'])))  {
	$id = $_REQUEST['vid'];
	$venueName = $_REQUEST['vname'];
	$venueName= preg_replace ('/-/', ' ', $venueName);
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


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	$query = "SELECT Events.EventID, EventName, EventTypeID FROM Events inner join Productions on (Events.EventID = Productions.EventID) where  VenueID = $id GROUP BY Events.EventID ORDER BY EventName ASC";

	if($query_result = mysql_query($query) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
			$eventID = $table_row[0];
			$eventName = $table_row[1];
			$url = make_event_url($eventName, $eventID);

			$eventTypeID = $table_row[2];
			$events[] = array("name" => "$eventName", "url" => "$url");
		}

		# $keywords = BuildEventKeywordList($categoryID,$eventName, $eventTypeID, $id);
		#$keywords = AmpersandToAnd($keywords);
		#$smarty->assign("SeoKeywords", $keywords);

		#$title = "$eventName Tickets @ " . COMPANY_NAME;
		#$smarty->assign("title", $title);
                $title = "$venueName Tickets @ " . COMPANY_NAME;
                $smarty->assign("title", $title);

                #$keywords = GetKeywordsForCategoryID($categoryID);
                $smarty->assign("SeoKeywords", $keywords);


		$smarty->display('main.tpl');

		$breadcrumb_str =  "<a href=\"$root_url/\">Home</a>";
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$venueName Events");
                $smarty->assign("Breadcrumbs", $breadcrumb_str);


		if(count($events) > 0) {
			$smarty->assign("venueName", $venueName);
			$smarty->assign("EventsArray", $events);
			$smarty->assign("NumEvents", count($events));
			$smarty->display('venue_events.tpl');
		}
		else {
			echo "<div id=\"content\">";
			echo "<div id=\"breadcrumb_trail\">$breadcrumb_str</div>";
			echo "<div id=\"no_tickets\">";
			echo "<h1>$venueName Events</h1>";
			echo "<p>There are currently no events at $venueName</p>";
			echo "</div>";
		}
	}
	else {
		handle_error_no_exit ('productions.code: ' . mysql_error());
		$smarty->display('main.tpl');
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('error_page.tpl');
	}
	mysql_close($dbh);
}
else {
	handle_error_no_exit ('productions.code: I cannot connect to the database because: ' . mysql_error());
	$smarty->display('main.tpl');
	$error_message = get_error_message();
	$smarty->assign("ErrorMessage", $error_message);
	$smarty->display('error_page.tpl');
}

$smarty->display('footer.tpl');

?>
