<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/new_urls/ticket_db.php');
require_once('err.php');
require_once('../include/mail.php');




$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('RenameEventTable: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);



$bsql = "SELECT distinct Events.EventName FROM Events WHERE Events.EventId NOT IN (SELECT Events_old.EventId FROM Events_old)";

$eventName = "aba ";
if($query_result = mysql_query($bsql)) {

	while ($table_row = mysql_fetch_row($query_result)) {
		if(strlen($eventName) == 0) {
			$eventName = $table_row[0];
		}
		else
		{
			$eventName = "$eventName\n, " . $table_row[0];
		}
			
	}
	send_an_email('admin@email.com','New Events!',"Events $eventName added!");
}
else {
	die ('RenameEventTable: failed in table diff' . mysql_error());
}


mysql_close($dbh);

?>
