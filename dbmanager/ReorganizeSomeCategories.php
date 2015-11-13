<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


# Preconditions:
#       1) AdjacencyListCategories_temp created
#	2) ModifiedPreorderTreeTraversalCategories_temp not created
#       3) CreateAdditionalSubcategories.php already called

require_once('../include/ticket_db.php');
require_once('err.php');

print_message("Renaming Some Categories.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('ReorganizeSomeCategories: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

#Rename Boxing (253) to Boxing and Fighting
#Rename "More Basketball" to "Other Basketball"
#Format: categoryID => newCategoryName

$rename_array = array(
			253 => "Boxing and Fighting", 
			165 => "Women's College Basketball", 
			156 => "Other Basketball",
			 24 => "NFL",
			 82 => "NBA",
			 91 => "NHL");
foreach($rename_array as $categoryID => $newCategoryName) {

	$bsql = "UPDATE AdjacencyListCategories_temp SET CategoryName='" . mysql_escape_string($newCategoryName) . "' WHERE CategoryID=" . $categoryID;
	$query_result = mysql_query($bsql) or print('ReorganizeSomeCategories: ' . mysql_error());

}

#Change parentCategoryID of NASCAR to 1501
# More-NCAA-Basketball-Tickets-C155 -> NCAA-Basketball-Tickets-C14
#Change parentCategoryID of WNBA, CBA, ABA to categoryID 6
#move Division 1-AA (48) under 7->13
#Change parentCategoryID of American League (15) and  National League (19) to 1999
# NHL-Stanley-Cup-Tickets-C251 -> NHL-Hockey-Tickets-C91
# Canadian-Football-Tickets-C79 -> Football-Tickets-C7
# All Division 1-A football -> College Footbal-C13
#Format: categoryID => newParentCategoryID
$mod_array = array(10 => 1501,
		   155 => 14,
		   157 => 6,
		   158 => 6,
		   161 => 6,
		   48  => 13,
		   15  => 1999,
		   19  => 1999,
		  177  => 13,
		   67  => 1519,
		   72  => 1519,
		  251  => 91,
		   79  => 7,
		   36  => 13,
		   37  => 13,
		   38  => 13,
		   39  => 13,
		   40  => 13,
		   41  => 13,
		   42  => 13,
		   43  => 13,
		   44  => 13,
		   45  => 13,
		   46  => 13,
		   47  => 13,
		   35  => 3000
);

foreach($mod_array as $categoryID => $newParentCategoryID) {

	$bsql = "UPDATE AdjacencyListCategories_temp SET ParentCategoryID= " . $newParentCategoryID . " WHERE CategoryID=" . $categoryID;
	$query_result = mysql_query($bsql) or print('ReorganizeSomeCategories: ' . mysql_error());

}
mysql_close($dbh);

print_message("ReorganizeSomeCategories: Done.");

?> 
