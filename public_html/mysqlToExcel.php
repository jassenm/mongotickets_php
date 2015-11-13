<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
require_once('../include/ticket_db.php');
require_once('../include/EventInventoryWebServices.inc.php');



$dbh=mysql_connect ($host_name, $db_username, $db_password)
			or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$Bsql = "SELECT DISTINCT(EventName),Events.EventID,AdjacencyListCategories.CategoryName from Events INNER JOIN Productions ON (Events.EventID=Productions.EventID) INNER JOIN AdjacencyListCategories ON (Events.CategoryID=AdjacencyListCategories.CategoryID) WHERE Events.CategoryID=2 OR Events.CategoryID=4 ORDER BY Events.CategoryID";
$query_result = mysql_query($Bsql)  or die ('SELECT FROM Events query failed: ' . mysql_error());

$num_fields = mysql_num_fields($query_result);

for ($i = 0; $i < $fields; $i++) {
    $header .= mysql_field_name($query_result, $i) . "\t";
} 

while($row = mysql_fetch_row($query_result)) {
    $line = '';
    foreach($row as $value) {                                            
        if ((!isset($value)) OR ($value == "")) {
            $value = "\t";
        } else {
            $value = str_replace('"', '""', $value);
            $value = '"' . $value . '"' . "\t";
        }
        $line .= $value;
    }
    $data .= trim($line)."\n";
}
$data = str_replace("\r","",$data);

mysql_close($dbh);

header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=extraction.xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";


?>
