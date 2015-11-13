<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/ticket_db.php');
require_once('../public_html/Utils.php');
require_once('err.php');

print_message("Creating CategoryKeywords Table.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('CreateCategoryKeywordsTables: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS CategoryKeywords_temp";
$query_result = mysql_query($bsql) or die ('CreateCategoryKeywordsTables: ' . mysql_error());

$bsql = "CREATE TABLE CategoryKeywords_temp (
             CategoryID INT NOT NULL,
             Keywords BLOB,
             PRIMARY KEY (CategoryID)
         )";
$query2_result = mysql_query($bsql) or die ('CreateCategoryKeywordsTables: ' . mysql_error());

print_message("Done.");


$query3_result = mysql_query('SELECT CategoryID,CategoryName FROM AdjacencyListCategories_temp')  or die ('CreateCategoryKeywordsTables: ' . mysql_error());


$categoryList = array();
while ($row = mysql_fetch_row($query3_result)) {
       // recursive execution of this function for each
       // child of this node
       // $right is the current right value, which is
       // incremented by the rebuild_tree function
       $categoryList[] = array("id" => $row[0], "name" => $row[1]);
}

mysql_free_result ($query3_result);

$limit = 5;
foreach($categoryList as $key=>$categoryInfo) {
	$categoryName =  strtolower($categoryInfo['name']);
	switch ($categoryInfo['id']) {
		case 3: 
			$keywords = BuildSportsKeywordList();
			break;
                case 4:
                        $keywords = BuildTheaterKeywordList($categoryInfo['id'], $categoryName);
                        break;
                case 10:
                        $keywords = BuildNascarKeywordList($categoryInfo['id'], $categoryName);
                        break;
                case 13:
                case 14:
                        $keywords = BuildNCAAKeywordList($categoryInfo['id'], $categoryName);
                        break;
                case 82:
                case 91:
                case 24:
                case 1999:
			$keywords = BuildProfessionalLeagueKeywordList($categoryInfo['id'], $categoryName);
                        break;
		case 2:
			$keywords = BuildDefaultKeywordList($categoryInfo['id'], $categoryName);
			$keywords .= GetGenreKeywords($categoryInfo['id']);
			break;
		default:
			$keywords = BuildDefaultKeywordList($categoryInfo['id'], $categoryName);
	}
#echo "\nInserting category ID = ".$categoryInfo['id'].": $keywords";
	$keywords = AmpersandToAnd($keywords);
        $bsql = "INSERT INTO CategoryKeywords_temp (CategoryID, Keywords) " .
                "VALUES (" . $categoryInfo['id'] . ",'" .      
                mysql_escape_string($keywords) . "')";
        $query_result = mysql_query($bsql) or die('CreateCategoryKeywordsTables: ' . mysql_error());

}

print_message("Done.");

mysql_close($dbh);
print_message("Finished Creating CategoryKeywords Table");

function BuildSportsKeywordList() {
	$keywords = "nba tickets, nhl tickets, ncaa basketball tickets, ncaa football tickets, college basketball tickets, college football tickets, nfl tickets, mlb tickets, event tickets, sports tickets, college tickets, tickets";
	return $keywords;

}

function BuildTheaterKeywordList($categoryID, $categoryName) {
        $keywords = $categoryName . ", " . $categoryName . " tickets";
        $keywords .= ", theatre, theatre tickets";
	$events = GetTopEvents($categoryID);	

	if(count($events) > 0 ) {
	foreach($events as $key=>$event) {
                $keywords .= ", $event tickets";
                $keywords .= ", $event $categoryName tickets";
        }
	}
	$keywords .= ", event tickets, tickets";
	return $keywords;
}

function BuildNascarKeywordList($categoryID, $categoryName) {
        $keywords = "$categoryName tickets, $categoryName race tickets";
        $events = GetTopEvents($categoryID);

        if(count($events) > 0 ) {
        foreach($events as $key=>$event) {
                $keywords .= ", $event tickets, $event";
        }
        }
        $keywords .= ", tickets, nascar, race, car race, car race tickets";

        return $keywords;
}


function BuildProfessionalLeagueKeywordList($categoryID, $categoryName) {
	$sportMapping = array(
			"82" => 'basketball',
			"24" => 'football',
			"91" => 'hockey',
			"1999" => 'baseball'
			);

        $keywords = $categoryName . ", " . $categoryName . " tickets";
        $keywords .= ", pro " . $sportMapping[$categoryID]  . " tickets";
        $events = GetTopEvents($categoryID);

	$event = "";
        if(count($events) > 0 ) {
        foreach($events as $key=>$event) {
                $keywords .= ", $event tickets";
        }
        }
        $keywords .= ", $categoryName game tickets, " . $sportMapping[$categoryID] . " tickets, " . $sportMapping[$categoryID] . ", " . $sportMapping[$categoryID] . " game tickets, $event, tickets";
        return $keywords;
}

function BuildNCAAKeywordList($categoryID, $categoryName) {
        $sportMapping = array(
                        "14" => 'basketball',
                        "13" => 'football'
                        );

   	$keywords = '';
        $keywords .= "ncaa " . $sportMapping[$categoryID]  . " tickets";
        $keywords .= "college " . $sportMapping[$categoryID]  . " tickets";
        $events = GetTopEvents($categoryID);

        if(count($events) > 0 ) {
        foreach($events as $key=>$event) {
                $keywords .= ", $event tickets";
        }
        }
        $keywords .= ", ncaa " . $sportMapping[$categoryID];
        $keywords .= ", ncaa " . $sportMapping[$categoryID]  . " game tickets";
        $keywords .= ", " . $sportMapping[$categoryID]  . " tickets";
        $keywords .= ", " . $sportMapping[$categoryID]  . " game tickets";
        $keywords .= ", college " . $sportMapping[$categoryID]  . " game tickets";
        $keywords .= ", tickets";

        return $keywords;
}


function BuildDefaultKeywordList($categoryID, $categoryName) {
        $keywords = $categoryName . ", " . $categoryName . " tickets";
	$events = GetTopEvents($categoryID);	

	if(count($events) > 0 ) {
	foreach($events as $key=>$event) {
                $keywords .= ", $event tickets";
                $keywords .= ", $event $categoryName tickets";
        }
	return $keywords;
}
}

function GetTopEvents($categoryID) {
	$limit = 5;
        $Bsql = "SELECT EventName,EventID FROM ModifiedPreorderTreeTraversalCategories_temp as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories_temp as c2 ON (c2.CategoryID=" . $categoryID . ") INNER JOIN Events ON (Events.CategoryID=c1.CategoryID) WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY EventRank LIMIT " . $limit;
        $result = mysql_query($Bsql) or  die('GetTopEvents: ' . mysql_error());

	$events = array();
        while ($table_row = mysql_fetch_array($result)) {
                $events[] = strtolower($table_row['EventName']);
        }
        mysql_free_result ($result);
	return $events;
}

function GetGenreKeywords($categoryID) {

   $root = $categoryID;
   // retrieve the left and right value of the $root node
   $result = mysql_query('SELECT lft, rgt FROM ModifiedPreorderTreeTraversalCategories '.
                          'WHERE CategoryID='.$root.';');
   $row = mysql_fetch_array($result);

   // start with an empty $right stack
   $right = array();
   $categoryArray = array();

   mysql_free_result ($result);

   // now, retrieve all descendants of the $root node
   $result = mysql_query('SELECT CategoryID,CategoryName FROM ModifiedPreorderTreeTraversalCategories '.
                          'WHERE lft BETWEEN '.$row['lft'].' AND '.
                          $row['rgt'].' ORDER BY lft ASC;');

   $keywords = '';
   // save each row
   while ($row = mysql_fetch_array($result)) {
       // only check stack if there is one

        if($row['CategoryID'] != $categoryID) {
                $keywords .= ", " . strtolower($row['CategoryName']);
        }
   }
   mysql_free_result ($result);

	return $keywords;
}

?>
