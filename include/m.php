
<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/mail.php');



ini_set("sendmail_from", "info@mydomain.com");

send_an_email('admin@email.com','Test !!!!','Test ...!!!!');

?>
