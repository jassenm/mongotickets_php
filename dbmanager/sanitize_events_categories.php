<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/ticket_db.php');
include('../include/error.php');
include('../include/url_factory.inc.php');


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ("mongo_tickets2");

	$query = "SELECT EventName,EventID FROM Events WHERE 1";
	if($query_result = mysql_query($query) ) {

		$events = Array();
		while ($table_row = mysql_fetch_row($query_result)) {
			$eventName = $table_row[0];
			$eventID = $table_row[1];
			$events[$eventID] = strtolower(_prepare_url_text($eventName));
print ".";
		}

		foreach($events as $id=>$sanEventName) {

		$bsql = "UPDATE Events SET SanitizedEventName= '" . $sanEventName . "' WHERE EventID=" . $id;
		$query_result = mysql_query($bsql) or print(': ' . mysql_error()); 

		#$url = make_event_url($eventName, $id);
		}

	}
	$query = "SELECT CategoryName,CategoryID FROM AdjacencyListCategories WHERE 1";
        if($query_result = mysql_query($query) ) {

                $adjCats = Array();
                while ($table_row = mysql_fetch_row($query_result)) {
                        $catName = $table_row[0];
                        $catID = $table_row[1];
                        $adjCats[$catID] = strtolower(_prepare_url_text($catName));
print ".";
                }

                foreach($adjCats as $id=>$sanCatName) {

                $bsql = "UPDATE AdjacencyListCategories SET SanitizedCategoryName= '" . $sanCatName . "' WHERE CategoryID=" . $id;
                $query_result = mysql_query($bsql) or print(': ' . mysql_error());

                }

        }
        $query = "SELECT CategoryName,CategoryID FROM ModifiedPreorderTreeTraversalCategories_temp WHERE 1";
        if($query_result = mysql_query($query) ) {

                $modTreeCats = Array();
                while ($table_row = mysql_fetch_row($query_result)) {
                        $catName = $table_row[0];
                        $catID = $table_row[1];
                        $modTreeCats[$catID] = strtolower(_prepare_url_text($catName));
print ".";
                }

                foreach($modTreeCats as $id=>$sanCatName) {

                $bsql = "UPDATE ModifiedPreorderTreeTraversalCategories_temp SET SanitizedCategoryName= '" . $sanCatName . "' WHERE CategoryID=" . $id;
                $query_result = mysql_query($bsql) or print(': ' . mysql_error());

                }

        }


	mysql_close($dbh);
}
else {
	handle_error_no_exit ('productions.code: I cannot connect to the database because: ' . mysql_error());
}

?>
