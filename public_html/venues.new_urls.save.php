<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#



require_once('../include/smarty_package.php');
require_once('../lib/php/Smarty/Smarty.class.php');
require_once('../lib/nusoap.php');

include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');
require_once('../include/new_urls/breadcrumbs.inc.php');
require_once('../include/new_urls/url_factory.inc.php');


$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';
$smarty->compile_check = true;

$smarty->assign("RootUrl", $root_url);


if(isset($_REQUEST['venue_id']) && ($_REQUEST['venue_id'] < 100002))  {
        $venueID = $_REQUEST['venue_id'];
}
else {
   handle_error_no_exit ('tickets_for_venue.php: venue_id either not provided or invalid, venue_id= ' . 
		 $_REQUEST['venue_id'] . ' ' .  $_SERVER['REQUEST_URI'] . ' returning 301');
	redir_301();
}

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);
	list_events_at_venue($smarty, $venueID);
	mysql_close($dbh);
}
else {
        # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
        handle_error_no_exit ('venues.code: I cannot connect to the database because: ' . mysql_error() . ' ' . $_SERVER['REQUEST_URI'] . ' returning 500');
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('main.tpl');
        $smarty->display('error_page.tpl');
}

$smarty->display('footer.tpl');

function list_events_at_venue($smarty, $venueID) {

    $query = "SELECT Events.EventID, EventName, EventTypeID, VenueName, RegionCode,ProductionID,DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'),CategoryUrl,City,Events.CategoryID, SanitizedVenueName, Address1, PostalCode FROM Events inner join Productions on (Events.EventID = Productions.EventID) LEFT JOIN Venues on (Productions.VenueID = Venues.VenueID) LEFT JOIN ModifiedPreorderTreeTraversalCategories as c on (Events.CategoryID=c.CategoryID) WHERE  Productions.VenueID = '$venueID' ORDER BY EventDate ASC";

      if($query_result = mysql_query($query) ) {
		$num_rows = mysql_num_rows($query_result);
                while ($table_row = mysql_fetch_row($query_result)) {
                        $eventID = $table_row[0];
                        $eventName = $table_row[1];
                        $eventTypeID = $table_row[2];
                        $venueName = $table_row[3];
                        $regionCode = $table_row[4];
                        $productionID = $table_row[5];
                        $date = $table_row[6];
                        $categoryUrl = $table_row[7];
                        $city = utf8_decode($table_row[8]);
                        $categoryID = $table_row[9];
                        $sanitizedVenueName = $table_row[10];
                        $address1 = $table_row[11];
                        $zipcode = $table_row[12];
			if(strlen($eventName) < 1) {
				handle_error_no_exit ('venues.php: empty event name from db query vid=' . $_REQUEST['venue_id'] . ' ' .  $_SERVER['REQUEST_URI'] . ' returning 301');
				redir_301();
			}
 
				
			$prodUrl = make_production_url($eventName, $productionID, $city, $eventTypeID);
			$eventUrl = make_event_url($eventName);
                       	$events[] = array("name" => "$eventName", "date" => "$date", "event_url" => $eventUrl, "prod_url" => "$prodUrl", "event_id" => $eventID);
                }

		if($num_rows  < 1 ) {
			$query = "SELECT VenueName, RegionCode,City,SanitizedVenueName FROM Venues WHERE  VenueID = '$venueID'";
			if($query_result = mysql_query($query) ) {
				$num_rows = mysql_num_rows($query_result);
				while ($table_row = mysql_fetch_row($query_result)) {
					$venueName = $table_row[0];
					$regionCode = $table_row[1];
					$city = $table_row[2];
					$sanitizedVenueName = $table_row[3];
				}
				if($num_rows < 1) {
					handle_error_no_exit ('venues.new_urls.php: no productions at venue and cannot find venue id in database vid=' . $_REQUEST['venue_id'] . ' ' .  $_SERVER['REQUEST_URI'] . ' returning 301');
					redir_301();
				}
			}

		}


                $title = "$venueName Tickets, $venueName Seating Chart";
                $smarty->assign("title", $title);
		$keywords = "$venueName Seating Chart, $venueName Tickets";
                $smarty->assign("SeoKeywords", $keywords);
                $smarty->assign("MetaDescr", "$venueName Tickets. Buy tickets to events at $venueName in $city, $regionCode at MongoTickets.");

                $smarty->display('main.tpl');

                $breadcrumb_str =  '<a href="/">Home</a>';

		$venueUrl = make_venue_url($sanitizedVenueName);
#		$breadcrumb_str = AppendBreadcrumb($breadcrumb_str, "$venueUrl", $venueName);
                $breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$venueName Tickets");
                $smarty->assign("Breadcrumbs", $breadcrumb_str);

                $smarty->assign("Address", $address1);
                $smarty->assign("City", $city);
                $smarty->assign("ZipCode", $zipcode);
                $smarty->assign("State", $regionCode);

                if(count($events) > 0) {
			$venueMapUrl = GetVenueMapUrl($venueID, $events[0]['event_id']);
                        $smarty->assign("venueName", $venueName);
                        $smarty->assign("EventsArray", $events);
                        $smarty->assign("NumEvents", count($events));
                        $smarty->assign("Address", $address1);
                        $smarty->assign("City", $city);
                        $smarty->assign("ZipCode", $zipcode);
                        $smarty->assign("State", $regionCode);
                        $smarty->assign("SeatingChartUrl", $venueMapUrl);
                        $smarty->display('events_at_venue.tpl');
                }
                else {
                        echo '<div id="content">';
			echo '<div class="left_bar">';
                        echo "<div id=\"breadcrumb_trail\">$breadcrumb_str</div>";
                        echo "<div id=\"no_tickets\">";
                        echo "<h1>$venueName Events</h1>";
			echo '<p>Address: 4 Yawkey Way<br/>';
			echo 'City: Boston<br/>';
			echo 'State: MA<br/>';
			echo 'Zip: 02215';
			echo '</p>';
			echo '<h2><strong>Events at ' . $venueName . '</strong></h2>';
                        echo "<p>There are currently no events at $venueName.</p>";
                        echo "</div>";
			echo "</div> <!-- end left_bar -->";
			$smarty->display('right_bar.tpl');
			$smarty->display('left_column.tpl');
                }
        }
        else {
        # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
        handle_error_no_exit ('venues.code:list_events_at_venue(): query failed because: ' . mysql_error() . ' ' . $_SERVER['REQUEST_URI'] . ' returning 500');
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('main.tpl');
        $smarty->display('error_page.tpl');
        }



}

function list_venues_in_state($smarty, $sanitizedRegionCode) {

    $query = "SELECT VenueName, SanitizedVenueName, RegionCode FROM Productions LEFT JOIN Venues on (Productions.VenueID = Venues.VenueID) WHERE  SanitizedRegionCode = '$sanitizedRegionCode' GROUP BY VenueName ORDER BY VenueName ASC";

      if($query_result = mysql_query($query) ) {

                while ($table_row = mysql_fetch_row($query_result)) {
                        $venueName = $table_row[0];
                        $sanitizedVenueName = $table_row[1];
                        $regionCode = $table_row[2];
                        if($regionCode != '') {
				$url = "/venues/$sanitizedRegionCode/$sanitizedVenueName.html";
                        	$events[] = array("name" => "$venueName", "url" => "$url");
			}
                }

                $title = "$regionCode Venues";
                $smarty->assign("title", $title);
		$keywords = "$regionCode Venues, $regionCode tickets";
                $smarty->assign("SeoKeywords", $keywords);
		$smarty->assign("MetaDescr", "$regionCode Venues. Find you Venue to buy event Tickets at MongoTickets.");



                $smarty->display('main.tpl');


                $breadcrumb_str =  '<a href="/">Home</a>';

                $breadcrumb_str = AppendBreadcrumb($breadcrumb_str, '/venues/', 'Venues');
                $breadcrumb_str = AppendBreadcrumb($breadcrumb_str, '/venues/' . $sanitizedRegionCode . '/', $regionCode);
                $smarty->assign("Breadcrumbs", $breadcrumb_str);

                if(count($events) > 0) {
                        $smarty->assign("venueName", "$regionCode Venues");
                        $smarty->assign("EventsArray", $events);
                        $smarty->assign("NumEvents", count($events));
                        $smarty->display('venues_in_state.tpl');
                }
                else {
                        echo "<div id=\"content\">";
                        echo "<div id=\"breadcrumb_trail\">$breadcrumb_str</div>";
                        echo "<div id=\"no_tickets\">";
                        echo "<h1>$venueName Events</h1>";
                        echo "<p>There are no venues in $regionCode.</p>";
                        echo "</div>";
                }
        }
        else {
        # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
        handle_error_no_exit ('venues.code:list_venues_in_state(): I cannot connect to the database because: ' . mysql_error());
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('main.tpl');
        $smarty->display('error_page.tpl');

        }

}

function list_all_venues($smarty) {

    $query = "SELECT SanitizedRegionCode, RegionCode FROM Venues WHERE 1 GROUP BY RegionCode ORDER BY RegionCode ASC";
    # $query = "SELECT SanitizedRegionCode, RegionCode FROM Productions LEFT JOIN Venues on (Productions.VenueID = Venues.VenueID) WHERE 1 GROUP BY RegionCode ORDER BY RegionCode ASC";

      if($query_result = mysql_query($query) ) {

                while ($table_row = mysql_fetch_row($query_result)) {
                        $sanitizedRegionCode = $table_row[0];
                        $regionCode = $table_row[1];
			$url = "/venues/$sanitizedRegionCode/";
                       	$states[] = array("name" => "$regionCode", "url" => "$url");
                }

                $title = "Find All Venues";
                $smarty->assign("title", $title);
		$keywords = "find all Venues, find all venue tickets";
                $smarty->assign("SeoKeywords", $keywords);
		$smarty->assign("MetaDescr", "All Venues. Select a venue to find and buy event Tickets at MongoTickets.");

                $smarty->display('main.tpl');


                $breadcrumb_str =  '<a href="/">Home</a>';

                $breadcrumb_str = AppendBreadcrumb($breadcrumb_str, '/venues/', 'Venues');
                $smarty->assign("Breadcrumbs", $breadcrumb_str);

                $smarty->assign("h1", 'Choose a Region');
                if(count($states) > 0) {
                        $smarty->assign("States", $states);
                        $smarty->assign("NumStates", count($states));
                        $smarty->display('all_venues.tpl');
                }
                else {
                        echo "<div id=\"content\">";
                        echo "<div id=\"breadcrumb_trail\">$breadcrumb_str</div>";
                        echo "<div id=\"no_tickets\">";
                        echo "<h1>All Venues</h1>";
                        echo "<p>There are no venues</p>";
                        echo "</div>";
                }
        }
        else {
        # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
        handle_error_no_exit ('venues.code:list_venues_in_state(): I cannot connect to the database because: ' . mysql_error());
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('main.tpl');
        $smarty->display('error_page.tpl');

        }

}



function GetVenueMapUrl($venueID, $eventID) {
require_once('../include/EventInventoryWebServices.inc.php');
require_once('../lib/nusoap.php');


        $soapclient = new soapclient($serverpath);
        $method= 'GetVenueMapURL';
        $soapAction= $namespace . $method;

        $param = array(  'APPCLIENT_ID' => "$securitytoken",  'EVENT_ID' => "$eventID", 'VENUE_ID' => "$venueID");
        // make the call
        $result = $soapclient->call($method,$param,$namespace,$soapAction);

        // if a fault occurred, output error info
        if (isset($fault)) {
                    handle_error_no_exit ("venues.new_urls.php: web services returned fault when trying to get venue URL" . $fault);
        }
        else if ($result) {

                if (isset($result['faultstring']))
                {
                    handle_error_no_exit ("venues.new_urls.php: web services returned failure when trying to get venue URL" . $result['faultstring']);
                }
                else {
                        $root=$result['ROOT'];
                        $data = $root['DATA'];
                        if($data != '') {
                                $row = $data['row'];

                                $url = $row['!venuemap'];
                        } # end if no data
                }
        } # end if result
        else {
                handle_error_no_exit ("venues.new_urls.php: No result");

        }

        // kill object
        unset($soapclient);

	return $url;
}




?>
