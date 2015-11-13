<?php

define("DOMAIN_NAME", 'MongoTickets.com');
define("COMPANY_NAME", 'MongoTickets.com');
define("TITLE_TAG", 'Mongo Tickets - Sports Tickets, Concert Tickets, Theater Tickets');

function redir_301($url='') {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        exit();
}


?>
