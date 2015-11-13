<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../../include/smarty_package.php');
require_once('../../lib/php/Smarty/Smarty.class.php');
include_once('../../include/host_info.inc.php');
include_once('../../include/domain_info.inc.php');
require_once('../../include/new_urls/ticket_db.php');
require_once('DbUtils.php');
require_once('Utils.php');
include_once('../../include/error.php');
require_once('../../include/new_urls/breadcrumbs.inc.php');
require_once('../../include/event_paragraph.inc.php');
require_once('../../include/new_urls/url_factory.inc.php');




if((isset($_REQUEST['san_city'])) && (isset($_REQUEST['event_id']) && ($_REQUEST['event_id'] < 100000) && ($_REQUEST['event_id'] >= 0))) {
	 
	$eventID = $_REQUEST['event_id'];
	$sanCity = $_REQUEST['san_city'];
}
else {
	handle_error_no_exit ('tickets_for_venue.php: neither san_city nor event_id or valid event_id provided ' .
                $_SERVER['REQUEST_URI'] . ' returning 301');
        redir_301();
}

$smarty = new Smarty;

$smarty->template_dir = '../../smarty/templates/new_urls/';
$smarty->compile_dir = '../../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../../smarty/cache/new_urls/';
$smarty->config_dir = '../../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);


$keywords = BuildTheaterKeywordList(4,$eventName, $event_id);
$keywords = AmpersandToAnd($keywords);
$smarty->assign("SeoKeywords", $keywords);


$cleanEventName = preg_replace ('/-/', ' ', $eventName);
$title = "$cleanEventName Tickets";


if( $dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ($db_name);


	$Bsql = "SELECT Productions.EventID,VenueName, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), City, RegionCode, Venues.VenueID, ProductionID, EventName,Events.CategoryID,Events.EventTypeID FROM Productions LEFT JOIN (Venues)  ON (Venues.VenueID = Productions.VenueID) LEFT JOIN (Events) ON (Events.EventID = Productions.EventID) where Productions.EventID= " . $eventID . " AND Venues.SanitizedCity = '" . $sanCity . "' ORDER BY EventDate";
	if($query_result = mysql_query($Bsql) ) {

		$num_rows = mysql_num_rows($query_result);
		if($num_rows > 0) {
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

        			$url = make_production_url($eventName, $productionID, $city, $eventTypeID);

				$venueNameLocation = "$venueName, $city, $regionCode";
				$productions[] = array("date" => $eventDate, "venuename" => $venueNameLocation, "eventDescr" => "$eventName", "url" => "$url");
			}
		}
		else {
			handle_error_no_exit ('tickets_for_venue.php: EventID and city lookup returned 0 results: eventID=' . $eventID . ' sancity= ' . $sanCity . ' : attempting to find appropriate redirect ' . $_SERVER['REQUEST_URI'] . ' ');
			$Bsql = "SELECT EventName FROM Events where EventID=$eventID";
			if($query_result = mysql_query($Bsql) ) {

				$num_rows = mysql_num_rows($query_result);
				if($num_rows == 1) {
					$urlEventName = '';
					while ($table_row = mysql_fetch_row($query_result)) {
        					$eventName = $table_row[0]; # not used, was causing bug
						$urlEventName = make_event_url($eventName);
						$urlEventName = ltrim  ( $urlEventName, '/');
					}
					handle_error_no_exit ('tickets_for_venue.php: redirecting to ' . $urlEventName . ' uri = ' . $_SERVER['REQUEST_URI']);
					redir_301($urlEventName);
				}
				else {
					handle_error_no_exit ('tickets_for_venue.php: redirecting to home, uri = ' . $_SERVER['REQUEST_URI']);
					redir_301();
				}
			}
			else {
				handle_error_no_exit ('tickets_for_venue.php: query failed when attempting redirect, redirecting to home, uri = ' . $_SERVER['REQUEST_URI']);
				redir_301();
			}

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
		$descr =  "$city $eventName Tickets. Buy $city $eventName Tickets and all other Theater Tickets at MongoTickets. Buy your $city $eventName Tickets today.";
		$smarty->assign("MetaDescr", $descr);
		$smarty->display('main.tpl');

		$urlEventName = make_event_url($eventName);
		$breadcrumb_str = Breadcrumbs($categoryID, 0);
		$breadcrumb_str = AppendBreadcrumb($breadcrumb_str, $urlEventName, $eventName . " Tickets");

		if($venueName != "") {
			$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$eventName at $venueName");
		}

                $smarty->assign("Breadcrumbs", $breadcrumb_str);

		$smarty->assign("EventName", $eventName);
		$smarty->assign("City", $city);
		$eventBanner = GetEventText($categoryID, $eventTypeID, $eventName, $event_id);
		$smarty->assign("EventText", $eventBanner["intro_text"]);
		if($eventBanner["image_pathname"] != "") {
			$smarty->assign("EventImagePathname", $eventBanner["image_pathname"]);
		}

		$smarty->assign("Productions", $productions);
		$smarty->assign("NumProductions", count($productions));
		$smarty->display('productions_at_venue.tpl');
	}
	else {
		$smarty->assign("title", $title);
		$smarty->display('main.tpl');
		handle_error_no_exit ('tickets_for_venue.php: EventID and city lookup failed: eventID=' . $eventID . ' sancity= ' . $sanCity . ' : ' . mysql_error() . ' ' . $_SERVER['REQUEST_URI'] . ' returning 500');
                $error_message = get_error_message();
                $smarty->assign("ErrorMessage", $error_message);
                $smarty->display('error_page.tpl');
	}
	
	mysql_close($dbh);
}
else {
	$smarty->assign("title", $title);
	$smarty->display('main.tpl');
	handle_error_no_exit ('tickets_for_venue.php: I cannot connect to the database because: ' . mysql_error() . ' ' . $_SERVER['REQUEST_URI'] . ' returning 500');
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
