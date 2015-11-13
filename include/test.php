<?php

require_once('url_factory.inc.php');

$str = make_category_url('Sports', 3);
echo "\n$str\n";

$dt = date("Y/m/d");
echo "the date = $dt\n";
?>
