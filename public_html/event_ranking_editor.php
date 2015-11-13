<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#



include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');
include_once('../include/login.inc.php');


session_start();

if (!isset($_SESSION['uid']) ) {
	session_defaults();
}

if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ('mongo_tickets2');

	if ($_SESSION['logged']) {
		_checkSession();
	} elseif ( isset($_COOKIE['mtwebLogin']) ) {
		_checkRemembered($_COOKIE['mtwebLogin']);
	} else {
                mysql_close($dbh);
		header("Location: http://www.mongotickets.com/log_me_in.php");
		exit;
	}
	

}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}



?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Event Ranking Editor</title>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
        <meta name="robots" content="noindex, nofollow" />

</head>
<body>
<h2><a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=0">Category Editor</a></h2>
<a href="http://www.mongotickets.com/log_me_out.php">Logout</a>


<h2><a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=0">All Events</a>&nbsp<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=3">Sports Events</a>&nbsp;<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=2">Concert Events</a>&nbsp;<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=4">Theater Events</a>&nbsp;<a href="http://www.mongotickets.com/event_ranking_editor.php?top10=1">Theater Events</a></h2>


<table>
<form action="event_ranking_editor.php" method="get">
<tr>
	<th align="left"><strong>Keyword:</strong></th>
</tr>
<tr>
	<td align="left" width="55"><input type="text" name="keywords" id="keywords" size="55" value=""></td>
	<td> <input type="submit" value="Go" /> </td>
</tr>

</form>
</table>

<?php
$start=1;
$all_events = 0;
$is_sort_order_given = 0;
$sort_order = ''; # asc, desc
$rev_sort_order = ''; # asc, desc
$pager_url_params = '';
$num_prod_url_params = '';
$num_prod_url_params = modify_params($num_prod_url_params, 'order_by=num_prod');
$rank_url_params = '';
$rank_url_params = modify_params($rank_url_params, 'order_by=rank');
$url_params =  $_SERVER['QUERY_STRING'];
$order_by = ''; # rank (default:asc), num_prod (default:desc)
$ROWS_PER_PAGE = 25;
$type = '';
$search_by = '';
$keywords = '';
$se = '';
$top10 = 0;
if(isset($_REQUEST['type'])) {
 $type = $_REQUEST['type'];
}
if(isset($_REQUEST['sort_order']) && (strlen($_REQUEST['sort_order']) <= 100)) {
        $sort_order = $_REQUEST['sort_order'];
	$pager_url_params = modify_params($pager_url_params, 'sort_order=' . $sort_order);
}
if(isset($_REQUEST['order_by']) && (strlen($_REQUEST['order_by']) <= 100)) {
        $order_by = $_REQUEST['order_by'];
	$pager_url_params = modify_params($pager_url_params, 'order_by=' . $order_by);
	if($order_by == 'num_prod') {
		if(strlen($sort_order) > 0 ) {
			if($sort_order == 'asc') {
				$num_prod_url_params = modify_params($num_prod_url_params, 'sort_order=desc');
			}
			else {
				$num_prod_url_params = modify_params($num_prod_url_params, 'sort_order=asc');
			}
		}
		else {
			$num_prod_url_params = modify_params($num_prod_url_params, 'sort_order=asc');
		}
	}
	elseif ($order_by == 'rank') {
                if(strlen($sort_order) > 0 ) {
                        if($sort_order == 'asc') {
                                $rank_url_params = modify_params($rank_url_params, 'sort_order=desc');
                        }
                        else {
                                $rank_url_params = modify_params($rank_url_params, 'sort_order=asc');
                        }
                }
                else {
                        $rank_url_params = modify_params($rank_url_params, 'sort_order=desc');
                }

	}
	else {
	}
}
if(isset($_REQUEST['all_events']) && (strlen($_REQUEST['all_events']) <= 1)) {
        $all_events= $_REQUEST['all_events'];
	$pager_url_params = modify_params($pager_url_params, 'all_events=' . $all_events);
	$num_prod_url_params = modify_params($num_prod_url_params, 'all_events=' . $all_events);
	$rank_url_params = modify_params($rank_url_params, 'all_events=' . $all_events);
}
if(isset($_REQUEST['start']) && (strlen($_REQUEST['start']) < 4)) {
        $start = $_REQUEST['start'];
	$num_prod_url_params = modify_params($num_prod_url_params, 'start=' . $start);
	$rank_url_params = modify_params($rank_url_params, 'start=' . $start);
}
if(isset($_REQUEST['categoryID']) && (strlen($_REQUEST['categoryID']) < 7)) {
        $categoryID= $_REQUEST['categoryID'];
	$pager_url_params = modify_params($pager_url_params, 'categoryID=' . $categoryID);
	$num_prod_url_params= modify_params($num_prod_url_params, 'categoryID=' . $categoryID);
	$rank_url_params= modify_params($rank_url_params, 'categoryID=' . $categoryID);
}

if(isset($_REQUEST['keywords']) && (strlen($_REQUEST['keywords']) < 150)) {
	$keywords= $_REQUEST['keywords'];
	$pager_url_params = modify_params($pager_url_params, 'keywords=' . $keywords);

}
if(strlen($pager_url_params) > 0) {
	$pager_url_params = '?' . $pager_url_params;
}
if(strlen($num_prod_url_params) > 0) {
	$num_prod_url_params= '?' . $num_prod_url_params;
}
if(strlen($rank_url_params) > 0) {
	$rank_url_params= '?' . $rank_url_params;
}



if($dbh) {
	if($categoryID != '') {
        	$categoryName = GetCategoryName($categoryID);


		echo '<h1>' . $categoryName . '</h1>';

        	$breadcrumb_str = Breadcrumbs($categoryID, 0);
		echo $breadcrumb_str;
	
	        $subsubcategories = GetImmediateSubordinatesOfCategory($categoryID, -1);
		$suboord = '';
	        if((is_array($subsubcategories)) && (count($subsubcategories) > 0)) {
		     echo '<br/>Below: ';
	             foreach ($subsubcategories as $index=>$subsubcategoryArray) {
			 $anchor = '
			<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=' . $subsubcategoryArray[id] . '">' . $subsubcategoryArray[name] . '</a>';
			 if(strlen($suboord) > 0) {
	                 	$suboord .= '&nbsp;,&nbsp;';
			}
	                $suboord .= $anchor;
			
		     }
		     echo '
			' . $suboord;
		}
		echo '<br/>';

	}

		# limit p/p
		$limit = ' LIMIT ' . ($start-1)*$ROWS_PER_PAGE . ',' . $ROWS_PER_PAGE;
		if($keywords != '') {
                        # $bsql = 'SELECT SQL_CALC_FOUND_ROWS Events.EventName,Events.EventID,IFNULL(EventRankings.EventRank,2000) as ER,MinCost,MaxCost,COUNT(Productions.EventID) FROM Events LEFT JOIN Productions ON (Events.EventID = Productions.EventID) LEFT JOIN EventRankings ON (Events.EventID=EventRankings.EventID) WHERE EventName LIKE ' . "'%" . $keywords . "%' AND DATEDIFF(NOW(), EventDate) <= 0 GROUP BY Events.EventID ORDER BY ER ASC" . $limit;  
                        $bsql = 'SELECT SQL_CALC_FOUND_ROWS Events.EventName,Events.EventID,IFNULL(EventRankings.EventRank,2000) as ER FROM Events LEFT JOIN EventRankings ON (Events.EventID=EventRankings.EventID) WHERE EventName LIKE ' . "'%" . $keywords . "%' GROUP BY Events.EventID ORDER BY ER ASC" . $limit;



		}
		elseif ($all_events == '0') {
			$prefix = 'SELECT SQL_CALC_FOUND_ROWS Events.EventName,Events.EventID,IFNULL(EventRankings.EventRank,2000) as ER,MinCost,MaxCost,COUNT(Productions.EventID) FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID=' . $categoryID . ') INNER JOIN Events ON (Events.CategoryID=c1.CategoryID) LEFT JOIN EventRankings ON (Events.EventID=EventRankings.EventID)  LEFT JOIN Productions ON (Events.EventID = Productions.EventID) WHERE  (c1.lft BETWEEN c2.lft AND c2.rgt) AND DATEDIFF(NOW(), EventDate) <= 0 GROUP BY Events.EventID';

			if($order_by == 'num_prod') {
				$bsql = $prefix . ' ORDER BY COUNT(Productions.EventID)';
				$default_order = ' DESC';
			}
			elseif ($order_by == 'rank') {
				$bsql = $prefix . ' ORDER BY ER';
				$default_order = ' ASC';
			}
			else {
				$bsql = $prefix . ' ORDER BY ER';
				$default_order = ' ASC';
			}
                        if($sort_order == 'asc') {
                            $bsql .= ' ASC';
                        }
                        elseif ($sort_order == 'desc') {
                            $bsql .= ' DESC';
                        }
			else {
                            $bsql .= $default_order;

			}
                        $bsql .= $limit;

		}
		else {
			$bsql = 'SELECT SQL_CALC_FOUND_ROWS Events.EventName,Events.EventID,IFNULL(EventRankings.EventRank,2000) as ER,MinCost,MaxCost,Null FROM ModifiedPreorderTreeTraversalCategories as c1 LEFT JOIN ModifiedPreorderTreeTraversalCategories as c2 ON (c2.CategoryID=' . $categoryID . ') INNER JOIN Events ON (Events.CategoryID=c1.CategoryID) LEFT JOIN EventRankings ON (Events.EventID=EventRankings.EventID) WHERE  (c1.lft BETWEEN c2.lft AND c2.rgt) AND DATEDIFF(NOW(), EventDate) <= 0  GROUP BY Events.EventID ORDER BY ER ASC' . $limit;
		}


                  if($query_result = mysql_query($bsql) ) {
			$sql = "SELECT FOUND_ROWS()";
			$result = mysql_query($sql);
			$total_records = mysql_result($result, 0);


			$num_pages = ceil(((float)$total_records / (float)$ROWS_PER_PAGE));
			if($num_pages > 1) {
				print_page_nav($start, $num_pages, $pager_url_params );
			}

			print_table_header($rank_url_params, $num_prod_url_params);

			echo '<input type="hidden" name="start" value="' . $start . '"/>';
			echo '<input type="hidden" name="categoryID" value="' . $categoryID . '"/>';
			echo '<input type="hidden" name="keywords" value="' . $keywords . '"/>';

			$i = 0;
                      while ($table_row = mysql_fetch_row($query_result)) {
                          $eventName = $table_row[0];
                          $eventID = $table_row[1];
                          $eventRank = $table_row[2];
			  if(strlen($eventRank) < 1){
				$eventRank = 2000;
			  }
                          $minCost = $table_row[3];
                          $maxCost = $table_row[4];
                          $num_productions = $table_row[5];
                          $eventUrl = make_event_url($eventName, $eventID);
                          $eventName = htmlspecialchars($eventName);
                        echo '<tr align="left">';
			echo '<td align="left" width="300">' . $eventName . '</td>';
			echo '<td align="left" width="25">
			<input type="hidden" name="eventid_' . $i . '" value="' . $eventID . '"/>
			<input type="text" name="rank_' . $i . '" value="' . $eventRank . '"/>
			</td>';
			echo '<!-- 
				<td align="left" width="25"><input type="text" name="events[' . $eventID . '][global_rank]" value="' . $global_rank . '"/> 
				-->
				';

			echo '<td align="left" width="40">' . $num_productions . '</td>';
			echo '<td align="left" width="25">$' . $minCost . '-' . $maxCost . '</td>';
			echo '<td align="left" width="25"><a href="' . $eventUrl . '" onclick="window.open(\'' . $eventUrl . '\',\'popup\',\'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0\'); return false" style="color: red">' . $eventID . '</a></td>';
			echo '<td><input type="text" size="200" value="&lt;li&gt;&lt;a href=&quot' . $DOMAIN . $eventUrl . '&quot;&gt;' . $eventName . '&lt;/a&gt;&lt;/li&gt;"/></td>';
			# echo '<td><input type="text" value="><pre>&lt;li&gt;&lt;a href=&quot' . $DOMAIN . $eventUrl . '&quot;&gt;' . $eventName . '&lt;/a&gt;&lt;/li&gt;</pre>/></td>';
# <pre>&lt;a href=&quot;url&quot;&gt;Text to be displayed&lt;/a&gt;</pre>
# <li><a href="/events/New-England-Patriots-Tickets-E592.html">New England Patriots Tickets</a></li>

			echo '</tr>';
			$i++;
                	}
			echo '</form>';
			echo '</table>';
			if($num_pages > 1) {
				print_page_nav($start, $num_pages, $pager_url_params );
			}
		}
		else {
		}

	mysql_close($dbh);
}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}


function Breadcrumbs($categoryID, $eventID) {

        $Bsql = "SELECT C.CategoryID, C.CategoryName" .
                " FROM ModifiedPreorderTreeTraversalCategories AS B, ModifiedPreorderTreeTraversalCategories AS C" .
                " WHERE (B.lft BETWEEN C.lft AND C.rgt)" .
                " AND (B.CategoryID = $categoryID)" .
                " ORDER BY C.lft";
#Might want to change to (B.lft BETWEEN (C.lft+1) AND C.rgt)
        $result = mysql_query($Bsql);

        $top_level_category_array = array(2,3,4);

        $breadcrumb_string = "";
        while ($row = mysql_fetch_array($result)) {
                $categoryName = $row['CategoryName'];
                $categoryID = $row['CategoryID'];
                if(strlen($breadcrumb_string) > 0) {
                        $breadcrumb_string .= "&nbsp;&gt;&nbsp;";
                }

                if ($categoryID == 0) {
                        $breadcrumb_string .= '<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=0">All Events</a>';
                }
                else {
                        $breadcrumb_string .= '<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=' . $categoryID . '">' . $categoryName . '</a>';
                }
        }


        return $breadcrumb_string;
}

function modify_params($string, $new_param) {

	if(strlen($string) > 0) {
		$new_string =  $string . '&' . $new_param;
	}
	else {
		$new_string = $new_param;
	}
	return $new_string;
}

function print_table_header($rank_url_params, $num_prod_url_params) {
        echo '
                <table width="820" border="1" cellspacing="0" cellpadding="4">
                <form action="update_events.php" method="get">
                <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td> <input type="submit" value="Update" /> </td>
                <tr>
                <td align="left"><strong>Event</strong></th>
                <td align="left"><a href="http://www.mongotickets.com/event_ranking_editor.php' . $rank_url_params . '"><strong>Rank</strong></th>
<!--                    <th align="left">Global Rank</th> -->
                <td align="left" width="25"><a href="http://www.mongotickets.com/event_ranking_editor.php' . $num_prod_url_params . '"><strong># Prod.</strong></a></th>
                <td align="left"><strong>Cost</strong></th>
                <td align="left"><strong>Ev ID (link)</strong></th>
                <td>&nbsp;</td>
                </tr>';
}


function print_page_nav($startFrom, $num_pages, $url_params) { 

        $url =  "";

	echo '<br/>';
	if($startFrom > 1) { 
		$prevStartFrom = $startFrom-1;
		echo '<a href="' . $url . $url_params . '&start=' . $prevStartFrom . '" style="color: blue; ">&lt Previous</a>&nbsp;&nbsp;'; 
	}
	for ($i=1; $i <= $num_pages; $i++) {
		$offset = $i;
		if($startFrom == $i) {
			echo "<strong>$i</strong>&nbsp;";
		}
		else {
			echo '<a href="' . $url . $url_params . '&start=' . $offset . '" style="color: blue;">&nbsp;' . $i . '&nbsp;</a>&nbsp;'; 
 		}
	} # end for
	$nextStartFrom = $startFrom +1;
	if($nextStartFrom <= $num_pages) {
		echo '<a href="' . $url . $url_params . '&start=' . $nextStartFrom . '" style="color: blue; "> Next &gt;</a>&nbsp;'; 
	}
	echo '<br/>';
}



?>

</body>
</html>
