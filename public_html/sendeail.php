<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# MongoTickets.com Confidential Proprietary.
#


// put full path to Smarty.class.php
require('../include/smarty_package.php');
require('../lib/php/Smarty/Smarty.class.php');
include('../include/host_info.inc.php');
include('../include/domain_info.inc.php');


$smarty = new Smarty;

$smarty->template_dir = '../smarty/templates/new_urls/';
$smarty->compile_dir = '../smarty/templates_c/new_urls/';
$smarty->cache_dir = '../smarty/cache/new_urls/';
$smarty->config_dir = '../smarty/configs';

$smarty->compile_check = true;
#$smarty->debugging = true;

$smarty->assign("RootUrl", $root_url);

$smarty->display('main.tpl');

$smarty->assign("DomainName", DOMAIN_NAME);

?>

<div id="content">
<div class="left_bar">
<h1>Thank you for your inquiry!  </h1>

<br />
<div class="info_text">


<?php

$ip = $_POST['ip'];
$httpref = $_POST['httpref'];
$httpagent = $_POST['httpagent'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$visitormail = $_POST['visitormail'];
$areacode = $_POST['areacode'];
$firstthreedigits = $_POST['firstthreedigits'];
$lastfourdigits = $_POST['lastfourdigits'];
$eventname = $_POST['eventname'];
$eventdate = $_POST['eventdate'];
$message = $_POST['message'];


if (eregi('http:', $message)) {
die ("Invalid message, please try again.");
}
if(!$visitormail == "" && (!strstr($visitormail,"@") || !strstr($visitormail,".")))
{
	echo "<p>Email address is invalid. Use the Back Button and enter valid e-mail address</p>\n";
	$smarty->display('footer.tpl');
	die();
}

if(empty($firstname) || empty($lastname) || empty($visitormail) || empty($message)) {
	echo "<p>\n";
	if(empty($firstname)) {
		echo "First name is empty. Please enter first name.<br/>";
	}
	if(empty($lastname)) {
		echo "Last name is empty. Please enter last name.<br/>";
	}
	if(empty($visitormail)) {
		echo "Email address is empty. Please enter a valid email address.<br/>";
	}
	if(empty($message)) {
		echo "Message is empty. Please enter a message.<br/>";
	}

	echo '</div>';
	$smarty->display('footer.tpl');
	die();

}




$todayis = date("l, F j, Y, g:i a") ;

$subject = "Mongotickets.com visitor question/comment";

$message = stripcslashes($message);

$message = " $todayis [EST] \n
Message: $message \n
From: $firstname $lastname ($visitormail)\n
Additional Info : IP = $ip \n
Browser Info: $httpagent \n
Referral : $httpref \n
";

$from = "From: $visitormail\r\n";


mail("mongoticketsales@gmail.com", $subject, $message, $from);

?>

<p>
Date: <?php echo $todayis ?>
<br />
Thank You for your inquiry <?php echo "$firstname $lastname" ?> ( <?php echo $visitormail ?> ).<br /> A representative will contact you within 48 hours.
<br />
</p>

</div> <!-- end info_text -->
</div> <!-- end left -->

<?php

$smarty->display('right_bar.tpl');
$smarty->display('left_column.tpl');

$smarty->display('footer.tpl');




?>

