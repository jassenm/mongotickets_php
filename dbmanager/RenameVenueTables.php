<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/new_urls/ticket_db.php');
require_once('err.php');


$tables = array(
		"Venues"
		);

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('RenameVenueTables: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

foreach($tables as $table) {

        $bsql = "SELECT COUNT(*) From " . $table . "_temp";
        $query_result = mysql_query($bsql) or die ('RenameVenueTables: ' . mysql_error() . ' mysql_query failed, exiting!');
        $num_rows = mysql_num_rows($query_result) or die ('RenameVenueTables: ' . mysql_error() . ' mysql_num_rows failed, exiting!');
        if($num_rows != 1) {
                die ('RenameVenueTables: SELECT COUNT returned ' . $num_rows . ' rows!!');
        }
        else {
		$table_row = mysql_fetch_row($query_result) or die ('RenameVenueTables: ' . mysql_error() . ' mysql_fetch_row failed exiting!');
		$number_of_venues = $table_row[0];
		if($number_of_venues < 1000) {
                	die ('RenameVenueTables: !!!FAILURE!!!! SELECT COUNT returned ' . $number_of_venues . ' venues exiting!!');
		}
		else {
                	echo "\nRenameVenueTables: Sanity check on " . $table . "_temp passed, " . $number_of_venues . " venues returned!!\n";
		}
        }

	$from_table = $table . "_temp";
	print_message("Renaming $table tables....");
	$bsql = "DROP TABLE IF EXISTS $table";
	$query_result = mysql_query($bsql) or die ('RenameVenueTables: ' . mysql_error());

	$bsql = "ALTER TABLE $from_table RENAME $table";
	$query_result = mysql_query($bsql) or die ('RenameVenueTables: ' . mysql_error());
	
	print_message("Renamed $from_table to $table");
}

mysql_close($dbh);

?>
