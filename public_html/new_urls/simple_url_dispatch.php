<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


include('../../include/host_info.inc.php');
include('../../include/domain_info.inc.php');
require_once('../../include/new_urls/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../../include/error.php');

$query_string = $_SERVER['QUERY_STRING'];

$requested_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$original = @parse_url($requested_url);

// Some PHP setups turn requests for / into /index.php in REQUEST_URI
#$original['path'] = preg_replace('|/index\.php$|', '/', $original['path']);
$redirect = $original;
$redirect_url = false;

$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];

$production_id_given = 0;
if(isset($req_uri_array[1])) {
	$query = $req_uri_array[1];
	if(preg_match('#event_id#', $query, $matches)) {
		$production_id_given = 1;
		#echo 'is production';
	}
}


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

# /<category name>-tickets/ 
# /<event name>-tickets/   
# /<venue name>-tickets/   
# /<venue name>-event-tickets/   
# /<event name>-tickets/?event_id=XXXX
# /<theater event name>-tickets-<city>/
# remove query string from uri, ?.*

$common_pattern = '#^(.+)-tickets\/#';
$ambiguous_venue_name_pattern = '#^(.+)-event-tickets\/#';
$theater_venue_productions_pattern = '#^(.+)-tickets-(.+)\/#';
if(preg_match($common_pattern, $req_uri, $matches)) {
	# echo ' ' . $matches[1] . ' tickets<br/>';
	$name_in_url = $matches[1];
	$subject = 'common';
}
elseif(preg_match($ambiguous_venue_name_pattern, $req_uri, $matches)) {
	 echo ' ' . $matches[1] . ' tickets<br/>';
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
                $query = "SELECT TableID, ID FROM UrlLookup_temp WHERE Url='" . $name_in_url . "'";
                if($query_result = mysql_query($query) ) {
                        $num_rows = mysql_num_rows($query_result);
# echo $num_rows . ' ' . $sanEventName; exit();
                        if ($num_rows == 1) {
                                $table_row = mysql_fetch_row($query_result);
                                $tableID = $table_row[0];
                                $ID = $table_row[1];
				switch ($tableID) {
					case 1:
                			$query = "SELECT EventName, EventID FROM Events WHERE EventID='$ID'";
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
				# echo $query;

				if($query_result = mysql_query($query) ) {
					$num_rows = mysql_num_rows($query_result);
					 # echo 'Event query ' . $num_rows . ' ' . $sanEventName; exit();
					if ($num_rows == 1) {
						$table_row = mysql_fetch_row($query_result);
						$name = $table_row[0];
						$id = $table_row[1];
					}
					else  {
     						handle_error_no_exit ('simple_url_dispatch.php: ' . $type . ' table lookup returned wrong number of results ' . $_SERVER['REQUEST_URI'] . ' returning 301');
        					redir_301();
					}
					# echo $name . ' ' . $id ;
				}
				else {
        				redir_301();
				}
			}
			else  { # ($num_rows < 1)
     				handle_error_no_exit ('simple_url_dispatch.php: UrlLookup returned wrong number of results ' . $_SERVER['REQUEST_URI'] . ' returning 301');
        			redir_301();
			}
			if(!$production_id_given) {
				switch ($type) {
					case 'event':
						if($sanCity == '') {
							# event id
							$_REQUEST['event_id'] = $id;
							mysql_close($dbh);
							require('productions.php');
						}
						else {
							$_REQUEST['san_city'] = $sanCity;
							$_REQUEST['event_id'] = $id;
							mysql_close($dbh);
							require('tickets_for_venue.php');
						}
					break;
					case 'category':
						$_REQUEST['category_id'] = $id;
						$_REQUEST['name'] = $name;
						mysql_close($dbh);
						require('category.php');
						exit();
					break;
					case 'venue':
						$_REQUEST['venue_id'] = $id;
						mysql_close($dbh);
						require('venues.php');
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
				# echo $_REQUEST['production_id'] . ' ' .$_REQUEST['event_id'];
						mysql_close($dbh);
						require('tickets.php');
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
     			handle_error_no_exit ('simple_url_dispatch.php: UrlLookup_temp query failed: ' . $_SERVER['REQUEST_URI'] . ' ' . mysql_error());
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


?>
