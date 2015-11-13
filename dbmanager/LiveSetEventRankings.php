<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


require_once('../include/ticket_db.php');
require_once('../public_html/DbUtils.php');



$input_filename = "EventRankings.csv";



if(($fh = fopen($input_filename, "r")) === FALSE){
        die('Failed to open $input_filename file!');
}

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                       or die ('LiveSetEventRankings: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$buffer = fgetcsv($fh, 1000, ",");
while (($buffer = fgetcsv($fh, 1000, ",")) !== FALSE) {
	$eventName = $buffer[0];
	$eventID = $buffer[1];
	$rank = $buffer[2];
	$bsql = "UPDATE Events SET EventRank=$rank WHERE EventID=$eventID;";
	$query_result = mysql_query($bsql) or die ('LiveSetEventRankings: ' . "$eventName $eventID $rank" . mysql_error());
}

mysql_close($dbh);
	
print "\nLiveSetEventRankings: Done.\n";


?>
