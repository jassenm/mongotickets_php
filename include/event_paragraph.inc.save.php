<?php

global $sportcodeToEventParagraphTextTemplateTable;
$sportcodeToEventParagraphTextTemplateTable = array(
	"NBA" => "Find your tickets today as the start of the NBA season is coming quickly and with recent trades and acquisitions you know this year is going to be the year for %%EVENT_NAME%%.  The NBA's brightest stars are shining harder and harder with players such as Lebron James and Dwane Wade, so don't miss  your chance to see them live.  The San Antonio Spurs are living their respected dynasty, but this may be the year that %%EVENT_NAME%% take the coveted NBA Championship home.  Get your tickets today.",
	"NFL" => "Find your tickets today as the start of the start of the NFL season is coming quickly and with recent trades and acquisitions you know this year is going to be the year for %%EVENT_NAME%%.  The NFL's brightest stars are shining harder and harder with players such as Reggie Bush and Peyton Manning, so don't miss  your chance to see them live.  The Indianapolis Colts are living their respected dream, but this may be the year that %%EVENT_NAME%% takes the coveted NFL Championship home.  Get your tickets today.",
	"NHL" => "Find your tickets today as the start of the start of the NHL season is coming quickly and with recent trades and acquisitions you know this year is going to be the year for %%EVENT_NAME%%.  The NHL's brightest stars are shining harder and harder with players such as Syndey Crosby and Eric Lindros, so don't miss  your chance to see them live.  The Anaheim Ducks are living their respected dream, but this may be the year that %%EVENT_NAME%% takes the coveted NHL Championship home.  Get your tickets today.",
	"MLB" => "Find your tickets today as the start of the MLB season is moving quickly and with recent trades and acquisitions you know this year is going to be the year for %%EVENT_NAME%%.  The MLB's brightest stars are shining harder and harder with players such as Albert Pujols and Alex Rodriguez, so don't miss  your chance to see them live.  The St. Louis Cardinals are living their respected dream, but this may be the year that %%EVENT_NAME%% takes the coveted MLB World Series Championship home.  Get your tickets today.",
	"Nascar" => "Nascar has gotten off to a great start with some great finishes at Daytona and Las Vegas, but the Nextel Cup is still up for grabs with plenty of Nascar tickets available.  Find your tickets to come out and watch Dale Earnhardt Jr. or Jeff Gordon as they chase the Cup.  Find your tickets today for the Nascar race near you.  Nascar tickets are going fast, so get your tickets today.",
	"NCAAF" => "Find your tickets today as the start of the College Football season is about to begin and with some new recruits you know this year is going to be the year for %%EVENT_NAME%% football.  College football's brightest stars not only playing for the coveted NCAA Football National Championship, but also for the Heisman Trophy.  The Florida Gators are living their respected dream, but this may be the year that %%EVENT_NAME%% takes the coveted National Championship home.  Get your tickets today.",
	"NCAAB" => "Find your tickets today as the start of the College Basketball season is about to begin and with some new recruits you know this year is going to be the year for %%EVENT_NAME%% Basketball.  College Basketball's brightest stars not only playing for the coveted NCAA Basketball National Championship, but they're also playing for the right to earn ungodly amounts of money in the pros.  Which only means for some great play.  The Florida Gators are living their respected dream, but this may be the year that %%EVENT_NAME%% takes the coveted National Championship home.   Get your tickets today.");


global $categoryidToSportCodeTable;
$categoryidToSportCodeTable = array(
	"10" => "Nascar",
	"64" => "Nascar",
	"26" => "NFL",
	"27" => "NFL",
	"28" => "NFL",
	"29" => "NFL",
	"31" => "NFL",
	"32" => "NFL",
	"33" => "NFL",
	"34" => "NFL",
	"84" => "NBA",
	"85" => "NBA",
	"86" => "NBA",
	"88" => "NBA",
	"89" => "NBA",
	"90" => "NBA",
	"94" => "NHL",
	"95" => "NHL",
	"96" => "NHL",
	"98" => "NHL",
	"99" => "NHL",
	"100" => "NHL",
	"16" => "MLB",
	"17" => "MLB",
	"18" => "MLB",
	"20" => "MLB",
	"21" => "MLB",
	"22" => "MLB",
	"49" => "NCAAF",
	"50" => "NCAAF",
	"51" => "NCAAF",
	"52" => "NCAAF",
	"53" => "NCAAF",
	"54" => "NCAAF",
	"55" => "NCAAF",
	"56" => "NCAAF",
	"57" => "NCAAF",
	"58" => "NCAAF",
	"59" => "NCAAF",
	"60" => "NCAAF",
	"61" => "NCAAF",
	"62" => "NCAAF",
	"63" => "NCAAF"
);

function GetEventText($categoryID, $eventTypeID, $eventName, $eventID) {

	global $categoryidToSportCodeTable;
	global $sportcodeToEventParagraphTextTemplateTable;
        $text = "";
        $eventName = htmlspecialchars($eventName);
        if ($eventTypeID == 3) {



		$Bsql = "SELECT EventIntroText FROM EventText WHERE EventId=$eventID";
		if($result = mysql_query($Bsql)) {
			while ($row = mysql_fetch_array($result)) {
				$text = $row['EventIntroText'];
				return $text;
			}
		}



		if(array_key_exists($categoryID,$categoryidToSportCodeTable)) {
			$code = $categoryidToSportCodeTable[$categoryID];
			$textTemplate = $sportcodeToEventParagraphTextTemplateTable[$code];
			$text = $textTemplate;
			#mixed str_replace ( mixed $search, mixed $replace, mixed $subject [, int &$count] )
			$text = str_replace ( "%%EVENT_NAME%%", "$eventName", $text );
		}
		else {
	#		print "Error";
		}
	}
	else if ($eventTypeID == 2) {
		$categoryName = GetCategoryName($categoryID);
		$text = "It's not too late to get your tickets for the next $eventName concert.  Ticket availability is constantly changing so don't hesitate to buy your tickets today.  Find the best seats in the house or the love the view from the rafters, either way you can't miss out on your chance to see $eventName live.  $categoryName concert tickets have been some of the hottest tickets in town no matter who's playing.  So go ahead and click on your local venue and find the best seats available to see $eventName now.";
	}
	else if ($eventTypeID == 4) {
		$text = "Theater tickets have always been in high demand, not to mention the happening show of $eventName which is selling like hot cakes.  Tickets are available, but prices and availability are changing rapidly.  Find your front row tickets for $eventName and buy them today before they go.  We'll see you there. ";
        }
	else {
	#	print "Error";
	}
        return $text;
}

?>
