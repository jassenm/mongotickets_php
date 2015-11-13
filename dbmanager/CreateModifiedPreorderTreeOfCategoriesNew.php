<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/ticket_db.php');
require_once('err.php');
require_once('../include/url_factory.inc.php');




print_message("Creating Modified Preorder Tree of Categories 2.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateModifiedPreorderTreeOfCategoriesNew: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ('mongo_tickets2');

$bsql = "DROP TABLE IF EXISTS ModifiedPreorderTreeTraversalCategories";
$query_result = mysql_query($bsql) or die ('DROP TABLE ModifiedPreorderTreeTraversalCategories failed: ' . mysql_error());



$bsql = "CREATE TABLE ModifiedPreorderTreeTraversalCategories(
        	 CategoryID INT NOT NULL,
               	 CategoryName CHAR(100),
		 SanitizedCategoryName CHAR(100),
            	 lft INT NOT NULL,
                 rgt INT NOT NULL,
                 CategoryRank SMALLINT NOT NULL,
                 Depth INT NOT NULL,
		 CategoryUrl TEXT(300),
                 PRIMARY KEY (CategoryID)
        )";
$query_result = mysql_query($bsql) or die ('CREATE TABLE ModifiedPreorderTreeTraversalCategories failed: ' . mysql_error());


rebuild_tree(0, 'Top', 2000, 1, 0);

mysql_close($dbh);
print_message("Done.");




function rebuild_tree($parent, $parentName, $parentSanitizedCategoryName, $categoryRank, $left, $depth) {
   // the right value of this node is the left value + 1
   $right = $left+1;
   $depth = $depth+1;

   // get all children of this node
   $result = mysql_query('SELECT CategoryID,CategoryName,SanitizedCategoryName,CategoryRank FROM AdjacencyListCategories '.
                          'WHERE ParentCategoryID='.$parent)  or die ('SELECT query failed: ' . mysql_error());

   while ($row = mysql_fetch_array($result)) {
       // recursive execution of this function for each
       // child of this node
       // $right is the current right value, which is
       // incremented by the rebuild_tree function
       $right = rebuild_tree($row['CategoryID'], $row['CategoryName'], $row['SanitizedCategoryName'],$row['CategoryRank'], $right, $depth);
   }

   mysql_free_result ($result);

   // we've got the left value, and now that we've processed
   // the children of this node we also know the right value
   $parentName = mysql_escape_string($parentName);
   $result = mysql_query("INSERT INTO ModifiedPreorderTreeTraversalCategories (CategoryID, CategoryName, SanitizedCategoryName, lft, rgt, CategoryRank, Depth) VALUES (".$parent.",'".$parentName."','".$parentSanitizedCategoryName."',".$left.",".$right.",".$categoryRank.",".$depth.")") or die ('INSERT INTO ModifiedPreorderTreeTraversalCategories query failed: ' . mysql_error());


   // return the right value of this node + 1
   return $right+1;
}

?>
