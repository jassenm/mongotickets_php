<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


# Preconditions:
#       1) AdjacencyListCategories_temp created
#	2) ModifiedPreorderTreeTraversalCategories_temp not created

require_once('../include/ticket_db.php');
require_once('err.php');
require_once('../include/url_factory.inc.php');


print_message("Creating Additional Categories.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateAdditionalCategories: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$inputFile = "AdditionalCategories.csv";

$categoryRank = 2000;
print_message("Processing $inputFile.......");
$fh = fopen($inputFile, "r") or die('CreateAdditionalCategories: Cannot open ' . $inputFile . "\n");
# data format is:
# CategoryName	CategoryID	ParentCategoryID
# (0            (1)             (2)
# skip over first line
$data = fgetcsv($fh, 1000, ",");
while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
	$cat_name = mysql_escape_string($data[0]);
	$cat_id = $data[1];
	$parent_cat_id = $data[2];
	$bsql = "INSERT INTO AdjacencyListCategories_temp (CategoryID, CategoryName, SanitizedCategoryName, ParentCategoryID, CategoryRank)" .
		"VALUES (" . $cat_id . ",'" . 
		$cat_name . "','" . 
 strtolower(_prepare_url_text($cat_name)) . "','" .
		$parent_cat_id . "','" . 
		$categoryRank . "');";
	$query_result = mysql_query($bsql) or print('CreateAdditionalCategories: ' . mysql_error());

}

fclose($fh);

mysql_close($dbh);

print_message("CreateAdditionalCategories: Done.");

?> 
