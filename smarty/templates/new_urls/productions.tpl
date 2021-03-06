
<div id="content">
<div class="left_bar">
   <div id="breadcrumb_trail">
   {$Breadcrumbs}
   </div>

<h1><strong>{$EventName} Tickets</strong></h1>
	{if $EventImagePathname neq ""}
		<div class="event_image">
		<img src="{$RootUrl}/{$EventImagePathname}" alt="{$EventName}" class="left"  width="150" height="100" />
		</div>
	{/if}
	<div id="event_text">
		<p>{$EventIntroText}</p>
	</div>

{if $NumProductions > 0}

<div class="production_table">
	{if $DisplayHomeAwayOption > 0}
		<form name="input" action="{$ScriptName}" method="get">
			{if $HomeOnlyFlag == 1}
{*
			<input id="Home" type="radio" name="home_only" value="1" checked onclick="this.form.submit()"/>Home Games Only
			<input id="Away" type="radio" name="home_only" value="0" onclick="this.form.submit()"/>Home and Away Games
*}
			{elseif $HomeOnlyFlag == 0}
{*
			<input id="Home" type="radio" name="home_only" value="1" onclick="this.form.submit()"/>Home Games Only
			<input id="Away" type="radio" name="home_only" value="0" checked onclick="this.form.submit()"/>Home and Away Games
*}
			{elseif $HomeOnlyFlag == 2}
{*
			<input id="Home" type="radio" name="home_only" value="1" onclick="this.form.submit()"/>All Home Games
			<input id="Away" type="radio" name="home_only" value="0" onclick="this.form.submit()"/>All Home and Away Games
*}
			{/if}
		</form>
	{/if}

	<table class="sortable" id="sortable_example" cellspacing="0" cellpadding="3" border="0" width="100%">

		<tr class="ticketHeading"><th class="startsort">Date</th><th>Venue</th><th>Event</th><th class="unsortable">&nbsp;</th></tr>

       	 	{section name=production loop=$Productions}
        	        <tr><td>{$Productions[production].date}</td><td>{$Productions[production].venuename}</td><td>{$Productions[production].eventDescr}</td><td><b><a href="{$Productions[production].url}"><img src="{$RootUrl}/Images/tickets_vb.gif"  alt="{$Productions[production].ticket_page_title}"/></a></b></td></tr>
       	 	{/section}

</table>
{if $HomeOnlyFlag > 1}
	{if $NumProductions > 10}
<div style="text-align: center; padding: 5px 0px 5px 0px;">
	<a href="{$ScriptName}?home_only=0" style="color:blue; text-decoration:underline; font-size: 12px;" >View all available {$EventName} Tickets</a>
</div>
	{/if}
{/if}
</div> <!-- end production_table -->
{else}
<div id="no_tickets">
<p>There are currently no tickets available for the {$EventName}.</p>
</div>


{/if}
	{if $EventText neq ""}
	<h2>{$EventName} History</h2>
       <div id="event_history_text">
                {$EventText}
        </div>
	{/if}
	<h2>How To Buy</h2>
	<ol>
		<li>Choose a date and time, and click the View Tickets button.</li>
		<li>Choose the tickets you would like to buy and click the Buy Tickets button. You will be sent to the SECURE ticket ordering page.</li>
		<li>Select the quantity of tickets you would like to buy and fill out payment information to complete the order.</li>
	</ol>
	<div style="margin: 0px 6px 4px 12px;"><p>All tickets are 100% guaranteed. For more information about our guarantee please click <a href="/policy.html"> here</a>. We hope you enjoy yourself and that you come back and shop with MongoTickets.com in the future.</p><br/><p>If you are unable to find appropriate seats on MongoTickets.com, we recommend you check back often as our inventory for {$EventName} changes regularly. You could also visit <a href="http://www.ticketmaster.com">Ticketmaster.com</a></p><br/></div>



</div> <!-- end left_bar -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}

