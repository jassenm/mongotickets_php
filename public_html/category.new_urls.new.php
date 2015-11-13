<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#
require_once('../include/smarty_package.php');
require_once('../lib/php/Smarty/Smarty.class.php');
require_once('../include/new_urls/ticket_db.php');
require_once('DbUtils.new_urls.php');
require_once('Utils.php');
include_once('../include/error.php');
include_once('../include/host_info.inc.php');
include_once('../include/domain_info.inc.php');
require_once('../include/new_urls/breadcrumbs.inc.php');



if(isset($_REQUEST['name']) && isset($_REQUEST['category_id']) && (($_REQUEST['category_id'] < 2000) && ($_REQUEST['category_id'] >= 0)) ) {
	$id = $_REQUEST['category_id'];
	$categoryName = $_REQUEST['name'];
}
else {
	handle_error_no_exit ('category.php: neither name nor category id provided ' . $_SERVER['REQUEST_URI'] . ' returning 301');
        redir_301();
}

$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
$smarty->assign("RootUrl", $root_url);
$descr = "Buy " . preg_replace ('/-/', ' ', htmlspecialchars($categoryName)) . " Tickets at MongoTickets today! " . preg_replace ('/-/', ' ', htmlspecialchars($categoryName)) . " Tickets";
$smarty->assign("MetaDescr", $descr);


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {

	mysql_select_db ($db_name);

	$categoryID = $id;
	$limit = -1;
	$subcategories = GetImmediateSubordinatesOfCategory($categoryID, $limit);

	$bsql = "SELECT TitleTag FROM AdjacencyListCategories WHERE CategoryID=$categoryID";

	$categoryName = preg_replace ('/-/', ' ', $categoryName);
	$titleTag = "$categoryName Tickets";
	if($query_result = mysql_query($bsql) ) {
		while ($table_row = mysql_fetch_array($query_result)) {
			$titleTag = $table_row['TitleTag'];
		}
	}

	if(!is_array($subcategories)) {
		handle_error_no_exit ('category.php: no subcategories in this category ' . $_SERVER['REQUEST_URI'] . ' ');
                $error_message = get_error_message();
                $smarty->assign("ErrorMessage", $error_message);
                $smarty->display('error_page.tpl');
	}
	else {
		$smarty->assign("title", htmlspecialchars($titleTag));

		#$keywords = GetKeywordsForCategoryID($categoryID);
		$keywords = $categoryName . " tickets";
		$smarty->assign("SeoKeywords", htmlspecialchars($keywords));
		$smarty->display('main.tpl');

		$breadcrumb_str = Breadcrumbs($categoryID, 0);

		# NFL (24) , NBA (82),  MLB (1999), NHL (91)
		$league_categoryIDs = array(24, 82, 1999, 91);
		$playoff_categoryIDs = array(178=>24, 182=>82,134=>1999, 250=>91);
		if(array_key_exists($categoryID, $playoff_categoryIDs))  {
			echo "<div id=\"content\">\n";
			echo "<div class=\"left_bar\">\n";
			echo "<div id=\"breadcrumb_trail\">\n";
			echo $breadcrumb_str;
			echo "</div>\n";
			DisplayPlayoffEvents($playoff_categoryIDs[$categoryID],$categoryName);
		}
		elseif((in_array($categoryID, $league_categoryIDs)) && (count($subcategories) > 0) ) {
			if($categoryID == 24) {
				$css_class = "confheadnfl";
			}
			elseif($categoryID == 82) {
				$css_class = "confheadnba";
			}
			elseif($categoryID == 1999) {
				$css_class = "confheadmlb";
			}
			elseif($categoryID == 91) {
				$css_class = "confheadnhl";
			}

			echo "<div id=\"content\">\n";
			echo "<div class=\"left_bar\">\n";
			echo "<div id=\"breadcrumb_trail\">\n";
			echo $breadcrumb_str;
			echo "</div>\n";
			echo "<div class=\"leaguetable\">\n";
	
			echo "<h1><strong>$categoryName Tickets</strong></h1>\n";
			# division level 2 columns each row
			echo "<table>";
			for($i=0; $i < count($subcategories); $i++) {
				if(($i != 0) && (($i % 2) == 0)) {
					echo "</tr><tr>";
				}
					#echo '<td><ul><a href="/' . $subcategories[$i]['url'] . '">' . $subcategories[$i]['name'] . '</a>';
					echo '<td><ul><li class="' . $css_class . '">' . $subcategories[$i]['name'] . '</li>';
					echo "<li>";
					if($query_result = mysql_query('SELECT EventID,EventName FROM Events WHERE CategoryID = ' . $subcategories[$i]['id'] . ' ORDER BY EventName ASC')) {
						while ($table_row = mysql_fetch_array($query_result)) {
							$eventID = $table_row['EventID'];
							$eventName = $table_row['EventName'];
							$url = make_event_url($eventName, $subcategories[$i]['id']);
							$eventName = htmlspecialchars($eventName);
							echo "<li><a href=\"$url\">$eventName</a></li>\n";
						}
					}
					echo "</td></ul>";
			}
			echo "</table>";
			echo "<div>";
			echo "<h1>Related $categoryName</h1>";
			echo "<ul>";
			if(count($subcategories) > 2) {
				for ($i=2; $i < count($subcategories); $i++) {
					echo "<li><a href=\"".$subcategories[$i]['url']."\">" . $subcategories[$i]['name']. "</a></li>";
				}
			}
			if($query_result = mysql_query('SELECT EventID,EventName FROM Events WHERE CategoryID = ' . $categoryID . ' ORDER BY EventName ASC')) {
				while ($table_row = mysql_fetch_array($query_result)) {
					$eventID = $table_row['EventID'];
					$eventName = $table_row['EventName'];
					$url = make_event_url($eventName, $categoryID);
					$eventName = htmlspecialchars($eventName);
					echo "<li><a href=\"$url\">$eventName</a></li>\n";
				}
			}
			else {
				header('HTTP/1.0 500 Internal Server Error');
				handle_error_no_exit ('category.php: Event lookup faild for category ID=' . $categoryID . ' : ' .  $_SERVER['REQUEST_URI'] . ' ' . mysql_error());
				$smarty->display('error_page.tpl');
			}

			echo "</ul></div> <!-- end Related category -->";
        		echo "              </div> <!-- end league_table -->";
        		echo "       </div> <!-- end left_bar -->";
			$smarty->display('right_bar.tpl');
			$smarty->display('left_column.tpl');
		}
		else {
			$events = array();

			$smarty->assign("SubCategories", $subcategories);
			$smarty->assign("NumSubCategories", count($subcategories));
			$smarty->assign("Breadcrumbs", $breadcrumb_str);

                        # Exclude Events in "Other *"  categories that don't have productions
                        if(($categoryID == 1526) || ($categoryID == 1310) ||  ($categoryID == 1111)) {
                                $bsql = 'SELECT Events.EventID,Events.EventName,AdjacencyListCategories.CategoryName FROM Events LEFT JOIN (AdjacencyListCategories) ON (AdjacencyListCategories.CategoryID = Events.CategoryID) INNER JOIN Productions ON (Events.EventID = Productions.EventID) WHERE Events.CategoryID = ' . $categoryID . ' GROUP BY Events.EventID ORDER BY Events.EventName ASC';
                        }
                        else {
                                $bsql = 'SELECT EventID,EventName,AdjacencyListCategories.CategoryName FROM Events LEFT JOIN (AdjacencyListCategories) ON (AdjacencyListCategories.CategoryID = Events.CategoryID) WHERE Events.CategoryID = ' . $categoryID . ' ORDER BY EventName ASC';
                        }


			if($query_result = mysql_query($bsql) ) {
				while ($table_row = mysql_fetch_array($query_result)) {
					$eventID = $table_row['EventID'];
					$eventName = $table_row['EventName'];
					$url = make_event_url($eventName, $categoryID);
					$eventName = htmlspecialchars($eventName);
					$catName = $table_row['CategoryName'];
					$events[] = array("name" => htmlspecialchars($eventName), "url" => "$url");
				}
			}
			else {
				header('HTTP/1.0 500 Internal Server Error');
				handle_error_no_exit ('category.php: Event lookup failed for regular category lookup, category ID=' . $categoryID . ' : ' .  $_SERVER['REQUEST_URI'] . ' ' . mysql_error());
				$smarty->display('error_page.tpl');
			}
			
			$smarty->assign("EventsArray", $events);
			$smarty->assign("NumEvents", count($events));
			$smarty->assign("categoryName", htmlspecialchars($categoryName) . ' Tickets');
			if((count($events) > 0) || (count($subcategories) > 0)) {
				$smarty->display('events.tpl');
			}
			else {
				$smarty->assign("EventName", $categoryName);
				$smarty->display('no_tickets.tpl');
			}
		}
	}
	mysql_close($dbh);
}
else {
	header('HTTP/1.0 500 Internal Server Error');
	handle_error_no_exit ('category.code: failure: category id= ' .  $id .
                            ' uri= ' . $_SERVER['REQUEST_URI'] . ' I cannot connect to the database because: ' . mysql_error());
	$error_message = get_error_message();
	$smarty->assign("ErrorMessage", $error_message);
	$smarty->display('main.tpl');
	$smarty->display('error_page.tpl');

}

$smarty->display('footer.tpl');


function DisplayPlayoffEvents($categoryID, $categoryName) {

	$categoryToEventCategories = array(
				24 => array(25,30),
				82 => array(83,87),
				1999 => array(19,15),
				250 => array(93,97)
			);
	echo "<div class=\"category_event_list\">\n";
	
	echo "<h1>$categoryName</h1>\n";
	if(array_key_exists($categoryID, $categoryToEventCategories)) {
		echo "<ul>\n";
		foreach ($categoryToEventCategories[$categoryID] as $id) {
				$events = GetEventsUnderCategory($id);
				if((is_array($events)) && (count($events) > 0)) {
					foreach ($events as $index=>$eventInfo) {
						$eventName = $eventInfo['name'] . " Playoff Tickets";
						$url = make_event_url($eventName, $categoryID);
						$eventName = htmlspecialchars($eventName);
						echo "<li><a href=\"$url\">$eventName</a></li>\n";
					}
				}
		}
		echo "</ul>\n";
	}
	echo "</div>";
}


?>
