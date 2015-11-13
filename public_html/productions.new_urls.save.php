<?php
#echo 'hello';
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


$home_only = -1;
if(isset($_REQUEST['event_id']) && ($_REQUEST['event_id'] < 100002) && ($_REQUEST['event_id'] > 1))  {
	$eventID = $_REQUEST['event_id'];
}
else {
	handle_error_no_exit ('productions.php: event_id not provided ' .
		$_SERVER['REQUEST_URI'] . ' returning 301');
        redir_301();
}

if(isset($_GET["home_only"]) && (($_GET["home_only"] == '0') || ($_GET["home_only"] == '1') || ($_GET["home_only"] == '2'))) {
	$home_only = $_GET["home_only"];
}


# |   5 | Baseball         |
# |   6 | Basketball       |
# |   7 | Football         |
# |   9 | Hockey           |
# |  23 | Special Baseball |
# |  48 | Division 1-AA    |
# | 252 | Lacrosse         |
# | 255 | NCAA             |
$HomeAwayGameCategoryIDList = array(5, 6, 7, 9, 23, 48, 252, 255, 15, 19, 25, 30, 84, 85, 86, 88, 89, 90, 94, 95, 96, 98, 99, 100); 
$eventBanner = array();

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';
$smarty->compile_check = true;

$smarty->assign("RootUrl", $root_url);

$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];



if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name) or die ('unable to select ' . $dbname . 'db');

	$query = "SELECT CategoryID,EventName,EventTypeID,EventIntroText,EventText,EventImagePathname FROM Events LEFT JOIN EventText ON (Events.EventID=EventText.EventID) WHERE Events.EventID=$eventID";
	if($query_result = mysql_query($query) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
			$categoryID = $table_row[0];
			$eventName = $table_row[1];
			$eventTypeID = $table_row[2];
			$eventIntroText = $table_row[3];
			$eventText = $table_row[4];
			$eventImagePathname = $table_row[5];
		}

		if(strlen($eventName) < 1) {
			handle_error_no_exit ('productions.code: eventName returned from Events table is empty="' . $eventName . '"' . ' uri= ' . $_SERVER['REQUEST_URI'] . ' ' . mysql_error());
		}

		$keywords = BuildEventKeywordList($categoryID,$eventName, $eventTypeID, $eventID);
		$keywords = AmpersandToAnd($keywords);
		$smarty->assign("SeoKeywords", $keywords);

		$descr =  "$eventName Tickets - Buy $eventName Tickets and all other Tickets at MongoTickets. Buy your $eventName Tickets today.";

		$url = make_event_url($eventName);
		if ($url != $req_uri) {
	 		# 301 redirect to correct url
	 		$url = ltrim  ( $url, '/');
                         header('HTTP/1.1 301 Moved Permanently');
                         header('Location: http://www.mongotickets.com/' . $url);
	 		exit();
	 	}
                $breadcrumb_str = Breadcrumbs($categoryID);
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "$eventName Tickets");

		# if category is theater, list venues first.
		$disp_home_away = 0;
		if($eventTypeID == 4) {
			$title = "$eventName Tickets, $eventName Schedule, $eventName Dates, Discounted $eventName Tickets";
			$smarty->assign("title", $title);

			$descr =  "$eventName Tickets - Buy $eventName Tickets and all other Theater Tickets at MongoTickets. Buy your $eventName Tickets today.";
			$smarty->assign("MetaDescr", $descr);
			$smarty->display('main.tpl');
			$venues = GetVenueList($eventID, $categoryID);

			$smarty->assign("Breadcrumbs", $breadcrumb_str);
			$smarty->assign("EventName", $eventName);
			$smarty->assign("EventIntroText", $eventIntroText);
			if(strlen($eventText) > 2){ $smarty->assign("EventText", $eventText);}

			if(is_array($venues) ) {
				$smarty->assign("Venues", $venues);
				$smarty->assign("NumVenues", count($venues));
				$smarty->display('venue_list.tpl');
			}
			elseif ($eventIntroText != '') {
                                $smarty->display('venue_list.tpl');
			}
			else {
				$smarty->display('no_tickets.tpl');
			}
		}
		else
		{
                	if($eventTypeID == 3) {
				$title = "$eventName Tickets, Buy $eventName Tickets, Discounted $eventName Tickets";
			}
			else {
				$title = "$eventName Tickets, Discounted $eventName Tickets, $eventName Concert Tickets";
			}

			$smarty->assign("title", $title);
			$smarty->assign("MetaDescr", $descr);
			$smarty->display('main.tpl');
			# if sports event
			if($eventTypeID == 3) {
				# home/away games should be deciphered
				$parent_category_list = GetAllParentCategoriesOfEventID($eventID, $categoryID);
				if($home_only < 0) {
					$home_only = 0;
					foreach($parent_category_list as $catID) {
						if(in_array($catID, $HomeAwayGameCategoryIDList)) {
							$home_only = 1;
							#$home_only = 2;
							$disp_home_away = 1;
						}
					}
				}
				else {
					$disp_home_away = 1;
				}
			}
			if(($eventID == 599) || ($eventID == 592) ||($eventID == 339) ||($eventID == 755) ) {
				$smarty->assign("EventName", $productions[0]['eventname'] . ' Playoffs');
				$home_only = 0;
			}


			$productions = GetProductionList($eventID, $eventName, $home_only,$categoryID, 70);
	
                        $smarty->assign("Breadcrumbs", $breadcrumb_str);
			$smarty->assign("EventName", $eventName);
			$smarty->assign("EventIntroText", $eventIntroText);
			if(strlen($eventText) > 2){ $smarty->assign("EventText", $eventText);}
			if($eventImagePathname != "") {
				$smarty->assign("EventImagePathname", $eventImagePathname);
			}
			else {
				$smarty->assign("EventImagePathname", "");
			}


			if(is_array($productions)) {
				if($eventID == 1124) {
					$smarty->assign("EventName", $productions[0]['eventname'] . ' 42');
					$disp_home_away = 0;
				}
				$smarty->assign("DisplayHomeAwayOption", $disp_home_away);
				$smarty->assign("HomeOnlyFlag", $home_only);
				$smarty->assign("EventID", $eventID);
				$smarty->assign("ScriptName", $_SERVER['REQUEST_URI']);
				$smarty->assign("Productions", $productions);
				$smarty->assign("NumProductions", count($productions));
				$smarty->display('productions.tpl');
			}
			elseif(($productions == 0) && ($eventIntroText != '')) {
				$smarty->display('productions.tpl');
			}
			elseif($productions == 0) {
				$smarty->display('no_tickets.tpl');
			}
			else {
		                handle_error_no_exit ('productions.code: ' . mysql_error());
                		$error_message = get_error_message();
                		$smarty->assign("ErrorMessage", $error_message);
                		$smarty->display('error_page.tpl');
			}
		}
	}
	else {
               header('HTTP/1.0 500 Internal Server Error');
		handle_error_no_exit ('productions.code: main query failed, but should not have since calling function should have validated event id' . ' uri= ' . $_SERVER['REQUEST_URI'] . ' ' . mysql_error());
		$smarty->display('main.tpl');
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('error_page.tpl');
	}
	mysql_close($dbh);
}
else {
               header('HTTP/1.0 500 Internal Server Error');
                handle_error_no_exit ('productions.code: failure: event id= ' .  $eventID . 
                                        ' uri= ' . $_SERVER['REQUEST_URI'] . ' I cannot connect to the database because: ' . mysql_error());
                $error_message = get_error_message();
                $smarty->assign("ErrorMessage", $error_message);
                $smarty->display('main.tpl');
                $smarty->display('error_page.tpl');

}

$smarty->display('footer.tpl');

function BuildEventKeywordList($categoryID,$eventName, $eventTypeID, $eventID) {

	switch ($eventTypeID) {
		case 3: 
		if($eventID == 162) {
			$keywords = "chicagocubs, chicagocubs.com, chicagocubs com, chicago cubs com, chicagocub, cubs.com, cubs com";
		}
		else {
			$keywords = BuildSportsKeywordList($categoryID,$eventName);
		}
		break;
		case 4:
		$keywords = BuildTheaterKeywordList($categoryID,$eventName, $eventID);
		break;
		case 2:
		$keywords = BuildConcertKeywordList($categoryID,$eventName, $eventID);
		break;
		default:
#		$keywords = BuildDefaultKeywordList($categoryInfo['id'], $categoryName);
	}

        return $keywords;
}

function BuildSportsKeywordList($eventID,$eventName) {
	$sportName = "";
	$query = "SELECT SportName FROM CategoryToSportName WHERE CategoryID=$eventID";

	if($query_result = mysql_query($query) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
			$sportName = $table_row[0];
		}

		$lowerEventName =  strtolower($eventName);
	        $lowerEventName =  RemoveSpecialChars($lowerEventName); 
		$keywords = "$lowerEventName tickets";
	#if(strlen($sportName) > 0 ) {
	#	$keywords .= ", $lowerEventName $sportName, $sportName, $sportName tickets";
	#}
	#$keywords .= ", sports tickets, tickets";
	}
	else {
		handle_error_no_exit ('productions.code BuildSpKeyL: ' . mysql_error());
	}

	return $keywords;
}

function BuildTheaterKeywordList($categoryID,$eventName, $eventID) {

	$lowerEventName =  strtolower($eventName);
        $lowerEventName =  RemoveSpecialChars($lowerEventName); 
	$keywords = "$lowerEventName tickets";
	return $keywords;
}

function BuildConcertKeywordList($categoryID,$eventName, $eventID) {
        $lowerEventName =  strtolower($eventName);
        $lowerEventName =  RemoveSpecialChars($lowerEventName); 
        $keywords = "$lowerEventName tickets";
        return $keywords;
}
?>
