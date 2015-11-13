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

# /sports/<category>/<subcat>/team-tickets.html      OK
# /sports/mlb/al/team-tickets.html/	301 redirect to *.html
# /sports/mlb/al/team/city-<PID>.html	OK, do special processing
# /theater/<category>/<event>/<event>-at-<venue>.html
# /venues/<region code>/<venue name>.html OK
# /venues/<region code>/                OK Display venues for region code
# /sports/mlb/al/  			OK
# /sports/mlb/al   			301 redirect to /sports/mlb/al/
# /search.html
# remove query string from uri, ?.*
$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];


preg_match('#^/(.*)$#', $req_uri, $matches);

$req_uri = $matches[1];
$original_uri = $req_uri;

if($req_uri == '') {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://www.mongotickets.com/');
	exit();
}

$sanEventName = '';
$cats = '';
$city = '';
$pid = '';
$venue = '';
$action = ''; # list productions, list productions at venue, list tickets

$event_pattern = '#^(.*)\/(.+)-tickets\.html#';
#                         categories/<event>/<city>-<PID>.html
$production_pattern = '#^(.*)\/(.*)\/(.+)-(\d+)\.html#';
$production_no_city_pattern = '#^(.*)\/(.*)\/(\d+)\.html#';
#                         categories/<event>-at-<venue>.html
$theater_venue_productions_pattern = '#^(.*)\/(.+)-at-(.*)\.html#';
if(preg_match($event_pattern, $req_uri, $matches)) {
	#echo 'is event <br/>';
	$cats = $matches[1];
	$sanEventName = $matches[2];
	$action = 'list productions';
}
elseif(preg_match($production_pattern, $req_uri, $matches)) {
	#echo 'is production <br/>';
	$cats = $matches[1];
	$sanEventName = $matches[2];
	$city = $matches[3];
	$pid = $matches[4];
	$action = 'list tickets';
}
elseif(preg_match($production_no_city_pattern, $req_uri, $matches)) {
        #echo 'is production <br/>';
        $cats = $matches[1];
        $sanEventName = $matches[2];
        $pid = $matches[3];
        $action = 'list tickets';
}
elseif(preg_match($theater_venue_productions_pattern, $req_uri, $matches)) {
	# echo 'is venue productions <br/>';
	$cats = $matches[1];
	$sanEventName = $matches[2];
	$venue = $matches[3];
	$action = 'list productions at venue';
}
else {
	# echo 'is category <br/>';
	$pattern  = '#^(.*)$#';
	preg_match($pattern, $req_uri, $matches);
	$cats = $matches[1];
	$is_category = 1;
}

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ('mongo_tickets2');

	if(strlen($sanEventName) > 0) {
		$sanEventName = mysql_escape_string($sanEventName);
                $query = "SELECT EventID, EventName, CategoryID FROM Events WHERE SanitizedEventName='" . $sanEventName . "'";
                if($query_result = mysql_query($query) ) {
                        $num_rows = mysql_num_rows($query_result);
                        $eventDetails = Array();
# echo $num_rows . ' ' . $sanEventName; exit();
                        if ($num_rows == 1) {
                                $table_row = mysql_fetch_row($query_result);
                                $eventID = $table_row[0];
                                $eventName = $table_row[1];
                                $categoryID = $table_row[2];
			}
			elseif ($num_rows > 1) {
				# go through category and find parent of event
				# if can't find parent redirect to closest category
				$event_details = resolve_ambiguous_event($smarty, $cats, $sanEventName);
				$eventID = $event_details['eventID'];
				$categoryID= $event_details['categoryID'];
			}
			else  { # ($num_rows < 1)
				# go through category and find parent of event
				# if can't find parent redirect to closest category
                                $categoryInfo = fixup_url($smarty, $cats);
				header('HTTP/1.1 301 Moved Permanently');
                                header('Location: http://www.mongotickets.com/' . $categoryInfo['categoryUrl']);
				exit();
			}
			# url fix beyond this point is the responsibility of the respective script
			if($action == 'list tickets') {
			 	$_REQUEST['production_id'] = $pid;
                                $_REQUEST['event_id'] = $eventID;
                                $_REQUEST['category_id'] = $categoryID;
				mysql_close($dbh);
				require('tickets.php');
			}
			elseif($action == 'list productions at venue') {
# echo 'here';
				$_REQUEST['id'] = $eventID;
				$_REQUEST['vid'] = $venue;
				$_REQUEST['name'] = $eventName;
				$_REQUEST['catid'] = $categoryID;
				mysql_close($dbh);
				require('tickets_for_venue.php');
			}
			else {
				$_REQUEST['id'] = $eventID;
				mysql_close($dbh);
				require('productions.php');
			}
			exit();
                }
		else {
       			# 5xx status code
       			header('HTTP/1.0 500 Internal Server Error');
     			handle_error_no_exit ('ticket_dispatch.code: Events database event query failed: ' . mysql_error());
     			$error_message = get_error_message();
       			$smarty->assign("ErrorMessage", $error_message);
     			$smarty->display('main.tpl');
       			$smarty->display('error_page.tpl');
 		}
	}
	else {
		$sanCategoryUrl = mysql_escape_string($cats);
        	$query = "SELECT CategoryID, CategoryName FROM ModifiedPreorderTreeTraversalCategories " . " WHERE CategoryUrl ='$sanCategoryUrl'";
                if($query_result = mysql_query($query) ) {
                        $num_rows = mysql_num_rows($query_result);
                        if ($num_rows == 1) {
                                $table_row = mysql_fetch_row($query_result);
				$_REQUEST['id'] = $table_row[0];
				$_REQUEST['name'] = $table_row[1];
				mysql_close($dbh);
				require('category.php');
				exit();
			}
			else {
				$categoryInfo = fixup_url($smarty, $cats);
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: http://www.mongotickets.com/' . $categoryInfo['categoryUrl']);
				exit();
			}
		}
		else {
       			# 5xx status code
       			header('HTTP/1.0 500 Internal Server Error');
     			handle_error_no_exit ('ticket_dispatch.code: ModifiedPreorderTreeTraversalCategories database query failed: ' . mysql_error());
     			$error_message = get_error_message();
       			$smarty->assign("ErrorMessage", $error_message);
     			$smarty->display('main.tpl');
       			$smarty->display('error_page.tpl');
 		}
	}

	mysql_close($dbh);
}
else {
       # 5xx status code
        header('HTTP/1.0 500 Internal Server Error');
        handle_error_no_exit ('ticket_dispatch.code: I cannot connect to the database because: ' . mysql_error());
        $error_message = get_error_message();
        $smarty->assign("ErrorMessage", $error_message);
        $smarty->display('main.tpl');
        $smarty->display('error_page.tpl');
}

function fixup_url($smarty, $cats) {

	$categoryInfo = Array();
	$parentCategoryID=0;
	$parentCategoryName='Home';
	$parentCategoryUrl = '';
	$categories = split('/', trim($cats, '/'));
	foreach($categories as $idx=>$sanCategoryName) {
		if($sanCategoryName == '') {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: http://www.mongotickets.com/' . $parentCategoryUrl);
			exit();
		}
		else {
			$sanCategoryName = mysql_escape_string($sanCategoryName);
#echo $sanCategoryName . '<br/>';
			$query = "SELECT AdjCat.CategoryID,AdjCat.CategoryName, ModCat.CategoryUrl FROM AdjacencyListCategories as AdjCat LEFT JOIN ModifiedPreorderTreeTraversalCategories ModCat ON (AdjCat.CategoryID=ModCat.CategoryID) WHERE AdjCat.SanitizedCategoryName='" . $sanCategoryName . "' AND AdjCat.ParentCategoryID=$parentCategoryID";
			if($query_result = mysql_query($query) ) {
				$num_rows = mysql_num_rows($query_result);
				if($num_rows < 1) {
					# could be event due to url = /category/<event> or /category/<event>/
                			$query = "SELECT EventName, CategoryID FROM Events WHERE SanitizedEventName='" . $sanCategoryName . "'";
					if($query_result = mysql_query($query) ) {
						$num_rows = mysql_num_rows($query_result);
						if ($num_rows == 1) {
							$table_row = mysql_fetch_row($query_result);
							$url = make_event_url($table_row[0], $table_row[1]);
							$parentCategoryUrl = ltrim  ( $url, '/');
						}
					}
					#301 redirect to $parentCategory
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: http://www.mongotickets.com/' . $parentCategoryUrl);
					exit();
				}
				else {
					$table_row = mysql_fetch_row($query_result);
					$parentCategoryID= $table_row[0];
					$parentCategoryName=$table_row[1];
					$parentCategoryUrl=$table_row[2];
				}
			}
			else {
       				# 5xx status code
       				header('HTTP/1.0 500 Internal Server Error');
     				handle_error_no_exit ('ticket_dispatch.code: fixup_url database query failed: ' . mysql_error());
     				$error_message = get_error_message();
       				$smarty->assign("ErrorMessage", $error_message);
     				$smarty->display('main.tpl');
       				$smarty->display('error_page.tpl');
 			}
		}
	}
	$categoryInfo['categoryID'] = $parentCategoryID;
	$categoryInfo['categoryName'] = $parentCategoryName;
	$categoryInfo['categoryUrl'] = $parentCategoryUrl;

	return $categoryInfo;
	
}

function resolve_ambiguous_event($smarty, $cats, $sanEventName) {

	$categoryInfo = fixup_url($smarty, $cats);
	$query = "SELECT EventID, EventName, CategoryID FROM Events WHERE SanitizedEventName='" . $sanEventName . "' AND " .
		"CategoryID=" . $categoryInfo['categoryID'];
	if($query_result = mysql_query($query) ) {
		$num_rows = mysql_num_rows($query_result);
		if ($num_rows == 1) {
			$table_row = mysql_fetch_row($query_result);
                        $eventDetails['eventID'] = $table_row[0];
			$eventDetails['eventName'] = $table_row[1];
			$eventDetails['categoryID'] = $table_row[2];
		}
		## put redirect or something here
	}
	else {
       		# 5xx status code
       		header('HTTP/1.0 500 Internal Server Error');
     		handle_error_no_exit ('ticket_dispatch.code: resolve_ambiguous_event query failed: ' . mysql_error());
     		$error_message = get_error_message();
       		$smarty->assign("ErrorMessage", $error_message);
     		$smarty->display('main.tpl');
       		$smarty->display('error_page.tpl');
	}
	return $eventDetails;
}

?>
