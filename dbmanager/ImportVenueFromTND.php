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
require_once('../include/new_urls/url_factory.inc.php');




echo " Preparing for Import.......";

$dbh=mysql_connect ($host_name, $db_username, $db_password) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS TNDVenues_temp";
$query_result = mysql_query($bsql) or die ('DROP TABLE IF EXISTS TNDVenues_temp failed: ' . mysql_error());


$bsql = "CREATE TABLE TNDVenues_temp (
                VenueID INT NOT NULL,
                VenueName CHAR(100),
                SanitizedVenueName CHAR(100),
                Address1 CHAR(100),
                Address2 CHAR(100),
                City CHAR(100),
                SanitizedCity CHAR(100),
                MarketAreaID INT,
                RegionCode CHAR(4),
                SanitizedRegionCode CHAR(4),
                CountryCode CHAR(40),
                PostalCode CHAR(10),
                Phone CHAR(20),
                PRIMARY KEY (VenueID)
        )";
 $query_result = mysql_query($bsql) or die ('CREATE TABLE TNDVenues_temp failed: ' . mysql_error());


// create client object
$soapclient = new soapclient($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetVenue';
$soapAction= TND_WS_NAMESPACE . "/" . $method;

# TNServicesStringInputs.asmx?WSDL
# TNServices.asmx?WSDL WorkS!!!!!
 $param = array( websiteConfigID => $clientID, venueID => 21);


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
$num_venues = 0;
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

		$data = $result['Venue'];
		if($data != '') {
			# for($i=0;$i<count($data);$i++)
			# {
				$num_venues++ ;
				$vid = $data['ID'];
                                $vname = mysql_escape_string($data['Name']);
                                $addr1 = mysql_escape_string($data['Street1']);
                                $addr2 = mysql_escape_string($data['Street2']);
                                $city = mysql_escape_string($data['City']);
                                $maid = 1;
                                $rcode = mysql_escape_string($data['ZipCode']);
                                $ccode = mysql_escape_string($data['Country']);
                                $pcode = mysql_escape_string($data['ZipCode']);
                                $phone = mysql_escape_string($data['BoxOfficePhone']);

                                $san_vname = strtolower(_prepare_url_text($vname));
                                # $san_vname = str_replace( '-at-', '-'  , $san_vname );
                                $san_rc = strtolower(_prepare_url_text(utf8_decode($rcode)));
                                $san_city = strtolower(_prepare_url_text(utf8_decode($city)));


                                $bsql = "INSERT INTO TNDVenues_temp" .
                                "(VenueID, VenueName, SanitizedVenueName, Address1, Address2, City, SanitizedCity, MarketAreaID, RegionCode, SanitizedRegionCode, CountryCode, PostalCode, Phone) " .

                                "VALUES ('$vid', '$vname', '$san_vname', '$addr1', '$addr2', '$city', '$san_city', '$maid', '$rcode', '$san_rc', '$ccode', '$pcode', '$phone');";
                                $insert_result = mysql_query($bsql) or die ('query failed: ' . mysql_error() . '\n' . "$vid, $vname, $san_vname, $addr1, $addr2, $city, $san_city, $maid, $rcode, $san_rc, $ccode, $pcode, $phone");

#
			# } # end for

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

if ( $num_venues < 1 ) {
        echo "No venues returned, exiting...";
	die;
}

echo "Done.";

echo "Finished Importing .\n\n\n";
?>
