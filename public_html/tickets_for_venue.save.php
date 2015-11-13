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
require('../include/event_paragraph.inc.php');
require_once('../include/url_factory.inc.php');




if((isset($_REQUEST['name'])) && (isset($_REQUEST['id']) && ($_REQUEST['id'] < 100000) && ($_REQUEST['id'] >= 0))
	 && (isset($_REQUEST['vid']) && ($_REQUEST['vid'] < 100000) && ( $_REQUEST['vid']  >= 0)) ) {
	$event_id = $_REQUEST['id'];
	$eventName = $_REQUEST['name'];
        $venueID = $_REQUEST['vid'];
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


$keywords = BuildTheaterKeywordList(4,$eventName, $event_id);
$keywords = AmpersandToAnd($keywords);
$smarty->assign("SeoKeywords", $keywords);


$cleanEventName = preg_replace ('/-/', ' ', $eventName);
$title = "$cleanEventName Tickets";

$descr =  "Buy $cleanEventName Tickets. Discount $cleanEventName Tickets.";
$smarty->assign("MetaDescr", $descr);

if( $dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ($db_name);

	$Bsql = "SELECT Productions.EventID,VenueName, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), City, RegionCode, Venues.VenueID, ProductionID, EventName,Events.CategoryID,Events.EventTypeID FROM Productions LEFT JOIN (Venues)  ON (Venues.VenueID = Productions.VenueID) LEFT JOIN (Events) ON (Events.EventID = Productions.EventID) where Productions.EventID= " . $event_id . " AND Productions.VenueID = " . $venueID . " ORDER BY EventDate";
	if($query_result = mysql_query($Bsql) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
        		$eventID = $table_row[0]; # not used, was causing bug
        		$venueName = $table_row[1];
        		$eventDate = $table_row[2];
        		$city =  utf8_decode($table_row[3]);
        		$regionCode= $table_row[4];
        		$venueID = $table_row[5];
        		$productionID = $table_row[6];
        		$eventName = $table_row[7];
        		$categoryID = $table_row[8];
        		$eventTypeID = $table_row[9];

        		$url = make_production_url($eventName, $productionID);

			$venueNameLocation = "$venueName, $city, $regionCode";

			$productions[] = array("date" => $eventDate, "venuename" => $venueNameLocation, "eventDescr" => "$eventName", "url" => "$url");
		}
		if($city != "") {
			$title = "$city ";
		}
		$title .= "$eventName Tickets";
		if($venueName != "") {
			$title .= " at $venueName";
		}
		if($regionCode != "") {
			$title .= " - $regionCode";
		}
		$smarty->assign("title", $title);
		$smarty->display('main.tpl');

		# use event ID from GET parameter
		$urlEventName = make_event_url($eventName, $event_id);
		$breadcrumb_str = Breadcrumbs($categoryID, 0);
		$breadcrumb_str = AppendBreadcrumb($breadcrumb_str, $urlEventName, $eventName . " Tickets");

		if($venueName != "") {
			$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$eventName at $venueName");
		}

                $smarty->assign("Breadcrumbs", $breadcrumb_str);

		$smarty->assign("EventName", $eventName);
		$eventBanner = GetEventText($categoryID, $eventTypeID, $eventName, $event_id);
		$smarty->assign("EventText", $eventBanner["intro_text"]);
		if($eventBanner["image_pathname"] != "") {
			$smarty->assign("EventImagePathname", $eventBanner["image_pathname"]);
		}

		$smarty->assign("Productions", $productions);
		$smarty->assign("NumProductions", count($productions));
		$smarty->display('productions.tpl');
	}
	else {
		$smarty->assign("title", $title);
		$smarty->display('main.tpl');
		handle_error_no_exit ('tickets_for_venue.code: ' . mysql_error());
                $error_message = get_error_message();
                $smarty->assign("ErrorMessage", $error_message);
                $smarty->display('error_page.tpl');
	}
	
	mysql_close($dbh);
}
else {
	$smarty->assign("title", $title);
	$smarty->display('main.tpl');
	handle_error_no_exit ('tickets_for_venue.code: I cannot connect to the database because: ' . mysql_error());
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('error_page.tpl');
}
$smarty->display('footer.tpl');


function BuildTheaterKeywordList($categoryID,$eventName, $eventID) {
        $lowerEventName =  strtolower($eventName);
	$lowerEventName =  RemoveSpecialChars($lowerEventName);
        $keywords = "$lowerEventName tickets";
        return $keywords;
}


?>
