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
                header("Location: http://www.mongotickets.com/log_me_in.php");
                exit;
        }


}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}




if(isset($_REQUEST['start']) && (strlen($_REQUEST['start']) < 4) && isset($_REQUEST['categoryID'])) {
        $start = $_REQUEST['start'];
        $categoryID= $_REQUEST['categoryID'];
}
else {
	$start=1;
	$categoryID=0;
}


if( $dbh) {

# get parent category id
        $Bsql = 'SELECT ParentCategoryID,CategoryName from AdjacencyListCategories WHERE CategoryID= ' . $categoryID;

        if($query_result = mysql_query($Bsql) ) {
                $table_row = mysql_fetch_array($query_result);
                $parentCategoryID = $table_row['ParentCategoryID'];
                $categoryName = $table_row['CategoryName'];
        }


        $childCategoryIDList = GetAllSubordinatesOfCategoryOrderedRank('ModifiedPreorderTreeTraversalCategories', $categoryID);
	if (count($childCategoryIDList) > 0) {
		print_header();
		echo '<h1>' . $categoryName . '</h1>';
		echo '<a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=0">Up</a>
        	<table width="820" border="1" cellspacing="0" cellpadding="4">
	        <form>
	        <tr>
	        <th align="left">Category</th>
	        <th align="left">Rank</th>
	        <th align="left">Category ID</th>
	        <th align="left">Parent Category ID</th>
	        </tr>';

                foreach ( $childCategoryIDList as $childCategoryIDArray) {
                        $url = make_category_url($childCategoryIDArray['name'], $childCategoryIDArray['id']);
                        echo '<tr align="left">';
			echo '<td align="left" width="300"><a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=' . $childCategoryIDArray['id'] . '">' . $childCategoryIDArray['name'] . '</a></td>';
			echo '<td align="left" width="25"><input type="text" name="rank" id="rank" size="5" value="' . $childCategoryIDArray['rank'] . '"></td>';
			echo '<td align="left" width="25">' . $childCategoryIDArray['id'] . '</td>';
			echo '<td align="left" width="25"><input type="text" name="parentCategoryID" id=""parentCategoryID"" size="5" value="' . $parentCategoryID . '"></td>';
			echo '</tr>
				';
                }
		echo '</form>';
		echo '</table>';
	}
	else {
 		header("Location: http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=" . $categoryID );
	}

	mysql_close($dbh);
}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}

echo '
</body>
</html>';

function print_header() {
echo '
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Category Ranking Editor</title>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
</head>
<body>
<h2><a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=0">All Categories</a>&nbsp;<a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=3">Sports Categories</a>&nbsp;<a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=2">Concert Categories</a>&nbsp;<a href="http://www.mongotickets.com/category_ranking_editor.php?start=1&categoryID=4">Theater Categories</a></h2>

<h2><a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=0">All Events</a>&nbsp<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=3">Sports Events</a>&nbsp;<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=2">Concert Events</a>&nbsp;<a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=4">Theater Events</a></h2>

<br/>';

}


?>
