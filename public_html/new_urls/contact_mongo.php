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

/* $smarty->display('contact_us.tpl');
*/

?>


<div id="content">
<h1 style="margin: 0px 0px 0px 16px;"><strong>Contact 
<?php echo DOMAIN_NAME ?>
</strong></h1>

<br />
<div class="info_text">

<script language="Javascript" type="text/javascript">

function validateemail (string) { 
	var valregex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
	if (valregex.test(string)) {return 1;} 
	      else {return 0;} 
}
function validatephone (p1,p2,p3) {
	var p1test = /^([0-9]{3})/;
	var p2test = /^([0-9]{3})/;
	var p3test = /^([0-9]{4})/;
	if ((p1test.test(p1)) && (p2test.test(p2)) && (p3test.test(p3))) {return 1;}
	else {return 0;}
}

function validateform() {
	//Setting variables
	var error = '';
	var p1 = '' + document.getElementById('areacode').value;
	var p2 = '' + document.getElementById('firstthreedigits').value;
	var p3 = '' + document.getElementById('lastfourdigits').value;
	var first_name = document.getElementById('firstname').value;
	var last_name = document.getElementById('lastname').value;
	
	
	var name = first_name+' '+last_name;
	var phone = p1+' - '+p2+' - '+p3;
	var userEmail = '' + document.getElementById('visitormail').value;
	var eventName = document.getElementById('eventname').value;
	var eventDate = document.getElementById('eventdate').value;
	var message = document.getElementById('message').value;
	
	emailSubject = 'Events - '+ eventName + ' - ' +  eventDate;
	
	//Validation

	if (!document.getElementById('firstname').value) {alert ('Value required for first name'); return false;}
	if (!document.getElementById('lastname').value) {alert ('Value required for last name'); return false;}
	if (validateemail(userEmail) < 1) {alert ('Valid e-mail address required'); return false;}
	if (validatephone(p1, p2, p3) < 1) {alert ('Valid phone number is required'); return false;}
	if (!document.getElementById('message').value) {alert ('Please enter a message.'); return false;}

//	document.getElementById('contactform').action='http://www.mongotickets.com/sendeail.php';
//	document.getElementById('contactform').submit();
//	document.validateform_retval = 0;
return true;

}
</script>

<!-- DO NOT change ANY of the php sections -->
<?php
$ipi = getenv("REMOTE_ADDR");
$httprefi = getenv ("HTTP_REFERER");
$httpagenti = getenv ("HTTP_USER_AGENT");
?>

<p class="contact_form">Please fill out the form below and click submit so we can assist you.<br /> (* indicates field is required)</p>
<br />
<form action="sendeail.php" method="post" onsubmit="return validateform();" name="contactform">
	<input type="hidden" name="ip" value="<?php echo $ipi ?>" />
	<input type="hidden" name="httpref" value="<?php echo $httprefi ?>" />
	<input type="hidden" name="httpagent" value="<?php echo $httpagenti ?>" />


	<p class="contact_form">
	<div class="form_row">
		<span class="form_row_title">* First Name:</span>
		<div class="form_row_input"><input type="text" name="firstname" id="firstname" size="35" /></div>
		<div class="spacer"></div>
	</div>

	<div class="form_row">
		<span class="form_row_title">*Last Name:</span>
		<div class="form_row_input"><input type="text" name="lastname" id="lastname"  size="35" /></div>
		<div class="spacer"></div>
	</div>

	<div class="form_row">
		<span class="form_row_title">*Email:</span>
		<div class="form_row_input"> <input type="text" name="visitormail" id="visitormail" size="35" /></div>
		<div class="spacer"></div>
	</div>

	<div class="form_row">
		<span class="form_row_title">*Telephone:</span>
		<div class="form_row_input"> <input name="areacode" type="text" id="areacode" size="3" maxlength="3">-
        <input name="firstthreedigits" type="text" id="firstthreedigits" size="3" maxlength="3">
        -
        <input type="text" name="lastfourdigits" id="lastfourdigits" size="6"  maxlength="4"></div>
		<div class="spacer"></div>
	</div>


	<div class="form_row">
		<span class="form_row_title">Event Name:</span>
		<div class="form_row_input"> <input type="text" name="eventname" id="eventname" size="35" /></div>
		<div class="spacer"></div>
	</div>

	<div class="form_row">
		<span class="form_row_title">Event Date:</span>
		<div class="form_row_input"><input type="text" name="eventdate" id="eventdate" size="35" /></div>
		<div class="spacer"></div>
	</div>

	<div class="form_row">
		<span class="form_row_title">*Message:</span>
		<div class="form_row_input"><textarea name="message" id="message" rows="8" cols="45"></textarea></div>
		<div class="spacer"></div>
	</div>

	<div class="form_row">
		<div class="form_row_input"><input type="submit" value="Submit"/></div>
	</div>
	
</form>

</p>
</div>

<?php
$smarty->display('footer.tpl');




?>

