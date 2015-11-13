#!/usr/bin/php -q
<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

$absPath = dirname( __FILE__ );


echo "\n" . date('l F d Y h:i:s A');
echo " RefreshEventData.php: Importing Event Data from Ticketsnow Web Services\n";
echo date('l F d Y h:i:s A');

chdir($absPath);

# Order of files is important
require('ImportEventsFromWS.php');
require('AssignEventsToCategories.php');
require('SetEventRankings.php');
require('Reassign_Events_Re_moved_Categories.php');
require('CreateEventTableIndices.php');
# require('RenameEventTable.php');


echo "\n";
echo "\nRefreshEventData.php - Done.\n";
echo date('l F d Y h:i:s A');
echo "\n";

?>
