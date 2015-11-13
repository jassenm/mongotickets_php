<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../include/new_urls/ticket_db.php');

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateProductionTableIndices: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);


echo "\nCreateProductionTableIndices: Creating Productions indices.......";
$bsql = "CREATE INDEX productions_1 ON Productions_temp(EventID)";
$query_result = mysql_query($bsql) or die ("$bsql failed: " . mysql_error());
echo "Done.";

mysql_close($dbh);

?>
