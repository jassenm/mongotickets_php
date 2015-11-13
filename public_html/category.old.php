<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#
require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require('../include/breadcrumbs.inc.php');



if(isset($_REQUEST['name']) && isset($_REQUEST['id']) && (($_REQUEST['id'] < 2000) && ($_REQUEST['id'] >= 0)) ) {
	$id = $_REQUEST['id'];
	$categoryName = $_REQUEST['name'];
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
$descr = COMPANY_NAME . ", the best place to find " . preg_replace ('/-/', ' ', $categoryName) . " tickets and more.";
$smarty->assign("MetaDescr", $descr);


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {

	mysql_select_db ($db_name);

	$categoryID = $id;
	$limit = -1;
	$subcategories = GetImmediateSubordinatesOfCategory($categoryID, $limit);

	if(!is_array($subcategories)) {
                $error_message = get_error_message();
                $smarty->assign("ErrorMessage", $error_message);
                $smarty->display('error_page.tpl');
	}
	else {
		$categoryName = preg_replace ('/-/', ' ', $categoryName);
		$title = "$categoryName Tickets @ " . COMPANY_NAME;
		$smarty->assign("title", $title);

		$keywords = GetKeywordsForCategoryID($categoryID);
		$smarty->assign("SeoKeywords", $keywords);
		$smarty->display('main.tpl');

		$breadcrumb_str = Breadcrumbs($categoryID, 0);

		# NFL (24) , NBA (82),  MLB (1999), NHL (91)
		$league_categoryIDs = array(24, 82, 1999, 91);
		$playoff_categoryIDs = array(178=>24, 182=>82,134=>1999, 250=>91);
		if(array_key_exists($categoryID, $playoff_categoryIDs))  {
			echo "<div id=\"content\">\n";
			echo "<div id=\"breadcrumb_trail\">\n";
			echo $breadcrumb_str;
			echo "</div>\n";
			DisplayPlayoffEvents($playoff_categoryIDs[$categoryID],$categoryName);
		}
		elseif((in_array($categoryID, $league_categoryIDs)) && (count($subcategories) > 0) ) {

			echo "<div id=\"content\">\n";
			echo "<div id=\"breadcrumb_trail\">\n";
			echo $breadcrumb_str;
			echo "</div>\n";
			echo "<div class=\"leaguetable\">\n";
	
			echo "<h1>$categoryName</h1>\n";
			echo "<table>\n";
			echo "<tr>\n";
			# division level
			for($i=0; $i < 2; $i++) {
				echo "<td>" . $subcategories[$i]['name'] . "</td>";
			}
			echo "</tr>\n";
			echo "<tr>";
			for ($i=0; $i < 2; $i++) {
                	        $subsubcategories = GetImmediateSubordinatesOfCategory($subcategories[$i]['id'], $limit);
				if((is_array($subsubcategories)) && (count($subsubcategories) > 0)) {
                                	echo "<td>";
					foreach ($subsubcategories as $index=>$subsubcategoryArray) {
						echo "<ul><li>$subsubcategoryArray[name]</li>\n";
						if($query_result = mysql_query('SELECT EventID,EventName FROM Events WHERE CategoryID = ' . $subsubcategoryArray[id] . ' ORDER BY EventName ASC')) {
							while ($table_row = mysql_fetch_array($query_result)) {
								$eventID = $table_row['EventID'];
								$eventName = $table_row['EventName'];
								$url = make_event_url($eventName, $eventID);
								$eventName = htmlspecialchars($eventName);
								echo "<li><a href=\"$url\">$eventName</a></li>\n";
							}
						}
						else {
							handle_error_no_exit ('category.code: ' . mysql_error());
						}
						echo "</ul>";
                               		}
                                	echo "</td>";
                        	}
			}
			echo "</tr>";
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
					$url = make_event_url($eventName, $eventID);
					$eventName = htmlspecialchars($eventName);
					echo "<li><a href=\"$url\">$eventName</a></li>\n";
				}
			}
			echo "</ul></div>";
        		echo "</div>";
		}
		else {
			$events = array();

			#$smarty->assign("SubCategories", $subcategories);
			#$smarty->assign("NumSubCategories", count($subcategories));
			$smarty->assign("Breadcrumbs", $breadcrumb_str);
			$smarty->display('events.tpl');


			ob_start();
			echo "<h2>$categoryName</h2>";

			$number_of_events = 0;
			if(($categoryID == 1526) || ($categoryID == 1310) ||  ($categoryID == 1111)) {
				$bsql = 'SELECT Events.EventID,Events.EventName,AdjacencyListCategories.CategoryName FROM Events LEFT JOIN (AdjacencyListCategories) ON (AdjacencyListCategories.CategoryID = Events.CategoryID) INNER JOIN Productions ON (Events.EventID = Productions.EventID) WHERE Events.CategoryID = ' . $categoryID . ' GROUP BY Events.EventID ORDER BY Events.EventName ASC';
			}
			else {
				$bsql = 'SELECT EventID,EventName,AdjacencyListCategories.CategoryName FROM Events LEFT JOIN (AdjacencyListCategories) ON (AdjacencyListCategories.CategoryID = Events.CategoryID) WHERE Events.CategoryID = ' . $categoryID . ' ORDER BY EventName ASC';
			}
			if($query_result = mysql_query($bsql) ) {
				echo "<table class=\"category_event_list\">";
				while ($table_row = mysql_fetch_array($query_result)) {
					$eventID = $table_row['EventID'];
					$eventName = $table_row['EventName'];
					$url = make_event_url($eventName, $eventID);
					$eventName = htmlspecialchars($eventName);
					$catName = $table_row['CategoryName'];
					echo "<tr><td><a href=\"$url\">$eventName</a></td></tr>";
					$number_of_events++;
				}
				echo "</table>";
			}
			else {
				handle_error_no_exit ('category.code: ' . mysql_error());
			}
			
			$smarty->assign("EventsArray", $events);
			$smarty->assign("NumEvents", count($events));
			$smarty->assign("categoryName", $catName);
			if(count($subcategories) > 0) {
				echo "<div class=\"category_subcategory_list\">";
				echo "<ul>";
				foreach ($subcategories as $index=>$subcategoryInfo) {
                        		echo "<li><a href=\"" . $subcategoryInfo['url'] . "\">" . $subcategoryInfo['name'] . "</a> &nbsp;&nbsp;&nbsp;</li>";
				}
				echo "</ul>";
				echo "</div>";
			}
			#ob_end_clean();
			#$output = ob_get_contents();
			ob_end_flush();
			
			#file_put_contents("$categoryName1.html", $output);
                        if(($number_of_events < 1) && (count($subcategories) < 1)) {
				$smarty->assign("EventName", $categoryName);
				$smarty->display('no_tickets.tpl');

			}
		}
	}
	mysql_close($dbh);
}
else {
	handle_error_no_exit ('category.code: I cannot connect to the database because: ' . mysql_error());
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
						$url = make_event_url($eventName, $eventInfo['id']);
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
