<?php

define('DOMAIN_PATH', 'http://www.mongotickets.com');
// sitemap generator class
class Sitemap
{
  // constructor receives the list of URLs to include in the sitemap
  function Sitemap($items = array())
  {
    $this->_items = $items;
  }

  // add a new sitemap item 
  function addItem($url,
                   $lastmod = '',
                   $changefreq = '',
                   $priority = '',
                   $additional_fields = array())
  {
    $this->_items[] = array_merge(array('loc' => DOMAIN_PATH . $url,
                                        'lastmod' => $lastmod,
                                        'changefreq' => $changefreq,
                                        'priority' => $priority),
                                  $additional_fields);
  }

  // get Google sitemap 
  function getGoogle()
  {
    ob_start();
    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($this->_items as $i)
    {
      echo '<url>';
      foreach ($i as $index => $_i)
      {
        if (!$_i) continue;
        echo "<$index>" . $this->_escapeXML($_i) . "</$index>";
      }
      echo "</url>\n";
    }
    echo '</urlset>';
#    ob_end_flush(); 
#    return '';




/* PERFORM COMLEX QUERY, ECHO RESULTS, ETC. */
   $page = ob_get_contents();
   #ob_end_flush();
   #$cwd = getcwd();
   #$file = "$cwd" .'/'. "sitemap.xml";
   $file = "sitemap.new.xml";
   #@chmod($file,0755);
   $fw = fopen($file, "w");
   fputs($fw,$page, strlen($page));
   fclose($fw);


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
