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


#echo 'REQUEST_URI=' . $_SERVER['REQUEST_URI'] . '<br/>';
#echo 'QUERY_STRING=' . $_SERVER['QUERY_STRING'] . '<br/>';
$query_string = $_SERVER['QUERY_STRING'];

#$PHP_SELF = $_SERVER['PHP_SELF'];
#if ( empty($PHP_SELF) )
#        $_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);

$requested_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$original = @parse_url($requested_url);

#echo 'requested_url= ' . $requested_url . '<br/>';

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
# /
# /search.html
# remove query string from uri, ?.*
$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];




#echo 'req_uri=' . $req_uri . '<br/>';

preg_match('#^/(.*)$#', $req_uri, $matches);

$req_uri = $matches[1];
$original_uri = $req_uri;

if($req_uri == '') {
	# print main page
}

$sanEventName = '';
$cats = '';
$city = '';
$pid = '';
$venue = '';
$list_productions = 0;
$list_productions_at_venue = 0;
$list_tickets = 0;
$is_category = 0;

$event_pattern = '#^(.*)\/(.+)-tickets\.html#';
#                         categories/Event/City-<PID >.html
$production_pattern = '#^(.*)\/(.*)\/(.+)-(\d+)\.html#';
#                         categories/<event>-at-<venue>.html
$theater_venue_productions_pattern = '#^(.*)\/(.+)-at-(.*)\.html#';
if(preg_match($event_pattern, $req_uri, $matches)) {
	#echo 'is event <br/>';
	$cats = $matches[1];
	$sanEventName = $matches[2];
	$list_productions = 1;
}
elseif(preg_match($production_pattern, $req_uri, $matches)) {
	#echo 'is production <br/>';
	$cats = $matches[1];
	$sanEventName = $matches[2];
	$city = $matches[3];
	$pid = $matches[4];
	$list_tickets = 1;
}
elseif(preg_match($theater_venue_productions_pattern, $req_uri, $matches)) {
	# echo 'is venue productions <br/>';
	$cats = $matches[1];
	$sanEventName = $matches[2];
	$venue = $matches[3];
	$list_productions_at_venue = 1;
}
else {
	#echo 'is category <br/>';
	$pattern  = '#^(.*)$#';
	preg_match($pattern, $req_uri, $matches);
	$cats = $matches[1];
	$is_category = 1;
}
$categories = split('/', trim($cats, '/'));

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ('mongo_tickets2');

	$parentCategoryID=0;
	$parentCategoryName='Home';
	$parentCategoryUrl = '';
	
	foreach($categories as $idx=>$sanCategoryName) {
		if($sanCategoryName == '') {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: http://www.mongotickets.com/' . $parentCategoryUrl);
		}
		else {
			$sanCategoryName = mysql_escape_string($sanCategoryName);
			$query = "SELECT AdjCat.CategoryID,AdjCat.CategoryName, ModCat.CategoryUrl FROM AdjacencyListCategories as AdjCat LEFT JOIN ModifiedPreorderTreeTraversalCategories ModCat ON (AdjCat.CategoryID=ModCat.CategoryID) WHERE AdjCat.SanitizedCategoryName='" . $sanCategoryName . "' AND AdjCat.ParentCategoryID=$parentCategoryID";
			if($query_result = mysql_query($query) ) {
				$num_rows = mysql_num_rows($query_result);
				if($num_rows < 1) {
					#301 redirect to $parentCategory
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: http://www.mongotickets.com/' . $parentCategoryUrl);
				}
				else {
					$table_row = mysql_fetch_row($query_result);
					$parentCategoryID= $table_row[0];
					$parentCategoryName=$table_row[1];
					$parentCategoryUrl=$table_row[2];
				}
			}
		}
	}
        $Bsql = "SELECT CategoryUrl FROM ModifiedPreorderTreeTraversalCategories " .
                " WHERE CategoryID = $parentCategoryID";
        $result = mysql_query($Bsql);
        while ($row = mysql_fetch_array($result)) {
                $made_url = $row['CategoryUrl'];
        }
	if($is_category && ($req_uri != $made_url)) {
#echo '<br/> URL DOESNT Match, redirect to ' . $made_url. '<br/>';
		# 301 redirect
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://www.mongotickets.com/' . $made_url);

	}
	# make_category url and compare against req_uri
	if($sanEventName != '') {
		$sanEventName = mysql_escape_string($sanEventName);
                $query = "SELECT EventID,EventTypeID, EventName FROM Events WHERE SanitizedEventName='" . $sanEventName . "' AND CategoryID=" . $parentCategoryID;
# echo "<br/>$query";
                if($query_result = mysql_query($query) ) {
                        $num_rows = mysql_num_rows($query_result);
                        if($num_rows < 1) {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: http://www.mongotickets.com/' . $parentCategoryUrl);
                        }
                        else {
                                $table_row = mysql_fetch_row($query_result);
                                $eventID = $table_row[0];
                                $eventName = $table_row[2];
				if($list_tickets == 1) {
				 	$_REQUEST['id'] = $pid;
					mysql_close($dbh);
					require('tickets.php');
				}
				elseif($list_productions_at_venue == 1) {
					$_REQUEST['id'] = $eventID;
					$_REQUEST['vid'] = $venue;
					#$_REQUEST['vid'] = $matches[1];
					$_REQUEST['name'] = $eventName;
					$_REQUEST['catid'] = $parentCategoryID;
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
                }
 	}
	else {
		$is_category= 1;
		$_REQUEST['id'] = $parentCategoryID;
		$_REQUEST['name'] = $parentCategoryName;
		mysql_close($dbh);
		require('category.php');
		exit();
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

?>
