<?php
#
# Copyright (c) 2008, Jassen Moran. All rights reserved.
# MU Marketing, Inc. Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
// require_once('../include/new_urls/url_factory.tnd.inc.php');
require_once('../include/TicketNetworkWebServices.inc.php');
// require_once('err.php');



echo " Preparing for Import.......";


// create client object
$soapclient = new soapclient($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetTickets';
$soapAction= TND_WS_NAMESPACE . "/" . $method;

# TNServices.asmx?WSDL WorkS!!!!!
 $param = array( websiteConfigID => $clientID, numberOfRecords => null, eventID => 721113,  lowPrice => null, highPrice => null,  ticketGroupID => null, requestedSplit => null,  sortDescending => null );


echo "Invoking $method method..... ";

$soapclient -> debug = 1;
 // make the call
$result = $soapclient->call($method,$param,$namespace, $soapAction);

 # echo "$soapAction: \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n";
echo "Done Calling $soapAction.";
if ($soapclient->getError() || $soapclient->fault)
{
	echo "Error for $soapAction: \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n";
	die ('ImportEventsFromTND.php exiting');
}
else {

echo "Importing data.... ";


// if a fault occurred, output error info
$num_tickets = 0;
if (isset($fault)) {
	echo "Error: ". $fault;
}
else if ($result) {

	if (isset($result['faultstring']))
	{
		echo "<h2>Error:</h2>" . $result['faultstring'];
	}
	else {

		 $data = $result['TicketGroup'];
		if($data != '') {
				# print_r($data[$i]);
			# for($i=0;$i<1;$i++)
			 for($i=0;$i<count($data);$i++)
                        {
				 print_r($data[$i]);
				echo "\nTicket Group ID " . $data[$i][ID] . " Quant. " . 
					$data[$i][TicketQuantity] . " Sec. " .
					$data[$i][Section] . " Row " . $data[$i][Row] . " Pr." . $data[$i][ActualPrice] . " " . $data[$i][Notes];
#    [ID] => 343181254
#    [TicketQuantity] => 50
#    [Section] => 520
#    [Row] => 6
#    [LowSeat] => 3
#    [HighSeat] => 10
#    [FacePrice] => 15.50
#    [WholesalePrice] => 90.00
#    [RetailPrice] => 90.00
#    [ActualPrice] => 95.00
#    [Marked] => true
#    [Notes] => Upper reserved 1st base side seating. Will Ship By 4/1/2008
#    [EventID] => 721113
#    [ValidSplits] => Array
#        (
#            [int] => Array
#                (
#                    [0] => 50
#                    [1] => 48
#                    [2] => 47
#                )
#        )


			}

		} # end if no data
	}
}
else {
	echo "No result";
}
}
# } # end while
// kill object
unset($soapclient);


if ( $num_tickets < 1 ) {
        echo "No events returned, exiting...";
	die;
}

echo "Done.";

echo "Finished Importing .\n\n\n";





?>
