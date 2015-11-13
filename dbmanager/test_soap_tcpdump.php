<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once("../lib/nusoap.php");
require_once("../include/ticket_db.php");
require_once("../include/EventInventoryWebServices.inc.php");
require_once("err.php");


print_message("Preparing database for Category Import.......");

print_message("Done.");
// create client object
$soapclient = new soapclient($serverpath);
$soapclient -> timeout = 500;
$soapclient -> response_timeout = 500;


//set soap Action
$method= 'GetAllCategories';
$soapAction= $namespace . $method;

$param = array( 'SecurityToken' => "$securitytoken");

print_message(" Invoking $method web method..... ");
 // make the call
$result = $soapclient->call($method,$param,$namespace,$soapAction);

$num_categories_returned = 0;
print_message("Done calling $soapAction");
 if ($soapclient->getError())
{
    print_message("ImportCategoriesFromWS: Error for calling  $soapAction \nSTART_GET_ERROR\n" . $soapclient->getError() . "\nEND_GET_ERROR\nSTART_DEBUG_STR\n" . $soapclient->debug_str . "\nEND_DEBUG_STR\n");
}
else {
	print_message("ImportCategoriesFromWS: Importing data....");
	// if a fault occurred, output error info
	if (isset($fault)) {
		print_message(" Error: $fault");
		die;
	}
	else if ($result) {

		if (isset($result['faultstring']))
		{
			print_message("Error:" . $result['faultstring']);
			die;
		}
		else
		{
			$categoryRank = 2000;

			$root=$result['ROOT'];
                	if(isset($root['MESSAGE'])) {
                     		print_message($root['MESSAGE']);
                	}
                	else {

			$num_categories_returned++;
			$data = $root['DATA'];
			$row = $data['row'];             
			}

		}
	}
}
// kill object
unset($soapclient);

print_message("Done.");
print_message("Deleting unused Categories....");

if($num_categories_returned < 1 ) {
	print_message("No categories returned, exiting....");
	die;
}
print_message("Finished Importing Categories from $serverpath");

?>
