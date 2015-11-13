<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


# Preconditions:
#       1) AdjacencyListCategories_temp created
#	2) ModifiedPreorderTreeTraversalCategories_temp not created
#	3) Events_temp created

require_once('../include/new_urls/ticket_db.php');
require_once('err.php');

print_message("Assigning Events To Categories.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('AssignEventsToCategories: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$categoryRank = 2000;
$inputFiles = array("SportsEventCategoryAssignments.csv", "ConcertEventCategoryAssignments.csv","TheaterEventCategoryAssignments.csv");
foreach($inputFiles as $input_filename) {

	print_message("Processing $input_filename .......");
	$fh = fopen($input_filename, "r") or die('AssignEventsToCategories: Cannot open ' . $input_filename . "\n");
	# data format is:
	# Event	EventID	CategoryId
	# (0)   (1)     (2)
	# skip over first line
	$data = fgetcsv($fh, 1000, ",");
	while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
		$ename = $data[0];
		$eid = $data[1];
		$catid = $data[2];
		$bsql = "UPDATE Events_temp SET CategoryID= " . $catid . " WHERE EventID=" . $eid;
		$query_result = mysql_query($bsql) or print('AssignEventsToCategories: ' . mysql_error());
	}
	fclose($fh);
}

$unassigned_events_category_defaults_list = array(
				  2 => 1310,
				  3 => 1526,
				  4 => 1111,
				253 => 1510
				);

# All categories assigned to top-level categories are reassigned to "other" of each top-level category
foreach($unassigned_events_category_defaults_list as $categoryID=>$otherCategoryID) {
	$bsql = "UPDATE Events_temp SET CategoryID= " . $otherCategoryID . " WHERE CategoryID=" . $categoryID;
	$query_result = mysql_query($bsql) or print('AssignEventsToCategories: ' . mysql_error());
}

mysql_close($dbh);

print_message("AssignEventsToCategories: Done.");

?> 
