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
	mysql_select_db ($db_name);

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

ob_start("thepage");
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Campaign Manager</title>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="noindex, nofollow" />
<style type="text/css">
* {
	margin: 0;
	padding: 0;
}
body {
	margin: 20px 0 0 10px;
	background: #DDDDDD;
	font: Arial, Helvetica, sans-serif;
	font-size: 10px;
}

table * {
        font: Arial, Helvetica, sans-serif;
        font-size: 10px;
}
table tr td select option {
        font: Arial, Helvetica, sans-serif;
        font-size: 10px;
}
table tr td {
	padding: 0 5px 0 5px;
}
</style>
</head>
<body>
<h1><a href="campaign_manager.php">Campaign Manager</a></h1>
<a href="http://www.mongotickets.com/log_me_out.php">Logout</a>


<h2><a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=0">Event Editor</a></h2>


<?php
$sort_order = ''; # asc, desc
$DEFAULT_SORT_ORDER = array(
			'name' => 'asc',
			'cmpn' => 'desc',
			'cat'  => 'asc',
			'num_prod' => 'desc'
			);
$url_params =  $_SERVER['QUERY_STRING'];
$saved_url_params =  $_SERVER['QUERY_STRING'];
$order_by = ''; # rank (default:asc), num_prod (default:desc)
$ROWS_PER_PAGE = 25;
$keywords = '';
foreach($_GET as $name => $value) {
	$in_url_params[$name] = $value;
	if(strlen($value) > 100) {
		exit();
	}
}

$is_post = 0;
foreach($_POST as $name => $value) {
	$in_post_params[$name] = $value;
	if(strlen($value) > 100) {
		exit();
	}
	$is_post = 1;
        $start = 1;
	$url_params = '';
}

if($is_post) {
	$url_params = add_to_url_params('start', $start, $url_params);
	if (isset($_POST['keywords']) && (strlen($_POST['keywords']) > 0) ) {
		$keywords = mysql_escape_string($_POST['keywords']);
		$url_params = add_to_url_params('keywords', $keywords, $url_params);
	}
	if (isset($_POST['action_filter'])) {
		$action_filter = $_POST['action_filter'];
		$url_params = add_to_url_params('action_filter', $action_filter, $url_params);
	}
	$sort_order = ' ' . $DEFAULT_SORT_ORDER['num_prod'];
}
else {
	$start=1;
	if (isset($_GET['keywords'])) {
        	$keywords = mysql_escape_string($_GET['keywords']);
	}
	if (isset($_GET['action_filter'])) {
        	$action_filter = $_GET['action_filter'];
	}
	if(isset($_GET['sort_order'])) {
		$sort_order = ' ' . $_GET['sort_order'];
	}
	if(isset($_GET['start'])) {
		$start = $_GET['start'];
	}

}


$action_filter_param = '';
if($action_filter != '') {
	$action_filter_param = htmlspecialchars('?action_filter=' . $action_filter . '&');
}


# order_by
#	name
#	cmpn
#	cat
#	num_prod

?>
<table>
<form action=""campaign_manager.php"" method="post">
<tr>
        <th align="left"><strong>Type:</strong></th>
        <th align="left"><strong>SE:</strong></th>
        <th align="left"><strong>Search By:</strong></th>
        <th align="left"><strong>Keyword:</strong></th>
</tr>
<tr>
        <td align="left" width="25">
                <select name="type" id="type">
                        <option value="event">Event</option>
                        <!-- <option value="venue">Venue</option> -->
                        <!-- <option value="general">General</option> -->
                </select>
        </td>
        <td align="left" width="25">
                <select name="se" id="se">
                        <option value="google">Google</option>
                </select>
        </td>
        <td align="left" width="25">
                <select name="action_filter" id="action_filter">


<?php
                       	$all_option  = '<option value="ALL">ALL</option>';
                       	$turn_off_option  = '<option value="TURN OFF">TURN OFF</option>';
                       	$turn_on_option  = '<option value="TURN ON">TURN ON</option>';
                       	$upload_option = '<option value="UPLOAD">UPLOAD</option>';
                       	# $non_option = '<option value="NON">NON</option>';

			switch($action_filter) {
			case 'ALL':
                        	$all_option  = '<option SELECTED value="ALL">ALL</option>';
				break;
			case 'TURN OFF':
                        	$turn_off_option  = '<option SELECTED value="TURN OFF">TURN OFF</option>';
				break;
			case 'TURN ON':
                        	$turn_on_option  = '<option SELECTED value="TURN ON">TURN ON</option>';
				break;
			case 'UPLOAD':
                        	$upload_option = '<option SELECTED value="UPLOAD">UPLOAD</option>';
				break;
			case 'NON':
                        	$non_option = '<option SELECTED value="NON">NON</option>';
				break;
			}

			echo $all_option . $turn_off_option . $turn_on_option . 
				$upload_option . $non_option;
			echo '</select>';

?>

        </td>
        <td align="left" width="55"><input type="text" name="keywords" id="keywords" size="55" value=""
></td>
        <td> <input type="submit" value="Go" /> </td>
</tr>

</form>
</table>


<?php


if($dbh) {

# print_r($_POST);
   if($is_post) {
	$is_update = 0;
	$i = 0;
      while (isset($_POST['ID_' . $i]) && isset($_POST['cmpn_status_' . $i])) {
                $ID  = $_POST['ID_' . $i];
                $cmpn_status  = $_POST['cmpn_status_' . $i];
 
		$result = mysql_query('SELECT CampaignStatus from EventCampaigns WHERE ID = ' . $ID);

		$num_rows = mysql_num_rows($result);

		if($num_rows < 1) {
			$result = mysql_query('INSERT INTO EventCampaigns (ID,CampaignStatus) VALUES(' . $ID . ",'" . $cmpn_status . "')") or die ('INSERT INTO EventCampaigns failed: ' . mysql_error());
		}
		else {
			$result = mysql_query("UPDATE EventCampaigns SET CampaignStatus='" . $cmpn_status . "' WHERE ID=" . $ID) or die ('IPDATE EventCampaigns failed: ' . mysql_error());
		}

		$i++;
		$is_update = 1;
	}
	if($is_update == 1) {
		ob_end_clean();
		header("Location: http://www.mongotickets.com/campaign_manager.php?" . $saved_url_params);
	}
    }

	# limit p/p
	$limit = ' LIMIT ' . ($start-1)*$ROWS_PER_PAGE . ',' . $ROWS_PER_PAGE;
	$bsql = 'SELECT SQL_CALC_FOUND_ROWS EventName, Events.EventID, CampaignStatus, COUNT(Productions.EventID) as num_prods, EventTypeID from Events  LEFT JOIN Productions ON (Events.EventID = Productions.EventID) LEFT JOIN EventCampaigns ON (Events.EventID = EventCampaigns.ID)';
	if((is_post == 0) && isset($_GET['order_by'])) {

		switch($_GET['order_by']) {
			case 'num_prod':
				$order_by = ' ORDER BY COUNT(Events.EventID)';
				break;
			case 'name':
				$order_by = ' ORDER BY EventName';
				break;
			case 'cat':
				$order_by = ' ORDER BY EventTypeID';
				break;
			case 'cmpn':
				$order_by = ' ORDER BY CampaignStatus';
				break;
		}
		$order_by .= $sort_order;
	}
	else {
#echo 'here';
		# by default order by num_prod
		$order_by = ' ORDER BY COUNT(Events.EventID) DESC';
	}
	switch($action_filter) {
		case 'ALL':
			$bsql .= ' WHERE 1 GROUP BY EventID' . $order_by;
			break;
		case 'TURN OFF':
			$bsql .= ' WHERE CampaignStatus = \'ON\'  GROUP BY Events.EventID HAVING num_prods < 1' . $order_by;
			break;
		case 'TURN ON':
			$bsql .= ' WHERE CampaignStatus = \'OFF\'  GROUP BY Events.EventID HAVING num_prods > 0' . $order_by;
			break;
		case 'UPLOAD':
			$bsql .= ' WHERE (CampaignStatus = \'NO TERMS\') OR (CampaignStatus IS NULL)  GROUP BY Events.EventID HAVING num_prods > 0' . $order_by;
			break;
		case 'NON':
			$bsql .= ' WHERE (CampaignStatus = \'NO TERMS\') OR (CampaignStatus IS NULL)  GROUP BY Events.EventID HAVING num_prods > 0' . $order_by;
			break;
	}

	if($keywords != '') {
		$bsql = 'SELECT SQL_CALC_FOUND_ROWS EventName, Events.EventID, CampaignStatus, COUNT(Productions.EventID) as num_prods, EventTypeID from Events  LEFT JOIN Productions ON (Events.EventID = Productions.EventID) LEFT JOIN EventCampaigns ON (Events.EventID = EventCampaigns.ID) WHERE Events.EventName LIKE ' . "'%" . $keywords . "%'" . ' GROUP BY Events.EventID';
	}

	$bsql .= $limit;
echo $bsql;

                  if($query_result = mysql_query($bsql) ) {
			$sql = "SELECT FOUND_ROWS()";
			$result = mysql_query($sql);
			$total_records = mysql_result($result, 0);


			$num_pages = ceil(((float)$total_records / (float)$ROWS_PER_PAGE));
			# if($num_pages > 1) {
			#	print_page_nav($start, $num_pages, $url_params);
			#}

			print_table_header($action_filter_param, $url_params);

			echo '<input type="hidden" name="start" value="' . $start . '"/>';
			echo '<input type="hidden" name="keywords" value="' . $keywords . '"/>';

			$i = 0;
                      while ($table_row = mysql_fetch_row($query_result)) {
                          $name = $table_row[0];
                          $ID = $table_row[1];
                          $campaignStatus = $table_row[2];
                          $num_productions = $table_row[3];
                          $eventTypeID = $table_row[4];
                        echo '<tr align="left">';
			echo '<td align="left" width="24%">' . $name . '</td>';
			echo '<td align="left" width="9%">
				<select name="cmpn_status_' . $i . '" id="cmpn_status_' . $i . '" size="1" width="25" style="width: 85px;">';
                       	$upload = '<option width="25" value="UPLOAD">UPLOAD</option>';
                        $no_terms_option  = '<option width="25" value="NO TERMS">NO TERMS</option>';
                        $on_option  = '<option value="ON">ON</option>';
                        $off_option = '<option value="OFF">OFF</option>';
			if($campaignStatus == 'NO TERMS') {
                        	$no_terms_option  = '<option SELECTED value="NO TERMS">NO TERMS</option>';
			}
                        if($campaignStatus == 'ON') {
                                $on_option  = '<option SELECTED value="ON">ON</option>';
                        }
                        if($campaignStatus == 'OFF') {
                                $off_option = '<option SELECTED value="OFF">OFF</option>';
                        }

			echo $no_terms_option;
			echo $on_option;
			echo $off_option;
			echo '</select>';

			echo '</td>';

			echo '<td align="left" width="30">&nbsp;' . $num_productions . '</td>';
			echo '<td align="left" width="25">&nbsp;';
			if($eventTypeID == 2) {
				echo 'Con';
			}
			elseif($eventTypeID == 3) {
				echo 'Spr';
			}
			elseif($eventTypeID == 4) {
				echo 'Thr';
			}
			elseif($eventTypeID == 1) {
				echo '???';
			}
			else {
				echo 'Unk';
			}
			echo '</td>';
			echo '<td align="left" width="6%">&nbsp;' . $keywords . '</td>';
			echo '<td align="left" width="7%">&nbsp;';
			$action = getAction($campaignStatus, $num_productions);
			echo "$action</td>";
			echo '<td align="left" width="4%">&nbsp;' . $dates . '</td>';
			echo '<td align="left" width="30">&nbsp;' . $ID . 
				'<input type="hidden" name="ID_' . $i . '" id="ID_' . $i . '" value="' . $ID .
				'"/></td>';

			$ev_url = make_event_url($name);
			$ev_url = "www.mongotickets.com" . $ev_url;

			
			echo '<td align="left" width="33%"><input type="text" value="' . $ev_url . '" size="60"/></td>';
			echo '</tr>';

			$i++;
                	}
			echo '<tr>
                		<td colspan="9" align="center"> <input type="submit" value="Update" /> </td>
				</tr>';
			echo '</form>';
			echo '</table>';
			if($num_pages > 1) {
				print_page_nav($start, $num_pages, $url_params );
			}
		}
		else {
		}

	mysql_close($dbh);
}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}

ob_end_flush();

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

function rev_sort_order($cur_sort_order) {

	$new_sort_order = ($cur_sort_order == 'asc' ? 'desc' : 'asc');
	return $new_sort_order;

	
}

function print_table_header($action_filter_param,$url_params) {

	
	global $DEFAULT_SORT_ORDER;
# print_r($DEFAULT_SORT_ORDER);
        $name_url_params = $action_filter_param . "order_by=name";
        $cmpn_url_params = $action_filter_param . "order_by=cmpn";
        $cat_url_params = $action_filter_param . "order_by=cat";
        $num_prod_url_params = $action_filter_param . "order_by=num_prod";
	$reversed_sort_order = '';
	if(strstr($url_params, 'sort_order=') != false) {
		# get sort order
		$val = getUrlParamValue($url_params,'sort_order');
echo '<br/> url_params = ' . $url_params;
		$reversed_sort_order = rev_sort_order($val);
	}
	$order_by = getUrlParamValue($url_params,'order_by');

echo 'order_by ' . $order_by . '<br/>';
	switch($order_by) {
		case 'name':
			$name_url_params .= '&sort_order=' . ($reversed_sort_order == '' ? rev_sort_order($DEFAULT_SORT_ORDER['name']) : $reversed_sort_order);
		break;
		case 'cmpn':
			$cmpn_url_params .= '&sort_order=' . ($reversed_sort_order == '' ? rev_sort_order($DEFAULT_SORT_ORDER['name']) : $reversed_sort_order);
		break;
		case 'cat':
			$cat_url_params .= '&sort_order=' . ($reversed_sort_order == '' ? rev_sort_order($DEFAULT_SORT_ORDER['name']) : $reversed_sort_order);
		break;
		case 'num_prod_url_params':
			$num_prod_url_params .= '&sort_order=' . ($reversed_sort_order == '' ? rev_sort_order($DEFAULT_SORT_ORDER['name']) : $reversed_sort_order);
		break;
	}

        echo '
                <table width="980" border="1" cellspacing="0" cellpadding="4">
                <form action="campaign_manager.php?' . $url_params . '" method="post">
		</tr>
                <tr>
                <td align="left"><a href="campaign_manager.php' . $name_url_params . '"><strong>Name</strong></a></td>
                <td align="left"><a href="campaign_manager.php' . $cmpn_url_params . '"><strong>Campaign</strong></td>
                <td align="left"><a href="campaign_manager.php' . $num_prod_url_params . '"><strong># Prod</strong></td>
                <td align="left"><a href="campaign_manager.php' . $cat_url_params . '"><strong>Cat</strong></td>
                <td align="left" width="25">Keywords</td>
                <td align="left">Action</td>
                <td align="left"><strong>Dates</strong></td>
                <td align="left"><strong>Ev ID</strong></td>
                <td width="33%">URL</td>
                </tr>';
}


function print_page_nav($startFrom, $num_pages, $url_params) { 

	$start_param = "start=";
	$max_listed_page_links = 30;

	if($url_params == '') {
		$url_params .= "$start_param";
	}
	else {
		$new_url_params = remove_query_param($url_params, 'start');

		$url_params = $new_url_params . "&$start_param";
	}
        $url_params = "?" . $url_params;

	echo '<br/>';
	$begin = floor($startFrom / $max_listed_page_links) * $max_listed_page_links;
	if (!$begin) $begin = 1;
	$end =  ($num_pages < ($begin + $max_listed_page_links - 1)) ? 
           $num_pages : $begin + $max_listed_page_links - 1;
echo '<br/>begin = ' . $begin . '<br/>';
echo '<br/>end = ' . $end . '<br/>';
echo '<br/>total pages = ' . $num_pages . '<br/>';

	if($begin > ($max_listed_page_links - 1)) {
		$rw = $begin-1;
		echo '<a href="' . $url_params . $rw . '" style="color: blue; "> &lt;&lt; RW</a>&nbsp;'; 
	}
	if($startFrom > 1) { 
		$prevStartFrom = $startFrom-1;
		echo '<a href="' . $url_params . $prevStartFrom . '" style="color: blue; ">&lt Previous</a>&nbsp;&nbsp;'; 
	}
	if($begin <= $end) { # protection against inf loop
	for ($i=$begin; $i <= $end; $i++) {
		$offset = $i;
		if($startFrom == $i) {
			echo "<strong>$i</strong>&nbsp;";
		}
		else {
			echo '<a href="' . $url_params . $offset . '" style="color: blue;">&nbsp;' . $i . '&nbsp;</a>&nbsp;'; 
 		}
		$last_page_anchor = $i;
	} # end for
	}
	$nextStartFrom = $startFrom +1;
	if($nextStartFrom <= $num_pages) {
		echo '<a href="' . $url_params . $nextStartFrom . '" style="color: blue; "> Next &gt;</a>&nbsp;'; 
	}
	if($end < $num_pages) {
		$ff = $end+1;
		echo '<a href="' . $url_params . $ff . '" style="color: blue; "> FF &gt;&gt;</a>&nbsp;'; 
	}
	echo '<br/>';
}


function getAction($campaignStatus, $num_productions) {
	$rv = "";
	if(($campaignStatus == 'NO TERMS') || ($campaignStatus == '')) {
		if($num_productions > 0) {
			$rv = 'UPLOAD';
		}
		else {
			$rv = 'NON';
		}
	}
	if($campaignStatus == 'ON') {
		if($num_productions < 1 ) {
			$rv = 'TURN OFF';
		}
		else {
			$rv = 'NON';
		}
	}
	if($campaignStatus == 'OFF') {
		if($num_productions > 0 ) {
			$rv = 'TURN ON';
		}
		else {
			$rv = 'NON';
		}
	}
	return $rv;


}


function remove_query_param($url, $param)
{
  // remove $param 
  $new_url = preg_replace("#$param=?(.*?(&|$))?#", '', $url); 

  // remove ending ? or &, in case it exists
  if (substr($new_url, -1) == '?' || substr($new_url, -1) == '&') 
  { 
    $new_url = substr($new_url, 0, strlen($new_url) - 1);
  } 

  // return the new URL
  return $new_url;
}


function add_to_url_params($param_name, $value, $url_params) {
# add_to_url_params('keywords', 'keywords=', $keywords, $url_params);
#	$new_url_params = preg_replace("#$param_name=?(.*?(&|$))?#", "$param_name=$value", $url_params);
        if(strstr($url_params, "$param_name") != "") {
echo '' . "$param_name is in url" . '<br/>';
                # param is in url
                $pattern = "/($param_name=)([[:space:]a-zA-Z]+)/";
                $replacement = urlencode($param_name) . '=' . urlencode($value);
                $url_params = preg_replace($pattern, $replacement, urldecode($url_params));
        }
        else {
echo '' . "$param_name not in url" . '<br/>';
                # param_name isn't in url
                $url_params .= ($url_params == '' ? '' : '&') .
				urlencode($param_name) . '=' . urlencode($value);
        }
	return $url_params;
}



// transforms a query string into an associative array
function parse_query_string($query_string)
{
  // split the query string into individual name-value pairs
  $items = explode('&', $query_string);

  // initialize the return array
  $qs_array = array();

  // create the array
  foreach($items as $i)
  {
    // split the name-value pair and save the elements to $qs_array
    $pair = explode('=', $i);
    $qs_array[urldecode($pair[0])] = urldecode($pair[1]); 
  }

  // return the array
  return $qs_array;
}



function getUrlParamValue($query_string, $param) {
	$items = explode('&', $query_string);
echo 'qs = ' . $query_string . '<br/>';
	$qs_array = array();
	foreach($items as $i) {
		$pair = explode('=',$i);
		$qs_array[urldecode($pair[0])] = urldecode($pair[1]);
	}
# print_r ($qs_array);
	return $qs_array[$param];
}


?>

</body>
</html>
