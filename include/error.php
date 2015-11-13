<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


ini_set('display_errors','0n');
ini_set('log_errors','On');
#ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('error_log','../logs/error_log.txt');
ini_set('error_reporting', E_ALL);

error_reporting(E_ALL);


function handle_error($err_string) {
	error_log($err_string, 0);
        exit;
}

function handle_error_no_exit($err_string) {
	error_log($err_string, 0);
}

function get_error_message() {
	$error_message = "Sorry, for the time being the site is not available. The site administration is already aware of the problem. Please come back later.";
	return $error_message;
}

?>
