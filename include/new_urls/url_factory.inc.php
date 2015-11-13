<?php


// prepares a string to be included in an URL
function _prepare_url_text($string)
{
  // replace & with and
  $string = preg_replace('/\s*&\s*/' , ' and ', $string);
  // remove all characters that aren't a-z, 0-9, dash, underscore or space
  $NOT_acceptable_characters_regex = '#[^-a-zA-Z0-9_ .]#';
  $string = preg_replace($NOT_acceptable_characters_regex, '', $string);

  // remove all leading and trailing spaces
  $string = trim($string);

  // change all dashes, underscores and spaces to dashes
  $string = preg_replace('#[-_ .]+#', '-', $string);
  $string = rtrim($string, '-');

  // return the modified string
  return $string;
}

function make_main_category_url($categoryName) {

	$urlCategoryName = strtolower(_prepare_url_text($categoryName));

	$url = SITE_DOMAIN . "/$urlCategoryName/";
	return $url;
}

function make_category_url($categoryName) {

	$urlCategoryName = strtolower(_prepare_url_text($categoryName));

 	# cat up to
	$url = SITE_DOMAIN . "/$urlCategoryName-tickets/";
	return $url;
}

function make_event_url($eventName) {


	$urlEventName = strtolower(_prepare_url_text($eventName));

        $url = SITE_DOMAIN . "/$urlEventName-tickets/";
        return $url;
}

function make_production_url($eventName, $productionID, $city, $eventTypeID) {

	$urlEventName = strtolower(_prepare_url_text($eventName));
	$urlCity = strtolower(_prepare_url_text($city));

       	$url = SITE_DOMAIN .  "/$urlEventName-tickets/?event_id=$productionID";
	if($eventTypeID == 4) {
		if(strlen($urlCity) > 0) {
	        	$url = SITE_DOMAIN .  "/$urlEventName-tickets-$urlCity/?event_id=$productionID";
		}
	}
        return $url;
}

function make_production_at_venue_url($eventName, $city) {

	$sanitizedEventName =  strtolower(_prepare_url_text($eventName));
	$sanitizedCity =  strtolower(_prepare_url_text($city));

        $url = SITE_DOMAIN . "/$sanitizedEventName-tickets-$sanitizedCity/";
        return $url;
}

function make_venues_url($venueName, $sanitizedVenueName, $regionCode) {

        $Bsql = "SELECT SanitizedVenueName FROM Venues " . 
                " WHERE VenueID = $venueID";
        $result = mysql_query($Bsql);
        while ($row = mysql_fetch_array($result)) {
                $sanitizedVenueName = $row[0];
        }

        $url = SITE_DOMAIN . "/$sanitizedVenueName-tickets/";
        return $url;
}
function make_venue_url($sanitizedVenueName) {
        $url = SITE_DOMAIN . "/$sanitizedVenueName-tickets/";
        return $url;

}
?>
