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

?>

<div class="policy">
   
<table style="margin:0; padding:0; border:solid 0px black; border-collapse:collapse;">
	<tr>
		<td>
			<table>
				<tr><td><h2>MongoTickets Sales Policies </h2></td></tr>
				<tr><td valign="top" class="featured" height="200" style="padding-left:10px; padding-right:10px; "> <h2>Our Guarantee</h2>
				<ul>
					<li>All tickets are guaranteed to be 100% authentic, this is something that fan-to-fan and internet auction sites cannot provide</li>
					<li>Speedy order processing and professional ticket delivery - you will always be on time for the event!</li>
					<li>All transactions are processed on our secure server with full 128-bit encryption. This means all of your personal information, including your credit card number, cannot be read as the information travels over the internet.</li>
					<li>We guarantee a 100% refund for any event that is cancelled and not rescheduled</li>
					<li>After receiving an order confirmation from us, if for some reason the tickets you selected were not available, we will substitute comparable or better seats or we will refund your money. We guarantee it!</li>
</ul>
				</td></tr> 
			</table>
		</td>
	</tr>
</table>
			                 
			

<?php

$smarty->display('footer.tpl');

?>

