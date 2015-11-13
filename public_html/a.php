<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/TicketNetworkWebServices.inc.php');

require_once('../include/smarty_package.php');
require_once('../lib/php/Smarty/Smarty.class.php');
require_once('../lib/nusoap.php');
include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');
require_once('../include/new_urls/breadcrumbs.inc.php');
require_once('../include/new_urls/url_factory.inc.php');

$soapclient = new soapclientNusoap($serverpath);
$soapclient->debug = 1;
$method= 'GetTickets';
$soapAction= TND_WS_NAMESPACE . "/" . $method;

$parameters = array( "websiteConfigID" => 4589, numberOfRecords => null, eventID => $id,  lowPrice => null, highPrice => null,  ticketGroupID => null, requestedTixSplit => null, whereClause => null, orderByClause => 'ActualPrice');

	// make the call

#$proxy = $soapclient->getProxy();
#$stockprice = $proxy->GetTickets('ABC'); 
	#	
$id = 1098259;
	$result = $soapclient->call($method,
array( websiteConfigID => 4589, numberOfRecords => null, eventID => $id,  lowPrice => null, highPrice => null,  ticketGroupID => null, requestedTixSplit => null, orderByClause => 'ActualPrice'),$namespace,$soapAction);
	echo '<h2>Debug</h2>';
	echo '<pre>' . htmlspecialchars($soapclient->debug_str, ENT_QUOTES) . '</pre>';


#echo '<h2>Request</h2>';
 # echo '<pre>' . htmlspecialchars($soapclient->request, ENT_QUOTES) . '</pre>';
 # echo '<h2>Response</h2>';
# echo '<pre>' . htmlspecialchars($soapclient->response, ENT_QUOTES) . '</pre>';

	

?>
