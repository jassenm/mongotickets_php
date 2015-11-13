<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#
require_once('../include/ticket_db.php');


$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$result = mysql_query('SELECT CategoryID FROM ModifiedPreorderTreeTraversalCategories WHERE rgt - lft <>1');
while($row = mysql_fetch_row($result)) {
    	# $nonleafCategoryID
    	$nonleafCategoryIDList[] = $row[0];
}

mysql_free_result ($result);

$data = "EventID\tEventName\tCategoryID\n";
foreach($nonleafCategoryIDList as $catID) {
   $result = mysql_query('SELECT EventID,EventName FROM Events '.
                          'WHERE CategoryID='.$catID);
   while($row = mysql_fetch_row($result)) {
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
}
$data = str_replace("\r","",$data);

mysql_free_result ($result);
mysql_close($dbh);

header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=eventsNonLeafCategories.xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";

?>
