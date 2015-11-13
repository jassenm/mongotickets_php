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
require_once('../include/url_factory.inc.php');



print_message("Preparing database for Category Import.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('ImportCategoriesFromWS: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS AdjacencyListCategories_temp";
$query_result = mysql_query($bsql) or die ('drop AdjacencyListCategories_temp table failed: ' . mysql_error());


$bsql = "CREATE TABLE AdjacencyListCategories_temp (
             CategoryID INT NOT NULL,
             CategoryName CHAR(100),
             SanitizedCategoryName CHAR(100),
             ParentCategoryID INT,
             CategoryRank SMALLINT,
             PRIMARY KEY (CategoryID)
         )";
$query_result = mysql_query($bsql) or die ('table AdjacencyListCategories_temp create failed: ' . mysql_error());


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
    mysql_close($dbh);
    die;
}
else {
	print_message("ImportCategoriesFromWS: Importing data....");
	// if a fault occurred, output error info
	if (isset($fault)) {
		print_message(" Error: $fault");
		mysql_close($dbh);
		die;
	}
	else if ($result) {

		if (isset($result['faultstring']))
		{
			print_message("Error:" . $result['faultstring']);
			mysql_close($dbh);
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
			for($i=0;$i<count($row);$i++)
			{
				$bsql = "INSERT INTO AdjacencyListCategories_temp (CategoryID, CategoryName, SanitizedCategoryName, ParentCategoryID, CategoryRank)" .
				"VALUES (" . $row[$i]['!CategoryID'] . ",'" . 
 					$row[$i]['!CategoryName'] . "','" . 
		strtolower(_prepare_url_text($row[$i]['!CategoryName'])) . "','" . 
					$row[$i]['!ParentCategoryID'] . "','" . 
					$categoryRank . "');";
   				$query_result = mysql_query($bsql) or die ('INSERT INTO AdjacencyListCategories_temp query failed: ' . mysql_error());

			}

		}
	}
}
	else {
		print_message("No result");
		mysql_close($dbh);
		die;
	}
}
// kill object
unset($soapclient);

print_message("Done.");
print_message("Deleting unused Categories....");
$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryName LIKE '%gift card%'";
$query_result = mysql_query($bsql) or die ('DELETE FROM AdjacencyListCategories_temp WHERE CategoryName LIKE query failed: ' . mysql_error());


$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryName='Other'";
$query_result = mysql_query($bsql) or die ('DELETE FROM AdjacencyListCategories_temp WHERE CategoryName LIKE query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=212";
$query_result = mysql_query($bsql) or die ('DELETE Sugar Bowl FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=213";
$query_result = mysql_query($bsql) or die ('DELETE Rose Bowl FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=214";
$query_result = mysql_query($bsql) or die ('DELETE Orange Bowl FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=215";
$query_result = mysql_query($bsql) or die ('DELETE Fiesta Bowl FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=178";
$query_result = mysql_query($bsql) or die ('DELETE NFL Playoff FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=178";
$query_result = mysql_query($bsql) or die ('DELETE NFL Playoff descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());


$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=179";
$query_result = mysql_query($bsql) or die ('DELETE Super Bowl FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=179";
$query_result = mysql_query($bsql) or die ('DELETE Super Bowl descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=143";
$query_result = mysql_query($bsql) or die ('DELETE MLB Baseball World Series FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=134";
$query_result = mysql_query($bsql) or die ('DELETE MLB Baseball Playoff FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=134";
$query_result = mysql_query($bsql) or die ('DELETE MLB Baseball Playoff descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=135";
$query_result = mysql_query($bsql) or die ('DELETE MLB Baseball Playoff AL descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=139";
$query_result = mysql_query($bsql) or die ('DELETE MLB Baseball Playoff NL descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());


$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=250";
$query_result = mysql_query($bsql) or die ('DELETE NHL Playoff FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=250";
$query_result = mysql_query($bsql) or die ('DELETE NHL Playoff descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());


$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE CategoryID=182";
$query_result = mysql_query($bsql) or die ('DELETE NBA Playoff FROM AdjacencyListCategories_temp query failed: ' . mysql_error());

$bsql = "DELETE FROM AdjacencyListCategories_temp WHERE ParentCategoryID=182";
$query_result = mysql_query($bsql) or die ('DELETE NBA Playoff descendent FROM AdjacencyListCategories_temp query failed: ' . mysql_error());




mysql_close($dbh);

if($num_categories_returned < 1 ) {
	print_message("No categories returned, exiting....");
	die;
}
print_message("Finished Importing Categories from $serverpath");

?>
