<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/EventInventoryWebServices.inc.php');

require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
require_once('../lib/nusoap.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
require('../include/breadcrumbs.inc.php');
require_once('../include/url_factory.inc.php');


if(isset($_REQUEST['id']) && ($_REQUEST['id'] < 1000000) && ($_REQUEST['id'] >= 0) ) {
	$id = $_REQUEST['id'];
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
        header("Location: $root_url");
}



$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates';
$smarty->compile_dir = '../smarty/templates_c';
$smarty->cache_dir = '../smarty/cache';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);



$soapclient = new soapclient($serverpath);
$method= 'SearchTickets';
$soapAction= $namespace . $method;

$param = array(  'SecurityToken' => "$securitytoken",  'ProductionID' => "$id", 'MaximumPrice' => '');
// make the call
$result = $soapclient->call($method,$param,$namespace,$soapAction);

$tickets = array();
// if a fault occurred, output error info
if (isset($fault)) {
	handle_error_no_exit ("tickets.code: ". $fault);
}
else if ($result) {

	if (isset($result['faultstring']))
        {
            handle_error_no_exit ("tickets.code: " . $result['faultstring']);
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
					$sec = $row['!SeatSection'];
					$sdescr = $row['!SeatDescription'];
					$srow = $row['!SeatRow'];
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
                                        for($i=0;$i<count($row);$i++)
                                        {
                                        $seatdescr = mysql_escape_string($row[$i]['!SeatDescription']);
                                        $tid = $row[$i]['!TicketID'];
                                        $avail = $row[$i]['!Available'];
                                        $edate = $row[$i]['!EventDate'];
                                        $sec = $row[$i]['!SeatSection'];
					$sdescr = $row[$i]['!SeatDescription'];
                                        $srow = $row[$i]['!SeatRow'];
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
	handle_error_no_exit ("tickets.code: No result");
	
}

// kill object
unset($soapclient);


if( $dbh=mysql_connect ($host_name, $db_username, $db_password) ) {
	mysql_select_db ($db_name);

        $breadcrumb_str = '';
	$eventID = '';
	$venueName = '';
	$eventDate = '';
	$city = '';
	$regionCode= '';
	$venueID = '';
	$eventName = '';
	$categoryID = '';
	$eventTypeID = '';
	$oppEventName = '';
	$shortNote = '';
	$shortDate = '';

	$Bsql = "SELECT Productions.EventID, VenueName, DATE_FORMAT(EventDate, '%a. %M %e, %Y %h:%i %p'), City, RegionCode, Venues.VenueID, e1.EventName, e1.CategoryID, e1.EventTypeID, e2.EventName, ShortNote, DATE_FORMAT(EventDate, '%b %e, %Y') FROM Productions LEFT JOIN (Venues)  ON (Venues.VenueID = Productions.VenueID) LEFT JOIN Events as e1 ON (Productions.EventID = e1.EventID) LEFT JOIN Events as e2 ON (Productions.OpponentEventID = e2.EventID) where ProductionID= " . $id;
	if($query_result = mysql_query($Bsql) ) {

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
		}
	}
	else {
		handle_error_no_exit ('tickets.code: I cannot connect to the database because: ' . mysql_error());
	}
	if($eventID != "") {
		$url = make_event_url($eventName, $eventID);
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
		$titleEventName = "$eventName Tickets vs. $oppEventName";
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, " $fullEventName Tickets");
		$title = "$eventName Tickets vs $oppEventName at $venueName $regionCode on $shortDate";
		$heading1 = "$eventName Tickets vs $oppEventName";
		$subheading = "<strong>$eventName Tickets</strong>";
		$eventName = $fullEventName;
	}
	else {
		$fullEventName = "$eventName at $venueName"; 
		$subheading = "<strong>$eventName Tickets</strong> $city";
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, "&nbsp;$fullEventName Tickets");
		$title = "$eventName Tickets at $venueName in $city, $regionCode on $shortDate";
		$heading1 = "$eventName Tickets";

	}


	mysql_close($dbh);
}
else
{
         handle_error_no_exit ('tickets.code: I cannot connect to the database because: ' . mysql_error());
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

$smarty->assign("NumTickets", count($tickets));

$smarty->display('tickets.tpl');


if( count($tickets) > 0) {
	$soapclient = new soapclient($serverpath);
	$method= 'GetVenueMapURL';
	$soapAction= $namespace . $method;

	$param = array(  'APPCLIENT_ID' => "$securitytoken",  'EVENT_ID' => "$eventID", 'VENUE_ID' => "$venueID");
	// make the call
	$result = $soapclient->call($method,$param,$namespace,$soapAction);

	// if a fault occurred, output error info
	if (isset($fault)) {
		handle_error_no_exit ("tickets.code: ". $fault);
	}
	else if ($result) {

		if (isset($result['faultstring']))
	        {
	            handle_error_no_exit ("tickets.code: " . $result['faultstring']);
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

	ticketslist_to_sorted_grouped($sortBy,$sortOrder,$tickets,$startFrom,$eventName,$eventDate,$venueName, $id);

} #end if there are tickets

$smarty->display('footer.tpl');


function BuildEventKeywordList($eventName, $city) {

	$lowerEventName = strtolower($eventName);
	$lowerCity = strtolower($city);
	$keywords = "$lowerEventName, $lowerEventName $lowerCity, $lowerEventName tickets" . 
			", $lowerEventName $lowerCity tickets, tickets";

	return $keywords;

}


function ticketslist_to_sorted_grouped($sortBy, $sortOrder, $ticket_list, $startFrom, $eventName,$eventDate,$venueName, $productionID)
{
include('../include/host_info.inc.php');


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
		print_page_nav($startFrom, $num_pages, $sortBy, $sortOrder);
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


	$url = make_production_url($eventName, $productionID);
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

		$url_sort_params = $url . "?sortBy=$columnID&sortOrder=$nextSortOrder";
                echo '<a href="' . $url_sort_params . "\">$columnHeading</a><img src=\"$arrow\" alt=\"&darr;\"/></th>";

	}
        echo '<th width="' . $SEAT_WIDTH . '">Seat No.</th><th width="' . $QUANTITY_WIDTH . '">Quantity</th><th width="' . $BUYNOW_WIDTH . '">&nbsp;&nbsp;</th>';

	echo "</tr>";
	echo "</table>";
	$ticket_index = 1;

	for($j=($startFrom-1)*$MAX_TIX_PP; ($j < ((($startFrom)*$MAX_TIX_PP) - 1) ) && ($j < count($sorted_array)); $j++) {
	# foreach($sorted_array as $index=>$ticket_info) {
                echo '<form action="/ticket_order.html" method="post">';
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
# echo '<td width="' . $BUYNOW_WIDTH . '"><a href="https://www.ticketsnow.com/buytickets.aspx?id=' . $ticketid . '&reqqty=0&client=4089"><img src="' . "$root_url/Images/buynow.gif" . '" alt="buy tickets now"/></a>';
#########

  		echo '<td width="18%"><input type="hidden" name="ticket_id" value="' . $ticketid . '"/>';
                echo '<input type="hidden" name="event_name" value="' . $eventName . '"/>';
                echo '<input type="hidden" name="event_date" value="' . $eventDate . '"/>';
                echo '<input type="hidden" name="venue_name" value="' . $venueName . '"/>';
                echo '<input type="hidden" name="quantity" value="0"/> <button title="Buy these Tickets." type="submit" name="submit" value="1">Order</button></td>';

		#echo "<td><span class=tiny>" . utf8_decode($sorted_array[$j]['Descr']) . "</span>&nbsp;</td>";
	
#		echo "</form>";
		echo "</tr>";
		echo "<tr $class><td style=\"text-align=left;\" colspan=\"6\"><span class=\"tiny\">" . utf8_decode($sorted_array[$j]['Descr']) . "</span></td></tr>";
        	echo "</table>";
		echo "</form>";

		$ticket_index++;
	}
       if($num_pages > 1) {
                print_page_nav($startFrom, $num_pages, $sortBy, $sortOrder);
        }

	echo "</div>";
}

function print_trivial_num_tickets_option_list($max_num_tickets, $min_num_tickets) {

	for($num_tickets = $max_num_tickets; $num_tickets > 0; $num_tickets -= $min_num_tickets) {
		echo "<option value =\"$num_tickets\">$num_tickets</option>";
	}
}

function print_page_nav($startFrom, $num_pages, $sortBy, $sortOrder) {

        $url = "";
	echo "Page $startFrom of $num_pages<br/>";
	if($startFrom > 1) { 
		$prevStartFrom = $startFrom-1;
		echo '<a href="' . $url . "?sortBy=$sortBy&sortOrder=$sortOrder&startFrom=$prevStartFrom \" style=\"color: blue; \"><strong>&lt Previous</strong></a>&nbsp;&nbsp;"; 
	}
	for ($i=1; $i <= $num_pages; $i++) {
		$offset = $i;
		if($startFrom == $i) {
			echo "<strong>$i&nbsp;</strong>";
		}
		else {
			echo '<a href="' . $url . "?sortBy=$sortBy&sortOrder=$sortOrder&startFrom=$offset\" style=\"color: blue;\">&nbsp;<strong>$i</strong>&nbsp;</a>&nbsp;";
		}
	}
	$nextStartFrom = $startFrom+1;
	if($nextStartFrom <= $num_pages) {
		echo '<a href="' . $url . "?sortBy=$sortBy&sortOrder=$sortOrder&startFrom=$nextStartFrom \" style=\"color: blue; \"><strong> Next &gt;</strong></a>&nbsp;";
	}
}


?>
