<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../../include/ticket_db.php');
require_once('../../include/host_info.inc.php');
include('../../include/error.php');
include('../../include/url_factory.inc.php');


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ("mongo_tickets2");

echo 'here';
	$query = "SELECT EventName,EventID FROM Events WHERE EventID=107";
	if($query_result = mysql_query($query) ) {

		while ($table_row = mysql_fetch_row($query_result)) {
			$eventName = $table_row[0];
			$eventID = $table_row[1];


		$sanitized_eventName = _prepare_url_text(mysql_escape_string($eventName));
		$bsql = "UPDATE Events SET SanitizedEventName= " . $sanitized_eventName . " WHERE EventID=" . $eventID;
		$query_result = mysql_query($bsql) or print('sanitize_events_categories.php: ' . mysql_error()); 

#		$url = make_event_url($eventName, $id);

		}
	}
	mysql_close($dbh);
}
else {
}

?>
