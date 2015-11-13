<?php

	include('../include/new_urls/ticket_db.php');
	include('../include/login.inc.php');

	session_start();

	if (!isset($_SESSION['uid']) ) {
		session_defaults();
	}

	if( $dbh=mysql_connect ($host_name, $db_username, $db_password)) {
       		mysql_select_db ($db_name);

		# $user = new User($db);
		if(_checkLogin($_POST['login'], $_POST['passwd'], $_POST['PersistentCookie'])) {
			echo '<br/><br/><a href="http://www.mongotickets.com/campaign_manager.php">Campaign Manager</a>';
			echo '<br/><br/><a href="http://www.mongotickets.com/event_ranking_editor.php?start=1&categoryID=0">Event Rank Editor</a>';
			echo '<br/><br/><a href="http://www.mongotickets.com/event_text_editor.php?start=1&categoryID=0">Event Text Editor</a>';
			echo '<br/><br/><a href="http://www.mongotickets.com/category_ranking_editor.php">Category Editor</a>';
		}
		else
		{
			echo '<h1>Access Denied!</h1>';
			echo '<a href="/log_me_in.php">Login</a>';
		}
		mysql_close();
	}

?>
