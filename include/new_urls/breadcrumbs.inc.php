<?php

function AppendBreadcrumb($currentString, $url, $text) {
include('../include/host_info.inc.php');
	$currentString .= "&gt;" . "<a href=\"$url\">" . $text . "</a>";
	return $currentString;
}

function AppendBreadcrumbNoAnchor($currentString, $text) {
include('../include/host_info.inc.php');
	$currentString .= "&gt;&nbsp;" . $text;
	return $currentString;
}

function Breadcrumbs($categoryID) {
include('../include/host_info.inc.php');


	$Bsql = "SELECT C.CategoryID, C.CategoryName, C.CategoryUrl" . 
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
    		$categoryUrl = $row['CategoryUrl'];
    		$categoryID = $row['CategoryID'];
    		if(strlen($breadcrumb_string) > 0) {
        		$breadcrumb_string .= "&gt;";
    		}
    		if ($categoryID == 0) {
        		$breadcrumb_string .= '<a href="' . $root_url . '/">Home</a>';
    		}
    		else {
			$url = make_category_url($categoryName);
        		$breadcrumb_string .= "<a href=\"$url\">" . htmlspecialchars($categoryName) . " Tickets</a>";
		}
	}
 
 
	return $breadcrumb_string;
}


?>
