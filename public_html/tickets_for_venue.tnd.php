<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/smarty_package.php');
require_once('../lib/php/Smarty/Smarty.class.php');
include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');
require_once('../include/new_urls/breadcrumbs.inc.php');
require_once('../include/event_paragraph.inc.php');
require_once('../include/new_urls/url_factory.inc.php');



$eventTypeID = 4;

if(isset($_REQUEST['tn_event_id']) && ($_REQUEST['tn_event_id'] < 100000) && ($_REQUEST['tn_event_id'] >= 0)) {
	 
	$_REQUEST['san_city'] = $sanCity;

	$tndEventID = $_REQUEST['tnd_event_id'];
	$tndVenueID = $_REQUEST['tnd_venue_id'];
        $tn_cat_id = $_REQUEST['cat_id'];
        $tnEventID = $_REQUEST['tn_event_id'];
}
else {
	handle_error_no_exit ('tickets_for_venue.tnd.php: neither san_city nor event_id or valid event_id provided ' .
                $_SERVER['REQUEST_URI'] . ' returning 301');
        redir_301();
}

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);



$cleanEventName = "$eventID";
$title = "$cleanEventName Tickets";


if( $dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ($db_name);


  	$bsql = "SELECT TNDProductions.ProductionID, DATE_FORMAT(ProductionDate, '%a. %M %e, %Y %h:%i %p'), VenueName, ProductionName, City, StateProvince, EventName FROM TNDProductions left join TNDEventPerformers on (TNDProductions.ProductionID=TNDEventPerformers.ProductionID) where TNDEventPerformers.EventID=" . $tndEventID . " AND VenueID=" . $tndVenueID . " AND DATEDIFF(NOW(), ProductionDate) <= 0 ORDER BY ProductionDate ASC";


	if($query_result = mysql_query($bsql) ) {

		$num_rows = mysql_num_rows($query_result);
		if($num_rows > 0) {
			while ($table_row = mysql_fetch_row($query_result)) {
        			$productionID = $table_row[0]; # not used, was causing bug
       		 		$eventDate = $table_row[1];
        			$venueName = $table_row[2];
        			$productionName = $table_row[3];
        			$city =  utf8_decode($table_row[4]);
        			$regionCode= $table_row[5];
        			$eventName= $table_row[6];
        			$ticket_page_title_date = $table_row[1];

        			$url = make_production_url($eventName, $productionID, $city, $eventTypeID);

				$venueNameLocation = "$venueName, $city, $regionCode";
				$productions[] = array("date" => $eventDate, "venuename" => $venueNameLocation, "eventDescr" => "$productionName", "url" => "$url", "city" => "$city", "state" => "$regionCode", "ticket_page_title_date" => "$ticket_page_title_date", "venuename_no_loc" => "$venueName");
			}
		}
		else {
			handle_error_no_exit ('tickets_for_venue.tnd.php: EventID and city lookup returned 0 results: eventID=' . $eventID . ' sancity= ' . $sanCity . ' : attempting to find appropriate redirect ' . $_SERVER['REQUEST_URI'] . ' ');
			$Bsql = "SELECT EventName FROM Events where EventID=$tnEventID";
			if($query_result = mysql_query($Bsql) ) {

				$num_rows = mysql_num_rows($query_result);
				if($num_rows == 1) {
					$urlEventName = '';
					while ($table_row = mysql_fetch_row($query_result)) {
        					$eventName = $table_row[0]; # not used, was causing bug
						$urlEventName = make_event_url($eventName);
						$urlEventName = ltrim  ( $urlEventName, '/');
					}
					handle_error_no_exit ('tickets_for_venue.tnd.php: redirecting to ' . $urlEventName . ' uri = ' . $_SERVER['REQUEST_URI']);
					redir_301($urlEventName);
				}
				else {
					handle_error_no_exit ('tickets_for_venue.tnd.php: redirecting to home, uri = ' . $_SERVER['REQUEST_URI']);
					redir_301();
				}
			}
			else {
				handle_error_no_exit ('tickets_for_venue.tnd.php: query failed when attempting redirect, redirecting to home, uri = ' . $_SERVER['REQUEST_URI']);
				redir_301();
			}

		}
		$title = "$eventName Tickets";
		if($city != "") {
			$title .= " $city";
		}
		if($venueName != "") {
			$title .= " at $venueName";
		}
		$H1 = $title;
		if($regionCode != "") {
			$title .= " - $regionCode";
		}
		$clEventName = RemoveSpecialChars($eventName);
		$clVenueName = RemoveSpecialChars($venueName);
		$keywords = "$clEventName Tickets $city, $clEventName Tickets, $clEventName $clVenueName";
        	$keywords = strtolower($keywords);
		$smarty->assign("SeoKeywords", $keywords);
		$smarty->assign("title", $title);
		$descr =  "$city $eventName Tickets. Buy $city $eventName Tickets and all other Theater Tickets at MongoTickets. Buy your $city $eventName Tickets today.";
		$smarty->assign("MetaDescr", $descr);
		$smarty->display('main.tpl');

		$smarty->assign("H1", $H1);
		$urlEventName = make_event_url($eventName);
		$breadcrumb_str = Breadcrumbs($tn_cat_id , 0);
		$breadcrumb_str = AppendBreadcrumb($breadcrumb_str, $urlEventName, $eventName . " Tickets");

		if($venueName != "") {
			$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$eventName at $venueName");
		}

                $smarty->assign("Breadcrumbs", $breadcrumb_str);

		$smarty->assign("EventName", $eventName);
		$smarty->assign("City", $city);
		$eventBanner = ""; 
		#$smarty->assign("EventText", $eventBanner["intro_text"]);
		$smarty->assign("EventText", "");
		#if($eventBanner["image_pathname"] != "") {
		#	$smarty->assign("EventImagePathname", $eventBanner["image_pathname"]);
		#}

		$smarty->assign("Productions", $productions);
		$smarty->assign("NumProductions", count($productions));
		$smarty->display('productions_at_venue.tpl');
	}
	else {
		$smarty->assign("title", $title);
		$smarty->display('main.tpl');
		handle_error_no_exit ('tickets_for_venue.tnd.php: EventID and city lookup failed: eventID=' . $eventID . ' sancity= ' . $sanCity . ' : ' . mysql_error() . ' ' . $_SERVER['REQUEST_URI'] . ' returning 500');
                $error_message = get_error_message();
                $smarty->assign("ErrorMessage", $error_message);
                $smarty->display('error_page.tpl');
	}
	
	mysql_close($dbh);
}
else {
	$smarty->assign("title", $title);
	$smarty->display('main.tpl');
	handle_error_no_exit ('tickets_for_venue.tnd.php: I cannot connect to the database because: ' . mysql_error() . ' ' . $_SERVER['REQUEST_URI'] . ' returning 500');
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('error_page.tpl');
}
$smarty->display('footer.tpl');
#$smarty->display('footer.no_urchin.tpl');


function BuildTheaterKeywordList($eventName) {
        $lowerEventName =  strtolower($eventName);
	$lowerEventName =  RemoveSpecialChars($lowerEventName);
        $keywords = "$lowerEventName";
        return $keywords;
}




?>
