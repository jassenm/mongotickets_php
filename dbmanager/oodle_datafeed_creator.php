<?php
// redirect requests to dynamic to their keyword rich versions
require_once('../include/oodle.inc.php');
// load configuration
// load URL factory
require_once('../include/new_urls/url_factory.inc.php');
require_once('../include/new_urls/ticket_db.php');
include_once('../include/error.php');
require_once('../public_html/DbUtils.new_urls.php');
require_once('../public_html/Utils.php');


// create the object
$feed = new Oodle();

# make mainpage url
# get all categories
#    create category urls
# get all events
#    create event urls
#
// add sitemap items
#  YYYY-MM-DD
$dt = date("Y-m-d");

$event_type_array = array( 2, 4);


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
			while ($table_row = mysql_fetch_array($query_result)) {
				$prodID = $table_row['ProductionID'];
				$postalCode = $table_row['PostalCode'];
				$regionCode = $table_row['RegionCode'];
				$city = utf8_decode($table_row['City']);
				$eventName = $table_row['EventName'];
				$title = $eventName;

				if($event_type == 3) {
					if(strlen($table_row['opponent']) > 1 ) {
						$title .= " vs. " . $table_row['opponent'];
					}
				}
				$title .= " Tickets";

				$eventDate = $table_row['event_date'];
				$eventTimeRss = $table_row['event_time_rss'];
				$eventDateTimeTitle = $table_row['event_date_title'];
				if($table_row['event_date_time_title'] != '11:59 PM') {
					$eventDateTimeTitle .= ' ' . $table_row['event_time_title'];
				}
				$categoryID = $table_row['CategoryID'];
				$venueName = $table_row['VenueName'];
				$price = $table_row['MinCost'];
				if(substr_count  ( $price, '.' ) == 0 ) {
					$price .= '.00';
				}
				$url= make_production_url($eventName, $prodID, $city, $event_type);
				if(strlen($city) > 0 ) {
					$feed->addItem($url,
						$category,
						$prodID,
						$title,
						$eventDate,
						$eventTimeRss,
						$venueName,
						$city,
						$regionCode,
						$postalCode,
						$price);
				}
				# else skip production
			}
		}
		else {
			echo "Database error when getting Events!";
		}

	}

	mysql_close($dbh);
}
else {
        handle_error_no_exit ('main.code: I cannot connect to the database because: ' . mysql_error());

}

$feed->getOodle();

?> 
