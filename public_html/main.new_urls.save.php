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
$title = "Mongo Tickets - Houston Rodeo Tickets, MLB Baseball Tickets, High School Musical Tickets";
$smarty->assign("title", $title);
$descr = COMPANY_NAME . ", the best place to find concert, theater, and sports tickets.";
$smarty->assign("MetaDescr", $descr);
$breadcrumb_string = "<a href=\"$root_url/\">Home</a>";
$smarty->assign("Breadcrumbs", $breadcrumb_string);
$keywords = "concert tickets, theater tickets, sports tickets, mlb tickets, nba tickets, nfl tickets, football tickets";
$smarty->assign("SeoKeywords", $keywords);

$additional_text_content = "";

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	$smarty->display('main.tpl');


	$topThreeArray = GetMainCategories();
	$categories = array();
	$text_content = "Enjoy the best seats in the house, from the latest concert, theater, or sports events live at your nearest venue.  Choose from ";
	for ($i=0; $i < count($topThreeArray); $i++) {
		$limit = 10;
		$events =  GetTopEventsSubordinateToCategoryID($topThreeArray[$i]['id'],$limit);
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
			$category_name = $topThreeArray[$i]['name'];
			$url_category_name = make_category_url($category_name);
			$category_id = $topThreeArray[$i]['id'];
			$num_events = count($events);
			if($num_events > 0) {
				$events[$num_events-1]['url'] = $url_category_name; 
				$events[$num_events-1]['name'] = "More $category_name Events&hellip;"; 
			}
			$categories[] = array("catname" => "$category_name Tickets", "caturl" => "$url_category_name", "top_event_name" => "$top_event_name", "top_events" => $events, "catimage" => CATEGORY_IMAGES_PATH . $topThreeArray[$i]['catimage']);
			if(strlen($additional_text_content) > 0 ) {
				$additional_text_content .= ", to $top_event_name Tickets";
			}
			else {
				$additional_text_content = "$top_event_name Tickets";
			}
		}
		
	}
	$text_content .= $additional_text_content . " and more. MongoTickets.com carries tickets for all venue and seat locations.";

	$numRelatedCategories = 0;
	for ($i=0; $i < count($topThreeArray); $i++) {
       		$limit = 5;
		if($topThreeArray[$i]['id'] == 3) {
       			$subcategoryArray =  GetHotSportsCategories($limit);
		}
		else {
       			$subcategoryArray =  GetTopSubcategoriesOfCategoryID($topThreeArray[$i]['id'],$limit);
		}

		if(!is_array($subcategoryArray)) {
                        $error_message = get_error_message();
                        $smarty->assign("ErrorMessage", $error_message);
                        $smarty->display('error_page.tpl');
                        $smarty->display('footer.tpl');
                        mysql_close($dbh);
                        exit;
		}
		elseif(count($subcategoryArray) > 0 ) {
			$numRelatedCategories += count($subcategoryArray);
			$relatedCategories = array();
			foreach($subcategoryArray as $subcategory) {
       				$url= make_category_url($subcategory['name']);
				$relatedCategories[] = array("catname" => $subcategory['name'], "caturl" => "$url");
			}
			$catName = $topThreeArray[$i]['name'];
			$allRelatedCategories["$catName"] = $relatedCategories;
		}
	}
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
