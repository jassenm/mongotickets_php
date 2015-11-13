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


if(isset($_REQUEST['id']) && ($_REQUEST['id'] < 100002) && ($_REQUEST['id'] >= 0))  {
	$id = $_REQUEST['id'];
}
else {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://www.mongotickets.com/');
        exit();

}

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ('mongo_tickets2');

	$query = "SELECT SanitizedEventName FROM Events WHERE EventID=$id";
	if($query_result = mysql_query($query) ) {
		while ($table_row = mysql_fetch_row($query_result)) {
			$sanitizedeventName = $table_row[0];
        		$url = SITE_DOMAIN . "/$sanitizedeventName-tickets/";
		       	header('HTTP/1.1 301 Moved Permanently');
		       	header('Location: http://www.mongotickets.com' . $url);
        		exit();
		}
	}
        redir_301();
}
redir_301();

?>
