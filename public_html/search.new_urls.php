<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#

require_once('../include/smarty_package.php');
require_once('../lib/php/Smarty/Smarty.class.php');

include_once('../include/domain_info.inc.php');
include_once('../include/host_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');
require_once('../include/new_urls/breadcrumbs.inc.php');


$by_venue = 0;
$category = '';
$subcategories = '';
$venues = array();
$venueID = '';

if(isset($_POST['keywordz']) && isset($_POST['search_preference'])) {
	$keywords = $_POST['keywordz'];
	$search_pref = $_POST['search_preference'];
       	switch ($search_pref) {
               	case 'by_event':
                       	break;
               	case 'by_venue':
			$by_venue = 1;
                       	break;
               	default:
  			header('Location: http://www.mongotickets.com/');
			exit();
       	}

}
else {
	header('Location: http://www.mongotickets.com/');
	exit();
}

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);

$smarty->display('main.tpl');
$smarty->assign("ByVenue", $by_venue);

if(strlen($keywords) == 0) {
       $h1 = htmlspecialchars("Search Results for Events Containing \"" . $keywords . "\"");
	$events = array();
	$smarty->assign("h1", $h1);
	$smarty->assign("categoryName", $category);
	$smarty->assign("Events", $events);
	$smarty->assign("NumEvents", count($events));

	$subcategories = array();
	$smarty->assign("SubCategories", $subcategories);
	$smarty->assign("NumSubCategories", count($subcategories));
	$breadcrumb_string = "<a href=\"$root_url/\">Home</a>";
	$breadcrumb_string .= "&gt;&nbsp;Search Results";
	$smarty->assign("Breadcrumbs", $breadcrumb_string);
	$smarty->display('search.tpl');
}
else {

if($dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ($db_name);

	$keywords = mysql_escape_string($keywords);
	if($search_pref == 'by_event') {
		$keywords = str_ireplace("tickets","",$keywords);
		$keywords = str_ireplace("ticket","",$keywords);
		$keywords = trim($keywords);
		if($query_result = mysql_query('SELECT EventID,EventName,CategoryID FROM Events WHERE EventName LIKE ' . "'%" . $keywords . "%' ORDER BY EventName ASC") ) {
			while ($table_row = mysql_fetch_array($query_result)) {
				$eventID = $table_row['EventID'];
				$eventName = $table_row['EventName'];
				$categoryID = $table_row['CategoryID'];
				$url = make_event_url($eventName, $categoryID);
				$events[] = array("name" => "Buy $eventName tickets", "url" => "$url");
			}
			$h1 = htmlspecialchars("Search Results for Events Containing \"" . $keywords . "\"");
			$smarty->assign("h1", $h1);
			$smarty->assign("categoryName", $category);
			$smarty->assign("Events", $events);
			$smarty->assign("NumEvents", count($events));

			$smarty->assign("SubCategories", $subcategories);
			$smarty->assign("NumSubCategories", count($subcategories));
			$breadcrumb_string = "<a href=\"$root_url/\">Home</a>";
			$breadcrumb_string .= "&gt;&nbsp;Search Results";
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

		if($query_result = mysql_query('SELECT VenueID,VenueName,SanitizedVenueName,RegionCode FROM Venues WHERE VenueName LIKE ' . "'%" . $keywords . "%' ORDER BY VenueName ASC") ) {
			while ($table_row = mysql_fetch_array($query_result)) {
				$venueID = $table_row['VenueID'];
				$venueName = utf8_decode($table_row['VenueName']);
				$sanitizedVenueName = $table_row['SanitizedVenueName'];
				$regionCode = $table_row['RegionCode'];
				$url = make_venues_url($venueName, $sanitizedVenueName, $regionCode);
				$venues[] = array("name" => "View $venueName events", "url" => "$url");
			}
			$h1 = htmlspecialchars("Search Results for Venues Containing \"" . $keywords . "\"");
			$smarty->assign("h1", $h1);
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
}

$smarty->display('footer.tpl');

?>
