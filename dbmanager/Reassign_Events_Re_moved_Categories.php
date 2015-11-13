<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

# Preconditions:
#       1) AssignEventsToCategories.php has been called

require_once('../include/new_urls/ticket_db.php');
require_once('err.php');

print_message("Reassigning Events who categories has been removed or moved.......");

$dbh=mysql_connect ($host_name, $db_username, $db_password)
                   or die ('removed_categories_redir.php: I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$input_filename = "Re_moved_Categories_Redir.csv";

	print_message("Processing $input_filename .......");
	$fh = fopen($input_filename, "r") or die('removed_categories_redir.php: Cannot open ' . $input_filename . "\n");
	# data format is:
	# old category id, new category id
	# (0)              (1)
	# skip over first line
	$data = fgetcsv($fh, 1000, ",");
	while (($data = fgetcsv($fh, 100, ",")) !== FALSE) {
		$old_cat_id = $data[0];
		$new_cat_id = $data[1];
		$bsql = "UPDATE Events_temp SET CategoryID= " . $new_cat_id . " WHERE CategoryID=" . $old_cat_id;
		$query_result = mysql_query($bsql) or print('removed_categories_redir.php: ' . mysql_error());
	}
	fclose($fh);

mysql_close($dbh);

print_message("removed_categories_redir.php: Done.");

?> 
