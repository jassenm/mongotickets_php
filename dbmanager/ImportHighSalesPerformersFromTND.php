<?php
#
# Copyright (c) 2008, Jassen Moran. All rights reserved.
# MU Marketing, Inc. Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
// require_once('../include/new_urls/ticket_db.php');
// require_once('../include/new_urls/url_factory.tnd.inc.php');
require_once('../include/TicketNetworkWebServices.inc.php');
// require_once('err.php');



echo " Preparing for Import.......";

# $dbh=mysql_connect ($host_name, $db_username, $db_password)
# 			or die ('I cannot connect to the database because: ' . mysql_error());
# mysql_select_db ($db_name);

# $bsql = "DROP TABLE IF EXISTS Venues_t";
# $query_result = mysql_query($bsql) or die ('drop table failed: ' . mysql_error());


# $bsql = "CREATE TABLE Venues_temp (
# 		VenueID INT NOT NULL,
# 		VenueName CHAR(100),
 #               SanitizedVenueName CHAR(100),
#		Address1 CHAR(100),
#		Address2 CHAR(100),
#		City CHAR(100),
 #               SanitizedCity CHAR(100),
#		MarketAreaID INT,
#		RegionCode CHAR(4),
#		SanitizedRegionCode CHAR(4),
#		CountryCode CHAR(4),
#		PostalCode CHAR(10),
#		Phone CHAR(20),
#		PRIMARY KEY (VenueID)
#	)";
#$query_result = mysql_query($bsql) or die ('table create failed: ' . mysql_error());



// create client object
$soapclient = new soapclient($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetHighSalesPerformers';
$soapAction= "http://webservices2.ticketnetwork.com/" . $method;

$param = array( websiteConfigID => "$clientID");

echo "Invoking $method method..... ";

 // make the call
$result = $soapclient->call($method,$param,$namespace, $soapAction);

$num_countries = 0;
echo "Done Calling $soapAction.";
if ($soapclient->getError())
{
	echo "Error for $soapAction: \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n";
	die ('ImportVenuesFromWS.php exiting');
}
else {

echo "Importing data.... ";


// if a fault occurred, output error info
if (isset($fault)) {
	echo "Error: ". $fault;
}
else if ($result) {

print_r($result);
	if (isset($result['faultstring']))
	{
		echo "<h2>Error:</h2>" . $result['faultstring'];
	}
	else {
		$root=$result['ROOT'];
                if(isset($root['MESSAGE'])) {
                     echo $root['MESSAGE'];
                }
		else {

		$data = $root['DATA'];
		if($data != '') {
			$num_venues_returned++;
	 		$row = $data['row'];             
			for($i=0;$i<count($row);$i++)
			{
				print_r($row[$i]);
#
			} # end for

		} # end if no data
	}
}
}
else {
	echo "No result";
}
}
# } # end while
// kill object
unset($soapclient);

# 'Unknown Venue'
# mysql_query("DELETE FROM Venues_t WHERE VenueName LIKE '%Unknown Venue%'");

# mysql_close($dbh);

if ( $num_countries < 1 ) {
        echo "No returned, exiting...";
	die;
}

echo "Done.";

echo "Finished Importing .\n\n\n";



?>
