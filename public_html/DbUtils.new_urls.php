<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


include_once('../include/host_info.inc.php');
require_once('../include/new_urls/url_factory.inc.php');



function GetCategoryName($categoryID)
{
include('../include/host_info.inc.php');

	$Bsql = "SELECT CategoryName FROM AdjacencyListCategories WHERE CategoryID = $categoryID";

	$categoryName = '';
        if($query_result = mysql_query($Bsql) ) {
                while ($table_row = mysql_fetch_array($query_result)) {
                        $categoryName = htmlspecialchars($table_row['CategoryName']);
                }
        }
        else {
                handle_error_no_exit('GetCategoryName: ' . mysql_error());
        }
        return $categoryName;
}


function GetImmediateSubordinatesOfCategory($categoryID, $limit)
{
include('../include/host_info.inc.php');

	$league_category_ids = array(24, 82, 1999, 91);
	$order_by = " ORDER BY CategoryName ASC";
	if(in_array($categoryID, $league_category_ids)) {
		$order_by = " ORDER BY CategoryID ASC";
	}

	$Bsql = 'SELECT AdjacencyListCategories.CategoryID, AdjacencyListCategories.CategoryName, AdjacencyListCategories.CategoryRank, ModifiedPreorderTreeTraversalCategories.CategoryUrl from AdjacencyListCategories LEFT JOIN ModifiedPreorderTreeTraversalCategories ON (AdjacencyListCategories.CategoryID=ModifiedPreorderTreeTraversalCategories.CategoryID) WHERE ParentCategoryID = ' . $categoryID  . $order_by;
	if($limit > 0) {
		$Bsql .= ' LIMIT ' . $limit;
	}

	if($query_result = mysql_query($Bsql) ) {

		$subcategories = array();
		while ($table_row = mysql_fetch_array($query_result)) {
			$subcategoryID = $table_row['CategoryID'];
			$subcategoryName = $table_row['CategoryName'];
			$subcategoryRank = $table_row['CategoryRank'];
			$subcategoryUrl = make_category_url($subcategoryName);
			$subcategories[] = array("name" => "$subcategoryName", "url" => "$subcategoryUrl", "rank" => "$subcategoryRank", "id" => "$subcategoryID");
		}
	}
	else {
		handle_error_no_exit('GetImmediateSubordinatesOfCategory: ' . mysql_error());
		$subcategories = "Error";
	}
	return $subcategories;

}

function GetAllParentCategoriesOfEvent($eventName)
{
	$bsql = "select CategoryID from Events_temp where EventName ='$eventName'";
	$query_result = mysql_query($bsql) or handle_error_no_exit ('GetAllParentCategoriesOfEvent: ' . mysql_error());

        while ($table_row = mysql_fetch_array($query_result)) {
                $categoryID = $table_row['CategoryID'];
        	while ($categoryID != 0) {
			$parentCategoryList[] = $categoryID;
			$bsql = "SELECT ParentCategoryID from AdjacencyListCategories_temp where CategoryID='$categoryID'";
			$query_result = mysql_query($bsql) or handle_error_no_exit ('GetAllParentCategoriesOfEvent: ' . mysql_error());
        		while ($table_row = mysql_fetch_array($query_result)) {
		                $categoryID = $table_row['ParentCategoryID'];
			}
		}
        }

        return $parentCategoryList;
}

function GetAllParentCategoriesOfEvent_str($eventName) {

         $parentCategoryList = array();


        $bsql = "select CategoryID from Events where EventName ='$eventName'";
        $query_result = mysql_query($bsql) or handle_error_no_exit ('GetAllParentCategoriesOfEvent_str: ' . mysql_error());

        while ($table_row = mysql_fetch_array($query_result)) {
                $categoryID = $table_row['CategoryID'];
                while ($categoryID != 0) {
                        $parentCategoryList[] = $categoryID;
                        $bsql = "SELECT ParentCategoryID from AdjacencyListCategories_temp where CategoryID='$categoryID'";
                        $query_result = mysql_query($bsql) or handle_error_no_exit ('GetAllParentCategoriesOfEvent_str: ' . mysql_error());
                        while ($table_row = mysql_fetch_array($query_result)) {
                                $categoryID = $table_row['ParentCategoryID'];
                        }
                }
        }

        return $parentCategoryList;
}

function GetAllParentCategoriesOfEventID($eventID, $categoryID)
{
#        $bsql = "SELECT CategoryID from Events where EventID =$eventID";
 #       if($query_result = mysql_query($bsql) ) {
  #      	while ($table_row = mysql_fetch_array($query_result)) {
   #             	$categoryID = $table_row['CategoryID'];
    #            	while ($categoryID != 0) {
     #                   	$parentCategoryList[] = $categoryID;
      #                  	$bsql = "SELECT ParentCategoryID from AdjacencyListCategories where CategoryID='$categoryID'";
       #                 	if($query_result = mysql_query($bsql) ) {
        #                		while ($table_row = mysql_fetch_array($query_result)) {
         #                       		$categoryID = $table_row['ParentCategoryID'];
          #              		}
	#			}
	#			else {
	#				handle_error_no_exit ('GetAllParentCategoriesOfEventID: ' . mysql_error());
	#				$parentCategoryList = "Error";
	#				break 2;
	#			}
         #       	}
        #	}
#	}
#	else {
#		handle_error_no_exit ('GetAllParentCategoriesOfEventID: ' . mysql_error());
#		$parentCategoryList = "Error";
#	}
        $Bsql = "SELECT C.CategoryID" .
                " FROM ModifiedPreorderTreeTraversalCategories AS B, ModifiedPreorderTreeTraversalCategories AS C" .
                " WHERE (B.lft BETWEEN C.lft AND C.rgt)" .
                " AND (B.CategoryID = $categoryID)" .
                " ORDER BY C.lft";
#Might want to change to (B.lft BETWEEN (C.lft+1) AND C.rgt)
	if($result = mysql_query($Bsql) ) {
        	while ($row = mysql_fetch_array($result)) {
			$parentCategoryList[] = $row['CategoryID'];
		}
	}
	else {
		$parentCategoryList = "GetAllParentCategoriesOfEventID: Error";
	}


        return $parentCategoryList;
}


function GetEventsUnderCategory($categoryID) {

   $eventsArray = array();

   $Bsql = "SELECT e1.EventName, e1.EventID FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID= $categoryID) INNER JOIN Events as e1 ON (e1.CategoryID =c1.CategoryID) WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY c1.lft ASC";
   if($result = mysql_query($Bsql)) {
        // save each row
        while ($row = mysql_fetch_row($result)) {
		$eventsArray[] = array("name" => $row[0], "id" => $row[1]);
        }
   }
   else {
        $eventsArray = "Error";
   }
   return $eventsArray;


}


function GetAllSubordinatesOfCategory($tableName, $categoryID) {
   $root = $categoryID;
   $categoryArray = array();

   $Bsql = "SELECT c1.CategoryName, c1.CategoryID, c1.Depth FROM $tableName as c1 LEFT JOIN $tableName as c2 ON (c2.CategoryID= $categoryID) WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY c1.lft ASC";
   if($result = mysql_query($Bsql)) {
	// save each row
	while ($row = mysql_fetch_array($result)) {
		if($row['CategoryID'] != $categoryID) {
			$catName =  $row['CategoryName'];
			$categoryArray[] = array("name" => $catName, "id" => $row['CategoryID'], "depth" => $row['Depth']);
		}
	}
   }
   else {
	$categoryArray = "Error";
   }
   return $categoryArray;
}

function GetAllSubordinatesOfCategoryOrderedRank($tableName, $categoryID) {
   $root = $categoryID;
   $categoryArray = array();

   $Bsql = "SELECT c1.CategoryName, c1.CategoryID, c1.Depth, c1.CategoryRank FROM $tableName as c1 LEFT JOIN $tableName as c2 ON (c2.CategoryID= $categoryID) WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY c1.CategoryRank ASC";
   if($result = mysql_query($Bsql)) {
	// save each row
	while ($row = mysql_fetch_array($result)) {
		if($row['CategoryID'] != $categoryID) {
			$catName =  $row['CategoryName'];
			$categoryArray[] = array("name" => $catName, "id" => $row['CategoryID'], "depth" => $row['Depth'], "rank" => $row['CategoryRank']);
		}
	}
   }
   else {
	$categoryArray = "Error";
   }
   return $categoryArray;
}


function GetSubordinatesOfCategory($categoryID, $limit) {
   $root = $categoryID;
   $limit += 1;
   // retrieve the left and right value of the $root node
#   $result = mysql_query('SELECT lft, rgt FROM ModifiedPreorderTreeTraversalCategories '.
 #                         'WHERE CategoryID='.$root.';');
 #  $row = mysql_fetch_array($result);

   // start with an empty $right stack
   $categoryArray = array();

   // now, retrieve all descendants of the $root node
 #  $result = mysql_query('SELECT CategoryName, CategoryID FROM ModifiedPreorderTreeTraversalCategories '.
 #                         'WHERE lft BETWEEN '.$row['lft'].' AND '.
 #                         $row['rgt'].' ORDER BY lft ASC LIMIT ' . $limit);
   $result = mysql_query('SELECT c1.CategoryName, c1.CategoryID FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID= ' . $categoryID . ') WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY c1.lft ASC');

   // save each row
   while ($row = mysql_fetch_array($result)) {
       // only check stack if there is one

        if($row['CategoryID'] != $categoryID) {
                $categoryArray[] = array("name" => $row['CategoryName'], "id" => $row['CategoryID']);
        }
   }
   return $categoryArray;
}


function GetTopSubcategoriesOfCategoryID($categoryID, $limit) {
   $limit += 1;

   $categoryArray = array();
   $result = mysql_query('SELECT c1.CategoryName, c1.CategoryID FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID= ' . $categoryID . ') WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY c2.CategoryRank ASC LIMIT ' . $limit);

   // save each row
   while ($row = mysql_fetch_array($result)) {
       // only check stack if there is one

        if($row['CategoryID'] != $categoryID) {
                $categoryArray[] = array("name" => $row['CategoryName'], "id" => $row['CategoryID']);
        }
   }
   return $categoryArray;
}


function GetTopEventsByEventTypeID($eventTypeID, $limit) {
	include('../include/host_info.inc.php');
	require('../include/new_urls/ticket_db.php');

	$dbh=mysql_connect ($host_name, $db_username, $db_password)
        	 or handle_error_no_exit ('GetTopEventsByEventTypeID: I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($db_name);


	$events = array();
        $query_result = mysql_query('SELECT EventID,EventName FROM Events WHERE EventTypeID = ' . $eventTypeID . ' ORDER BY EventRank ASC LIMIT ' . $limit)  or handle_error_no_exit('GetTopEventsByEventTypeID: ' . mysql_error());
        while ($table_row = mysql_fetch_array($query_result)) {
                $eventID = $table_row['EventID'];
                $eventName = $table_row['EventName'];
                $url= make_event_url($eventName, $eventID);
                $events[] = array("name" => "$eventName", "url" => "$url");
        }
	mysql_close($dbh);
	return $events;
}

function GetTopEventsByCategoryID($categoryID, $limit) {
include('../include/host_info.inc.php');

        require('../include/new_urls/ticket_db.php');
        $dbh=mysql_connect ($host_name, $db_username, $db_password)
                 or handle_error_no_exit ('GetTopEventsByCategoryID: I cannot connect to the database because: ' . mysql_error());
        mysql_select_db ($db_name);


        $events = array();
	if ($categoryID == 0) {
        	 $query_result = mysql_query('SELECT EventID,EventName FROM Events ORDER BY EventRank ASC LIMIT ' . $limit)  or handle_error_no_exit ('GetTopEventsByCategoryID: ' . mysql_error());
	}
	else {
        	$query_result = mysql_query('SELECT EventID,EventName FROM Events WHERE CategoryID= ' . $categoryID . ' ORDER BY EventRank ASC LIMIT ' . $limit)  or handle_error_no_exit ('GetTopEventsByCategoryID: ' . mysql_error());
	}
        while ($table_row = mysql_fetch_array($query_result)) {
                $eventID = $table_row['EventID'];
                $eventName = $table_row['EventName'];
                $url= make_event_url($eventName, $eventID);
                $events[] = array("name" => "$eventName", "url" => "$url");
        }
        mysql_close($dbh);

        return $events;
}


function GetTopEventsSubordinateToCategoryID($categoryID, $limit) {
	include('../include/host_info.inc.php');
        require('../include/new_urls/ticket_db.php');


        $events = array();
	$num_events = 0;

#	$Bsql = 'SELECT EventName, Events.EventID, EventRankings.EventRank, Events.CategoryID FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID=' . $categoryID . ') INNER JOIN Events ON (Events.CategoryID=c1.CategoryID) INNER JOIN EventRankings ON (Events.EventID=EventRankings.EventID) WHERE c1.lft BETWEEN c2.lft AND c2.rgt AND (Events.EventID IN (SELECT DISTINCT EventID FROM Productions)) ORDER BY EventRank LIMIT ' . $limit;
$Bsql = 'SELECT EventName, Events.EventID, EventRankings.EventRank, Events.CategoryID,(Select Count(EventID) AS count FROM Productions WHERE Productions.EventID = Events.EventID) AS prodCount FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID=' . $categoryID . ') INNER JOIN Events ON (Events.CategoryID=c1.CategoryID) INNER JOIN EventRankings ON (Events.EventID=EventRankings.EventID) WHERE c1.lft BETWEEN c2.lft AND c2.rgt ORDER BY prodCount DESC LIMIT ' . $limit;
	if($result = mysql_query($Bsql)) {
		while ($table_row = mysql_fetch_array($result)) {
			$eventID = $table_row['EventID'];
			$eventName = $table_row['EventName'];
			$eventRank = $table_row['EventRank'];
			$categoryID = $table_row['CategoryID'];
			$url= make_event_url($eventName);
			if(strlen($table_row['EventName']) > 29) {
				$num_events++;
			}
			if(strlen($table_row['EventName']) > 58) {
				$num_events++;
			}
			$eventName = htmlspecialchars($table_row['EventName']);
			$num_events++;
			if($num_events > $limit) {
				break;
			}
			$events[] = array("name" => "$eventName", "url" => "$url", "rank" => "$eventRank", "id" => "$eventID");
        	}
	}
	else {
		handle_error_no_exit('GetTopEventsSubordinateToCategoryID: ' . mysql_error());
		$events = "Error";
	}
	return $events;
}

function GetProductionList($eventID, $eventName, $home_only, $categoryID, $max_display) {
include('../include/host_info.inc.php');


        $Bsql = "SELECT CategoryUrl FROM ModifiedPreorderTreeTraversalCategories " .
                " WHERE CategoryID = $categoryID";
        $result = mysql_query($Bsql);
        while ($row = mysql_fetch_array($result)) {
                $categoryUrl = $row['CategoryUrl'];
        }

       if($home_only == 1) {
               $bsql = "SELECT ProductionID, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), VenueName, e1.EventName, e1.EventTypeID, City, RegionCode, e2.EventID, e2.EventName, ShortNote, DATE_FORMAT(EventDate, '%M %e, %Y') FROM Productions left join Venues on (Venues.VenueID = Productions.VenueID) left join Events as e1 on (e1.EventID = Productions.EventID) left join Events as e2 on (e2.EventID = Productions.OpponentEventID) where Productions.EventID=" . $eventID . " AND DATEDIFF(NOW(), EventDate) <= 0 ORDER BY EventDate ASC";
       }
       elseif($home_only <= 0) {
                $bsql = "SELECT ProductionID, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), VenueName, e1.EventName, e1.EventTypeID, City, RegionCode, e2.EventID, e2.EventName, ShortNote, DATE_FORMAT(EventDate, '%M %e, %Y') FROM Productions left join Venues on (Venues.VenueID = Productions.VenueID) left join Events as e1 on (e1.EventID = Productions.EventID) left join Events as e2 on (e2.EventID = Productions.OpponentEventID) where Productions.EventID=" . $eventID . " OR Productions.OpponentEventID=" . $eventID . " AND DATEDIFF(NOW(), EventDate) <= 0 ORDER BY EventDate ASC";
       }
       # only display first x for game sports
       elseif($home_only == 2) {
                $bsql = "SELECT ProductionID, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), VenueName, e1.EventName, e1.EventTypeID, City, RegionCode, e2.EventID, e2.EventName, ShortNote, DATE_FORMAT(EventDate, '%M %e, %Y') FROM Productions left join Venues on (Venues.VenueID = Productions.VenueID) left join Events as e1 on (e1.EventID = Productions.EventID) left join Events as e2 on (e2.EventID = Productions.OpponentEventID) where Productions.EventID=" . $eventID . " OR Productions.OpponentEventID=" . $eventID . " AND DATEDIFF(NOW(), EventDate) <= 0 ORDER BY EventDate ASC LIMIT $max_display";
       }
	$num_productions = 0;
	$count = 0;
       if($query_result = mysql_query($bsql)) {
          while ($table_row = mysql_fetch_row($query_result)) {
                $productionID = $table_row[0];
                $eventDate = preg_replace ('/11:59 PM$/', 'TBD', $table_row[1]);
                $venueName = utf8_decode($table_row[2]);
                $homeEventName = $table_row[3];
                $eventTypeID = $table_row[4];
                $city = utf8_decode($table_row[5]);
                $regionCode = $table_row[6];
                $opponentEventID = $table_row[7];
                $opponentEventName = $table_row[8];
                $opponentName = $opponentEventName;
		if(($count % 5) == 0) {
			$opponentEventName = str_replace(' ', '', $opponentEventName);
		}
		elseif(($count % 9) == 0) {
			$opponentEventName = rtrim($opponentEventName, 's');
		}
		elseif(($count % 13) == 0) {
			$opponentEventName = str_replace(' ', '', $opponentEventName . '.com');
		}
                $shortNote = utf8_decode($table_row[9]);
		$shortNote = str_replace('?','-',$shortNote);
                $ticket_page_title_date = $table_row[10];

		if ( ($eventTypeID == 3) && (($opponentName != '') && (strcmp($opponentName, 'Unknown Event') != 0) ) ) {
			$ticket_page_title = "$eventName vs $opponentName Tickets at $venueName $regionCode on $ticket_page_title_date";
		}
		else {
			$ticket_page_title = "$eventName Tickets at $venueName in $city, $regionCode on $ticket_page_title_date";
		}

                if($opponentEventID != 1 && $opponentEventID != "") {
                        if($eventTypeID == 3) {
				if($eventName == $homeEventName) {
					$eventDescr =  "$opponentEventName";
				}
				else {
					$eventDescr =  " at $homeEventName";
				}
			}
                        else if($eventTypeID == 2) { $eventDescr =  "$homeEventName w/$opponentEventName"; }
                }
		elseif ($eventID == 1124) {
			$eventName .= " $shortNote";
			$eventDescr = $eventName;
		}
                else { $eventDescr =  "$homeEventName"; }
               	$url= make_production_url($eventName, $productionID, $city, $eventTypeID);
                $productions[] = array("eventname" => "$eventName", "url" => "$url", "venuename" => "$venueName<br />$city, $regionCode", "date" => "$eventDate<br />$shortNote", "eventid" => "$eventID", "eventDescr" => "$eventDescr", "ticket_page_title" => "$ticket_page_title");
                $count++;
		$num_productions++;
           }
	   if($num_productions < 1 ) {
		$productions = 0;
	   }
	}
	else {
  		handle_error_no_exit ('GetProductionList: ' . mysql_error());
		$productions = "Error";
	}
        return $productions;
} # end getProductionList()


function GetVenueList($eventID, $categoryID) {
include('../include/host_info.inc.php');

        $Bsql = "SELECT ProductionID, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), VenueName, e1.EventID, e1.EventName, e1.EventTypeID, City, RegionCode, Venues.VenueID, SanitizedVenueName FROM Productions left join Venues on (Venues.VenueID = Productions.VenueID) left join Events as e1 on (e1.EventID = Productions.EventID) where Productions.EventID=" . $eventID . " ORDER BY VenueName ASC";
        if($query_result = mysql_query($Bsql) ) {

        	while ($table_row = mysql_fetch_row($query_result)) {
                	$productionID = $table_row[0];
                	$eventDate = $table_row[1];
                	$venueName = utf8_decode($table_row[2]);
                	$eventID = $table_row[3];
                	$eventName = $table_row[4];
                	$eventTypeID = $table_row[5];
                	$city = utf8_decode($table_row[6]);
                	$regionCode = $table_row[7];
                	$venueID = $table_row[8];
                	$sanVenueName = $table_row[9];

                	$eventDescr =  "$eventName";
			if(strlen($city) > 0 ) {
                		$url=  make_production_at_venue_url($eventName, $city);

                		$venues[$venueName] = array("url" => "$url", "city" => "$city", "region_code" => "$regionCode");
			}

        	} # end while
	}
	else {
		handle_error_no_exit ('GetVenueList: ' . mysql_error());
		$venues = "Error";
	}

        return $venues;
}

function GetHotSportsCategories($limit) {
	$limit +=1;

        $categories = array();
	$Bsql = "SELECT AdjacencyListCategories.CategoryID, CategoryName FROM HotSportsCategoryCandidates INNER JOIN AdjacencyListCategories WHERE (HotSportsCategoryCandidates.CategoryID=AdjacencyListCategories.CategoryID) ORDER BY CategoryRank";
	if($limit > 0) {
		$Bsql .= " LIMIT $limit";
	}
	if($query_result = mysql_query($Bsql)) {
        	while ($table_row = mysql_fetch_array($query_result)) {
                	$catID = $table_row['CategoryID'];
                	$catName = $table_row['CategoryName'];
                	$categories[] = array("id" => $catID, "name" => $catName);
        	}
	}
	else {
		handle_error_no_exit ('GetHotSportsCategories: ' . mysql_error());
		$categories = "Error";
	}
        return $categories;
}

function GetHotCategories($categoryID) {

        $categories = array();
        $Bsql = "SELECT AdjacencyListCategories.CategoryID,CategoryName FROM AdjacencyListCategories WHERE ParentCategoryID=" . $categoryID . " ORDER BY CategoryRank";
        if($query_result = mysql_query($Bsql)) {
        	while ($table_row = mysql_fetch_array($query_result)) {
                	$catID = $table_row['CategoryID'];
                	$catName = $table_row['CategoryName'];
                	$categories[] = array("id" => $catID, "name" => $catName);
        	}
	}
	else {
		handle_error_no_exit('GetHotCategories: ' . mysql_error());
		$categories = "Error";
	}
        return $categories;

}

function GetMainCategories() {

        $categories = array("Sports" => 3, "Concert" => 2, "Theater" => 4);
	$images = array("Sports" => 'sports-3.jpg', "Concert" => 'concert-2.jpg', "Theater" => 'theater-4.jpg');
	foreach($categories as $categoryName=>$categoryID) {
                $mainCategories[] = array("id" => $categoryID, "name" => "$categoryName", "catimage" => $images[$categoryName]);
        }
        return $mainCategories;

}


function GetKeywordsForCategoryID($categoryID) {
        $Bsql = "SELECT Keywords FROM CategoryKeywords WHERE CategoryID=" . $categoryID;
        if($query_result = mysql_query($Bsql)) {
        	while ($table_row = mysql_fetch_array($query_result)) {
                	$keywords = $table_row['Keywords'];
        	}
	}
	else {
		handle_error_no_exit ('GetKeywordsForCategoryID: ' . mysql_error());
		$keywords = '';
	}
        return $keywords;
}

function GetEventCategoryPath($categoryID) {
        $Bsql = "SELECT C.CategoryID, C.SanitizedCategoryName" .
                " FROM ModifiedPreorderTreeTraversalCategories AS B, ModifiedPreorderTreeTraversalCategories AS C" .
                " WHERE (B.lft BETWEEN C.lft AND C.rgt)" .
                " AND (B.CategoryID = $categoryID)" .
                " ORDER BY C.lft";
#Might want to change to (B.lft BETWEEN (C.lft+1) AND C.rgt)
        $result = mysql_query($Bsql);
        $path = '/';
        while ($row = mysql_fetch_array($result)) {
                $categoryName = $row['SanitizedCategoryName'];
                # $categoryID = $row['CategoryID'];
		$path .= $categoryName . '/';
        }
	return $path;
}

function GetCategoryUrl($categoryID) {
	$categoryUrl = '';
        $Bsql = "SELECT CategoryUrl FROM ModifiedPreorderTreeTraversalCategories " .
                " WHERE CategoryID = $categoryID";
        $result = mysql_query($Bsql);
        while ($row = mysql_fetch_array($result)) {
                $categoryUrl = '/' . $row['CategoryUrl'];
        }
	return $categoryUrl;
}

function GetLatestEventsUnderCategory($categoryID, $limit){
	$eventsArray = array();
	$Bsql = "Select EventID, EventName, EventTypeID, SanitizedEventName ";
	$Bsql .= "FROM events ";
	$Bsql .= "WHERE EventTypeID = $categoryID ";
	$Bsql .= "AND EventID IN (SELECT DISTINCT EventID FROM Productions ORDER BY EventID DESC) ";
	$Bsql .= "LIMIT $limit";

	if ($result = mysql_query($Bsql)) {
		while ($row = mysql_fetch_row($result)) {
			$eventsArray[] = array("EventID" => $row[0], "EventName" => $row[1], "EventTypeID" => $row[2], "SanitizedEventName" => $row[3]);
		}
	}
	else {
		$eventsArray = "Error";
	}
	return $eventsArray;

}

function GetUnformattedProductionList($eventID)
{
	$productionArray = array();

	$sql = "SELECT ProductionID, DATE_FORMAT( EventDate, '%a. %M %e, %Y %h:%i %p' ) as dateTime, VenueName, e1.EventName, e1.EventTypeID, City, RegionCode, e2.EventID, e2.EventName, ShortNote, DATE_FORMAT( EventDate, '%M %e, %Y' ) as Date ";
	$sql .= "FROM Productions ";
	$sql .= "LEFT JOIN Venues ON ( Venues.VenueID = Productions.VenueID ) ";
	$sql .= "LEFT JOIN EVENTS AS e1 ON ( e1.EventID = Productions.EventID ) ";
	$sql .= "LEFT JOIN EVENTS AS e2 ON ( e2.EventID = Productions.OpponentEventID ) ";
	$sql .= "WHERE Productions.EventID = " . $eventID;
	$sql .= " OR Productions.OpponentEventID = " . $eventID;
	$sql .= " ORDER BY EventDate ASC ";

	if ($result = mysql_query($sql)) {
		while ($row = mysql_fetch_row($result)) {
		$productionArray[] = array("ProductionID" => $row[0],
								   "DateTime" => $row[1],
								   "VenueName" => $row[2],
								   "EventName" => $row[3],
								   "EventTypeID" => $row[4],
								   "City" => $row[5],
								   "RegionCode" => $row[6],
								   "EventID" => $row[7],
								   "EventName" => $row[8],
								   "ShortNote" => $row[9],
								   "Date" => $row[10]);

 		}
	}
	else {
		$productionArray = "error";
	}
	return $productionArray;
}

?>

