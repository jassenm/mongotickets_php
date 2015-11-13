<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
require_once('../include/new_urls/ticket_db.php');
require_once('../include/EventInventoryWebServices.inc.php');
require_once('err.php');
require_once('../include/mail.php');

ini_set("memory_limit","300M");

print_message("Preparing database for Productions Import.......");


$dbh=mysql_connect ($host_name, $db_username, $db_password)
			or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS Productions_temp";
$query_result = mysql_query($bsql) or die ('DROP TABLE IF EXISTS Productions_temp failed: ' . mysql_error());


$bsql = "CREATE TABLE Productions_temp (
		ProductionID INT NOT NULL,
		EventDate DATETIME,
		EventID INT,
		OpponentEventID INT,
		VenueID INT,
		ShortNote CHAR(100),
		MinCost CHAR(16),
		MaxCost CHAR(16),
		PRIMARY KEY (ProductionID)
	) ENGINE MyISAM";

$query_result = mysql_query($bsql) or die ('CREATE TABLE Productions_temp failed: ' . mysql_error());


print_message("Done.");

// create client object
print_message("Getting Productions from $serverpath");

//set soap Action
$method= 'GetAllProductions';
$soapAction= $namespace . $method;


$num_productions_returned = 0;
$soapclient = new SoapClient($serverpath, array("trace" => 1));
$soapclient -> timeout = 1500;
$soapclient -> response_timeout = 1500;
	
$param = array( 'SecurityToken' => "$securitytoken");
print_message("ImportProductionsFromWS: Invoking $method web method..... ");
try {
	$result = $soapclient->GetAllProductions($param);
	print_message("ImportProductionsFromWS: Done Calling $soapAction.");
        $xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", trim($soapclient->__getLastResponse()));

	try {
		libxml_use_internal_errors(true);
                $soap_root = new SimpleXMLElement($xmlString);

                $data= $soap_root->soapBody->GetAllProductionsResponse->GetAllProductionsResult->ROOT->DATA;
		foreach ($data->row as $production) {
			$attrib = $production->attributes();
			$num_productions_returned++;
			$pid = $attrib->ProductionID;
			$edate = tnow_date_to_mysqldate($attrib->EventDate);
			$eid = $attrib->EventID;
			$oeid = $attrib->OpponentEventID;
			$vid = $attrib->VenueID;
			$snote = mysql_escape_string($attrib->ShortNote);
			$mincost = $attrib->MinCost;
			$maxcost = $attrib->MaxCost;

			$bsql = "INSERT INTO Productions_temp " .
				"(ProductionID, EventDate, EventID, OpponentEventID, VenueID, ShortNote, MinCost, MaxCost) " . 
				"VALUES ('$pid', '$edate', '$eid', '$oeid', '$vid', '$snote', '$mincost', '$maxcost')";
   				$insert_result = mysql_query($bsql) or print_message('ImportProductionsFromWS: ' . mysql_error());

		} # end for
	}
	catch (Exception $e) {
                $errors = libxml_get_errors();
                $error = $errors[0];
        }

}
catch (SoapFault $flt)
{
	handle_error_no_exit ("ImportProductionsFromWS.php: ". $flt);
	echo "SOAP Fault: (faultcode: {" . $flt->faultcode . "}\n" .
	"faultstring: {" . $flt->faultstring . " })";
}


unset($soapclient);



print_message("ImportProductionsFromWS: Done. ");

mysql_close($dbh);

if($num_productions_returned < 1) {
        print_message("ImportProductionsFromWS: No productions returned, exiting...");
}


print_message("Finished Importing " . $num_productions_returned . " Productions.\n");


function tnow_date_to_mysqldate($date) {
	$date = str_replace( 'T', ' ', $date );
	return $date;
}



?>
