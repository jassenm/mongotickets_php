<?php

function AppendBreadcrumb($currentString, $url, $text) {
include('../include/host_info.inc.php');
	$currentString .= "&nbsp;&gt;&nbsp;" .  "<a href=\"$url\">$text</a>";
	return $currentString;
}

function AppendBreadcrumbNoAnchor($currentString, $text) {
include('../include/host_info.inc.php');
	$currentString .= "&nbsp;&gt;&nbsp;" .
				"$text";
	return $currentString;
}

function Breadcrumbs($categoryID, $eventID) {
include('../include/host_info.inc.php');

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
    		if(in_array($categoryID, $top_level_category_array) ) {
			$url =  make_main_category_url($categoryName);
        		$breadcrumb_string .= "<a href=\"$url\">$categoryName Tickets</a>";
    		}
    		elseif ($categoryID == 0) {
        		$breadcrumb_string .= "<a href=\"$root_url/\">Home</a>";
    		}
    		else {
			$url =  make_category_url($categoryName, $categoryID);
        		$breadcrumb_string .= "<a href=\"$url\">$categoryName</a>";
		}
	}
 
#if(pagetype is not category then add event or event and venue)
#do not anchor current page breadcrumb. (optional)
 
#if event page add event name to breadcrumb with no anchor,
 
#if venue page add "even name at venue"with no anchor
 
#if tickets list page, same as venue page.
 
	return $breadcrumb_string;
}


?>
