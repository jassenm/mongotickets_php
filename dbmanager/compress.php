<?php


   $file_to_compress = "oodle_feed.xml"; //any type of file
   $gz_file_to_produce="oodle_feed.xml.gz";

   $data = implode("", file($file_to_compress));
   $gzdata = gzencode($data, 9);
   $fp = fopen($gz_file_to_produce, "w");
   fwrite($fp, $gzdata);
   fclose($fp);

?>
