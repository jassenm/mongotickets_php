<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


// include the SOAP classes
require_once('../include/new_urls/ticket_db.php');
require_once('err.php');
require_once('../include/mail.php');
require_once('../include/new_urls/url_factory.inc.php');



print_message("Creating URL Lookup table....... ");


$dbh=mysql_connect ($host_name, $db_username, $db_password)
			or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($db_name);

$bsql = "DROP TABLE IF EXISTS UrlLookup_temp";
$query_result = mysql_query($bsql) or die ('drop table failed: ' . mysql_error());


$bsql = "CREATE TABLE UrlLookup_temp (
		Url CHAR(100),
		TableID INT NOT NULL,
		ID INT NOT NULL,
		PRIMARY KEY (Url)
	) ENGINE MyISAM";
$query_result = mysql_query($bsql) or die ('CREATE TABLE UrlLookup_temp failed: ' . mysql_error());

$bsql = "INSERT INTO UrlLookup_temp SELECT SanitizedEventName, 1, EventID FROM Events_temp";

$query_result = mysql_query($bsql);
if(!$query_result) {

	send_an_email('admin@email.com','INTO UrlLookup_temp failed!!!!','INSERT Events INTO UrlLookup_temp failed' .  mysql_error());
	die ('INSERT Events INTO UrlLookup_temp failed: ' . mysql_error());
}

$bsql = "INSERT INTO UrlLookup_temp SELECT SanitizedVenueName, 2, VenueID FROM Venues_temp";
# $bsql = "INSERT INTO UrlLookup_temp SELECT SanitizedVenueName, 2, VenueID FROM Venues_temp";

$query_result = mysql_query($bsql);
if(!$query_result) {


	send_an_email('admin@email.com','INTO UrlLookup_temp failed!!!!','INSERT Venues INTO UrlLookup_temp failed: ' . mysql_error());
	die ('INSERT Venues INTO UrlLookup_temp failed: ' . mysql_error());
}

$bsql = "INSERT INTO UrlLookup_temp SELECT SanitizedCategoryName, 3, CategoryID FROM ModifiedPreorderTreeTraversalCategories";

$query_result = mysql_query($bsql);
if(!$query_result) {
	send_an_email('admin@email.com','INTO UrlLookup_temp failed!!!!','INSERT ModifiedPreorderTreeTraversalCategories INTO UrlLookup_temp failed: ' . mysql_error());
	die ('INSERT ModifiedPreorderTreeTraversalCategories INTO UrlLookup_temp failed: ' . mysql_error());
}

print_message("Done.\n");

#	send_an_email('admin@email.com','GetAllEvents succeeded!!!!','GetAllEvents succeeded!!!!');

mysql_close($dbh);


print_message("Creation of UrlLookup table is complete!!\n");


?>
