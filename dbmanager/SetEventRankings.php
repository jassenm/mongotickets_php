<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


require_once('../include/new_urls/ticket_db.php');



$input_filename = "EventRankings.csv";



if(($fh = fopen($input_filename, "r")) === FALSE){
        die('Failed to open $input_filename file!');
}

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                       or die ('SetEventRankings: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$buffer = fgetcsv($fh, 1000, ",");
while (($buffer = fgetcsv($fh, 1000, ",")) !== FALSE) {
	$eventName = $buffer[0];
	$eventID = $buffer[1];
	$rank = $buffer[2];
	$bsql = "UPDATE Events_temp SET EventRank=$rank where EventID=$eventID;";
	$query_result = mysql_query($bsql) or die ('SetEventRankings: ' . mysql_error());
	$rank++;
}

mysql_close($dbh);
	
print "\nSetEventRankings: Done.\n";


?>
