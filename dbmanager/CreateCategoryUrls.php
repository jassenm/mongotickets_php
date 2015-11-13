<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/ticket_db.php');
require_once('err.php');
require_once('../include/url_factory.inc.php');




print_message("CreateCategoryUrls.php .......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateCategoryUrls.php : I cannot connect to the database because: ' . mysql_error());
mysql_select_db ('mongo_tickets2');


rebuild_tree(0, '', '', 2000, '');

mysql_close($dbh);
print_message("Done.");




function rebuild_tree($parent, $parentName, $parentSanitizedCategoryName, $categoryRank, $url='') {
   // get all children of this node
   $result = mysql_query('SELECT CategoryID,CategoryName,SanitizedCategoryName,CategoryRank FROM AdjacencyListCategories '.
                          'WHERE ParentCategoryID='.$parent)  or die ('CreateCategoryUrls.php: SELECT query failed: ' . mysql_error());

	$this_url = '';
   while ($row = mysql_fetch_array($result)) {
       // recursive execution of this function for each
       // child of this node
       // $right is the current right value, which is
       // incremented by the rebuild_tree function
       $this_url = $url . $row['SanitizedCategoryName'] . '/';
       rebuild_tree($row['CategoryID'], $row['CategoryName'], $row['SanitizedCategoryName'],$row['CategoryRank'], $this_url);
   }
   $result = mysql_query("UPDATE ModifiedPreorderTreeTraversalCategories SET CategoryUrl='" . $url . "' WHERE CategoryID=" . $parent) or die ('CreateCategoryUrls.php: UPDATE ModifiedPreorderTreeTraversalCategories SET CategoryUrl query failed: ' . mysql_error());
echo "\ncategory $parentName url = $url";

   mysql_free_result ($result);

   $parentName = mysql_escape_string($parentName);

   return;
}

?>
