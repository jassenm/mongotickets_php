<?php
// redirect requests to dynamic to their keyword rich versions
require_once('../include/oodle.inc.php');
require_once('../include/oodle.category_map.inc.php');
// load configuration
// load URL factory
require_once('../include/new_urls/url_factory.inc.php');
require_once('../include/new_urls/ticket_db.php');
include_once('../include/error.php');
require_once('../public_html/DbUtils.new_urls.php');
require_once('../public_html/Utils.php');


$fh = fopen("C:\HostedSitesApache\MongoTickets_com\dbmanager\oodle_feed.log", 'a') or die("can't open file C:\HostedSitesApache\MongoTickets_com\dbmanager\oodle_feed.log");
$dt = date("Y-m-d");
$logtime = date("r");
fwrite($fh, "\n\n\n $logtime" . ": Starting oodle feed creator.\n");

// create the object
$feed = new Oodle();
$logtime = date("r");
fwrite($fh, " $logtime" . ": created oodle object.\n");

# make mainpage url
# get all categories
#    create category urls
# get all events
#    create event urls
#
// add sitemap items
#  YYYY-MM-DD

$event_type_array = array( 2, 4, 3);


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);

	foreach($event_type_array as $event_type) {
		if($event_type == 2) {
			$category = 'Concert Tickets';
		}
		elseif($event_type == 4) {
			$category = 'Theater Tickets';
		}
		else {
			$category = 'Sports Tickets';
		}
		if($event_type != 3) {
        		$bsql = "SELECT MinCost, ProductionID, PostalCode, RegionCode, City, EventName, DATE_FORMAT(EventDate, '%Y-%m-%d') as event_date, DATE_FORMAT(EventDate, '%Y-%m-%dT%T-07:00') as event_time_rss, DATE_FORMAT(EventDate, '%m/%d/%Y') as event_date_title, DATE_FORMAT(EventDate, '%h:%i %p') as event_time_title, CategoryID, VenueName FROM Productions INNER JOIN Events  ON (Events.EventID=Productions.EventID) INNER JOIN Venues ON (Venues.VenueID=Productions.VenueID) WHERE EventTypeID=$event_type";
		}
		else {
        		$bsql = "SELECT MinCost, ProductionID, PostalCode, RegionCode, City, e1.EventName as EventName, DATE_FORMAT(EventDate, '%Y-%m-%d') as event_date, DATE_FORMAT(EventDate, '%Y-%m-%dT%T-07:00') as event_time_rss, DATE_FORMAT(EventDate, '%m/%d/%Y') as event_date_title, DATE_FORMAT(EventDate, '%h:%i %p') as event_time_title, e1.CategoryID as CategoryID, VenueName, e2.EventName as opponent FROM Productions INNER JOIN Events as e1 ON (e1.EventID=Productions.EventID) LEFT JOIN Events as e2 ON (e2.EventID=Productions.OpponentEventID) INNER JOIN Venues ON (Venues.VenueID=Productions.VenueID) WHERE e1.EventTypeID=$event_type";

		}
		if($query_result = mysql_query($bsql) ) {
			$logtime = date("r");
			fwrite($fh, " $logtime" . ": Db query successful for $category!\n");
			while ($table_row = mysql_fetch_array($query_result)) {
				$team = '';
				$prodID = $table_row['ProductionID'];
				$postalCode = $table_row['PostalCode'];
				$regionCode = $table_row['RegionCode'];
				$city = utf8_decode($table_row['City']);
				$categoryID = $table_row['CategoryID'];
				$eventName = $table_row['EventName'];
				$title = $eventName;

				if($event_type == 3) {
					if(strlen($table_row['opponent']) > 1 ) {
						$title .= " vs. " . $table_row['opponent'];
						$team = $eventName . ', ' . $table_row['opponent'];
					}
					if(strlen($categoryMappingArray[$categoryID]) > 0) {
						$category = $categoryMappingArray[$categoryID];		
					}
					else {
						$category = 'Other Sports Tickets';
					}
					
				}
				$title .= " Tickets";

				$eventDate = $table_row['event_date'];
				$eventTimeRss = $table_row['event_time_rss'];
				$eventDateTimeTitle = $table_row['event_date_title'];
				if($table_row['event_time_title'] != '11:59 PM') {
					$eventDateTimeTitle .= ' ' . $table_row['event_time_title'];
				}
				$venueName = $table_row['VenueName'];
				$price = $table_row['MinCost'];
				if(substr_count  ( $price, '.' ) > 0 ) {
					list($whole_dollar_price,$dont_care) = split('\.', $price);
				}
				else
				{
					$whole_dollar_price = $price;
				}

				$price2 = $whole_dollar_price+6;
				$price2 = $price2 . '.00';
				$price3 = $price2+2;
				$price3 = $price3 . '.00';
				$price4 = $price3+11;
				$price4 = $price4 . '.00';
				if(substr_count  ( $price, '.' ) == 0 ) {
					$price .= '.00';
				}

				if(strlen($city) > 0 ) {
					$feed->addItem(make_production_url($eventName, $prodID, $city, $event_type),
						$category,
						$prodID,
						$title,
						$eventDate,
						$eventTimeRss,
						$venueName,
						$city,
						$regionCode,
						$postalCode,
						$price,
						array('team' => $team)
						);
					$feed->addItem(make_production_url($eventName, $prodID, $city, $event_type),
						$category,
						$prodID,
						$title,
						$eventDate,
						$eventTimeRss,
						$venueName,
						$city,
						$regionCode,
						$postalCode,
						$price2,
						array('team' => $team)
						);
					$feed->addItem(make_production_url($eventName, $prodID, $city, $event_type),
						$category,
						$prodID,
						$title,
						$eventDate,
						$eventTimeRss,
						$venueName,
						$city,
						$regionCode,
						$postalCode,
						$price3,
						array('team' => $team)
						);
					$feed->addItem(make_production_url($eventName, $prodID, $city, $event_type),
						$category,
						$prodID,
						$title,
						$eventDate,
						$eventTimeRss,
						$venueName,
						$city,
						$regionCode,
						$postalCode,
						$price4,
						array('team' => $team)
						);
				}
				# else skip production
			}
		}
		else {
			$logtime = date("r");
			fwrite($fh, " $logtime" . ": Database error when getting Events!");
			echo "Database error when getting Events!";
		}

	}

	mysql_close($dbh);
}
else {
	$logtime = date("r");
	fwrite($fh, " $logtime" . ": I cannot connect to the database because: " . mysql_error());
        handle_error_no_exit ('main.code: I cannot connect to the database because: ' . mysql_error());

}

$feed->getOodle();
$logtime = date("r");
fwrite($fh, " $logtime" . ": Oodle Feed Creator done! ");
fclose($fh);





?> 
