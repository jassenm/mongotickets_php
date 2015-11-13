<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/new_urls/ticket_db.php');


include_once('../include/host_info.inc.php');
require_once('../include/new_urls/url_factory.inc.php');





$dbh=mysql_connect ($host_name, $db_username, $db_password) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);




                        for($i=2;$i<5;$i++)
{
echo "\n\n";

	$Bsql = 'SELECT VenueName, SanitizedVenueName, COUNT(Productions.ProductionID) as num_prods FROM Venues inner join Productions on (Venues.VenueID = Productions.VenueID) inner join Events on (Events.EventID = Productions.EventID) WHERE EventTypeID='  . $i . ' GROUP BY Venues.VenueID ORDER BY num_prods DESC LIMIT 10';


	if($query_result = mysql_query($Bsql) ) {

		$ary = array();
		while ($table_row = mysql_fetch_array($query_result)) {
			echo "\n" . '<a href="' . make_venue_url($table_row['SanitizedVenueName']) . '">' . 
 $table_row['VenueName'] . '</a>';
		}
	}
	else {
		handle_error_no_exit(': ' . mysql_error());
	}

	}
?>
