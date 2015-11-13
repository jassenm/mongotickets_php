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

if((isset($_REQUEST['name'])) && (isset($_REQUEST['id']) && ($_REQUEST['id'] < 100000) && ($_REQUEST['id'] >= 0))
	 && (isset($_REQUEST['vid']) && ($_REQUEST['vid'] < 100000) && ( $_REQUEST['vid']  >= 0)) ) {
	$eventID = $_REQUEST['id'];
	$eventName = $_REQUEST['name'];
        $venueID = $_REQUEST['vid'];
}
else {
	redir_301();
}


if( $dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ('mongo_tickets2');


        $Bsql = "SELECT SanitizedEventName, SanitizedCity FROM Productions left join Venues on (Venues.VenueID = Productions.VenueID) left join Events as e1 on (e1.EventID = Productions.EventID) where Productions.EventID=$eventID AND Venues.VenueID=$venueID GROUP BY EventName LIMIT 1 ";
        if($query_result = mysql_query($Bsql) ) {

                while ($table_row = mysql_fetch_row($query_result)) {
                        $sanitizedEventName = $table_row[0];
                        $sanitizedCity = $table_row[1];
		}
	}

	$url = "/";
	if(strlen($sanitizedEventName) > 0 ) {
		$url .= "$sanitizedEventName-tickets";
		$url .= ($sanitizedCity == '') ? '' : '-' . $sanitizedCity;
		$url .= '/';
	}
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://www.mongotickets.com' . $url);
	exit();

}
redir_301();
?>
