<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../include/ticket_db.php');

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateCategoryTableIndices: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

echo "\nCreateCategoryTableIndices: Creating AdjacencyListCategories indices.......";
$bsql = "CREATE INDEX adjacencyListCategories_1 ON AdjacencyListCategories_temp(ParentCategoryID)";
$query_result = mysql_query($bsql) or die ("$bsql failed: " . mysql_error());

$bsql = "CREATE INDEX adjacencyListCategories_2 ON AdjacencyListCategories_temp(SanitizedCategoryName)";
$query_result = mysql_query($bsql) or die ("$bsql failed: " . mysql_error());
echo "Done.";



mysql_close($dbh);

?>
