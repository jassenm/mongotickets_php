<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
require_once('../include/new_urls/ticket_db.php');
require_once('../include/new_urls/url_factory.inc.php');
require_once('../include/EventInventoryWebServices.inc.php');
require_once('err.php');



print_message(" Preparing database for Venue Import.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
			or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS Venues_temp";
$query_result = mysql_query($bsql) or die ('drop table failed: ' . mysql_error());


$bsql = "CREATE TABLE Venues_temp (
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
		CountryCode CHAR(4),
		PostalCode CHAR(10),
		Phone CHAR(25),
		PRIMARY KEY (VenueID)
	) ENGINE MyISAM";
$query_result = mysql_query($bsql) or die ('table create failed: ' . mysql_error());


print_message("Done.");

// create client object
$soapclient = new nusoap_client($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;

##$soapOptions =array("trace" => 1, "encoding"=>'ISO-8859-1');
##$soapclient = new SoapClient($serverpath, $soapOptions);
##$param = array(  "SecurityToken" => "$securitytoken",  "ProductionID" => "$id");
##try {
##	$result = $soapclient->GetAllVenues($param);
##	$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", trim($soapclient->__getLastResponse()));
##	try {
##		libxml_use_internal_errors(true);
##		$soap_root = new SimpleXMLElement($xmlString);
	
##		$data= $soap_root->soapBody->SearchTicketsResponse->SearchTicketsResult->ROOT->DATA;
##		foreach ($data->row as $ticket)
##			{
//set soap Action
$method= 'GetAllVenues';
$soapAction= $namespace . $method;

$param = array( 'SecurityToken' => "$securitytoken");

print_message("Invoking $method method..... ");

 // make the call
$result = $soapclient->call($method,$param,$namespace,$soapAction);

$num_venues_returned = 0;
print_message("Done Calling $soapAction.");
if ($soapclient->getError())
{
	print_message("Error for $soapAction: \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n");
	die ('ImportVenuesFromWS.php exiting');
}
else {

print_message("Importing data.... ");


// if a fault occurred, output error info
if (isset($fault)) {
	print_message("Error: ". $fault);
}
else if ($result) {

	if (isset($result['faultstring']))
	{
		print_message("<h2>Error:</h2>" . $result['faultstring']);
	}
	else {
		$root=$result['ROOT'];
                if(isset($root['MESSAGE'])) {
                     print_message($root['MESSAGE']);
                }
		else {

		$data = $root['DATA'];
		if($data != '') {
			$num_venues_returned++;
	 		$row = $data['row'];             
			for($i=0;$i<count($row);$i++)
			{
				$vid = $row[$i]['!VenueID'];
  				$vname = mysql_escape_string($row[$i]['!VenueName']);
  				$addr1 = mysql_escape_string($row[$i]['!Address1']);
  				$addr2 = mysql_escape_string($row[$i]['!Address2']);
  				$city = mysql_escape_string($row[$i]['!City']);
				$maid = $row[$i]['!MarketAreaID'];
				if($maid == "")
				{
					$maid = 0;
				}
  				$rcode = mysql_escape_string($row[$i]['!RegionCode']);
  				$ccode = mysql_escape_string($row[$i]['!CountryCode']);
  				$pcode = mysql_escape_string($row[$i]['!PostalCode']);
  				$phone = mysql_escape_string($row[$i]['!Phone']);

				$san_vname = strtolower(_prepare_url_text($vname));
				# $san_vname = str_replace( '-at-', '-'  , $san_vname );
				$san_rc = strtolower(_prepare_url_text(utf8_decode($rcode)));
				$san_city = strtolower(_prepare_url_text(utf8_decode($city)));


				$bsql = "INSERT INTO Venues_temp" .
				"(VenueID, VenueName, SanitizedVenueName, Address1, Address2, City, SanitizedCity, MarketAreaID, RegionCode, SanitizedRegionCode, CountryCode, PostalCode, Phone) " . 

				"VALUES ('$vid', '$vname', '$san_vname', '$addr1', '$addr2', '$city', '$san_city', '$maid', '$rcode', '$san_rc', '$ccode', '$pcode', '$phone');";
   				$insert_result = mysql_query($bsql) or die ('query failed: ' . mysql_error() . '\n' . "$vid, $vname, $san_vname, $addr1, $addr2, $city, $san_city, $maid, $rcode, $san_rc, $ccode, $pcode, $phone");

			} # end for

		} # end if no data
	}
}
}
else {
	print_message("No result");
}
}
# } # end while
// kill object
unset($soapclient);

# 'Unknown Venue'
mysql_query("DELETE FROM Venues_temp WHERE VenueName LIKE '%Unknown Venue%'");

$venueID_url_mod_list = Array(
		262,
		784,
		1623,
		1625,
		329,
		410,
		621,
		1457,
		147,
		180,
		716,
		928,
		3986,
		4194,
		4344,
		2890,
		3970,
		3287,
		4714,
		4158,
		2786,
		4759,
		5159,
		5207,
		5580,
		5479,
		5670,
		5522,
		5885,
		1070,
		4056,
		5473,
		6468,
		6544,
		8542,
		8879,
		8921,
		1754,
		3915,
		4382,
		);

foreach($venueID_url_mod_list as $vid) {

	$bsql = "UPDATE Venues_temp SET SanitizedVenueName=(SELECT Concat(SanitizedVenueName,'-event') from (select * from Venues_temp) as x WHERE VenueID=$vid) WHERE VenueID=$vid";

        $query_result = mysql_query($bsql) or die ('UPDATE Venues_temp SET SanitizedVenueName failed: ' . mysql_error());
}


$venueName_reName_list = Array(
                1967 => 'Wisconsin State Fairgrounds',
                1547 => 'Bank of America Theatre',
                1596 => 'Saint Paul Rodeo',
                6162 => 'Monterey Jazz Festival CA',
                5145 => 'Chicago City Limits Venue',
                5273 => 'The Station Venue',
                5806 => 'Oregon Jamboree Venue',
                6216 => 'High Fidelity Venue',
                6687 => 'The Graduate Venue',
                7113 => 'Tribeca Film Festival NY',
                7272 => 'Circus Flora - St. Louis',
                7275 => 'ComedySportz - Chicago'
                );

foreach($venueName_reName_list as $vid => $new_vname) {
        $san_vname = strtolower(_prepare_url_text($new_vname));
        $bsql = "UPDATE Venues_temp SET VenueName='$new_vname',SanitizedVenueName='$san_vname' WHERE VenueID=$vid";
        $query_result = mysql_query($bsql) or die ('UPDATE Venues_temp SET VenueName renme failed: ' . mysql_error());
}


mysql_close($dbh);

if ( $num_venues_returned < 1 ) {
        print_message("No venues returned, exiting...");
	die;
}

print_message("Done.");

print_message("Finished Importing Venues.\n\n\n");



?>
