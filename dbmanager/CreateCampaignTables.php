<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/ticket_db.php');
require_once('../public_html/Utils.php');
require_once('err.php');

print_message("Creating Campaign Tables.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS EventCampaigns";
$query_result = mysql_query($bsql) or die (mysql_error());

$bsql = "CREATE TABLE EventCampaigns (
                ID INT NOT NULL,
		CampaignStatus ENUM('NO TERMS', 'ON', 'OFF'),
		WebSiteID INT,
		SearchEngineID INT,
		PRIMARY KEY (ID)
         )";
$query2_result = mysql_query($bsql) or die (mysql_error());

print_message("Done.");


$bsql = "DROP TABLE IF EXISTS VenueCampaigns";
$query_result = mysql_query($bsql) or die (mysql_error());

$bsql = "CREATE TABLE VenueCampaigns (
                ID INT NOT NULL,
                CampaignStatus ENUM('NO TERMS', 'ON', 'OFF'),
                PRIMARY KEY (ID)
         )";
$query2_result = mysql_query($bsql) or die (mysql_error());

print_message("Done.");


mysql_close($dbh);
print_message("Finished Creating VenueCampaigns Table");

?>
