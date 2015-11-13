<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

// include the SOAP classes
require_once('../lib/nusoap.php');
require_once('../include/ticket_db.php');
require_once('err.php');



print_message("Creating Hot Sports Category Candidate Table.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateHotCategoryCandidateList: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS HotSportsCategoryCandidates_temp";
$query_result = mysql_query($bsql) or die ('CreateHotCategoryCandidateList: ' . mysql_error());


$bsql = "CREATE TABLE HotSportsCategoryCandidates_temp (
             CategoryID INT NOT NULL,
             PRIMARY KEY (CategoryID)
         )";
$query_result = mysql_query($bsql) or die ('CreateHotCategoryCandidateList: ' . mysql_error());


print_message("Done.");

$categoryIDList = array(945, 1516, 1505, 1500, 1999, 134, 143, 14, 82, 182, 13, 24, 178, 179, 177, 8, 91, 250, 251, 10, 11, 12, 253, 255);
foreach($categoryIDList as $categoryID) {
	$bsql = "INSERT INTO HotSportsCategoryCandidates_temp (CategoryID) " .
		"VALUES (" . $categoryID . ");";
	$query_result = mysql_query($bsql) or die ('CreateHotCategoryCandidateList: ' . mysql_error());

}

mysql_close($dbh);
print_message("Finished Creating Hot Sports Category Table");

?>
