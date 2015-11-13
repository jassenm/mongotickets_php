<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../lib/nusoap.php');
require_once('../include/new_urls/ticket_db.php');
require_once('../include/EventInventoryWebServices.inc.php');
require_once('err.php');
require_once('../include/mail.php');
require_once('../include/new_urls/url_factory.inc.php');



print_message("Preparing database for Event import....... ");


$dbh=mysql_connect ($host_name, $db_username, $db_password) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);


$eventName_mod_list = Array(
                14255 => 'Ourglass Concert',
                8405 => 'Strunz n Farah',
                14340 => 'The Jena Six Empowerment Concert'
                );



foreach($eventName_mod_list as $eid => $ename) {
        $san_ename = strtolower(_prepare_url_text($ename));
        $bsql = "UPDATE Events SET EventName='$ename',SanitizedEventName='$san_ename' WHERE EventID=$eid";
        $query_result = mysql_query($bsql) or die ('UPDATE Events_temp SET EventName failed: ' . mysql_error());
}




mysql_close($dbh);

?>
