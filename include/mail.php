<?php

function send_an_email($to, $subject, $body) {

	$headers = 'From: mongoticketssales@gmail.com' . "\r\n" .
    'Reply-To: mongoticketssales@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


	if (mail($to, $subject, $body, $headers)) {
		echo("<p>Message successfully sent!</p>");
	} 
	else {
		echo("<p>Message delivery failed...</p>");
	}
}
?>
