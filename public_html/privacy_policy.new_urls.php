<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#

// put full path to Smarty.class.php
require_once('../include/smarty_package.php');
require_once('../lib/php/Smarty/Smarty.class.php');
include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');



$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);
#$smarty->debugging = true;

$smarty->assign("title", "MongoTickets.com Privacy Policy");
$smarty->assign("MetaDescr", "MongoTickets.com Privacy Policy - Keeping Information Safe and Secure is our Goal.");
$smarty->assign("SeoKeywords", "MongoTickets.com Privacy Policy");



$smarty->display('main.tpl');

$smarty->display('privacy_policy.tpl');


$smarty->display('footer.tpl');

?>

