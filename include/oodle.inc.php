<?php

define('DOMAIN_PATH', 'http://www.mongotickets.com');
// sitemap generator class
class Oodle
{
  // constructor receives the list of URLs to include in the sitemap
  function Oodle($items = array())
  {
    $this->_items = $items;
  }

  // add a new item 
  function addItem($url,
                   $category = '',
                   $id = '',
                   $title = '',
                   $event_date = '',
		   $event_time = '',
		   $event_venue = '',
		   $city = '',
		   $state = '',
		   $zip_code = '',
		   $price ='',
		   $additional_fields = array())
  {
    $this->_items[] = array_merge(array('category' => $category,
					'id' => $id,
					'title' => $title,
					'url' => DOMAIN_PATH . $url,
					'price' => $price,
                                        'event_date' => $event_date,
                                        'event_time' => $event_time,
					'event_venue' => $event_venue,
					'city' => $city,
					'state' => $state,
					'zip_code' => $zip_code,
					'currency' => 'USD',
					'seller_url' => DOMAIN_PATH),
					$additional_fields
				);
  }

  // get Google sitemap 
  function getOodle()
  {
   $gzfilename = "C:\HostedSitesApache\MongoTickets_com\public_html\oodle_feed.xml.gz";
   $zp = gzopen($gzfilename, "w9");

    gzwrite ($zp, '<?xml version="1.0" encoding="ISO-8859-1"?><listings>');
    foreach ($this->_items as $i)
    {
      gzwrite ($zp, '<listing>');
      foreach ($i as $index => $_i)
      {
        if (!$i) continue;
        gzwrite ($zp, "<$index>" . $this->_escapeXML($_i) . "</$index>");
      }
      gzwrite ($zp, "</listing>\n");
    }
    gzwrite ($zp, '</listings>');


   // close gz file
   gzclose($zp);
   return;



  } 

  // get Yahoo sitemap
  function getYahoo()
  {
    #ob_start();
    header('Content-type: text/plain');
    foreach ($this->_items as $i)
    {
      echo $i['loc'] . "\n";
    }
    #return ob_get_clean();
    return '';
  }
  // escape string characters for inclusion in XML structure
  function _escapeXML($str)
  {
    $translation = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);   
    foreach ($translation as $key => $value)
    {
      $translation[$key] = '&#' . ord($key) . ';';
    }
    $translation[chr(38)] = '&';  
    return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&#38;" ,
                        strtr($str, $translation));
  }
}
?>
