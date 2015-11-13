<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#



include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
include('../include/login.inc.php');


session_start();

if (!isset($_SESSION['uid']) ) {
	session_defaults();
}

if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);

        if ($_SESSION['logged']) {
                _checkSession();
        } elseif ( isset($_COOKIE['mtwebLogin']) ) {
                _checkRemembered($_COOKIE['mtwebLogin']);
        } else {
		mysql_close($dbh);
                header("Location: http://www.mongotickets.com/log_me_in.php");
                exit;
        }


}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}



if(isset($_REQUEST['categoryID']) && (strlen($_REQUEST['categoryID']) < 7)) {
        $categoryID= $_REQUEST['categoryID'];
}
if(isset($_REQUEST['start']) && (strlen($_REQUEST['start']) < 7)) {
        $start= $_REQUEST['stat'];
}
if(isset($_REQUEST['keywords']) && (strlen($_REQUEST['keywords']) < 7)) {
        $keywords= $_REQUEST['keywords'];
}


if( $dbh) {

	$i = 0;
	while (isset($_REQUEST['eventid_' . $i]) ) {
     		$eventID  = mysql_escape_string($_REQUEST['eventid_' . $i]);
     		$category_rank = mysql_escape_string($_REQUEST['rank_' . $i]);
 
		$sql  = "SELECT EventID FROM EventRankings WHERE EventID=$eventID";
		if ( !$result = mysql_query($sql) ) {
			die("Could not update the record!<br /><b>EventID: $eventID</b>");
		}
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0) {
			$sql  = "UPDATE EventRankings SET EventRank=$category_rank WHERE EventID=$eventID";
			if ( !$result = mysql_query($sql) ) {
				die("Could not update the record!<br /><b>EventID: $eventID</b>");
			}
		}
		else {
			$sql  = "INSERT INTO EventRankings (EventID,SanitizedEventName,EventRank) VALUES($eventID,'',$category_rank)";
			if ( !$result = mysql_query($sql) ) {
				die("Could not insert the record!<br /><b>EventID: $eventID</b>");
			}

		}
		$i++;
	}

	mysql_close($dbh);

	header("Location: http://www.mongotickets.com/event_ranking_editor.php?start=" . $start . '&categoryID=' . $categoryID . '&keywords=' . $keywords);
}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}

?>
