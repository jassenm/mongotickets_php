<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#
require_once('../include/ticket_db.php');
require_once('../include/url_factory.inc.php');


$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$date="";
   // retrieve the left and right value of the $root node
   $bsql = 'SELECT Events.EventID, EventName, COUNT(Productions.EventID) as num_prods from Events  LEFT JOIN Productions ON (Events.EventID = Productions.EventID) WHERE 1 GROUP BY Events.EventID HAVING num_prods > 0 ORDER BY num_prods DESC';
   $result = mysql_query($bsql) or ('Error: query failed' . mysql_error());;
   $data .= "EventName,Url\n";
   while($row = mysql_fetch_row($result)) {
    	$line = '';
        $eventID = $row[0];
        $eventName = $row[1];
        $count = $row[2];

	
	$url = "http://www.mongotickets.com" . make_event_url($eventName, $eventID);
        $line .= "\"$eventName\",$url";
    	$data .= trim($line)."\n";
    }
$data = str_replace("\r","",$data);

mysql_close($dbh);

header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=active_events.csv");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";


?>
