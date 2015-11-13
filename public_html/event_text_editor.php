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



?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Event Ranking Editor</title>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
        <meta name="robots" content="noindex, nofollow" />

</head>
<body>
<a href="http://www.mongotickets.com/log_me_out.php">Logout</a>


<h2><a href="http://www.mongotickets.com/event_rank_editor.php?start=1&categoryID=0">All Events</a>&nbsp<a href="http://www.mongotickets.com/event_rank_editor.php?start=1&categoryID=3">Sports Events</a>&nbsp;<a href="http://www.mongotickets.com/event_rank_editor.php?start=1&categoryID=2">Concert Events</a>&nbsp;<a href="http://www.mongotickets.com/event_rank_editor.php?start=1&categoryID=4">Theater Events</a></h2>



<?php


if(isset($_GET['eventID']) && ($_GET['eventID'] < 100000)) {

	$eventID = $_GET['eventID'];
}
if($dbh) {

	if(isset($_POST['eventID']) && ($_POST['eventID'] < 100000)) {

		$eventIntroText = mysql_escape_string($_POST['eventIntroText']);
		$eventText = mysql_escape_string($_POST['eventText']);
		$eventImagePathname = mysql_escape_string($_POST['eventImagePathname']);
		$eventID = $_POST['eventID'];

                $result = mysql_query('SELECT EventID from EventText WHERE EventID = ' . $_POST['eventID']);

                $num_rows = mysql_num_rows($result);

                if($num_rows < 1) {
                        $result = mysql_query('INSERT INTO EventText (EventID,EventIntroText,EventText,EventImagePathname) VALUES(' . $eventID . ",'" . $eventIntroText . "','" . $eventText . "','" . $eventImagePathname  . "')") or die ('INSERT INTO EventText failed for EventID: ' . $eventID . mysql_error());
echo '<br/> Record inserted</br>';
                }
                else {
			$column = '';
			if($eventIntroText != '') { $column = 'EventIntroText'; $value = $eventIntroText; }
			if($eventText != '') { $column = 'EventText';  $value = $eventText;}
			if($eventImagePathname != '') {$column = 'EventImagePathname'; $value = $eventImagePathname;}

                        $bsql = "UPDATE EventText SET $column='" . $value . " WHERE EventID=$eventID";
                        $result = mysql_query("UPDATE EventText SET $column='" . $value . "' WHERE EventID=$eventID") or die ('UPDATE EventText failed for EventID= ' . $eventID . ' ' . mysql_error());
echo '<br/> Record updated</br>';
                }

        }



			$bsql = 'SELECT EventName,EventIntroText,EventText.EventText,EventImagePathname,Events.EventID FROM Events LEFT JOIN EventText ON (EventText.EventID= Events.EventID) WHERE Events.EventID=' . $eventID;

		echo '<form action="event_text_editor.php" method="post">';
		echo '<table>';
                  if($query_result = mysql_query($bsql) ) {
                      while ($table_row = mysql_fetch_row($query_result)) {
                          $eventName = $table_row[0];
                          $eventIntroText = $table_row[1];
                          $eventText = $table_row[2];
                          $eventImagePathname = $table_row[3];
                          $eventID = $table_row[4];
                          $eventUrl = make_event_url($eventName, $eventID);
                          $eventName = htmlspecialchars($eventName);
			echo '<tr><td align="left"><strong>Event Name: </strong>' . $eventName . '</td></tr>';
			echo '<form action="event_text_editor.php" method="post">';
			echo '<table>';
			echo '<tr><td align="left"><strong>Event Intro Text: </strong><textarea name="eventIntroText" id="eventIntroText" rows="6" cols="100">' . $eventIntroText . '</textarea></td></tr>';
			echo '<input type="hidden" name="eventID" value="' . $eventID . '"/>';
			 echo '<tr> <td colspan="2" align="center"> <input type="submit" value="Update" /> </td> </tr>';
			echo '</form></table>';


			echo '<form action="event_text_editor.php" method="post">';
			echo '<table>';
			echo '<tr><td align="left" valign="top"><strong>Event Text: </strong><textarea name="eventText" id="eventText" rows="12" cols="100">' . $eventText . '</textarea></td></tr>';
			echo '<input type="hidden" name="eventID" value="' . $eventID . '"/>';
			 echo '<tr> <td colspan="2" align="center"> <input type="submit" value="Update" /> </td> </tr>';
			echo '</form></table>';



			echo '<form action="event_text_editor.php" method="post">';
			echo '<table>';
			echo '<tr><td align="left" valign="top"><strong>Event Image Pathname: </strong><input type="text" name="eventImagePathname" id="eventImagePathname" size="100" value="' . $eventImagePathname . '"/></td></tr>';
			echo '<input type="hidden" name="eventID" value="' . $eventID . '"/>';
			 echo '<tr> <td colspan="2" align="center"> <input type="submit" value="Update" /> </td> </tr>';
			echo '</form></table>';
			# <input type="text" name="rank_' . $i . '" value="' . $eventRank . '"/>
                	}
			 echo '<tr> <td colspan="2" align="center"> <input type="submit" value="Update" /> </td> </tr>';

		}
		else {
		}
		echo '</form></table>';

	mysql_close($dbh);
}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}


?>

</body>
</html>
