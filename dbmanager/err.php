<?php

ini_set('error_reporting', E_ALL);

error_reporting(E_ALL);

function print_message($message_string) {
        echo "\n" . date('l F d Y h:i:s A') . " __FILE__: $message_string";

}


?>
