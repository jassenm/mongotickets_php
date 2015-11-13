<?php
#
# Copyright (c) 2008, Jassen Moran. All rights reserved.
# MU Marketing, Inc. Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
require_once('../include/new_urls/ticket_db.php');
// require_once('../include/new_urls/url_factory.tnd.inc.php');
require_once('../include/TicketNetworkWebServices.inc.php');
// require_once('err.php');



echo " Preparing for Import.......";

$dbh=mysql_connect ($host_name, $db_username, $db_password) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS TNDEvents_temp";
$query_result = mysql_query($bsql) or die ('DROP TABLE IF EXISTS TNDEvents_temp failed: ' . mysql_error());


$bsql = "CREATE TABLE TNDEvents_temp (
                ProductionID INT NOT NULL,
                EventDate DATETIME,
                EventID INT, # performer ID
                OpponentEventID INT, # opp performer ID
                ProductionName CHAR(200), 
                VenueID INT,
                ShortNote CHAR(100),
                MinCost CHAR(16),
                MaxCost CHAR(16),
                PRIMARY KEY (ProductionID)
        )";
 $query_result = mysql_query($bsql) or die ('CREATE TABLE TNDEvents_temp failed: ' . mysql_error());


// create client object
$soapclient = new soapclient($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetEvents';
 $soapAction= TND_WS_NAMESPACE . "/" . $method;
# $soapAction= "http://webservices2.ticketnetwork.com/" . $method;

# TNServices.asmx?WSDL WorkS!!!!!
# $param = array( websiteConfigID => $clientID, numberOfEvents => null, eventID => null,  eventDate => null, beginDate => null,  endDate => null, venueID => null,  stateProvinceID => null, parentCategoryID => 1, childCategoryID => 63, grandchildCategoryID => 16, performerID => null, noPerformers => null, lowPrice => null, highPrice => null,  sortDescending => null, modificationDate => null, onlyMine => null );
 $param = array( websiteConfigID => $clientID, numberOfEvents => null, eventID => null,  eventDate => null, beginDate => null,  endDate => null, venueID => null,  stateProvinceID => null, parentCategoryID => null, childCategoryID => null, grandchildCategoryID => null, performerID => 154, noPerformers => null, lowPrice => null, highPrice => null,  sortDescending => null, modificationDate => null, onlyMine => null );


echo "Invoking $soapAction method..... ";

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
$num_events = 0;
if (isset($fault)) {
	echo "Error: ". $fault;
}
else if ($result) {

 # print_r($result);
	if (isset($result['faultstring']))
	{
		echo "<h2>Error:</h2>" . $result['faultstring'];
	}
	else {

		$data = $result['Event'];
		if($data != '') {
			for($i=0;$i<count($data);$i++)
			{
				$num_events++ ;
				# print_r($data[$i]);
                                $pid = $data[$i]['ID'];
				$edate = ws_date_to_mysqldate($data[$i]['Date']);
				$eid = 154; # performer id
                                $oeid = 667; # ?????
                                $vid = $data[$i]['VenueID'];
				$snote = '';
				$mincost = 0;
				$maxcost = 100;
				$prod_name = $data[$i]['Name'];


                                $bsql = "INSERT INTO TNDEvents_temp " .
                                "(ProductionID, EventDate, EventID, OpponentEventID, ProductionName, VenueID, ShortNote, MinCost, MaxCost) " .
                                "VALUES ('$pid', '$edate', '$eid', '$oeid', '$prod_name', '$vid', '$snote', '$mincost', '$maxcost')";
				# echo "\n" . $bsql;
                                 $insert_result = mysql_query($bsql) or print_message('ImportEventsFromTND: ' . mysql_error());

#
			} # end for

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



mysql_close($dbh);

if ( $num_events < 1 ) {
        echo "No events returned, exiting...";
	die;
}

echo "Done.";

echo "Finished Importing .\n\n\n";



function ws_date_to_mysqldate($date) {
        $date = str_replace( 'T', ' ', $date );
        return $date;
}


?>
