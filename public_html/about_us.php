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
#$smarty->debugging = true;

$smarty->assign("RootUrl", $root_url);

$smarty->assign("title", "About MongoTickets");
$smarty->assign("MetaDescr", "About MongoTickets - We securely sell premium Sports, Concert, and Theater tickets with a guarantee");
$smarty->assign("SeoKeywords", "About MongoTickets");

$smarty->display('main.tpl');

$smarty->assign("DomainName", DOMAIN_NAME);
$smarty->display('about_us.tpl');

$smarty->display('footer.tpl');




?>

