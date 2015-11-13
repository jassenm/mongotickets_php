<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../../include/smarty_package.php');
require_once('../../lib/php/Smarty/Smarty.class.php');
include_once('../../include/host_info.inc.php');
include_once('../../include/domain_info.inc.php');
require_once('../../include/new_urls/ticket_db.php');
require_once('DbUtils.php');
require_once('Utils.php');
include_once('../../include/error.php');
require_once('../../include/new_urls/breadcrumbs.inc.php');
require_once('../../include/event_paragraph.inc.php');


$smarty = new Smarty;

$smarty->template_dir = '../../smarty/templates/new_urls/';
$smarty->compile_dir = '../../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../../smarty/cache/new_urls/';
$smarty->config_dir = '../../smarty/configs';
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
