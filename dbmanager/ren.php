<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../include/new_urls/ticket_db.php');
require_once('../include/new_urls/url_factory.inc.php');
require_once('err.php');



print_message(" Preparing database for Venue Import.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
			or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$venueName_reName_list = Array(
                1967 => 'Wisconsin State Fairgrounds'
                );

foreach($venueName_reName_list as $vid => $new_vname) {
        $san_vname = strtolower(_prepare_url_text($new_vname));
        $bsql = "UPDATE Venues SET VenueName='$new_vname',SanitizedVenueName='$san_vname' WHERE VenueID=$vid";
echo $bsql;
        $query_result = mysql_query($bsql) or die ('UPDATE Venues_temp SET VenueName renme failed: ' . mysql_error());
}


mysql_close($dbh);

print_message("Done.");



?>
