<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
require('../include/breadcrumbs.inc.php');
require('../include/event_paragraph.inc.php');

#                              sid => did
$removed_categories_redir = Array(
				16 => 15,
				17 => 15,
				18 => 15,
				20 => 19,
				21 => 19,
				22 => 19,
				26 => 25,
				27 => 25,
				28 => 25,
				29 => 25,
				31 => 30,
				32 => 30,
				33 => 30,
				34 => 30,
			      1510 => 253,
				 7 => 66,
				48 => 13,
				35 => 13,
				 6 => 156,
				 5 => 1530,
				 6 => 1530,
				 9 => 1528,
				68 => 67,
				69 => 67,
				70 => 67,
				71 => 67,
				73 => 72,
				74 => 72,
				75 => 72,
				76 => 72,
				77 => 72,
				78 => 72,
			);

$home_only = -1;
if(isset($_GET["home_only"]) && (($_GET["home_only"] == '0') || ($_GET["home_only"] == '1') || ($_GET["home_only"] == '2'))) {
	$home_only = $_GET["home_only"];
}

echo 'REQUEST_URI=' . $_SERVER['REQUEST_URI'] . '<br/>';
echo 'QUERY_STRING=' . $_SERVER['QUERY_STRING'] . '<br/>';

$PHP_SELF = $_SERVER['PHP_SELF'];
if ( empty($PHP_SELF) )
        $_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);

$requested_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$original = @parse_url($requested_url);

echo 'requested_url= ' . $requested_url . '<br/>';

// Some PHP setups turn requests for / into /index.php in REQUEST_URI
#$original['path'] = preg_replace('|/index\.php$|', '/', $original['path']);
$redirect = $original;
$redirect_url = false;

# /sports/mlb/al/team-tickets.html      OK
# /sports/mlb/al/team-tickets.html/	301 redirect to *.html
# /sports/mlb/al/  			OK
# /sports/mlb/al   			301 redirect to /sports/mlb/al/
# /
# /search.html
# remove query string from uri, ?.*
$req_uri= $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];




echo 'req_uri=' . $req_uri . '<br/>';

$event_pattern = '#^(.*)\/(.+)-tickets\.html#';
preg_match('#^/(.*)$#', $req_uri, $matches);

$req_uri = $matches[1];
$original_uri = $req_uri;

if($req_uri == '') {
	# print main page
}

if(preg_match('#^(.*)\.html$#', $req_uri, $matches)) {
	echo 'is event <br/>';
	$pattern  = $event_pattern;
}
else {
	echo 'is category <br/>';
	$pattern  = '#^(.*)$#';

}
preg_match($event_pattern, $req_uri, $matches);
echo 'original_uri= ' . $original_uri . '<br/>';
preg_match($pattern, $req_uri, $matches);
echo 'matches ';
print_r($matches);
$sanEventName = $matches[2];
echo '<br/>sanEventName= ' . $sanEventName . '<br/>';
$cats = $matches[1];
$categories = split('/', trim($cats, '/'));
echo '<br/>';
print_r($categories);


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ("mongo_tickets2");

	$parentCategoryID=0;
	$parentCategoryName='Home';
	
#	$sanEventName = $1;
	foreach($categories as $idx=>$sanCategoryName) {
		if($sanCategoryName == '') {
			# 404 error
			echo '<br/> Header: 404';
		}
		else {
		   # Shall redirect requests for removed directories to approapriate URLs
		   #  e.g. /category/Central-Tickets-C17.html shall be redirected to
		   #       /sports/mlb/al/
		   #       17 => 15
		   #       18 => 15, etc.
		   #
		   # the following should not be a concern since it will be detected
		   #  e.g. /category/American-League-Tickets-C15.html shall be redirected to
		   #      /sports/mlb/al/
		   #      which will be easy to detect when requested URL does not match gener URL
			$sanCategoryName = mysql_escape_string($sanCategoryName);
			$query = "SELECT CategoryID FROM AdjacencyListCategories WHERE SanitizedCategoryName='" . $sanCategoryName . "'";
			if($query_result = mysql_query($query) ) {
				$num_rows = mysql_num_rows($query_result);
				if($num_rows < 1) {
					#301 redirect to $parentCategoryID
					echo '<h1>Invalid Category in URL: ' . $sanCategoryName . '</h1><br/>';
               	                 echo "<h2>Redirect to category $parentCategoryName $parentCategoryID</h2><br/>";
				}
				else {
					$table_row = mysql_fetch_row($query_result);
					$parentCategoryID= $table_row[0];
					$parentCategoryName=$sanCategoryName;
				}
			}
		}
	}
	if($sanEventName != '') {
		$sanEventName = mysql_escape_string($sanEventName);
                $query = "SELECT EventID,EventTypeID FROM Events WHERE SanitizedEventName='" . $sanEventName . "' AND CategoryID=" . $parentCategoryID;
echo "<br/>$query";
                if($query_result = mysql_query($query) ) {
                        $num_rows = mysql_num_rows($query_result);
                        if($num_rows < 1) {
                                #301 redirect to $parentCategoryID
                                echo "<h1>Invalid Event $sanEventName $parentCategoryID</h1><br/>";
                                echo "<h2>Redirect to category $parentCategoryName $parentCategoryID</h2><br/>";
                        }
                        else {
                                $table_row = mysql_fetch_row($query_result);
                                $eventID= $table_row[0];
		echo '<br/>EventTypeID = ' . $table_row[1]; 
				echo '<h1>Good event id=' . $eventID . '</h1>';
                        }
                }
		# all is good, check QUERY_STRING to determine action.
		# if QUERY_STRING contains pid=
		#     display tickets - same for all EventTypeIDs
		#     additional parameters might be present for sorting
		# elseif EventTypeID == 4 (theater)
		#     if vid=
		#          display productions
		#     else
		#          display venues
		#     endif
		# endif

 	}
	else {
		$is_category= 1;
		echo '<h1>Category id ' . $cats . ' are valid</h1><h2>Display events in this cat</h2>';
		# there shall be no query string

	}
	mysql_close($dbh);
}
else {
}

?>
