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
require_once('../include/new_urls/ticket_db.php');
require('DbUtils.php');
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

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);


if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);


	$smarty->assign("CategoryName", $categoryName);
	$breadcrumb_str = Breadcrumbs($categoryID, 0);
       	$smarty->assign("Breadcrumbs", $breadcrumb_str);
	switch ($categoryID) {
		case 3:
			$smarty->display('sports.tpl');
		break;
		case 2:
			$smarty->display('concerts.tpl');
		break;
		case 4:
			$smarty->display('theater.tpl');
		break;
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

?>
