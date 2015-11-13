<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#



include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
include('../include/error.php');


if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	if($dbh) {
		$inputFile = "EventTextMongo.csv.new";
		echo "Processing $inputFile.......";
		$fh = fopen($inputFile, "r") or die('"update_event_text.php": Cannot open ' . $inputFile . "\n");
		# data format is:
		# Event Name	EventID	EventIntroText	EventText	EventImagePathname
		# (0            (1)	(2)		(3)		(4)
		# skip over first line
		$data = fgetcsv($fh, 1000, ",");
		while (($data = fgetcsv($fh, 5000, ",")) !== FALSE) {
			$eventName = mysql_escape_string($data[0]);
			$eventID = $data[1];
			$eventIntroText = mysql_escape_string($data[2]);
			$eventText = mysql_escape_string($data[3]);
			$eventImagePathname = mysql_escape_string($data[4]);

                	$result = mysql_query("SELECT EventID from EventText WHERE EventID=$eventID");
                	$num_rows = mysql_num_rows($result);

                	if($num_rows < 1) {
                       		$result = mysql_query('INSERT INTO EventText (EventID,EventIntroText,EventText,EventImagePathname) VALUES(' . $eventID . ",'" . $eventIntroText . "','" . $eventText . "','" . $eventImagePathname  . "')") or die ('update_event_text.php: INSERT INTO EventText failed for EventID: ' . $eventID . mysql_error());
				echo "\n$eventName Data inserted";
                	}
                	else {
                        	$bsql = "UPDATE EventText SET EventIntroText='$eventIntroText', EventText='$eventText', EventImagePathname='$eventImagePathname' WHERE EventID=$eventID";
                        	$result = mysql_query($bsql) or die ('update_event_text.php: UPDATE EventText failed for ' . $eventName . 'EventID= ' . $eventID . mysql_error());
				echo "\n$eventName Data updated";
                	}

        	}
		fclose($fh);
	}

	mysql_close($dbh);
}
else {
        echo 'I cannot connect to the database because: ' . mysql_error();
}


?>

