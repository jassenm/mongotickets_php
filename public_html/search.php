<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');

include('../include/domain_info.inc.php');
include('../include/host_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
require('../include/breadcrumbs.inc.php');



if(isset($_REQUEST['keywords']) && isset($_REQUEST['search_preference'])) {
	$keywords = $_REQUEST['keywords'];
	if(strlen($keywords) == 0) {
		header("Location: $root_url");
	}
	else {
		$search_pref = $_REQUEST['search_preference'];
        	switch ($search_pref) {
                	case 'by_event':
                        	break;
                	case 'by_venue':
                        	break;
                	default:
				header("Location: $root_url");
        	}
	}

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

$smarty->display('main.tpl');

if($dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ($db_name);

	$keywords = mysql_escape_string($keywords);
	if($search_pref == 'by_event') {
		if($query_result = mysql_query('SELECT EventID,EventName FROM Events WHERE EventName LIKE ' . "'%" . $keywords . "%' ORDER BY EventName ASC") ) {
			while ($table_row = mysql_fetch_array($query_result)) {
				$eventID = $table_row['EventID'];
				$eventName = $table_row['EventName'];
				$url = make_event_url($eventName, $eventID);
				$events[] = array("name" => "$eventName", "url" => "$url");
			}
			$smarty->assign("categoryName", $category);
			$smarty->assign("Events", $events);
			$smarty->assign("NumEvents", count($events));

			$smarty->assign("SubCategories", $subcategories);
			$smarty->assign("NumSubCategories", count($subcategories));
			$breadcrumb_string = "<a href=\"$root_url/\">Home</a>";
			$breadcrumb_string .= "<img src=\"$root_url/Images/SmallArrow.jpg\" alt=\" \"/>&nbsp;Search Results";
			$smarty->assign("Breadcrumbs", $breadcrumb_string);


			$smarty->display('search.tpl');
		}
		else {
			handle_error_no_exit ('search.code: ' . mysql_error());
			$error_message = get_error_message();
			$smarty->assign("ErrorMessage", $error_message);
			$smarty->display('error_page.tpl');
		}
	}
	else if($search_pref == 'by_venue') {

		if($query_result = mysql_query('SELECT VenueID,VenueName FROM Venues WHERE VenueName LIKE ' . "'%" . $keywords . "%' ORDER BY VenueName ASC") ) {
			while ($table_row = mysql_fetch_array($query_result)) {
				$venueID = $table_row['VenueID'];
				$venueName = utf8_decode($table_row['VenueName']);
				$url = make_venues_url($venueName, $venueID);
				$venues[] = array("name" => "$venueName", "url" => "$url");
			}
			$smarty->assign("Venues", $venues);
			$smarty->assign("NumVenues", count($venues));

			$smarty->display('search.tpl');
		}
		else {
			handle_error_no_exit ('search.code: ' . mysql_error());
			$error_message = get_error_message();
			$smarty->assign("ErrorMessage", $error_message);
			$smarty->display('error_page.tpl');
		}
	}


	mysql_close($dbh);
}
else {
	handle_error_no_exit ('search.code: I cannot connect to the database because: ' . mysql_error());
	$error_message = get_error_message();
	$smarty->assign("ErrorMessage", $error_message);
	$smarty->display('error_page.tpl');

}

$smarty->display('footer.tpl');

?>
