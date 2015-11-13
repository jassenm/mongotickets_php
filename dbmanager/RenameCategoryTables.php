<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/ticket_db.php');
require_once('err.php');


$tables = array(
		"AdjacencyListCategories",
		"ModifiedPreorderTreeTraversalCategories",
		"HotSportsCategoryCandidates",
		"CategoryToSportName",
		"CategoryKeywords"
		);

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('RenameCategoryTables: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

foreach($tables as $table) {

	$from_table = $table . "_temp";
	print_message("Renaming $table tables....");
	$bsql = "DROP TABLE IF EXISTS $table";
	$query_result = mysql_query($bsql) or die ('RenameCategoryTables: ' . mysql_error());

	$bsql = "ALTER TABLE $from_table RENAME $table";
	$query_result = mysql_query($bsql) or die ('RenameCategoryTables: ' . mysql_error());
	
	print_message("Renamed $from_table to $table");
}

mysql_close($dbh);

?>
