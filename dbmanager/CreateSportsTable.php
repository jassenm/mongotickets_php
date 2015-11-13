<?php
require_once('../include/ticket_db.php');
require_once('err.php');
require_once('../public_html/DbUtils.php');



print_message("Creating Sports table.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateSportsTable: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS CategoryToSportName_temp";
$query_result = mysql_query($bsql) or die ('CreateSportsTable: DROP TABLE CategoryToSportName_temp failed: ' . mysql_error());

$bsql = "CREATE TABLE CategoryToSportName_temp(
        	 CategoryID INT NOT NULL,
               	 SportName CHAR(100),
                 PRIMARY KEY (CategoryID)
        )";
$query_result = mysql_query($bsql) or die ('CreateSportsTable: ' . mysql_error());


$bsql = "SELECT CategoryID,CategoryName FROM AdjacencyListCategories_temp WHERE ParentCategoryID=3";

$result = mysql_query($bsql) or die ('CreateSportsTable: ' . mysql_error());

while ($row = mysql_fetch_array($result)) {
       $top_level_categories[] = array('id' => $row['CategoryID'], 'name' => $row['CategoryName']);
}

mysql_free_result ($result);


for($i=0; $i < count($top_level_categories); $i++) {
	$categories = GetAllSubordinatesOfCategory('ModifiedPreorderTreeTraversalCategories_temp', 
				$top_level_categories[$i]['id']);
	$categoryName = strtolower($top_level_categories[$i]['name']);
	$result = mysql_query("INSERT INTO CategoryToSportName_temp (CategoryID, SportName) VALUES (".$top_level_categories[$i]['id'].",'".$categoryName."')") or die ('CreateSportsTable: ' . mysql_error());

	for($j=0; $j < count($categories); $j++) { 
		$result = mysql_query("INSERT INTO CategoryToSportName_temp (CategoryID, SportName) VALUES (".$categories[$j]['id'].",'".$categoryName."')") or die ('CreateSportsTable: ' . mysql_error());
	}

}

mysql_close($dbh);


?>
