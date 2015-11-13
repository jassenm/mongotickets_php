<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');
require_once('../include/ticket_db.php');
require('DbUtils.php');
require('Utils.php');
include('../include/error.php');

if((isset($_REQUEST['id']) && ($_REQUEST['id'] < 2001)) ) {
        $categoryID = $_REQUEST['id'];
}
else {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://www.mongotickets.com/');
        exit();
}

if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
	mysql_select_db ('mongo_tickets2') or die('category.redir.php: failed to select db');

    $Bsql = "SELECT SanitizedCategoryName FROM ModifiedPreorderTreeTraversalCategories " .
                " WHERE CategoryID = $categoryID";
        $result = mysql_query($Bsql);
        while ($row = mysql_fetch_array($result)) {
                $sanitizedCategoryName = $row['SanitizedCategoryName'];
        }

	$url = ($sanitizedCategoryName == '') ? '' : $sanitizedCategoryName . '-tickets/';
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://www.mongotickets.com/' . $url);
        exit();
}
header('HTTP/1.1 301 Moved Permanently');
header('Location: http://www.mongotickets.com/');
exit();

?>
