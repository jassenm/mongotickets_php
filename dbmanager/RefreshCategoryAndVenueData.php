#!/usr/bin/php -q
<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

$absPath = dirname( __FILE__ );

echo "\nImporting Category and Venue Data from Ticketsnow Web Services\n";
echo date('l F d Y h:i:s A');

chdir($absPath);

# Order of files is important
#require('ImportCategoriesFromWS.php');
#require('CreateAdditionalCategories.php');
#require('ReorganizeSomeCategories.php');
#require('CreateModifiedPreorderTreeOfCategories.php');
#require('CreateHotCategoryCandidateList.php');
#require('CreateSportsTable.php');
require('ImportVenuesFromWS.php');
#require('CreateCategoryTableIndices.php');
#require('SetCategoryRankings.php');
#require('CreateCategoryKeywordsTables.php');
#require('RenameCategoryTables.php');
#require('RenameVenueTables.php');


echo date('l F d Y h:i:s A');
echo "\n";

?>
