<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


require_once('../include/new_urls/ticket_db.php');
include('../include/error.php');
include('../include/new_urls/url_factory.inc.php');


if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ("mongo_tickets2");

	$query = "SELECT CategoryName,CategoryID FROM AdjacencyListCategories WHERE 1";
        if($query_result = mysql_query($query) ) {

                $adjCats = Array();
                while ($table_row = mysql_fetch_row($query_result)) {
                        $catName = $table_row[0];
                        $catID = $table_row[1];
                        $adjCats[$catID] = strtolower(_prepare_url_text($catName));
print ".";
                }

                foreach($adjCats as $id=>$sanCatName) {

                $bsql = "UPDATE AdjacencyListCategories SET SanitizedCategoryName= '" . $sanCatName . "' WHERE CategoryID=" . $id;
                $query_result = mysql_query($bsql) or print(': ' . mysql_error());

                }

        }


	mysql_close($dbh);
}
else {
	handle_error_no_exit ('sanitize_AdjacencyListCategories.php: I cannot connect to the database because: ' . mysql_error());
}

?>
