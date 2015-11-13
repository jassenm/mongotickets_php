<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#

require_once('../include/mail.php');

$to      = 'admin@email.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: admin@email.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);



?>
