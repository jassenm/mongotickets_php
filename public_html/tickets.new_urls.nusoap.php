<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/EventInventoryWebServices.inc.php');

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

$eventTypeID = '';
$givenSanitizedCity = '';

if(isset($_REQUEST['production_id']) && ($_REQUEST['production_id'] < 1000000) && ($_REQUEST['production_id'] >= 0) && isset($_REQUEST['event_id']) ) {
	$id = $_REQUEST['production_id'];
	$eventID = $_REQUEST['event_id'];
	$inEventID = $eventID;
	if((isset($_REQUEST['sortBy']) && (($_REQUEST['sortBy'] == 'sec') || ($_REQUEST['sortBy'] == 'pr') || ($_REQUEST['sortBy'] == 'row') ))) {
		$sortBy = $_REQUEST['sortBy'];
	}
	else {
		$sortBy = '';
	}
	if ((isset($_REQUEST['sortOrder']) && (($_REQUEST['sortOrder'] == 'asc') || ($_REQUEST['sortOrder'] == 'desc') ))){
		$sortOrder = $_REQUEST['sortOrder'];
	}
	else {
		$sortOrder = '';
	}
	if(isset($_REQUEST['startFrom']) && ($_REQUEST['startFrom'] < 1000) && ($_REQUEST['startFrom'] > 0)) {

		$startFrom = $_REQUEST['startFrom'];
	}
	else {
		$startFrom = 1;
	}

}
else {
	handle_error_no_exit ('tickets.new_urls.php: event_id not provided or production_id is missing or invalid, event_id= ' . $_REQUEST['event_id'] . ' production_id=' . $_REQUEST['production_id']  . ' ' .  $_SERVER['REQUEST_URI'] . ' returning 301');
	redir_301();
}

if(isset($_REQUEST['san_city'])) {

	$givenSanitizedCity = $_REQUEST['san_city'];
}


$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);

$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];
$query_string = (strlen($req_uri_array[1]) > 0) ? "&$req_uri_array[1]" : '';

if( $dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
        mysql_select_db ($db_name);

        $breadcrumb_str = '';
        $venueName = '';
        $eventDate = '';
        $city = '';
        $regionCode= '';
        $venueID = '';
        $eventName = '';
        $eventTypeID = '';
        $oppEventName = '';
        $shortNote = '';
        $shortDate = '';


        $Bsql = "SELECT Productions.EventID, VenueName, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), City, RegionCode, Venues.VenueID, e1.EventName, e1.CategoryID, e1.EventTypeID, e2.EventName, ShortNote, DATE_FORMAT(EventDate, '%M %e, %Y'), SanitizedVenueName, e2.EventID FROM Productions LEFT JOIN Venues  ON (Venues.VenueID = Productions.VenueID) LEFT JOIN Events as e1 ON (Productions.EventID = e1.EventID) LEFT JOIN Events as e2 ON (Productions.OpponentEventID = e2.EventID) where ProductionID=$id";

	$num_rows = 0;
        if($query_result = mysql_query($Bsql) ) {

		$num_rows = mysql_num_rows($query_result);

		$city ='';
		$sanitizedCity = '';
		if($num_rows > 0 ) {
			while ($table_row = mysql_fetch_row($query_result)) {
                        	$eventID = $table_row[0];
                        	$venueName = $table_row[1];
                        	$eventDate = preg_replace ('/11:59 PM$/', 'TBD', $table_row[2]);
                        	$city = utf8_decode($table_row[3]);
                        	$regionCode= $table_row[4];
                        	$venueID = $table_row[5];
                        	$eventName = $table_row[6];
                        	$categoryID = $table_row[7];
                        	$eventTypeID = $table_row[8];
                        	$oppEventName = $table_row[9];
                        	$shortNote = $table_row[10];
                        	$shortDate = $table_row[11];
                        	$sanitizedVenueName = $table_row[12];
                        	$secondaryEventID = $table_row[13];
                	}
		}
		
		$sanitizedCity = strtolower(_prepare_url_text($city));

		$city_check_result = 1;
		if((strlen($givenSanitizedCity) > 0) && ($sanitizedCity != $givenSanitizedCity)) {
			$city_check_result = 0;
		}
		if( !(($num_rows > 0 ) && (($eventID == $inEventID) || ($secondaryEventID == $inEventID)) && ($city_check_result == 1))) 
		{
# echo '<br/>' . "in event id = $inEventID, opp ev id = $secondaryEventID ev id = $eventID";
			handle_error_no_exit ('tickets.new_urls.php: EventID=' . $eventID . ' and prodid = ' . $id . ' lookup returned 0 results: attempting to find appropriate redirect, uri= ' . $_SERVER['REQUEST_URI'] . ' ');

			if($query_result = mysql_query('SELECT EventName FROM Events WHERE EventID = ' . $inEventID)) {
				$url = '';
				while ($table_row = mysql_fetch_array($query_result)) {
					$redirEventName = $table_row['EventName'];
				}
				if(strlen($redirEventName) < 0) {
					handle_error_no_exit ('tickets.new_urls.php: redirecting production id=' .  $id  .  ' eventid=' . $_REQUEST['event_id']  .  ' from uri= ' .  $_SERVER['REQUEST_URI']  . ' to home, returning 301');
					redir_301();
				}
				$url = make_event_url($redirEventName);
 				$url = ltrim  ( $url, '/');
				handle_error_no_exit ('tickets.new_urls.php: redirecting production id=' .  $id  .  ' eventid=' . $_REQUEST['event_id']  .  ' from uri= ' .  $_SERVER['REQUEST_URI']  . ' to ' . $url . ', returning 301');
				redir_301($url);
			}
			else {
				handle_error_no_exit ('tickets.new_urls.php: query failed during redirect find: '  .  mysql_error() . ' production id' .  $id  .  ' eventid=' . $_REQUEST['event_id']  .  ' uri= ' .  $_SERVER['REQUEST_URI']  . ' to home returning 301');
				redir_301();
			}
		}
        }
        else {
		header('HTTP/1.0 500 Internal Server Error');
		handle_error_no_exit ('tickets.code: query failed: production id' .  $id . ' eventid=' . $_REQUEST['event_id'] . ' uri= ' . $_SERVER['REQUEST_URI']  . ' ' .  mysql_error());
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('main.tpl');
		$smarty->display('error_page.tpl');
        }

	if($regionCode != '') {
		$venueUrl = make_venue_url($sanitizedVenueName);
	}


	$soapclient = new nusoap_client($serverpath);
	$method= 'SearchTickets';
	$soapAction= $namespace . $method;

	$id = 828731;
	$param = array(  'SecurityToken' => "$securitytoken",  'ProductionID' => "$id", 'MaximumPrice' => '');
	// make the call
	$result = $soapclient->call($method,$param,$namespace,$soapAction);
# header('Content-Type: text/xml; ');
	
#	  print($soapclient->response);
	  echo '<pre>' . htmlspecialchars($soapclient->response, ENT_QUOTES) . '</pre>';
	exit;

	$tickets = array();
	// if a fault occurred, output error info
	if (isset($fault)) {
		handle_error_no_exit ("tickets.code: ". $fault);
	}
	else if ($result) {

		if (isset($result['faultstring']))
	        {
	            handle_error_no_exit ("tickets.code: received faultstring error from web services = " . $result['faultstring']);
	        }
	        else {
			$root=$result['ROOT'];
			$data = $root['DATA'];
			if($data != '') {
				$row = $data['row'];             
				if(is_array($row[0]) == '') {
	
					$seatdescr = mysql_escape_string($row['!SeatDescription']);
	
					$tid = $row['!TicketID'];
					$avail = $row['!Available'];
					$edate = $row['!EventDate'];
					$sec = htmlspecialchars($row['!SeatSection']);
					$sdescr = htmlspecialchars($row['!SeatDescription']);
					$srow = htmlspecialchars($row['!SeatRow']);
					$sfrom = $row['!SeatFrom'];
					$sthru = $row['!SeatThru'];
					$tprice = $row['!TicketPrice'];
					$bprice = $row['!BrokerPrice'];
					$bid = $row['!BrokerID'];

				        $f_price = (float) $tprice;
				        $tprice = number_format($f_price, 2);

					$tickets[] = array("TicketID" => "$tid",
						"Available" => "$avail",
						"SeatSection" => "$sec",
						"SeatRow" => "$srow",
						"TicketPrice" => "$$tprice",
						"Descr" => "$sdescr",
						"SeatFrom" => "$sfrom",
						"SeatThru" => "$sthru"
					);


				}
				else {
					for($i=0;$i<count($row);$i++) {
						$seatdescr = mysql_escape_string($row[$i]['!SeatDescription']);
						$tid = $row[$i]['!TicketID'];
						$avail = $row[$i]['!Available'];
						$edate = $row[$i]['!EventDate'];
						$sec = htmlspecialchars($row[$i]['!SeatSection']);
						$sdescr = htmlspecialchars($row[$i]['!SeatDescription']);
						$srow = htmlspecialchars($row[$i]['!SeatRow']);
						$sfrom = $row[$i]['!SeatFrom'];
						$sthru = $row[$i]['!SeatThru'];
						$tprice = $row[$i]['!TicketPrice'];
						$bprice = $row[$i]['!BrokerPrice'];
						$bid = $row[$i]['!BrokerID'];
						$f_price = (float) $tprice;
						$tprice = number_format($f_price, 2);

						$tickets[] = array("TicketID" => "$tid",
                                                "Available" => "$avail",
                                                "SeatSection" => "$sec",
                                                "SeatRow" => "$srow",
                                                "TicketPrice" => "$$tprice",
						"Descr" => "$sdescr",
						"SeatFrom" => "$sfrom",
							"SeatThru" => "$sthru"
                                                );

					} # end for
				}

			} # end if no data
		}
	} # end if result
	else {
		handle_error_no_exit ("tickets.code: No result from web services");
	
	}

	// kill object
	unset($soapclient);


#####################
####    Can't do this since will cause problems
####    since url will come out differently for games
#######################
#	  $url = make_production_url($eventName, $id, city, $eventTypeID);
 
#echo 'url = ' . $url . ' and req_uri = ' . $req_uri;
 #        if ($url != $req_uri) {
                   # 301 redirect to correct url
  #                 $url = ltrim  ( $url, '/');
#echo 'here';
   #                header('HTTP/1.1 301 Moved Permanently');
    #               header('Location: http://www.mongotickets.com/' . $url);
 #		  exit();
  #       }


	$origEventName = $eventName;

	if($eventID != "") {
		$url = make_event_url($eventName);
		$breadcrumb_str = Breadcrumbs($categoryID, 0);
		$breadcrumb_str = AppendBreadcrumb($breadcrumb_str, "$url", $eventName . " Tickets");
	}
	if( $eventID == 1124 ) {
                $eventName .= " $shortNote";
		$fullEventName = $eventName;
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$fullEventName Tickets");
		$title = "$fullEventName Tickets, $fullEventName Schedule, $fullEventName Dates";
		$heading1 = $fullEventName;		
		$subheading = "<strong>$eventName Tickets</strong>";
        }
	elseif( ($eventTypeID == 3) && (($oppEventName != '') && (strcmp($oppEventName, 'Unknown Event') != 0) ) ) {
		$fullEventName = "$eventName vs. $oppEventName"; 
		$titleEventName = "$eventName vs. $oppEventName Tickets";
		$meta_descr = "$titleEventName - Buy $titleEventName for $shortDate at $venueName in $city, $regionCode at MongoTickets!" ;
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, " $fullEventName Tickets");
		$title = "$eventName vs $oppEventName Tickets at $venueName $regionCode on $shortDate";
		$heading1 = "$eventName vs $oppEventName Tickets";
		$subheading = "<strong>$eventName Tickets</strong>";
		$eventName = $fullEventName;
	}
	else {
		$fullEventName = "$eventName at $venueName"; 
		$meta_descr = "$eventName at $venueName Tickets - Buy $eventName Tickets for $shortDate at $venueName in $city, $regionCode at MongoTickets!";
		$subheading = "<strong>$eventName Tickets</strong> $city";
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$fullEventName Tickets");
		$title = "$eventName Tickets at $venueName in $city, $regionCode on $shortDate";
		$heading1 = "$eventName Tickets";

	}
	$smarty->assign("MetaDescr", $meta_descr);


	mysql_close($dbh);
}
else
{
		header('HTTP/1.0 500 Internal Server Error');
		handle_error_no_exit ('tickets.code: I cannot connect to the database because: ' . mysql_error() . ' production id= ' .  $id . ' event id = ' . $eventID . ' uri= ' . $_SERVER['REQUEST_URI'] . ' returning 500');
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('main.tpl');
		$smarty->display('error_page.tpl');
}


$keywords = "";
$keywords = BuildEventKeywordList($fullEventName, $city);
$keywords = AmpersandToAnd($keywords);
$smarty->assign("SeoKeywords", $keywords);

$smarty->assign("title", $title);


$smarty->display('main.tpl');

$smarty->assign("Breadcrumbs", $breadcrumb_str);
$smarty->assign("EventName", $eventName);
$smarty->assign("Heading1", $heading1);
$smarty->assign("SubHeading", $subheading);
$smarty->assign("EventDate", $eventDate);
$smarty->assign("ShortDate", $shortDate);
$smarty->assign("EventID", $eventID);
$smarty->assign("VenueName", $venueName);
$smarty->assign("City", $city);
$smarty->assign("RegionCode", $regionCode);
$smarty->assign("VenueID", $venueID);
$smarty->assign("VenueUrl", $venueUrl);

$smarty->assign("NumTickets", count($tickets));

$smarty->display('tickets.tpl');


if( count($tickets) > 0) {
	$soapclient = new nusoap_client($serverpath);
	$method= 'GetVenueMapURL';
	$soapAction= $namespace . $method;

	$param = array(  'APPCLIENT_ID' => "$securitytoken",  'EVENT_ID' => "$eventID", 'VENUE_ID' => "$venueID");
	// make the call
	$result = $soapclient->call($method,$param,$namespace,$soapAction);

	// if a fault occurred, output error info
	if (isset($fault)) {
	            handle_error_no_exit ("tickets.code: web services returned fault when trying to get venue URL" . $fault);
	}
	else if ($result) {

		if (isset($result['faultstring']))
	        {
	            handle_error_no_exit ("tickets.code: web services returned failure when trying to get venue URL" . $result['faultstring']);
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
		handle_error_no_exit ("tickets.code: No result");
	
	}

	// kill object
	unset($soapclient);

	$altstring = preg_replace('/\s*&\s*/' , ' and ', $venueName);
	// remove all characters that aren't a-z, 0-9, dash, underscore or space
	$NOT_acceptable_characters_regex = '#[^-a-zA-Z0-9_ ]#';
	$altstring = preg_replace($NOT_acceptable_characters_regex, '', $altstring);

	
	# print_r($tickets);
	$url = htmlspecialchars($url);
	echo "<div class=\"venueMap\"><strong>$venueName</strong> Seating Chart<a href=\"$url\" onclick=\"window.open('$url','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false\" style=\"color: red\"><img height=\"100\" width=\"100\" src=\"$url\" alt=\"$altstring seating chart\"/><br />Click to Enlarge</a>";
	#echo "<div class=\"venueMap\"><a href=\"$url\" onclick=\"window.open('$url','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false\" style=\"color: black\"><img height=\"100\" width=\"100\" src=\"$url\" alt=\"venue map\"/>$venueName</a>";
	# echo "<div style=\"padding: 0px 0px 0px 12px;\"><a href=\"$url\" onclick=\"window.open('$url','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false\" style=\"color: red\"><img height=\"100\" width=\"100\" src=\"$url\" alt=\"venue map\"/><br />Venue Map</a><br /><br />";
	echo "</div>";
	echo "</div>";

	ticketslist_to_sorted_grouped($sortBy,$sortOrder,$tickets,$startFrom,$origEventName,$eventDate,$venueName, $id, $city);

} #end if there are tickets
?>

</div> <!-- end left_bar -->

<?php
$smarty->display('right_bar.tpl');
$smarty->display('left_column.tpl');

$smarty->display('footer.tpl');


function BuildEventKeywordList($eventName, $city) {

	$lowerEventName = strtolower($eventName);
	$lowerCity = strtolower($city);
	$keywords = "$lowerEventName, $lowerEventName $lowerCity, $lowerEventName tickets" . 
			", $lowerEventName $lowerCity tickets, tickets";

	return $keywords;

}


function ticketslist_to_sorted_grouped($sortBy, $sortOrder, $ticket_list, $startFrom, $eventName,$eventDate,$venueName, $productionID, $city)
{
include('../include/host_info.inc.php');

	global $query_string;
	global $eventTypeID;

	# Section Row Price Seat Quanity Button
	$SECTION_WIDTH = "29%";
	$ROW_WIDTH = "15%";
	$PRICE_WIDTH = "15%";
	$SEAT_WIDTH = "13%";
	$QUANTITY_WIDTH = "12%";
	$BUYNOW_WIDTH = "16%";
	$MAX_TIX_PP = 500;
	$NUM_TIX_PP = $MAX_TIX_PP;


        echo '<div class="tickets">';

	$num_pages = ceil(((float)count($ticket_list) / (float)$MAX_TIX_PP));


        #echo count($ticket_list) . " tickets sets available for this Event<br/>";
	if($num_pages > 1) {
		print_page_nav($startFrom, $num_pages, $sortBy, $sortOrder, $productionID);
	}


	$image_path = "$root_url/Images/";
	$image_up = "arrow-up.gif";
	$image_down = "arrow-down.gif";
	$image_none = "arrow-none.gif";
	$ticket_limit_reached = 0;


	if(($sortBy == 'sec') || ($sortBy == 'row') ) {
		foreach ($ticket_list as $key => $row) {
			$SeatSection[$key]  = $row['SeatSection'];
			$TicketPrice[$key] = $row['TicketPrice'];
			$SeatRow[$key] = $row['SeatRow'];
		}
		if($sortOrder == 'desc') {
			$sortOrderCode = SORT_DESC;
		}
		else {
			$sortOrderCode = SORT_ASC;
		}
		if($sortBy == 'sec') {
			array_multisort($SeatSection, $sortOrderCode, $TicketPrice, SORT_ASC, $ticket_list);
		}
		else {
			array_multisort($SeatRow, $sortOrderCode, $TicketPrice, SORT_ASC, $ticket_list);
		}
		$sorted_array = $ticket_list;
	}
	elseif($sortBy == 'pr') {
		if($sortOrder == 'desc') {
		 	$sorted_array = $ticket_list;
		}
		else {
		 	$sorted_array = array_reverse($ticket_list);
		}

        }
	else {
	 	$sorted_array = array_reverse($ticket_list);
	}

        echo '<table  width="100%" cellspacing="0">';


	$url = make_production_url($eventName, $productionID, $city, $eventTypeID);
	$url_array = explode('?', $url);
	$url = $url_array[0];
	$query_string = "&event_id=$productionID";
	echo "<tr class=\"tableHeading\">";
	$sortable_columns = array("sec" => "Section", "row" => "Row", "pr" => "Price");
	foreach($sortable_columns as $columnID => $columnHeading) {

		if((strlen($sortBy) == 0 ) && ($columnID == 'pr')) {
			$nextSortOrder = 'desc';
			$arrow = $image_path . $image_up;
		}
		elseif ($sortBy == $columnID) {
			if($sortOrder == 'desc') {
				$nextSortOrder = 'asc';
				$arrow = $image_path . $image_down;
			}
			else {
				$nextSortOrder = 'desc';
				$arrow = $image_path . $image_up;
			}
		}
		else {
			$nextSortOrder = 'asc';
			$arrow = $image_path . $image_none;
		}

                if($columnID == 'sec') {
                        echo '<th width="' . $SECTION_WIDTH . '">';
                }
                elseif($columnID == 'pr') {
                        echo '<th width="' . $PRICE_WIDTH . '">';
                }
                else {
                        echo '<th width="' . $ROW_WIDTH . '">';
                }

		$url_sort_params = $url . "?sortBy=$columnID&sortOrder=$nextSortOrder" . $query_string;
                echo '<a href="' . $url_sort_params . "\">$columnHeading</a><img src=\"$arrow\" alt=\"&darr;\"/></th>";

	}
        echo '<th width="' . $SEAT_WIDTH . '">Seat No.</th><th width="' . $QUANTITY_WIDTH . '">Quantity</th><th width="' . $BUYNOW_WIDTH . '">&nbsp;&nbsp;</th>';

	echo "</tr>";
	echo "</table>";
	$ticket_index = 1;

	for($j=($startFrom-1)*$MAX_TIX_PP; ($j < ((($startFrom)*$MAX_TIX_PP) - 1) ) && ($j < count($sorted_array)); $j++) {
	# foreach($sorted_array as $index=>$ticket_info) {
                # echo '<form action="/ticket_order.html" method="post">';
                echo '<table width="100%" cellspacing="0">';

		if(($ticket_index % 2) == 0) {
			$class = "class=\"even\"";
		}
		else {
			$class = "class=\"odd\"";
		}
		echo "\n<tr $class>";
                echo '<td width="' . $SECTION_WIDTH . '"><strong>Section: </strong>' . $sorted_array[$j]['SeatSection'] . "\n" . '</td><td width="' . $ROW_WIDTH . '"><strong>Row: </strong>' . $sorted_array[$j]['SeatRow'] . '</td><td width="' . $PRICE_WIDTH . '">' . $sorted_array[$j]['TicketPrice'] . '</td><td width="' . $SEAT_WIDTH . '">' . $sorted_array[$j]['SeatFrom'] . "-" . $sorted_array[$j]['SeatThru'] . '</td><td width="' . $QUANTITY_WIDTH . '"> ';


		$section_lower = strtolower($sorted_array[$j]['SeatSection']);
		$seat_lower = strtolower($sorted_array[$j]['SeatRow']);
		if(preg_match("/^lawn$/", $section_lower) || preg_match("/^ga$/", $section_lower) || 
			preg_match("/^g\.a$/", $section_lower) || preg_match("/^ga$/", $seat_lower) || 
				preg_match("/^g\.a\.$/", $section_lower) || preg_match("/^ga\.$/", $seat_lower)
			) {
			$min_num_tickets = 1;
		}
		else if ( ($sorted_array[$j]['Available'] % 2) == 0 ) {
			$min_num_tickets = 2;
		}
		else {
			$min_num_tickets = 1;
			$max_num_tickets = $sorted_array[$j]['Available'];
			$num_tickets_array = array();
		        for($num_tickets = $min_num_tickets; $num_tickets <= $max_num_tickets; $num_tickets++) {
				if(($max_num_tickets-$num_tickets) != 1) {
					$num_tickets_array[] = $num_tickets;
				}
       			}
			$num_tickets_descending_array = array_reverse($num_tickets_array);

		}
		$ticketid = $sorted_array[$j]['TicketID'];
	echo "\n<span class=\"tiny\">Up to " . $sorted_array[$j]['Available'] . " Available</span>";
		echo "</td>";

#########
# OLD, non-framed
 echo '<td width="' . $BUYNOW_WIDTH . '"><a href="https://www.ticketsnow.com/buytickets.aspx?id=' . $ticketid . '&reqqty=0&client=4089" rel="nofollow"><img src="/Images/buy_tix.png" onmouseover="this.src=\'/Images/buy_tix_mouse_over.png\';" onmouseout="this.src=\'/Images/buy_tix.png\';" alt="' . "Buy $eventName $venueName Section " . $sorted_array[$j]['SeatSection'] . " Row " . $sorted_array[$j]['SeatRow'] . ' tickets"/></a>';
		# echo ' <input type="image" src="/Images/buy_tix.png" onmouseover="this.src=\'/Images/buy_tix_mouse_over.png\';" onmouseout="this.src=\'/Images/buy_tix.png\';" value="Submit" alt="' . "Buy $eventName $venueName Section " . $sorted_array[$j]['SeatSection'] . " Row " . $sorted_array[$j]['SeatRow'] . ' tickets" /></td>';
#########

  		echo '<td width="18%">';
		# echo '<input type="hidden" name="ticket_id" value="' . $ticketid . '"/>';
                # echo '<input type="hidden" name="event_name" value="' . $eventName . '"/>';
                # echo '<input type="hidden" name="event_date" value="' . $eventDate . '"/>';
                # echo '<input type="hidden" name="venue_name" value="' . $venueName . '"/>';
                # echo '<input type="hidden" name="quantity" value="0"/>';
		# echo ' <input type="image" src="/Images/buy_tix.png" onmouseover="this.src=\'/Images/buy_tix_mouse_over.png\';" onmouseout="this.src=\'/Images/buy_tix.png\';" value="Submit" alt="' . "Buy $eventName $venueName Section " . $sorted_array[$j]['SeatSection'] . " Row " . $sorted_array[$j]['SeatRow'] . ' tickets" /></td>';
                # echo '<input type="hidden" name="quantity" value="0"/> <input type="image" src="/Images/buy_tix.png" value="Submit" alt="' . "Buy $eventName $venueName Section " . $sorted_array[$j]['SeatSection'] . " Row " . $sorted_array[$j]['SeatRow'] . ' tickets" /></td>';
                # old echo '<input type="hidden" name="quantity" value="0"/> <button title="Buy these Tickets." type="submit" name="submit" value="1">Order</button></td>';

		#echo "<td><span class=tiny>" . utf8_decode($sorted_array[$j]['Descr']) . "</span>&nbsp;</td>";
	
#		# echo "</form>";
		echo "</tr>";
		echo "<tr $class><td style=\"text-align=left;\" colspan=\"6\"><span class=\"tiny\">" . utf8_decode($sorted_array[$j]['Descr']) . "</span></td></tr>";
        	echo "</table>";
		#echo "</form>";

		$ticket_index++;
	}
       if($num_pages > 1) {
                print_page_nav($startFrom, $num_pages, $sortBy, $sortOrder, $productionID);
        }

	echo "</div>";
}

function print_trivial_num_tickets_option_list($max_num_tickets, $min_num_tickets) {

	for($num_tickets = $max_num_tickets; $num_tickets > 0; $num_tickets -= $min_num_tickets) {
		echo "<option value =\"$num_tickets\">$num_tickets</option>";
	}
}

function print_page_nav($startFrom, $num_pages, $sortBy, $sortOrder, $prod_id) {

        $event_id_string = "&event_id=$prod_id";
	$url_param = "";

	$url_param = "sortBy=$sortBy&sortOrder=$sortOrder";

        $url = "";
	echo "Page $startFrom of $num_pages<br/>";
	if($startFrom > 1) { 
		$prevStartFrom = $startFrom-1;
		$prev_url = "?$url_param&startFrom=$prevStartFrom$event_id_string";
		echo '<a href="' . $prev_url . "\" style=\"color: blue; \"><strong>&lt Previous</strong></a>&nbsp;&nbsp;"; 
	}
	for ($i=1; $i <= $num_pages; $i++) {
		$offset = $i;
		if($startFrom == $i) {
			echo "<strong>$i&nbsp;</strong>";
		}
		else {
			$page_num_url = "?$url_param&startFrom=$offset$event_id_string";
			echo '<a href="' . $page_num_url . "\" style=\"color: blue;\">&nbsp;<strong>$i</strong>&nbsp;</a>&nbsp;";
		}
	}
	$nextStartFrom = $startFrom+1;
	if($nextStartFrom <= $num_pages) {
		$next_url = "?$url_param&startFrom=$nextStartFrom$event_id_string";
		echo '<a href="' . $next_url . "\" style=\"color: blue; \"><strong> Next &gt;</strong></a>&nbsp;";
	}
}


?>
