<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// put full path to Smarty.class.php
require('/usr/local/lib/php/Smarty/Smarty.class.php');
require_once('../lib/nusoap.php');
require_once('../include/ticket_db.php');
require_once('../include/EventInventoryWebServices.inc.php');
require('DbUtils.php');

$eventName = $argv[1];
$productionID= $argv[2];


	$id = $productionID;

$securitytoken = "7DC759790BF840E2878E2EBE76FD6B25";
define("EI_WS_PREVIEW_URL", 'http://services.Preview.EventInventory.com/webservices/TicketSearch.asmx');
$serverpath = EI_WS_PREVIEW_URL;
$namespace= 'http://www.eventinventory.com/webservices/';


$soapclient = new soapclient($serverpath);
$method= 'SearchTickets';
$soapAction= $namespace . $method;

$param = array(  'SecurityToken' => "$securitytoken",  'ProductionID' => "$id", 'MaximumPrice' => '');
// make the call
$result = $soapclient->call($method,$param,$namespace,$soapAction);


// if a fault occurred, output error info
if (isset($fault)) {
	print "Error: ". $fault;
}
else if ($result) {

	if ($result[faultstring])
        {
            print "<h2>Error:</h2>" . $result[faultstring];
        }
        else {
                        $root=$result[ROOT];
                        $data = $root[DATA];
                        if($data != '') {
                                $row = $data[row];             
                                if(is_array($row[0]) == '') {

                                        $seatdescr = mysql_escape_string($row['!SeatDescription']);

					$tid = $row['!TicketID'];
					$avail = $row['!Available'];
					$edate = $row['!EventDate'];
					$sec = $row['!SeatSection'];
					$srow = $row['!SeatRow'];
					$sfrom = $row['!SeatFrom'];
					$sthru = $row['!SeatThru'];
					$tprice = $row['!TicketPrice'];
					$bprice = $row['!BrokerPrice'];
					$bid = $row['!BrokerID'];

        $f_price = (float) $tprice;
        $tprice = number_format($f_price, 2);

if($sec == "503") {
					$tickets[] = array("TicketID" => "$tid",
							"Available" => "$avail",
							"SeatSection" => "$sec",
							"SeatRow" => "$srow",
							"TicketPrice" => "$$tprice"
							);
}


                                }
                                else {
                                        for($i=0;$i<count($row);$i++)
                                        {
                                        $seatdescr = mysql_escape_string($row[$i]['!SeatDescription']);



                                        $tid = $row[$i]['!TicketID'];
                                        $avail = $row[$i]['!Available'];
                                        $edate = $row[$i]['!EventDate'];
                                        $sec = $row[$i]['!SeatSection'];
                                        $srow = $row[$i]['!SeatRow'];
                                        $sfrom = $row[$i]['!SeatFrom'];
                                        $sthru = $row[$i]['!SeatThru'];
                                        $tprice = $row[$i]['!TicketPrice'];
                                        $bprice = $row[$i]['!BrokerPrice'];
                                        $bid = $row[$i]['!BrokerID'];

        $f_price = (float) $tprice;
        $tprice = number_format($f_price, 2);

if($sec == "503") {


                                        $tickets[] = array("TicketID" => "$tid",
                                                        "Available" => "$avail",
                                                        "SeatSection" => "$sec",
                                                        "SeatRow" => "$srow",
                                                        "TicketPrice" => "$$tprice"
                                                        );

}


                                        } # end for
                                }

                        } # end if no data
                }
} # end if result
else {
	print "<h2>No result</h2>";
	
}

// kill object
unset($soapclient);

print_r($tickets);


?>
