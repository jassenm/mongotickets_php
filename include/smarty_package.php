<?php

define('SMARTY_DIR', '/var/www/vhosts/cpcworks.com/mongotickets/lib/php/Smarty');
$include_path = get_include_path();
$include_path .= (PATH_SEPARATOR.SMARTY_DIR); 
set_include_path($include_path);

?>
