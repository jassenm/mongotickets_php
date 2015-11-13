<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../lib/nusoap.php');
require_once('../include/new_urls/ticket_db.php');
require_once('../include/EventInventoryWebServices.inc.php');
require_once('err.php');
require_once('../include/mail.php');
require_once('../include/new_urls/url_factory.inc.php');



print_message("Preparing database for Event import....... ");


$dbh=mysql_connect ($host_name, $db_username, $db_password)
			or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS Events_temp";
$query_result = mysql_query($bsql) or die ('drop table failed: ' . mysql_error());


$bsql = "CREATE TABLE Events_temp (
		EventID INT NOT NULL,
		EventName CHAR(100),
		EventTypeID CHAR(100),
                SanitizedEventName CHAR(100),
		CategoryID INT,
		EventRank SMALLINT NOT NULL,
		PRIMARY KEY (EventID)
	) ENGINE MyISAM";
$query_result = mysql_query($bsql) or die ('CREATE TABLE Events_temp failed: ' . mysql_error());



print_message("Done.");

// create client object
$soapclient = new SoapClient($serverpath, array("trace" => 1));

$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetAllEvents';
$soapAction= $namespace . $method;

$num_events_returned = 0;
$eventRank = 2000;
$method = 'GetAllEvents';
$param = array( 'SecurityToken' => "$securitytoken");
print_message("Invoking $method web method.....");

# 6/29 Jassene commented out was taking too long
#$result = $soapclient->call($method,$param,$namespace,$soapAction);
try {
	$result = $soapclient->GetAllEvents($param);
	print_message("Done Calling $soapAction.");
	$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", trim($soapclient->__getLastResponse()));

	try{
		libxml_use_internal_errors(true);
		$soap_root = new SimpleXMLElement($xmlString);

		$data= $soap_root->soapBody->GetAllEventsResponse->GetAllEventsResult->ROOT->DATA;

		foreach ($data->row as $event) {
			$attrib = $event->attributes();
			$num_events_returned++;
			$eid = $attrib->EventID;
  			$raw_ename = $attrib->EventName;
  			$ename = mysql_escape_string($attrib->EventName);
 			$etypeid = $attrib->EventTypeID;
	 		$cid = $attrib->CategoryID;
   			InsertIntoEvents_temp($eid, $ename, $raw_ename, $etypeid, $cid, $eventRank);
		}
	}
	catch (Exception $e) {
		$errors = libxml_get_errors();
		$error = $errors[0];
	}
}
catch (SoapFault $flt)
{
                handle_error_no_exit ("ImportEventsFromWS.php: ". $flt);
echo "SOAP Fault: (faultcode: {" . $flt->faultcode . "}\n" .
"faultstring: {" . $flt->faultstring . " })";
}

// kill object
unset($soapclient);

$bsql = "DELETE FROM Events_temp WHERE EventID=1";
$query_result = mysql_query($bsql) or die ('DELETE EventID=1 FROM Events_temp query failed: ' . mysql_error());

$eventName_mod_list = Array(
                14255 => 'Ourglass Concert',
                8405 => 'Strunz n Farah',
                14340 => 'The Jena Six Empowerment Concert',
		15874 => 'Revolution Dance',
		18251 => 'BamaJam Music n Arts Festival',
		19734 => 'Rebel Concert',
		24620 => 'Mr Brown and Cora Comedy',
                );

foreach($eventName_mod_list as $eid => $ename) {
	$san_ename = strtolower(_prepare_url_text($ename));
        $bsql = "UPDATE Events_temp SET EventName='$ename',SanitizedEventName='$san_ename' WHERE EventID=$eid";
        $query_result = mysql_query($bsql) or die ('UPDATE Events_temp SET EventName failed: ' . mysql_error());
}



mysql_close($dbh);

if($num_events_returned < 1) {
	send_an_email('admin@email.com','No result!!!!','No result!!!!' . $$root['MESSAGE']);
	print_message("No events returned, exiting...");
}
print_message("Done.");
print_message("$num_events_returned Events returned from Web Services");


print_message("Done.");
print_message("ImportEventsFromWS complete\n");



function InsertIntoEvents_temp($eid, $ename, $raw_ename, $etypeid, $cid, $eventRank)
{
	$san_ename = strtolower(_prepare_url_text($ename));
        # $san_ename = str_replace( '-at-', '-'  , $san_ename);
 
	$san_raw_ename = strtolower(_prepare_url_text($raw_ename));
	if((strlen($san_raw_ename) < 1) || (strlen($raw_ename) < 1) || ($raw_ename == '!!!')) {
		#skip
	}
	else {
	$bsql = "INSERT INTO Events_temp " .
		"(EventID,EventName,EventTypeID,SanitizedEventName,CategoryID,EventRank) " .
		"VALUES ('$eid', '$ename', '$etypeid', '$san_ename', '$cid', '$eventRank');";
	$insert_result = mysql_query($bsql) or die ('InsertIntoEvents_temp: query failed: ' . mysql_error() . '\n' . "$eid, $ename, $etypeid, $cid, $eventRank");

	}

}


?>
