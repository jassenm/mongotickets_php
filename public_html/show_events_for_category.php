<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#
require_once('../include/ticket_db.php');

$top_level_cats = array(2, 3, 4);

if(isset($_REQUEST['id']) && ($_REQUEST['id'] < 1000000) ) {
   $catID = $_REQUEST['id'];
}
else {
exit;
}

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$date="";
   $result = mysql_query('SELECT EventName,EventID,CategoryID FROM Events '.
                          'WHERE CategoryID='.$catID);
   $data .= "EventName\tEventID\tCategoryID\n";
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
$data = str_replace("\r","",$data);

mysql_close($dbh);

header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=category_$catID_events.xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";

?>
