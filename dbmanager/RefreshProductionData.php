#!/usr/bin/php -q
<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

$absPath = dirname( __FILE__ );


echo "\nImporting Data from Ticketsnow Web Services\n";
echo date('l F d Y h:i:s A');

chdir($absPath);

# Order of files is important
require('ImportProductionsFromWS.php');
require('CreateProductionTableIndices.php');
require('RenameProductionTables.php');


echo date('l F d Y h:i:s A');
echo "\n";

?>
