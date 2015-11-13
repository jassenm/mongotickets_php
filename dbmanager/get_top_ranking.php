<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


require_once('../include/ticket_db.php');
require_once('../public_html/DbUtils.php');



$input_filename = "EventRankings.csv";



if(($fh = fopen($input_filename, "r")) === FALSE){
        die('Failed to open $input_filename file!');
}

$buffer = fgetcsv($fh, 1000, ",");
while (($buffer = fgetcsv($fh, 1000, ",")) !== FALSE) {
	echo "\n" . $buffer[0];
	echo "#" . $buffer[1];
	echo "#" . $buffer[2];
}

	
fclose($fh);


?>
