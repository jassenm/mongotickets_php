<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


require_once('../include/ticket_db.php');

$html = <<<ENDHTML
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>{$title}</title>
        <META name="description" content="QuadTickets.com offers cheap tickets with a guarantee. ">
        <META name="keywords" content="tickets, baseball, football">
                <style type="text/css">
                <!--
                .style1 {
                        font-size: 18px;
                        font-weight: bold;
                }
                -->
                </style>

</head>
<body>
ENDHTML;

echo $html;
$top_level_cats = array(2, 3, 4);

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

foreach($top_level_cats as $catID) {
   // retrieve the left and right value of the $root node
   $result = mysql_query('SELECT EventName,EventID FROM Events '.
                          'WHERE CategoryID='.$catID);
   $row = mysql_fetch_array($result);

echo "<h1>$catID</h1>";
echo "<ul style=\"list-style-type:none;\"";
   // display each row
   while ($row = mysql_fetch_array($result)) {
       // only check stack if there is one
echo "<li>". $row[0] . " " . $row[1] . "</li>";
   }
	echo "</ul>";
}
?>
