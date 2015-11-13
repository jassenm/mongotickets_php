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


$home_only = -1;
if(isset($_REQUEST['id']) && ($_REQUEST['id'] < 100002) && ($_REQUEST['id'] >= 0))  {
	$id = $_REQUEST['id'];
}
else {
        header("Location: $root_url");
}

if(isset($_GET["home_only"]) && (($_GET["home_only"] == '0') || ($_GET["home_only"] == '1'))) {
	$home_only = $_GET["home_only"];
}
else {
}


# |   5 | Baseball         |
# |   6 | Basketball       |
# |   7 | Football         |
# |   9 | Hockey           |
# |  23 | Special Baseball |
# |  48 | Division 1-AA    |
# | 252 | Lacrosse         |
# | 255 | NCAA             |
$HomeAwayGameCategoryIDList = array(5, 6, 7, 9, 23, 48, 252, 255); 
$eventBanner = array();

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';
$smarty->compile_check = true;

$smarty->assign("RootUrl", $root_url);


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	$query = "SELECT CategoryID,EventName,EventTypeID FROM Events WHERE EventID=$id";
	if($query_result = mysql_query($query) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
			$categoryID = $table_row[0];
			$eventName = $table_row[1];
			$eventTypeID = $table_row[2];
		}


		$keywords = BuildEventKeywordList($categoryID,$eventName, $eventTypeID, $id);
		$keywords = AmpersandToAnd($keywords);
		$smarty->assign("SeoKeywords", $keywords);

		$title = "$eventName Tickets @ " . COMPANY_NAME;
		$smarty->assign("title", $title);
		$descr = COMPANY_NAME . ", the best place to find $eventName tickets and more.";
		$smarty->assign("MetaDescr", $descr);


		$smarty->display('main.tpl');

		$url = make_event_url($eventName, $id);
                $breadcrumb_str = Breadcrumbs($categoryID, 0);
		$breadcrumb_str = AppendBreadcrumb($breadcrumb_str, "$url", $eventName);

		# if category is theater, list venues first.
		$disp_home_away = 0;
		if($eventTypeID == 4) {
			$venues = GetVenueList($id);

			$smarty->assign("Breadcrumbs", $breadcrumb_str);
			$smarty->assign("EventName", $eventName);
			if(is_array($venues) ) {
				$smarty->assign("Venues", $venues);
				$smarty->assign("NumVenues", count($venues));
				$smarty->display('venue_list.tpl');
			}
			else {
				$smarty->display('no_tickets.tpl');
			}
		}
		else
		{
			# if sports
			if($eventTypeID == 3) {
				# find if home/away games should be deciphered
				$parent_category_list = GetAllParentCategoriesOfEventID($id, $categoryID);
				if($home_only < 0) {
					$home_only = 0;
					foreach($parent_category_list as $catID) {
						if(in_array($catID, $HomeAwayGameCategoryIDList)) {
							$home_only = 1;
							$disp_home_away = 1;
						}
					}
				}
				else {
					$disp_home_away = 1;
				}
			}
			$productions = GetProductionList($id, $eventName, $home_only);
	
			$smarty->assign("EventImagePathname", "");
                        $smarty->assign("Breadcrumbs", $breadcrumb_str);
			$smarty->assign("EventName", $eventName);
			$eventBanner = GetEventText($categoryID, $eventTypeID, $eventName, $id);
			$smarty->assign("EventText", $eventBanner["intro_text"]);
			if($id == 10475) {
				$eventBanner["image_pathname"] = "Images/hannah_montana.jpg";
			}
			if($eventBanner["image_pathname"] != "") {
				$smarty->assign("EventImagePathname", $eventBanner["image_pathname"]);
			}

			if(is_array($productions)) {
				if($id == 1124) {
					$smarty->assign("EventName", $productions[0]['eventname']);
					$disp_home_away = 0;
				}
				$smarty->assign("DisplayHomeAwayOption", $disp_home_away);
				$smarty->assign("HomeOnlyFlag", $home_only);
				$smarty->assign("EventID", $id);
				$smarty->assign("ScriptName", $_SERVER['REQUEST_URI']);
				$smarty->assign("Productions", $productions);
				$smarty->assign("NumProductions", count($productions));
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

function BuildSportsKeywordList($id,$eventName) {
	$sportName = "";
	$query = "SELECT SportName FROM CategoryToSportName WHERE CategoryID=$id";

	if($query_result = mysql_query($query) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
			$sportName = $table_row[0];
		}

		$lowerEventName =  strtolower($eventName);
	        $lowerEventName =  RemoveSpecialChars($lowerEventName); 
		$keywords = "$lowerEventName, $lowerEventName tickets";
		if(strlen($sportName) > 0 ) {
			$keywords .= ", $lowerEventName $sportName, $sportName, $sportName tickets";
		}
		$keywords .= ", sports tickets, tickets";
	}
	else {
		handle_error_no_exit ('productions.code BuildSpKeyL: ' . mysql_error());
	}

	return $keywords;
}

function BuildTheaterKeywordList($categoryID,$eventName, $eventID) {

	$lowerEventName =  strtolower($eventName);
        $lowerEventName =  RemoveSpecialChars($lowerEventName); 
	$keywords = "theater, theatre, theater tickets, theatre tickets, tickets, $lowerEventName, $lowerEventName tickets, $lowerEventName theater, $lowerEventName theater tickets";
	return $keywords;
}

function BuildConcertKeywordList($categoryID,$eventName, $eventID) {
        $lowerEventName =  strtolower($eventName);
        $lowerEventName =  RemoveSpecialChars($lowerEventName); 
        $keywords = "$lowerEventName, $lowerEventName tickets, $lowerEventName concert tickets, $lowerEventName concert";
        return $keywords;
}
?>
