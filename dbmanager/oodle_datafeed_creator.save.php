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


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);

        $bsql = "SELECT MinCost, ProductionID, PostalCode, RegionCode, City, EventName, DATE_FORMAT(EventDate, '%Y-%m-%e') as event_date, DATE_FORMAT(EventDate, '%Y-%m-%eT%T-07:00') as event_time, DATE_FORMAT(EventDate, '%m/%e/%Y %r') as event_date_time_title, CategoryID, VenueName FROM Productions INNER JOIN Events  ON (Events.EventID=Productions.EventID) INNER JOIN Venues ON (Venues.VenueID=Productions.VenueID) WHERE EventTypeID=2";
        if($query_result = mysql_query($bsql) ) {
                while ($table_row = mysql_fetch_array($query_result)) {
			$prodID = $table_row['ProductionID'];
			$postalCode = $table_row['PostalCode'];
			$regionCode = $table_row['RegionCode'];
			$city = utf8_decode($table_row['City']);
			$eventName = $table_row['EventName'];
			$eventDate = $table_row['event_date'];
			$eventTime = $table_row['event_time'];
			$eventDateTimeTitle = $table_row['event_date_time_title'];
			$categoryID = $table_row['CategoryID'];
			$venueName = $table_row['VenueName'];
			$price = $table_row['MinCost'];
			$url= make_production_url($eventName, $prodID, $city, 2);
			if(strlen($city) > 0 ) {
			$feed->addItem($url,
				'Concert Tickets',
				$prodID,
				"$eventName $eventDateTimeTitle $$price",
				$eventDate,
				$eventTime,
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


	mysql_close($dbh);
}
else {
        handle_error_no_exit ('main.code: I cannot connect to the database because: ' . mysql_error());

}

$feed->getOodle();

?> 
