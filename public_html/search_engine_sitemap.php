<?php
// redirect requests to dynamic to their keyword rich versions
require_once ('../include/sitemap.inc.php');
// load configuration
// load URL factory
require_once ('../include/url_factory.inc.php');
require_once('../include/ticket_db.php');
include('../include/error.php');
require_once('DbUtils.php');
require_once('Utils.php');


// create the Sitemap object
$s = new Sitemap();

# make mainpage url
# get all categories
#    create category urls
# get all events
#    create event urls
#
// add sitemap items
#  YYYY-MM-DD
$dt = date("Y-m-d");

$s->addItem($root_url . '/', $dt, 'weekly', '1.0');
$s->addItem($root_url . '/Sports-Tickets.html', $dt, 'weekly', '0.75');
$s->addItem($root_url . '/Concert-Tickets.html', $dt, 'weekly', '0.75');
$s->addItem($root_url . '/Theater-Tickets.html', $dt, 'weekly', '0.75');

if($dbh=mysql_connect ($host_name, $db_username, $db_password)) {
        mysql_select_db ($db_name);

	$categoryIDList = GetAllSubordinatesOfCategory('ModifiedPreorderTreeTraversalCategories', 0);
	
	if(is_array($categoryIDList)) {
		foreach ( $categoryIDList as $categoryIDArray) {
			$url = make_category_url($categoryIDArray['name'], $categoryIDArray['id']);
			$s->addItem($url, $dt, 'weekly', '0.75');
		}
	}
	else {
		echo "Database error!";
	}
        $bsql = "SELECT EventName, EventID FROM `Events` WHERE CategoryID<>1310 AND CategoryID<>1111";
        if($query_result = mysql_query($bsql) ) {
                while ($table_row = mysql_fetch_array($query_result)) {
			$eventName = $table_row['EventName'];
			$eventID = $table_row['EventID'];
			$url= make_event_url($eventName, $eventID);
			$s->addItem($url, $dt, 'daily','0.5');
		}
	}
	else {
		echo "Database error!";
	}


	$bsql = "SELECT EventName, Events.EventID FROM `Events` INNER JOIN Productions ON Productions.EventID=Events.EventID WHERE CategoryID=1310 OR CategoryID=1111 GROUP BY Events.EventID";
        if($query_result = mysql_query($bsql) ) {
                while ($table_row = mysql_fetch_array($query_result)) {
                        $eventName = $table_row['EventName'];
                        $eventID = $table_row['EventID'];
                        $url= make_event_url($eventName, $eventID);
                        $s->addItem($url, $dt, 'daily','0.5');
                }
        }
        else {
                echo "Database error!";
        }



	mysql_close($dbh);
}
else {
        handle_error_no_exit ('main.code: I cannot connect to the database because: ' . mysql_error());

}

#$_GET['target'] = 'google';
$_GET['target'] = 'google';


// output sitemap
if (isset($_GET['target']))
{  
  // generate Google sitemap
  if (($target = $_GET['target']) == 'google')
  {
#ob_start();

    #echo $s->getGoogle();
    $s->getGoogle();
#ob_get_clean();
  }
  // generate Yahoo sitemap
  else if ($target == 'yahoo')
  {
    echo $s->getYahoo();
  }
}
?> 
