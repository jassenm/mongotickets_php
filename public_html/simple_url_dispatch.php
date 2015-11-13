<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require('DbUtils.new_urls.php');
require('Utils.php');
include('../include/error.php');


$query_string = $_SERVER['QUERY_STRING'];
#echo 'this: ' . $query_string  . ' ';

$requested_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$original = @parse_url($requested_url);
#echo '$requested_url ' . $requested_url . "\n";

// Some PHP setups turn requests for / into /index.php in REQUEST_URI
#$original['path'] = preg_replace('|/index\.php$|', '/', $original['path']);
$redirect = $original;
$redirect_url = false;

$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];


if(isset($req_uri_array[1]))
{
	$qs = $req_uri_array[1];
}
else
{
	$qs = '';
}
$first_uri = $req_uri;

$production_id_given = 0;
if(isset($req_uri_array[1])) {
	$query = $req_uri_array[1];
	if(preg_match('#event_id#', $query, $matches)) {
		$production_id_given = 1;
		#echo 'is production';
	}
}

# echo $req_uri;

preg_match('#^/(.*)$#', $req_uri, $matches);

$req_uri = $matches[1];
$original_uri = $req_uri;


if($req_uri == '') {
	handle_error_no_exit ('simple_url_dispatch.php: empty url ' . $_SERVER['REQUEST_URI'] . ' returning 301');
	redir_301();
}

$sanEventName = '';
$city = '';
$sanCity = '';



$test_pattern = '#^testing123-tickets\/#';
if(preg_match($test_pattern, $req_uri, $matches)) {

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);
$_REQUEST['san_city'] = $sanCity;


	$answer = check_TND_Theater_event(4167, 'Wicked', 1109, 'chicago', &$tndEventID, &$tndVenueID);
  $_REQUEST['tnd_event_id'] = $tndEventID;
  $_REQUEST['tnd_venue_id'] = $tndVenueID;
  $_REQUEST['cat_id'] = 1109;
  $_REQUEST['tn_event_id'] = 4167;

}

  require('tickets_for_venue.tnd.php');
}

# /<category name>-tickets/ 
# /<event name>-tickets/   
# /<venue name>-tickets/   
# /<venue name>-event-tickets/   
# /<event name>-tickets/?event_id=XXXX
# /<theater event name>-tickets-<city>/
# remove query string from uri, ?.*
$no_trailing_slash_pattern = '#^(.+)-tickets$#';
if(preg_match($no_trailing_slash_pattern, $req_uri, $matches)) {
	$url = ltrim($first_uri,'/');
	$url = $url . '/';
	redir_301($url);
}


#$no_trailing_slash_theater_venue_pattern = '#^(.+)-tickets-(.+)$#';
#if(preg_match($no_trailing_slash_theater_venue_pattern, $req_uri, $matches)) {
#	$url = ltrim($first_uri,'/');
#	$url = $url . '/';
#	redir_301($url);
#}

$common_pattern = '#^(.+)-tickets\/#';
$ambiguous_venue_name_pattern = '#^(.+)-event-tickets\/#';
$theater_venue_productions_pattern = '#^(.+)-tickets-(.+)\/#';
if(preg_match($common_pattern, $req_uri, $matches)) {
	# echo ' ' . $matches[1] . ' tickets<br/>';
	$name_in_url = $matches[1];
	$subject = 'common';
}
elseif(preg_match($ambiguous_venue_name_pattern, $req_uri, $matches)) {
	# echo ' ' . $matches[1] . ' tickets<br/>';
	$name_in_url = $matches[1];
	$subject = 'common';
}
elseif(preg_match($theater_venue_productions_pattern, $req_uri, $matches)) {
	#echo 'is theater_venue_productions_pattern <br/>';
	$name_in_url = $matches[1];
	$sanCity = $matches[2];
	$subject = 'theater event at venue';
# echo $name_in_url . ' ' . $sanCity;
}
else {
	handle_error_no_exit ('simple_url_dispatch.php: url does not match pattern' . $_SERVER['REQUEST_URI'] . ' returning 301');
        redir_301();
}







if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ($db_name);

	if(strlen($name_in_url) > 0) {
		$sanEventName = mysql_escape_string($name_in_url);
                $query = "SELECT TableID, ID FROM UrlLookup WHERE Url='" . $name_in_url . "'";
                if($query_result = mysql_query($query) ) {
                        $num_rows = mysql_num_rows($query_result);
# echo $num_rows . ' ' . $sanEventName; exit();
                        if ($num_rows == 1) {
                                $table_row = mysql_fetch_row($query_result);
                                $tableID = $table_row[0];
                                $ID = $table_row[1];
				switch ($tableID) {
					case 1:
                			$query = "SELECT EventName, EventID, CategoryID, EventTypeID FROM Events WHERE EventID='$ID'";
					$type = 'event';
					break;
					case 2:
                			$query = "SELECT VenueName, VenueID FROM Venues WHERE VenueID='$ID'";
					$type = 'venue';
					break;
					case 3:
                			$query = "SELECT CategoryName, CategoryID FROM AdjacencyListCategories WHERE CategoryID='$ID'";
					$type = 'category';
					break;
					default:
						handle_error_no_exit ('simple_url_dispatch.php: invalid table type returned ' . $_SERVER['REQUEST_URI'] . ' returning 301');
        					redir_301();
					break;
				}
			#	 echo $query;exit;

				if($query_result = mysql_query($query) ) {
					$num_rows = mysql_num_rows($query_result);
					#  echo 'Event query ' . $num_rows . ' ' . $sanEventName; exit;
					if ($num_rows == 1) {
						$table_row = mysql_fetch_row($query_result);
						$name = $table_row[0];
						$id = $table_row[1];
						if($type == 'event') {
							$eventCategoryID = $table_row[2];
							$eventTypeID = $table_row[3];
						}
					}
					else  {
					 # echo "ERROR: name " . $name . ' ' . $id ; exit;
     						handle_error_no_exit ('simple_url_dispatch.php: ' . $type . ' table lookup returned wrong number of results ' . $_SERVER['REQUEST_URI'] . ' returning 301');
        					redir_301();
					}
					# echo "name " . $name . ' ' . $id ; exit;
				}
				else {
        				redir_301();
				}
			}
			else  { # ($num_rows < 1)
     				handle_error_no_exit ('simple_url_dispatch.php: UrlLookup returned wrong number of results ' . $_SERVER['REQUEST_URI'] . ' returning 301' . ' name_in_url=' . $name_in_url . ' sanitized event name = ' . $sanEventName);
        			redir_301();
			}
			if(!$production_id_given) {
				#echo $type; exit;
				switch ($type) {
					case 'event':
						#echo ' ' . $sanCity; exit;
						if($sanCity == '') {
 							$inTND = 0;
                                                        $inTND = check_TND_event($name, $eventCategoryID, $tndEventID);
						#echo ' ' . $sanCity; exit;
							# event id
							$_REQUEST['event_id'] = $id;
							mysql_close($dbh);
							if($inTND) {
								$_REQUEST['cat_id'] = $eventCategoryID;
							 	require('productions.tnd.works.php');
							 }
							 else {
						#echo 'calling productions.new_urls.php'; exit;
								require('productions.new_urls.php');
							 }
						}
						else {
							$_REQUEST['san_city'] = $sanCity;
							$_REQUEST['event_id'] = $id;
							$inTND = 0;
							$eventName = $name;
							if($id == 4167) {
                                                                if($sanCity == '
new-york') {
                $eventCategoryID = 9999;

        }

								$inTND = check_TND_Theater_event($id, $eventName, $eventCategoryID, $sanCity, &$tndEventID, &$tndVenueID);

								if($sanCity == 'new-york') {
		$eventCategoryID = 1109;
	}




							}
							mysql_close($dbh);
							if($inTND) {
							 $_REQUEST['tnd_event_id'] = $tndEventID;
							 $_REQUEST['tnd_venue_id'] = $tndVenueID;
							 $_REQUEST['cat_id'] = $eventCategoryID;
							 $_REQUEST['tn_event_id'] = $id;
							 require('tickets_for_venue.tnd.php');
							 }
							else {
							require('tickets_for_venue.new_urls.php');
							}
						}
					break;
					case 'category':
						$_REQUEST['category_id'] = $id;
						$_REQUEST['name'] = $name;
						mysql_close($dbh);
						require('category.new_urls.php');
						exit();
					break;
					case 'venue':
						$_REQUEST['venue_id'] = $id;
						mysql_close($dbh);
						require('venues.new_urls.php');
						exit();
					break;
					default:
						handle_error_no_exit ('simple_url_dispatch.php: invalid table type returned ' . $_SERVER['REQUEST_URI'] . ' returning 301');
						redir_301();
						break;
				}
			}
			else {
				switch ($type) {
					case 'event':
                        	       	 	$_REQUEST['production_id'] = $_REQUEST['event_id'];
                        	       	 	$_REQUEST['event_id'] = $id;
						$_REQUEST['san_city'] = $sanCity;
						$inTND = 0;
			
						if ($eventTypeID == 4) {
							if( $id==4167) {
								if($sanCity == 'new-york') {
		$eventCategoryID = 9999;
	}
								$inTND = check_TND_Theater_event($id, $name, $eventCategoryID, $sanCity, &$tndEventID, &$tndVenueID);
								if($sanCity == 'new-york') {
		$eventCategoryID = 1109;
	}
							}
	}
						else {
						$inTND = check_TND_event($name, $eventCategoryID, $tndEventID);
						}

				# echo $_REQUEST['production_id'] . ' ' .$_REQUEST['event_id'];
						mysql_close($dbh);
						 if($inTND) {
                        	       	 		$_REQUEST['event_id'] = $tndEventID;
                        	       	 		$_REQUEST['tn_event_id'] = $id;
							$_REQUEST['cat_id'] = $eventCategoryID;
							require('tickets.tnd.php');
						 }
						 else {
						require('tickets.new_urls.php');
						 }
					break;
					default:
						handle_error_no_exit ('simple_url_dispatch.php: production id provided to non-event url ' . $_SERVER['REQUEST_URI'] . ' returning 301');
						redir_301();
					break;
				}
				# handle_error_no_exit ('simple_url_dispatch.php: should not get here ' . $_SERVER['REQUEST_URI'] . ' returning 301');
				# redir_301();
				exit();
			}
                }
		else {
       			# 5xx status code
       			header('HTTP/1.0 500 Internal Server Error');
     			handle_error_no_exit ('simple_url_dispatch.php: UrlLookup query failed: ' . $_SERVER['REQUEST_URI'] . ' ' . mysql_error());
     			$error_message = get_error_message();
       			$smarty->assign("ErrorMessage", $error_message);
     			$smarty->display('main.tpl');
       			$smarty->display('error_page.tpl');
 		}
	}
	else {
     		handle_error_no_exit ('simple_url_dispatch.php: no event, category, or venue in URL ' . $_SERVER['REQUEST_URI'] . ' returning 301');
        	redir_301();
	}

	mysql_close($dbh);
}
else {
       # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
        handle_error_no_exit ('simple_url_dispatch.php: I cannot connect to the database because: ' . mysql_error());
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('main.tpl');
        $smarty->display('error_page.tpl');
}

function check_TND_event($eventName, $eventCategoryID, &$tndEventID) {

        $bsql = "SELECT TNDProductions.ProductionID, EventID FROM TNDProductions inner join TNDEventPerformers on (TNDProductions.ProductionID=TNDEventPerformers.ProductionID) inner join TnToTndCategoryID ON (TnToTndCategoryID.TndCategoryID=TNDProductions.ChildCategoryID) where TNDEventPerformers.EventName='" . $eventName . "' AND TnCategoryID=" . $eventCategoryID . " AND DATEDIFF(NOW(), ProductionDate) <= 0";

        $num_productions = 0;
        $count = 0;
        if($query_result = mysql_query($bsql)) {
          while ($table_row = mysql_fetch_row($query_result)) {
                $productionID = $table_row[0];
                $tndEventID = $table_row[1];
                $num_productions++;
          }
        }

        if($num_productions > 0) {
                return 1;
        }
        else {
                return 0;
        }
}


function check_TND_Theater_event($tn_event_id, $eventName, $eventCategoryID, $sanCity, &$tndEventID, &$tndVenueID) {

	$return_code = 0;

# check_TND_Theater_event(1455, 'Wayne Brady', 1103, 'las-vegas', &$tndEventID, &$tndVenueID);

        $bsql = "SELECT City FROM Productions LEFT JOIN (Venues)  ON (Venues.VenueID = Productions.VenueID) where Productions.EventID= " . $tn_event_id . " AND Venues.SanitizedCity = '" . $sanCity . "' AND DATEDIFF(NOW(), EventDate) <= 0 LIMIT 1";

        $venue_found = 0;
        if($query_result = mysql_query($bsql)) {
          while ($table_row = mysql_fetch_row($query_result)) {
                $venue_found = 1;
		$city = $table_row[0];
          }
        }

	if($venue_found > 0) {
        	$bsql = "SELECT EventID, VenueID FROM TNDProductions inner join TNDEventPerformers on (TNDProductions.ProductionID=TNDEventPerformers.ProductionID) inner join TnToTndCategoryID ON (TnToTndCategoryID.TndCategoryID=TNDProductions.ChildCategoryID) where TNDEventPerformers.EventName='" . $eventName . "' AND TnCategoryID=" . $eventCategoryID . " AND City='" . $city . "' AND DATEDIFF(NOW(), ProductionDate) <= 0 LIMIT 1";

        	$num_productions = 0;
        	$count = 0;
        	if($query_result = mysql_query($bsql)) {
          		while ($table_row = mysql_fetch_row($query_result)) {
                		$tndEventID = $table_row[0];
                		$tndVenueID = $table_row[1];
                		$num_productions++;
          		}
        	}

        	if($num_productions > 0) {
                	$return_code = 1;
        	}
        	else {
                	$return_code = 0;
        	}
	}
	return $return_code;
}


?>
