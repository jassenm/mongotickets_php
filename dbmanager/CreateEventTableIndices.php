<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


require_once('../include/new_urls/ticket_db.php');

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateEventTableIndices: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

echo "\nCreateEventTableIndices: Creating Events indices.......";
$bsql = "CREATE FULLTEXT INDEX events_1 ON Events_temp(EventName)";
$query_result = mysql_query($bsql) or die ("$bsql failed: " . mysql_error());

$bsql = "CREATE FULLTEXT INDEX events_4 ON Events_temp(SanitizedEventName)";
$query_result = mysql_query($bsql) or die ("$bsql failed: " . mysql_error());



$bsql = "CREATE INDEX events_3 ON Events_temp(CategoryID)";
$query_result = mysql_query($bsql) or die ("$bsql failed: " . mysql_error());
echo "Done.";



mysql_close($dbh);

?>
