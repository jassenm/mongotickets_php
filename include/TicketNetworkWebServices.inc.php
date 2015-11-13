<?php
#
# Copyright (c) 2008, Jassen Moran. All rights reserved.
# MU Marketing Confidential Proprietary.
#

#define("TND_WS_PREVIEW_URL", 'http://tnwebservices.ticketnetwork.com/TNWebservice/v3.0/TNWebserviceStringInputs.asmx?WSDL');
define("TND_WS_PREVIEW_URL", 'http://tnwebservices.ticketnetwork.com/TNWebservice/v3.0/TNWebservice.asmx?WSDL');
#'http://tnwebservices-test.ticketnetwork.com/TNWebservice/v3.0/TNWebservice.asmx?WSDL');
# http://tnwebservices.ticketnetwork.com/tnwebservice/v3.0/TNWebServicestringinputs.asmx
# works define("TND_WS_PREVIEW_URL", 'http://tnwebservices-test.ticketnetwork.com/TNWebservice/v3.0/TNWebservice.asmx?WSDL');
define("TND_WS_PRODUCTION_URL", 'http://.asmx');
define("TND_WS_NAMESPACE", 'http://tnwebservices.ticketnetwork.com/tnwebservice/v3.0');
$namespace= TND_WS_NAMESPACE;

$serverpath = TND_WS_PREVIEW_URL;
$clientID = 4589;
//$securitytoken = "BABFDADDA4EB49808D7D037B4AD39CFC";

define("BROKER_ID", 3195);
define("SITE_NUMBER", 0);
?>
