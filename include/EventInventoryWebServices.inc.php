<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

define("EI_WS_PREVIEW_URL", 'http://services.Preview.EventInventory.com/webservices/TicketSearch.asmx?WSDL');
define("EI_WS_PRODUCTION_URL", 'http://services.eventinventory.com/webservices/ticketsearch.asmx?WSDL');
$namespace= 'http://www.eventinventory.com/webservices/';

#$serverpath = EI_WS_PREVIEW_URL;
#$clientID = 3736;
#$securitytoken = "893EF2BE6BB94C3095A6942CBBA02AA5";
####
#### Don't forget to change ticket_order.php also ######
####

$serverpath = EI_WS_PRODUCTION_URL;
$clientID = 4089;
$securitytoken = "BABFDADDA4EB49808D7D037B4AD39CFC";

?>
