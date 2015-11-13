<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#

// put full path to Smarty.class.php
require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');



$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);
#$smarty->debugging = true;

$smarty->assign("title", "MongoTickets Sales Policies");
$smarty->assign("MetaDescr", "MongoTickets Sales Policies - Customer Satisfaction is our Goal. Our Sports, Concert, and Theater Tickets are guaranteed.");
$smarty->assign("SeoKeywords", "MongoTickets Sales Policies");



$smarty->display('main.tpl');

$smarty->display('sales_policy.tpl');


$smarty->display('footer.tpl');

?>

