<?php

require_once('../include/host_info.inc.php');

// prepares a string to be included in an URL
function _prepare_url_text($string)
{
  // replace & with and
  $string = preg_replace('/\s*&\s*/' , ' and ', $string);
  // remove all characters that aren't a-z, 0-9, dash, underscore or space
  $NOT_acceptable_characters_regex = '#[^-a-zA-Z0-9_ ]#';
  $string = preg_replace($NOT_acceptable_characters_regex, '', $string);

  // remove all leading and trailing spaces
  $string = trim($string);

  // change all dashes, underscores and spaces to dashes
  $string = preg_replace('#[-_ ]+#', '-', $string);

  // return the modified string
  return $string;
}

function make_main_category_url($categoryName) {

	$urlCategoryName = _prepare_url_text($categoryName);

	$url = SITE_DOMAIN . "/$urlCategoryName-Tickets.html";
	return $url;
}

function make_category_url($categoryName, $categoryID) {

	$urlCategoryName = _prepare_url_text($categoryName);

	$url = SITE_DOMAIN . "/category/$urlCategoryName-Tickets-C$categoryID.html";
	return $url;
}

function make_event_url($eventName, $eventID) {

	$urlEventName = _prepare_url_text($eventName);

        $url = SITE_DOMAIN . "/events/$urlEventName-Tickets-E$eventID.html";
        return $url;
}

function make_production_url($eventName, $productionID) {

	$urlEventName = _prepare_url_text($eventName);

        $url = SITE_DOMAIN .  "/productions/$urlEventName-Tickets-P$productionID.html";
        return $url;
}

function make_production_at_venue_url($eventName, $eventID, $venueID) {

	$urlEventName =  _prepare_url_text($eventName);

        $url = SITE_DOMAIN . "/productions/$urlEventName-Tickets-E$eventID.html?vid=$venueID";
        return $url;
}

function make_venues_url($venueName, $venueID) {

	$urlVenueName =  _prepare_url_text($venueName);

        $url = SITE_DOMAIN . "/venues/$urlVenueName-Tickets-V$venueID.html";
        return $url;
}
?>
