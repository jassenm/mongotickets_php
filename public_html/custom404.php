<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
require('../include/breadcrumbs.inc.php');
require('../include/event_paragraph.inc.php');


$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';
$smarty->compile_check = true;

$smarty->assign("RootUrl", $root_url);

$title = "Page Not Found | " . COMPANY_NAME;
$smarty->assign("title", $title);
$descr = COMPANY_NAME . ", the best place to find tickets.";
$smarty->assign("MetaDescr", $descr);

$smarty->display('main.tpl');

$smarty->display('page_not_found.tpl');

$smarty->display('footer.tpl');

?>
