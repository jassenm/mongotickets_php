#!/usr/bin/php -q
<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

$absPath = dirname( __FILE__ );


echo "\nCreating Url Lookup table\n";
echo date('l F d Y h:i:s A');

chdir($absPath);

# Order of files is important
require('CreateUrlLookupTable.php');
#require('RenameUrlLookupTable.php');
require('RenameEventTable.php');
require('RenameVenueTables.php');
#require('RenameUrlLookupTable.php');



echo date('l F d Y h:i:s A');
echo "\n";

?>
