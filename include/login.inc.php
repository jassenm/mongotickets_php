<?php

function session_defaults() {
	$_SESSION['logged'] = false;
	$_SESSION['uid'] = 0;
	$_SESSION['username'] = '';
	$_SESSION['cookie'] = 0;
	$_SESSION['remember'] = false;
}

function _checkLogin($username, $password, $remember) {
	$username = mysql_escape_string($username);
	$password = mysql_escape_string(md5($password));
	$sql = "SELECT * FROM member WHERE " .
	"username = '$username' AND " .
	"password = '$password'";

        if($query_result = mysql_query($sql) ) {
		if(mysql_num_rows($query_result) > 0 ) {
			$values = mysql_fetch_array($query_result);
			$uid = $values['id'];
			$username = htmlspecialchars($values['username']);
			$cookie = $values['cookie'];
			_setSession($uid, $username, $cookie, $remember);
			return true;
		}
		else {
			return false;
		}
	} else {
		echo 'Login failed: unable to connect to db';
	#	$this->failed = true;
		#_logout();
		return false;
	}
} 


function _setSession($uid, $username, $cookie, $remember, $init = true) {
	$_SESSION['uid'] = $uid;
	$_SESSION['username'] = $username;
	$_SESSION['cookie'] = $cookie;
	$_SESSION['logged'] = true;
	if ($remember == 'yes') {
		updateCookie($cookie, true);
	}
	if ($init) {
		$session = mysql_escape_string(session_id());
		$ip = mysql_escape_string($_SERVER['REMOTE_ADDR']);

		$sql = "UPDATE member SET session = '$session', ip = '$ip' WHERE " .
		"id = '$uid'";
        	if($query_result = mysql_query($sql)) {
		}
		else {
			echo 'Db error: ' . mysql_error();
                	mysql_close($dbh);
			exit();
		}
	}
} 

function updateCookie($cookie, $save) {
	$_SESSION['cookie'] = $cookie;
	if ($save) {
		$cookie = serialize(array($_SESSION['username'], $cookie) );
		setcookie('mtwebLogin', $cookie, time() + 31104000, '/directory/');
	}
}

function _checkRemembered($cookie) {
	list($username, $cookie) = @unserialize($cookie);
	if (!$username or !$cookie) return;
	$username = mysql_escape_string($username);
	$cookie = mysql_escape_string($cookie);
	$sql = "SELECT * FROM member WHERE " .
	"(username = '$username') AND (cookie = '$cookie')";
        if($query_result = mysql_query($sql) ) {
		if(mysql_num_rows($query_result) > 0 ) {
			$values = mysql_fetch_array($query_result);
			$uid = $values['id'];
			$username = htmlspecialchars($values['username']);
			$cookie = $values['cookie'];
			_setSession($uid, $username, $cookie, true);
			session_write_close();
			#header(Location: category_ranking_editor.php);
    echo '<br/><br/><a href="http://www.mongotickets.com/event_ranking_editor.ph
p?start=1&categoryID=0">Go to Event Editor</a>';
                        echo '<br/><br/><a href="http://www.mongotickets.com/category_ranking_editor
.php">Go to Category Editor</a>';

		}
		else {
			mysql_close($dbh);
			echo 'Denied';
			exit();
		}
	}
	else
	{
#		header(Location: login.html);
                mysql_close($dbh);
		return false;
	}	
	return true;
} 

function _checkSession() {
	$username = mysql_escape_string($_SESSION['username']);
	$cookie = mysql_escape_string($_SESSION['cookie']);
	$session = mysql_escape_string(session_id());
	$ip = mysql_escape_string($_SERVER['REMOTE_ADDR']);
	$sql = "SELECT * FROM member WHERE " .
	"(username = '$username') AND (cookie = '$cookie') AND " .
	"(session = '$session') AND (ip = '$ip')";
        if($query_result = mysql_query($sql) ) {
		if(mysql_num_rows($query_result) > 0 ) {
			$values = mysql_fetch_array($query_result);
			$uid = $values['id'];
			$username = htmlspecialchars($values['username']);
			$cookie = $values['cookie'];
			_setSession($uid, $username, $cookie, $remember);
			session_write_close();
			#header(Location: category_ranking_editor.php);
		}
		else {
			mysql_close($dbh);
			echo 'Denied';
			echo '<br/><a href="log_me_in.php">Try Login again</a>';
			echo '<br/><a href="log_me_out.php">Login out</a>';
			exit();
		}
	} 
	else {
		# $this->_logout();
                mysql_close($dbh);
		session_write_close();
#		header(Location: login.html);
		exit();
	}
} 


function _logout() {
	session_defaults();
	session_write_close();

}
?>
