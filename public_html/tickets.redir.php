<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');

if(isset($_REQUEST['id']) && ($_REQUEST['id'] < 10000002) && ($_REQUEST['id'] >= 0))  {
	$id = $_REQUEST['id'];
}
else {
	redir_301();
}


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ('mongo_tickets2');

	$bsql = "SELECT SanitizedEventName, EventTypeID, SanitizedCity FROM Productions left join Venues on (Venues.VenueID = Productions.VenueID) left join Events as e1 on (e1.EventID = Productions.EventID) where Productions.ProductionID=" . $id;
	$url = '';
	$eventTypeID = 0;
	$sanitizedEventName = '';
	$sanitizedCity = '';
       if($query_result = mysql_query($bsql)) {

          while ($table_row = mysql_fetch_row($query_result)) {
                $sanitizedEventName = $table_row[0];
                $eventTypeID = $table_row[1];
                $sanitizedCity = $table_row[2];
	  }
	}

        $url = "/";
        if(strlen($sanitizedEventName) > 0 ) {
                $url .= "$sanitizedEventName-tickets";
		if($eventTypeID == 4) {
			# /<event name>-tickets<city>/?event_id=xxxxx
                	$url .= ($sanitizedCity == '') ? '' : '-' . $sanitizedCity;
		}
                $url .= "/?event_id=$id";

	}
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://www.mongotickets.com' . $url);
	exit();
}
redir_301();

?>
