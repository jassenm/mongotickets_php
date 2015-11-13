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

$seo_data = array();
$seo_data = array(
		162 => array(
			'event' => 'Chicago Cubs',
			'title_tag' => 'Chicago Cubs Tickets, Baseball :: MongoTickets.com',
			'meta_descr' => 'Chicago Cubs Tickets - Buy Major League Baseball Tickets and all other Chicago Tickets at MongoTickets.com',
			'keywords' => 'chicago cubs tickets, baseball tickets, major league baseball tickets'),
		107 => array(
			'event' => 'Boston Red Sox',
			'title_tag' => 'Boston Red Sox Tickets, Major League Baseball',
			'meta_descr' => 'Boston Red Sox Tickets - Buy Boston Red Sox Tickets and all other Major League Tickets at MongoTickets.com',
			'keywords' => 'boston red sox tickets, major league baseball',
			), 
		592 => array(
			'event' => 'New England Patriots',
			'title_tag' => 'New England Patriots Tickets :: MongoTickets.com',
			'meta_descr' => 'New England Patriots Tickets - Buy New England Patriots Tickets and all other NFL Tickets at MongoTickets.com',
			'keywords' => 'new england patriots tickets, new england patriots, nfl schedule, football',
			), 
		214 => array(
			'event' => 'Dallas Cowboys',
			'title_tag' => 'Dallas Cowboys Tickets, NFL Games :: MongoTickets.com',
			'meta_descr' => 'Dallas Cowboys Tickets - Buy Dallas Cowboys Tickets and all other NFL Tickets at MongoTickets.com',

			'keywords' => 'dallas cowboys tickets, dallas cowboys, nfl schedule, football',
			), 
		4167 => array(
			'event' => 'Wicked',
			'title_tag' => 'Wicked Tickets, Broadway Musical :: MongoTickets.com',
			'meta_descr' => 'Wicked Tickets - Buy Wicked Tickets and all other Theater Tickets at MongoTickets.com',

			'keywords' => 'wicked tickets, broadway musical, theater tickets',
			) 
		);

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

		# uncomment
		 $url = make_event_url($eventName);
		# comment
		#$url = $req_uri;
		#if ($url != $req_uri) {
	 		# 301 redirect to correct url
	 	#	$url = ltrim  ( $url, '/');
                 #        header('HTTP/1.1 301 Moved Permanently');
                  #       header('Location: http://www.mongotickets.com/' . $url);
	 	#	exit();
	 	#}
                $breadcrumb_str = Breadcrumbs($categoryID);
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "$eventName Tickets");

		# if category is theater, list venues first.
		$disp_home_away = 0;
		if($eventTypeID == 4) {
			$title = "$eventName Tickets, $eventName Schedule, $eventName Dates, Discounted $eventName Tickets";

			$descr =  "$eventName Tickets - Buy $eventName Tickets and all other Theater Tickets at MongoTickets. Buy your $eventName Tickets today.";
			if(array_key_exists($eventID, $seo_data)) {
				$title = $seo_data[$eventID]['title_tag'];
				$descr = $seo_data[$eventID]['meta_descr'];	
				$keywords = $seo_data[$eventID]['keywords'];
				$smarty->assign("SeoKeywords", $keywords);
			}
			$smarty->assign("title", $title);
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

			if(array_key_exists($eventID, $seo_data)) {
				$title = $seo_data[$eventID]['title_tag'];
				$descr = $seo_data[$eventID]['meta_descr'];	
				$keywords = $seo_data[$eventID]['keywords'];
				$smarty->assign("SeoKeywords", $keywords);
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


			$productions = GetList($eventID, $eventName, $home_only,$categoryID, 70);
	
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
function GetList ($eventID, $eventName, $home_only, $categoryID, $max_display)
{
               $bsql = "SELECT TNDProductions.ProductionID, DATE_FORMAT(ProductionDate, '%a. %M %e, %Y %h:%i %p'), VenueName, ProductionName, City, StateProvince, EventName FROM TNDProductions left join TNDEventPerformers on (TNDProductions.ProductionID=TNDEventPerformers.ProductionID) where TNDEventPerformers.EventName='" . $eventName . "' AND DATEDIFF(NOW(), ProductionDate) <= 0 ORDER BY ProductionDate ASC";

        $num_productions = 0;
        $count = 0;
       if($query_result = mysql_query($bsql)) {
          while ($table_row = mysql_fetch_row($query_result)) {
                $productionID = $table_row[0];
                $eventDate = $table_row[1];
                $venueName = utf8_decode($table_row[2]);
                # $homeEventName = $table_row[3];
                $productionName = $table_row[3];
                $eventTypeID = 3;
                $city = utf8_decode($table_row[4]);
                $regionCode = $table_row[5];
                $eventName = utf8_decode($table_row[6]);
                $ticket_page_title_date = $table_row[1];
                # $opponentEventName = $table_row[9];
  		# $opponentEventName = substr($opponentEventName, 19);
  		# $opponentName = $opponentEventName;
                # if(($count % 5) == 0) {
                #        $opponentEventName = str_replace(' ', '', $opponentEventName);
                # }
                # elseif(($count % 9) == 0) {
                #        $opponentEventName = rtrim($opponentEventName, 's');
                # }
                # elseif(($count % 13) == 0) {
                #         $opponentEventName = str_replace(' ', '', $opponentEventName . '.com');
                # }

                if ( ($eventTypeID == 3) ) {
                        $ticket_page_title = "$eventName Tickets at $venueName $regionCode on $ticket_page_title_date";
                }
                else {
                        $ticket_page_title = "$eventName Tickets at $venueName in $city, $regionCode on $ticket_page_title_date";
                }
		$eventDescr =  $eventName;
                $url= make_production_url($eventName, $productionID, $city, $eventTypeID);
                $productions[] = array("eventname" => "$productionName", "url" => "$url", "venuename" => "$venueName<br />$city, $regionCode", "date" => "$eventDate<br />", "eventid" => "$eventID", "eventDescr" => "$productionName", "ticket_page_title" => "$ticket_page_title");
                $count++;
                $num_productions++;
           }
        }
        else {
                handle_error_no_exit ('GetProductionList: ' . mysql_error());
                $productions = "Error";
        }
        return $productions;





}


?>
