
<div id="content">
<div class="left_bar">
   <div id="breadcrumb_trail">
   {$Breadcrumbs}
   </div>
	<h1><strong>{$EventName}</strong> Tickets</h1>
        {if $EventImagePathname neq ""}
                <div class="event_image">
                <img src="{$RootUrl}/{$EventImagePathname}" alt="{$EventName}" class="left"  width="150" height="100" />
                </div>
        {/if}
	{if $EventIntroText neq ""}
	        <div id="event_text">
                	<p>{$EventIntroText}</p>
        	</div>
	{/if}


	<h2>Please <strong>SELECT A THEATER</strong> from the list below:</h2>
	<div id="venues">
	<table class="sortable" id="venue_list" cellspacing="0"  width="100%" cellpadding="3" border="0">
	        <tr><th>Venue</th><th>City</th><th>State</th></tr>

		{foreach from=$Venues key=venue_name item=venue_info}
                        <tr><td><a href="{$venue_info.url}">{$venue_name}</a></td><td><a href="{$venue_info.url}">{$venue_info.city}</a></td><td><a href="{$venue_info.url}">{$venue_info.region_code}</a></td></tr>
		{/foreach}
	</table>
	</div>
        {if $EventText neq ""}
        <h2>{$EventName} History</h2>
       <div id="event_history_text">
                {$EventText}
        </div>
        {/if}

	<div id="howtobuy">
	<h2>How To Buy</h2>
		<ol>
			<li>Select the theater by clicking on the venue name, city or state.</li>
			<li>Choose a date and time, and click the View Tickets button.</li>
			<li>Choose the tickets you would like to buy and click the Buy Tickets button. You will be sent to the SECURE ordering page.</li>
			<li>Select the quantity of tickets you would like to buy and fill out payment information to complete your order.</li>
		</ol>
		<div id="event_text"><p>All tickets are 100% guaranteed. For more info about our guarantee please click <a href="/policy.html">here</a>. We hope you enjoy your tickets and that you come back and shop with MongoTickets.com in the future.</p><br/><p>If you are unable to find appropriate seats on MongoTickets.com, we recommend you check back often as our inventory changes regularly. You could also visit <a href="http://www.ticketmaster.com">Ticketmaster.com</a></p><br/>
	</div>
	</div>

</div> <!-- end left_bar -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}

