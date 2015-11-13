<?php


include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');


if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);



	$Bsql = 'SELECT EventName, Events.EventID, Events.EventRank, CategoryName, COUNT(Productions.EventID) as num_prods from Events LEFT JOIN Productions ON (Events.EventID = Productions.EventID) LEFT JOIN ModifiedPreorderTreeTraversalCategories as Cat ON (Events.CategoryID = Cat.CategoryID) WHERE 1 GROUP BY EventID ORDER BY COUNT(Events.EventID) DESC';


	echo "Name,EventID,Rank,Category,MLB,NFL,NHL"; 
       if($result = mysql_query($Bsql)) {
                while ($table_row = mysql_fetch_array($result)) {
                        $eventName = str_replace  ( ",", '', $table_row['EventName']);
                        $eventID = $table_row['EventID'];
                        $eventRank = $table_row['EventRank'];
                        $categoryName = $table_row['CategoryName'];
			echo "\n$eventName,$eventID,$eventRank,$categoryName";
                }
		echo "\n";
        }

}
else {
        print 'I cannot connect to the database because: ' . mysql_error();
}


?>
