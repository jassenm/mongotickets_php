<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require('../include/smarty_package.php');

// put full path to Smarty.class.php
require('../lib/php/Smarty/Smarty.class.php');

include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.new.php');
require('Utils.php');
include('../include/error.php');
require('../include/breadcrumbs.inc.php');

if((isset($_REQUEST['id']) && ($_REQUEST['id'] <= 4) && ($_REQUEST['id'] >=0)) && (isset($_REQUEST['name']) && (strlen($_REQUEST['name']) < 10) )) {
        $categoryID = $_REQUEST['id'];
        $categoryName = $_REQUEST['name'];
}
else {
	header("Location: http://$domain/");
}

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);


$title = "$categoryName Tickets @ " . COMPANY_NAME;
$smarty->assign("title", $title);
$descr = COMPANY_NAME . ", the best place to find $categoryName tickets and more.";

$smarty->assign("MetaDescr", $descr);


if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	$keywords = GetKeywordsForCategoryID($categoryID);
	$smarty->assign("SeoKeywords", $keywords);
	$smarty->display('main.tpl');

	$topCategories = '';
	switch ($categoryID) {
		case 3:
			$topCategories = GetHotSportsCategories(-1);
    			break;
		case 2:
			$topCategories = GetHotCategories($categoryID);
			break;
		case 4:
			$topCategories = GetHotCategories($categoryID);
			break;
		default:
			handle_error_no_exit('top_level_categories: unknown category ID');
	}

	if(!is_array($topCategories)) {
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('error_page.tpl');
		$smarty->display('footer.tpl');
		mysql_close($dbh);
		exit;
	}
	else {
		$smarty->assign("CategoryName", $categoryName);
		for ($i=0; ($i < count($topCategories)) && ($i < 3); $i++) {
			$limit = 10;
			$events =  GetTopEventsSubordinateToCategoryID($topCategories[$i]['id'],$limit);
                	if(!is_array($events)) {
                        	$error_message = get_error_message();
                        	$smarty->assign("ErrorMessage", $error_message);
                        	$smarty->display('error_page.tpl');
                        	$smarty->display('footer.tpl');
                        	mysql_close($dbh);
                        	exit;
                	}
                	elseif(count($events) > 0 ) {

				$top_event_name = $events[0]['name'];
				$top_event_url = $events[0]['url'];
				$category_name = $topCategories[$i]['name'];
				$category_id = $topCategories[$i]['id'];
				$url_category = make_category_url($category_name, $category_id);
				$num_events = count($events);
				if($num_events > 0) {
					$events[$num_events-1]['url'] = "$url_category"; 
					$events[$num_events-1]['name'] = "More $category_name Events&hellip;"; 
				}
				if( $topCategories[$i]['catimage'] != "") {
					$catImage = CATEGORY_IMAGES_PATH . $topCategories[$i]['catimage'];
				}
				else {
					$catImage = "";
				}
				$categories[] = array("catname" => "$category_name", "caturl" => "$url_category", "top_event_name" => "$top_event_name", "top_events" => $events,  "catimage" => $catImage); 

				$top_event_names[] = "$top_event_name";
			}
		}
		$breadcrumb_str = Breadcrumbs($categoryID, 0);
        	$smarty->assign("Breadcrumbs", $breadcrumb_str);
		$text_content = BuildTextContent($categoryID, $top_event_names);
        	$smarty->assign("TextContent", $text_content);
		$smarty->assign("SubCategories", $categories);
		$smarty->assign("NumSubCategories", count($categories));
		$smarty->display('hot_category_events.tpl');

		if(count($topCategories) > 3) {

			$numRelatedCategories = count($topCategories) - 3;

			for($j=3; $j < $numRelatedCategories; $j++) {
				$category_id = $topCategories[$j]['id'];
				$url_category = make_category_url($topCategories[$j]['name'], $category_id);
				$relatedCategories[] = array("catname" => $topCategories[$j]['name'], "caturl" => "$url_category");

			}
			$smarty->assign("RelatedCategories", $relatedCategories);
			$smarty->assign("NumRelatedCategories", $numRelatedCategories);
			$smarty->display('related_events.tpl');
		}
	}
	mysql_close($dbh);
}
else {
	$smarty->display('main.tpl');
        handle_error_no_exit ('top_level_categories.code: I cannot connect to the database because: ' . mysql_error());
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('error_page.tpl');
}


$smarty->display('footer.tpl');


function BuildTextContent($categoryID, $top_event_names) {

	switch ($categoryID) {
		case 3:
			$textPrefix = "Find tickets for your favorite teams at MongoTickets.com.  We've compacted a complete array of tickets available for you to choose from, including ";
			$eventSuffix = "";
			$textSuffix = ".  View from the best seats in the house or the bleachers, either way you're sure to have an awesome time.";
    			break;
		case 2:
			$textPrefix = "Whether you're in New York, San Francisco, Las Vegas, or Chicago you can find premium seats to each and every concert at MongoTickets.com. ";
			$eventSuffix = " Tickets";
			$textSuffix = " are available in nearly all tour locations.  Enjoy your favorite genre of music from rock to classical, live at a venue near you.";
			break;
		case 4:
			$textPrefix = "Dating back to the ancient Roman's man has been attending theater events of one kind or another.  Today, we've mastered the art of acting showcasing events like ";
			$eventSuffix = "";
			$textSuffix = ".  Don't miss your chance to buy your tickets today.";
			break;
		default:
			handle_error_no_exit('BuildTextContent: unknown category ID');
	}
	for ($i=0; ($i < count($top_event_names)) && ($i < 3); $i++) {
		if(strlen($textEvents) > 0) {
			$textEvents .= ", " . $top_event_names[$i] . $eventSuffix;
		}
		else {
			$textEvents = $top_event_names[$i] . $eventSuffix;
		}
	}
	$text = $textPrefix . $textEvents . $textSuffix; 

	return $text;

}

?>
