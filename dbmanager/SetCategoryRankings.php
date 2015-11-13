<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/ticket_db.php');
require_once('../public_html/DbUtils.php');


$input_filename = "CategoryRankings.csv";


$dbh=mysql_connect ($host_name, $db_username, $db_password)
                 or die ('SetCategoryRankings: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);


if(($fh = fopen($input_filename, "r")) === FALSE){
        die('Failed to open $input_filename file!');
}

print "\nSetCategoryRankings: Updating Category Ranks in Database....";
$buffer = fgetcsv($fh, 1000, ",");
while (($buffer = fgetcsv($fh, 1000, ",")) !== FALSE) {
	$categoryName = $buffer[0];
	$categoryID = $buffer[1];
	$rank = $buffer[2];
	$bsql = "UPDATE AdjacencyListCategories_temp SET CategoryRank=$rank where CategoryID=$categoryID";
	$query_result = mysql_query($bsql) or die ('SetCategoryRankings: ' . mysql_error());

	$bsql = "UPDATE ModifiedPreorderTreeTraversalCategories_temp SET CategoryRank=$rank where CategoryID=$categoryID";
	$query_result = mysql_query($bsql) or die ('SetCategoryRankings: ' . mysql_error());
}


mysql_close($dbh);
	
print "\nSetCategoryRankings: Done.\n";


?>
