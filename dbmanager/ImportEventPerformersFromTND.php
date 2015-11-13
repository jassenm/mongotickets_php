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

$bsql = "DROP TABLE IF EXISTS TNDEventPerformers_temp";
$query_result = mysql_query($bsql) or die ('DROP TABLE IF EXISTS TNDEventPerformers_temp failed: ' . mysql_error());


$bsql = "CREATE TABLE TNDEventPerformers_temp (
                ProductionID INT NOT NULL,
                EventID INT NOT NULL, # performer ID
                PerformerName CHAR(120),
                PRIMARY KEY (EventID, ProductionID)
        )";
 $query_result = mysql_query($bsql) or die ('CREATE TABLE TNDEventPerformers_temp failed ImportEventPerformersFromTND: ' . mysql_error());


// create client object
$soapclient = new soapclient($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetEventPerformers';
 $soapAction= TND_WS_NAMESPACE . "/" . $method;


# TNServicesStringInputs.asmx?WSDL
# TNServices.asmx?WSDL WorkS!!!!!
 $param = array( websiteConfigID => $clientID);


echo "Invoking $method method..... ";

$soapclient -> debug = 1;
 // make the call
$result = $soapclient->call($method,$param,$namespace, $soapAction);

# echo "$soapAction: \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n";
echo "Done Calling $soapAction.";
if ($soapclient->getError() || $soapclient->fault)
{
	echo "Error for $soapAction: \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n";
	die ('ImportEventPerformersFromTND.php exiting');
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

		$data = $result['EventPerformers'];
		if($data != '') {
			for($i=0;$i<count($data);$i++)
			{
				$num_events++ ;
				# print_r($data[$i]);
                                $pid = $data[$i]['EventID'];
				$eid = $data[$i]['PerformerID'];
                                $pname = $data[$i]['PerformerName'];


                                $bsql = "INSERT INTO TNDEventPerformers_temp " .
                                "(ProductionID, EventID, PerformerName) " .
                                "VALUES ('$pid', '$eid', '$pname')";
				# echo "\n" . $bsql;
                                 $insert_result = mysql_query($bsql) or print_message('ImportEventPerformersFromTND: ' . mysql_error());

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


?>
