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
require_once('../include/new_urls/url_factory.inc.php');
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

$smarty->display('main.tpl');

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);

	echo '<div id="content">';
	echo '<div class="left_bar">';
	$top_level_categoryid_list = array("Sports" => 3, "Concert" => 2, "Theater" => 4);
	foreach( $top_level_categoryid_list as $categoryName=>$categoryID) {
    		echo "<div class=\"sitemapSection\">" .
             		"<div><a class=\"sitemapHeading\" href=\"$root_url/" . strtolower($categoryName) . "-tickets/\">$categoryName</a>" .
             		"</div>";
		$childCategoryIDList = GetAllSubordinatesOfCategory('ModifiedPreorderTreeTraversalCategories', $categoryID);
		foreach ( $childCategoryIDList as $childCategoryIDArray) {
			$url = make_category_url($childCategoryIDArray['name']);
			echo "<div class=\"indent_" . $childCategoryIDArray['depth'] . "\">" .
				"<a href=\"$url\">" .
				$childCategoryIDArray['name'] . "</a>" .
				"</div>";
			#$indent++;
		}
		echo "</div>";
	}
	echo '</div> <!-- end left_bar -->';

	$smarty->display('right_bar.tpl');
	$smarty->display('left_column.tpl');
    	# content to be closed by footer, echo "</div>";
}

$smarty->display('footer.tpl');

?>

