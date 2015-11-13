<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/smarty_package.php');

// put full path to Smarty.class.php
require_once('../lib/php/Smarty/Smarty.class.php');

include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);
$title = "Boston Red Sox Tickets, Dallas Cowboys Tickets, Wicked Tickets";
$smarty->assign("title", $title);
$descr = COMPANY_NAME . ", the best place to find concert, theater, and sports tickets.";
$smarty->assign("MetaDescr", $descr);
$breadcrumb_string = "<a href=\"$root_url/\">Latest Events</a>";
$smarty->assign("Breadcrumbs", $breadcrumb_string);
$keywords = "concert tickets, theater tickets, sports tickets, mlb tickets, nba tickets, nfl tickets, football tickets";
$smarty->assign("SeoKeywords", $keywords);

$additional_text_content = "";

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	$smarty->display('main.tpl');


	$topThreeArray = GetMainCategories();
	$categories = array();
	$text_content = "This page showcases the latest and greatest events available. Check this page often to get the best prices on concerts, theatre, or sports events.";
	$text_content .= "<script type=\"text/javascript\" src=\"misc_tools/tabber.js\"><pre></script><div class=\"tabber\">";
	for ($i=0; $i < count($topThreeArray); $i++) {
		$limit = 20;
		$events =  GetLatestEventsUnderCategory($topThreeArray[$i]['id'],$limit);
		if(!is_array($events)) {
        		$error_message = get_error_message();
        		$smarty->assign("ErrorMessage", $error_message);
        		$smarty->display('error_page.tpl');
			$smarty->display('footer.tpl');
			mysql_close($dbh);
			exit;
		}
		elseif(count($events) > 0 ) {
			$text_content .= "<div class=\"tabbertab\" title=\"" . $topThreeArray[$i]['name'] . "\"><h3>" .$topThreeArray[$i]['name'] . "</h3>";

			for ($j=0; $j< count($events); $j++)
			{
				$EventName = $events[$j]['EventName'];
				$EventID = $events[$j]['EventID'];
				$SanitizedEventName = $events[$j]['SanitizedEventName'];
				$EventTypeID = $events[$j]['EventTypeID'];

				$productions = GetUnformattedProductionList($EventID);
				if (count($productions) > 0)
				{
					for ($k=0; $k<count($productions); $k++)
					{
						$ProductionID = $productions[$k]['ProductionID'];
						$VenueName = $productions[$k]['VenueName'];
						$DateTime = $productions[$k]['DateTime'];
						$PDate = $productions[$k]['Date'];
						$VenueName = $productions[$k]['VenueName'];

						$text_content .= "<span class=\"bold\">" . $EventName . " at " . $VenueName. "</span>";

						$text_content .= "<span class=\"ticimg\"><a href=\"" . $SanitizedEventName . "-tickets/?event_id=" . $ProductionID . "\">";
						$text_content .= "<img src=\"/Images/tickets_vb.gif\"  alt=\"Buy " . $EventName . " tickets at " . $VenueName . " on " . $DateTime . " \"/></a></span><br>";

						$text_content .= "<span class=\"tictagLine\">" . $DateTime . "</span><br>";
						$text_content .= "<span class=\"ticbody\">Tickets for " . $EventName . " are now available. Get your " . $EventName . " tickets, for " . $VenueName . " on " . $PDate . " before all the " . $EventName . " tickets sell out</span><br>";
						$text_content .= "<span class=\"ticLink\"><a href=" . $SanitizedEventName . "-tickets/?event_id=" . $ProductionID . ">Buy " . $EventName . " tickets at " . $VenueName . " on " . $DateTime . "</a></span><br>";
					}
				}
			}
			$text_content .= "</div>";
		}
	}
	$text_content .= "</div>";

	$smarty->assign("TextContent", $text_content);
	#$home_url = "<a href=\"$root_url/\">Home</a>";
	#$smarty->assign("Breadcrumbs", $home_url);

	$smarty->assign("SubCategories", $categories);
	$smarty->assign("NumSubCategories", count($categories));
	$smarty->display('hot_category_events.tpl');

	$smarty->assign("RelatedCategories", $allRelatedCategories);
	$smarty->assign("NumRelatedCategories", $numRelatedCategories);
	$smarty->display('main_related_events.tpl');
	$smarty->display('right_bar.tpl');
	$smarty->display('left_column.tpl');

	mysql_close($dbh);

}
else {
        # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
	handle_error_no_exit ('main.code: I cannot connect to the database because: ' . mysql_error());

        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
	$smarty->display('main.tpl');
        $smarty->display('error_page.tpl');
}

$smarty->display('footer.tpl');

?>

