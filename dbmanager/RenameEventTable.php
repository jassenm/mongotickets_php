<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/new_urls/ticket_db.php');
require_once('err.php');
require_once('../include/mail.php');



$tables = array(
		"Events"
		);

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('RenameEventTable: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);


foreach($tables as $table) {


        $bsql = "SELECT COUNT(*) From " . $table . "_temp";
        $query_result = mysql_query($bsql) or die ('RenameEventTable: ' . mysql_error() . ' mysql_query failed, exiting!');
        $num_rows = mysql_num_rows($query_result) or die ('RenameEventTable: ' . mysql_error() . ' mysql_num_rows failed, exiting!');
        if($num_rows != 1) {
                die ('RenameEventTable: SELECT COUNT returned ' . $num_rows . ' rows!!');
        }
        else {
                $table_row = mysql_fetch_row($query_result) or die ('RenameEventTable: ' . mysql_error() . ' mysql_fetch_row failed exiting!');
                $number_of_events = $table_row[0];
                if($number_of_events < 5000) {
                        die ('RenameEventTable: !!!!FAILURE!!!! - SELECT COUNT returned ' . $number_of_events . ' events exiting!!');
                }
                else {
                        echo "\nRenameEventTable: Sanity check on " . $table . "_temp passed, " . $number_of_events . " events returned!!\n";
                }
        }

	$from_table = $table . "_temp";
	$old_table = $table . "_old";
	print_message("RenameEventTable $table tables....");
	$bsql = "DROP TABLE IF EXISTS $old_table";
	$query_result = mysql_query($bsql) or die ('RenameEventTable: ' . mysql_error());
	print_message("Dropped table $old_table");
	$bsql = "ALTER TABLE $table RENAME $old_table";
	$query_result = mysql_query($bsql) or die ('RenameEventTable: ' . mysql_error());
	print_message("Renamed $table to $old_table");

	$bsql = "ALTER TABLE $from_table RENAME $table";
	$query_result = mysql_query($bsql) or die ('RenameEventTable: ' . mysql_error());
	
	print_message("Renamed $from_table to $table");
}

# select distinct Events.EventName from Events where Events.EventId not in (SELECT Events_old.EventId from Events_old);


send_an_email('admin@email.com','Event table updates succeeded!!!!','Event table updates succeeded!!!!');


sleep(5);

$bsql = "SELECT distinct Events.EventName FROM Events WHERE Events.EventId NOT IN (SELECT Events_old.EventId FROM Events_old)";

$eventName = "";
if($query_result = mysql_query($bsql)) {

	while ($table_row = mysql_fetch_row($query_result)) {
		if(strlen($eventName) == 0) {
			$eventName = $table_row[0];
		}
		else
		{
			$eventName = "$eventName\n" . $table_row[0];
		}
			
	}
	send_an_email('admin@email.com','New Events!',"Events $eventName added!");
}
else {
	die ('RenameEventTable: failed in table diff' . mysql_error());
}


mysql_close($dbh);

?>
