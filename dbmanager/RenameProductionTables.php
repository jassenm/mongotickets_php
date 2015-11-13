<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/new_urls/ticket_db.php');
require_once('err.php');
require_once('../include/mail.php');



$tables = array(
		"Productions"
		);

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('RenameProductionTables: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

foreach($tables as $table) {

	$from_table = $table . "_temp";
	$old_table = $table . "_old";
	print_message("Renaming $table tables....");
	$bsql = "DROP TABLE IF EXISTS $old_table";
	$query_result = mysql_query($bsql) or die ('RenameProductionTables: ' . mysql_error());
	print_message("Dropped table $old_table\n");

	$bsql = "ALTER TABLE $table RENAME $old_table";
	$query_result = mysql_query($bsql) or die ('RenameProductionTables: ' . mysql_error());
	print_message("Renamed $table to $old_table\n");

	$bsql = "ALTER TABLE $from_table RENAME $table";
	$query_result = mysql_query($bsql) or die ('RenameProductionTables: ' . mysql_error());
	print_message("Renamed $from_table to $table\n");
}




 # $bsql = "SELECT EventName, EventDate, VenueName, DATE_FORMAT(EventDate, '%a %M %e, %Y %h:%i %p') FROM Productions_temp LEFT JOIN Events ON (Events.EventID=Productions_temp.EventID) LEFT JOIN Venues ON (Venues.VenueID=Productions_temp.VenueID) WHERE Productions_temp.ProductionID NOT IN (SELECT Productions.ProductionID FROM Productions) ORDER BY EventName";

$newEvents = "";


mysql_close($dbh);

?>
