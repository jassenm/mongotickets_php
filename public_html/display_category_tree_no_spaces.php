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

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

display_tree(0);


function display_tree($root) {
   // retrieve the left and right value of the $root node
   $result = mysql_query('SELECT lft, rgt FROM ModifiedPreorderTreeTraversalCategories '.
                          'WHERE CategoryID='.$root.';');
   $row = mysql_fetch_array($result);

   // start with an empty $right stack
   $right = array();

   // now, retrieve all descendants of the $root node
   $result = mysql_query('SELECT CategoryID, CategoryName, lft, rgt FROM ModifiedPreorderTreeTraversalCategories '.
                          'WHERE lft BETWEEN '.$row['lft'].' AND '.
                          $row['rgt'].' ORDER BY lft ASC;');

   // display each row
   while ($row = mysql_fetch_array($result)) {
       // only check stack if there is one
       if (count($right)>0) {
           // check if we should remove a node from the stack
           while ($right[count($right)-1]<$row['rgt']) {
               array_pop($right);
           }
       }

$indent = 1 + count($right);
$html =  $row['CategoryName'] . ' ' . "\t" . $row['CategoryID'] . "\n";

echo $html;

       // add this node to the stack
       $right[] = $row['rgt'];
   }
}
?>
