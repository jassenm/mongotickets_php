<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/TicketNetworkWebServices.inc.php');

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

if(isset($_REQUEST['production_id']) && ($_REQUEST['production_id'] < 1000000000) && ($_REQUEST['production_id'] >= 0) && isset($_REQUEST['event_id']) ) {
	$id = $_REQUEST['production_id'];
	$tn_cat_id = $_REQUEST['cat_id'];
	$eventID = $_REQUEST['event_id'];
	$inEventID = $eventID;
	$tnEventID = $_REQUEST['tn_event_id'];
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
	handle_error_no_exit ('tickets.tnd.php: event_id not provided or production_id is missing or invalid, event_id= ' . $_REQUEST['event_id'] . ' production_id=' . $_REQUEST['production_id']  . ' ' .  $_SERVER['REQUEST_URI'] . ' returning 301');
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


        $Bsql = "SELECT VenueName, DATE_FORMAT(ProductionDate, '%a. %M %e, %Y %h:%i %p'), City, StateProvince, DATE_FORMAT(ProductionDate, '%M %e, %Y'), ProductionName , DATEDIFF(NOW(), ProductionDate) as date_diff, MapURL, EventName, TnCategoryID FROM TNDProductions inner join TNDEventPerformers ON (TNDProductions.ProductionID=TNDEventPerformers.ProductionID) inner join  TnToTndCategoryID ON (TnToTndCategoryID.TndCategoryID=TNDProductions.ChildCategoryID) where TNDProductions.ProductionID=$id AND EventID=$eventID";

	$num_rows = 0;
	$event_found = 0;
        if($query_result = mysql_query($Bsql) ) {

		$num_rows = mysql_num_rows($query_result);

		$city ='';
		$sanitizedCity = '';
		if($num_rows > 0 ) {
			while ($table_row = mysql_fetch_row($query_result)) {
                        	$venueName = $table_row[0];
                        	$eventDate = $table_row[1];
                        	$city = utf8_decode($table_row[2]);
                        	$regionCode= $table_row[3];
                        	$shortDate = $table_row[4];
                        	$gameName = $table_row[5];
                        	$expired = $table_row[6];
                        	$mapURL = $table_row[7];
                        	$eventName = $table_row[8];
                        	$categoryID = $table_row[9];
				if($expired <= 0) {
					$event_found = 1;
# echo " found ";
				}
				$eventName;
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
			handle_error_no_exit ('tickets.tnd.php: EventID=' . $eventID . ', inEventID = ' . $inEventID . ' num_rows = ' . $num_rows . ' and prodid = ' . $id . ' , secondaryEventID =' . $secondaryEventID . ', city_check_result = ' . $city_check_result . ' lookup returned 0 results: attempting to find appropriate redirect, uri= ' . $_SERVER['REQUEST_URI'] . ' ');

			if($query_result = mysql_query('SELECT EventName FROM Events WHERE EventID = ' . $tnEventID)) {
				$url = '';
				while ($table_row = mysql_fetch_array($query_result)) {
					$redirEventName = $table_row['EventName'];
				}
				if(strlen($redirEventName) < 0) {
					handle_error_no_exit ('tickets.tnd.php: redirecting production id=' .  $id  .  ' eventid=' . $_REQUEST['event_id']  .  ' from uri= ' .  $_SERVER['REQUEST_URI']  . ' to home, returning 301');
					redir_301();
				}
				$url = make_event_url($redirEventName);
 				$url = ltrim  ( $url, '/');
				handle_error_no_exit ('tickets.tnd.php: redirecting production id=' .  $id  .  ' eventid=' . $_REQUEST['event_id']  .  ' from uri= ' .  $_SERVER['REQUEST_URI']  . ' to ' . $url . ', returning 301');
				redir_301($url);
			}
			else {
				handle_error_no_exit ('tickets.tnd.php: query failed during redirect find: '  .  mysql_error() . ' production id' .  $id  .  ' eventid=' . $_REQUEST['event_id']  .  ' uri= ' .  $_SERVER['REQUEST_URI']  . ' to home returning 301');
				redir_301();
			}
		}
        }
        else {
		header('HTTP/1.0 500 Internal Server Error');
		handle_error_no_exit ('tickets.tnd.php: query failed: production id' .  $id . ' eventid=' . $_REQUEST['event_id'] . ' uri= ' . $_SERVER['REQUEST_URI']  . ' ' .  mysql_error());
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('main.tpl');
		$smarty->display('error_page.tpl');
        }

	#	$venueUrl = make_venue_url($sanitizedVenueName);

	if($event_found > 0) {
#debug
#error_reporting( E_ALL );

		$soapclient = new SoapClient($serverpath, array("trace" => 1));
$soapclient->debug = 1;
		
		$parameters = array( "websiteConfigID" => 4589, 'numberOfRecords' => null, 'eventID' => $id,  'lowPrice' => null, 'highPrice' => null,  'ticketGroupID' => null, 'requestedTixSplit' => null, 'orderByClause' => 'ActualPrice');

		$result = $soapclient->GetTickets($parameters);

#		header('Content-Type: text/xml; ');
  #  print($soapclient->__getLastRequestHeaders());
  #  print($soapclient->__getLastRequest());
#   print($soapclient->__getLastResponse());
		

#		print "<pre>\n";  
#		print "?????? :\n".htmlspecialchars($soapclient->__getLastRequest())."\n";  
#		print "".htmlspecialchars($soapclient->__getLastResponse())."\n";  
#		print "</pre>"; 

		$tickets = array();
		# handle_error_no_exit ("tickets.tnd.php: ". $fault);

	       #     handle_error_no_exit ("tickets.tnd.php: received faultstring error from web services = " . $result['faultstring']);
  		$getTicketsResult = $result->GetTicketsResult;
#		print_r($getTicketsResult);
#		print_r($ticketGroups);
		$tG = $getTicketsResult->TicketGroup;
		if(is_array($tG))
		{
			#echo 'is array';
		   for ($i=0; 
			$i < count($getTicketsResult->TicketGroup); 
			$i++)
		   {
			#$ticketGroup = $ticketGroup[i];
			$seatdescr = mysql_escape_string($getTicketsResult->TicketGroup[$i]->Notes);
			$tid = $getTicketsResult->TicketGroup[$i]->ID;
			$avail = $getTicketsResult->TicketGroup[$i]->TicketQuantity;
			$edate = 1;
			$sec = htmlspecialchars($getTicketsResult->TicketGroup[$i]->Section);
			$sdescr = htmlspecialchars($getTicketsResult->TicketGroup[$i]->Notes);
			$srow = htmlspecialchars($getTicketsResult->TicketGroup[$i]->Row);
			$sfrom = $getTicketsResult->TicketGroup[$i]->LowSeat;
			$sthru = $getTicketsResult->TicketGroup[$i]->HighSeat;
			$tprice = $getTicketsResult->TicketGroup[$i]->ActualPrice;
			$bprice = $getTicketsResult->TicketGroup[$i]->WholesalePrice;
			$f_price = (float) $tprice;
			$tprice = number_format($f_price, 2);
			if(is_array($getTicketsResult->TicketGroup[$i]->ValidSplits->int)) {
				$vsplits = implode(' ', $getTicketsResult->TicketGroup[$i]->ValidSplits->int);
			}
			else {
				$vsplits = $getTicketsResult->TicketGroup[$i]->ValidSplits->int;
			}

			$tickets[] = array("TicketID" => "$tid",
                                           "Available" => "$avail",
                                           "SeatSection" => "$sec",
                                           "SeatRow" => "$srow",
                                           "TicketPrice" => "$$tprice",
					   "Descr" => "$sdescr",
					   "SeatFrom" => "$sfrom",
					   "SeatThru" => "$sthru",
					   "ValidSplits" => $vsplits
                                           );

		   } # end for each
		}
		else{
			$seatdescr = mysql_escape_string($getTicketsResult->TicketGroup->Notes);
			$tid = $getTicketsResult->TicketGroup->ID;
			$avail = $getTicketsResult->TicketGroup->TicketQuantity;
			$edate = 1;
			$sec = htmlspecialchars($getTicketsResult->TicketGroup->Section);
			$sdescr = htmlspecialchars($getTicketsResult->TicketGroup->Notes);
			$srow = htmlspecialchars($getTicketsResult->TicketGroup->Row);
			$sfrom = $getTicketsResult->TicketGroup->LowSeat;
			$sthru = $getTicketsResult->TicketGroup->HighSeat;
			$tprice = $getTicketsResult->TicketGroup->ActualPrice;
			$bprice = $getTicketsResult->TicketGroup->WholesalePrice;
			$f_price = (float) $tprice;
			$tprice = number_format($f_price, 2);
			if(is_array($getTicketsResult->TicketGroup->ValidSplits->int)) {
				$vsplits = implode(' ', $getTicketsResult->TicketGroup->ValidSplits->int);
			}
			else {
				$vsplits = $getTicketsResult->TicketGroup->ValidSplits->int;
			}

			$tickets[] = array("TicketID" => "$tid",
                                           "Available" => "$avail",
                                           "SeatSection" => "$sec",
                                           "SeatRow" => "$srow",
                                           "TicketPrice" => "$$tprice",
					   "Descr" => "$sdescr",
					   "SeatFrom" => "$sfrom",
					   "SeatThru" => "$sthru",
					   "ValidSplits" => $vsplits
                                           );

		}
	
#		handle_error_no_exit ("tickets.tnd.php: No result from web services");

	// kill object
	unset($soapclient);

	} # end if $event_found

	$origEventName = $eventName;

	######
	#$categoryID = 15;

	if($eventID != "") {
		$url = make_event_url($eventName);
		$breadcrumb_str = Breadcrumbs($tn_cat_id, 0);
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
	else {
		$meta_descr = "$eventName - Buy $eventName tickets for $shortDate at $venueName in $city, $regionCode at MongoTickets!" ;
		$breadcrumb_str = AppendBreadcrumbNoAnchor($breadcrumb_str, " $eventName Tickets");
		$title = "$gameName Tickets at $venueName $regionCode on $shortDate";
		$heading1 = "$gameName Tickets";
		$subheading = "<strong>$gameName Tickets</strong>";
#		$eventName = $eventName;
	}
	$smarty->assign("MetaDescr", $meta_descr);


	mysql_close($dbh);
}
else
{
		header('HTTP/1.0 500 Internal Server Error');
		handle_error_no_exit ('tickets.tnd.php: I cannot connect to the database because: ' . mysql_error() . ' production id= ' .  $id . ' event id = ' . $eventID . ' uri= ' . $_SERVER['REQUEST_URI'] . ' returning 500');
		$error_message = get_error_message();
		$smarty->assign("ErrorMessage", $error_message);
		$smarty->display('main.tpl');
		$smarty->display('error_page.tpl');
}


$keywords = "";
$keywords = BuildEventKeywordList($eventName, $city);
$keywords = AmpersandToAnd($keywords);
$smarty->assign("SeoKeywords", $keywords);

$smarty->assign("title", $title);


$smarty->display('main.tpl');

$venueUrl = '';
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
	$url = $mapURL;
	$altstring = preg_replace('/\s*&\s*/' , ' and ', $venueName);
	// remove all characters that aren't a-z, 0-9, dash, underscore or space
	$NOT_acceptable_characters_regex = '#[^-a-zA-Z0-9_ ]#';
	$altstring = preg_replace($NOT_acceptable_characters_regex, '', $altstring);

	
	# print_r($tickets);
	$url = htmlspecialchars($url);
	if( $event_found != 0) {
	echo "<div class=\"venueMap\"><strong>$venueName</strong><a href=\"$url\" onclick=\"window.open('$url','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false\" style=\"color: red\">";
	echo "<img height=\"100\" width=\"100\" src=\"$url\" alt=\"$altstring seating chart\"/><br />Click to Enlarge</a>";
	echo "</div>";
	echo "</div>";
	}

?>

<script type="text/javascript" language="javascript">


	function makeGuid(){
		var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
	var guid_length = 5;
	var guid = '';
	for (var i=0; i<guid_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		guid += chars.substring(rnum,rnum+1);
	}
	return guid;}

	function SubmitPurchaseLink(idNum,tgid,price,eid)
	{
		var requestSelect = document.getElementById(idNum);
		var ticketsRequested = requestSelect.options[requestSelect.selectedIndex].text;
        

		var purchaseUrl = 'https://tickettransaction2.com/Checkout.aspx?brokerid=3195&sitenumber=0&tgid=' + tgid + '&treq=' + ticketsRequested+ '&evtID=' + eid + '&price=' + price + '&SessionId=' + makeGuid();
		
			location.href = purchaseUrl;
	}

</script>

<?php

# if( count($tickets) > 0) {
	ticketslist_to_sorted_grouped($sortBy,$sortOrder,$tickets,$startFrom,$eventName,$eventDate,$venueName, $id, $city, $eventID);
	}

#
#
#  } #end if there are tickets
#


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


function ticketslist_to_sorted_grouped($sortBy, $sortOrder, $ticket_list, $startFrom, $eventName,$eventDate,$venueName, $productionID, $city, $eventID)
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
		 	$sorted_array = array_reverse($ticket_list);
		}
		else {
		 	$sorted_array = $ticket_list;
		}

        }
	else {
		 	$sorted_array = $ticket_list;
	}

	echo "\n";
        echo '<table  width="100%" cellspacing="0">';


	$url = make_production_url($eventName, $productionID, $city, $eventTypeID);
	$url_array = explode('?', $url);
	$url = $url_array[0];
	$query_string = "&amp;event_id=$productionID";
	echo "\n<tr class=\"tableHeading\">";
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

		echo "\n";
                if($columnID == 'sec') {
                        echo '<th width="' . $SECTION_WIDTH . '">';
                }
                elseif($columnID == 'pr') {
                        echo '<th width="' . $PRICE_WIDTH . '">';
                }
                else {
                        echo '<th width="' . $ROW_WIDTH . '">';
                }

		$url_sort_params = $url . "?sortBy=$columnID&amp;sortOrder=$nextSortOrder" . $query_string;
                echo '<a href="' . $url_sort_params . "\">$columnHeading</a><img src=\"$arrow\" alt=\"&darr;\"/></th>";

	}
	echo "\n";
        echo '<th width="' . $SEAT_WIDTH . '">Seat No.</th><th width="' . $QUANTITY_WIDTH . '">Quantity</th><th width="' . $BUYNOW_WIDTH . '">&nbsp;&nbsp;</th>';

	echo "\n</tr>";
	echo "\n</table>";
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
                echo '<td width="' . $SECTION_WIDTH . '"><strong>Section: </strong>' . $sorted_array[$j]['SeatSection'] . "\n" . '</td><td width="' . $ROW_WIDTH . '"><strong>Row: </strong>' . $sorted_array[$j]['SeatRow'] . '</td><td width="' . $PRICE_WIDTH . '">' . $sorted_array[$j]['TicketPrice'] . '</td><td width="' . $SEAT_WIDTH . '">' . $sorted_array[$j]['SeatFrom'] . "-" . $sorted_array[$j]['SeatThru'] . '</td>';
		echo '<td width="' . $QUANTITY_WIDTH . '">';
		echo '<select id="Ticks' . $ticket_index . '">';
		$split_ary = explode(' ', $sorted_array[$j]['ValidSplits']);
echo $sorted_array[$j]['ValidSplits']. "<br/>";
		for($i=0; $i < count($split_ary); $i++) {
			echo '<option>' . $split_ary[$i] . '</option>';
		}
		echo '</select>';
		echo '</td>';


		$section_lower = strtolower($sorted_array[$j]['SeatSection']);
		$seat_lower = strtolower($sorted_array[$j]['SeatRow']);
		$ticketid = $sorted_array[$j]['TicketID'];
	# echo "\n<span class=\"tiny\">Up to " . $sorted_array[$j]['Available'] . " Available</span>";
	#	echo "</td>";

#########
# OLD, non-framed
# https://tickettransaction2.com/Checkout.aspx?brokerid=982&sitenumber=6&tgid=455676449&treq=8&evtID=545276&price=187.00&SessionId=JFv2u

	$tprice = substr($sorted_array[$j]['TicketPrice'], 1);
	$session_id = GenerateSessionId();
	$req_quantity = 1;






 echo '<td width="' . $BUYNOW_WIDTH .  '">';

echo '<a target="_self" href="javascript:SubmitPurchaseLink(' . 
"'Ticks$ticket_index', '" . $sorted_array[$j]['TicketID'] . "', '$tprice', '$productionID')" . '">';
echo '<img src="' . $root_url . '/Images/tickets_br.gif" alt="Buy ' . $eventName . " " . $venueName . " Section " . $sorted_array[$j]['SeatSection'] . " Row " . $sorted_array[$j]['SeatRow'] . ' tickets"/></a>';



		echo "\n";
		echo "\n</tr>";
		echo "\n<tr $class>\n<td style=\"text-align=left;\" colspan=\"6\"><span class=\"tiny\">" . utf8_decode($sorted_array[$j]['Descr']) . "</span></td></tr>";
        	echo "\n</table>";

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

        $event_id_string = "&amp;event_id=$prod_id";
	$url_param = "";

	$url_param = "sortBy=$sortBy&amp;sortOrder=$sortOrder";

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

function GenerateSessionId() {

        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $chars_len = strlen($chars);
        $sid_length = 5;
        $sid = '';
        for ($i=0; $i < $sid_length; $i++) {
                $r = (float)rand()/(float)getrandmax();
                $r = (float)($r * $chars_len);
                $rnum = floor($r);
                $sid = $sid .  substr($chars, $rnum, 1);

        }
	return $sid;
}

?>
