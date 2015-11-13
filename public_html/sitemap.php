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
require_once('../include/url_factory.inc.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');



$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);

$smarty->display('main.tpl');

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);

	echo "<div id=\"content\">";
	$top_level_categoryid_list = array("Sports" => 3, "Concert" => 2, "Theater" => 4);
	foreach( $top_level_categoryid_list as $categoryName=>$categoryID) {
    		echo "<div class=\"sitemapSection\">" .
             		"<div><a class=\"sitemapHeading\" href=\"$root_url/$categoryName/\">$categoryName</a>" .
             		"</div>";
		$childCategoryIDList = GetAllSubordinatesOfCategory('ModifiedPreorderTreeTraversalCategories', $categoryID);
		foreach ( $childCategoryIDList as $childCategoryIDArray) {
			$url = make_category_url($childCategoryIDArray['name'], $childCategoryIDArray['id']);
			echo "<div class=\"indent_" . $childCategoryIDArray['depth'] . "\">" .
				"<a href=\"$url\">" .
				$childCategoryIDArray['name'] . "</a>" .
				"</div>";
			#$indent++;
		}
		echo "</div>";
	}

    	# content to be closed by footer, echo "</div>";
}

$smarty->display('footer.tpl');

?>

