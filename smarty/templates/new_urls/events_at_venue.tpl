
<div id="content">
<div class="left_bar">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
<h1>{$title}</h1>
<br/>
<div style="margin: 0px 6px 4px 12px;">
<p>Address: {$Address}<br/>
City: {$City}<br/>
State: {$State}<br/>
Zip: {$ZipCode}
</p>
</div>
{if $NumEvents > 0}
<div style="float: left; clear: none; width: 550px; margin: 5px 0px 10px 20px;"><a href="{$SeatingChartUrl}" onclick="window.open('{$SeatingChartUrl}','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false" style="color: red"><img src="{$SeatingChartUrl}" alt="{$venueName} Seating Chart"/><br /></a>
{*
<div style="float: left; clear: none; width: 600px; margin: 5px 0px 10px 12px;"><a href="{$SeatingChartUrl}" onclick="window.open('{$SeatingChartUrl}','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false" style="color: red"><img height="479" width="479" src="{$SeatingChartUrl}" alt="{$SeatingChartUrl}"/><br /></a>
*}
</div>

<h2><strong>Events at {$venueName}</strong></h2>
<br/>
<div class="production_table">

        <table class="sortable" id="sortable_example" cellspacing="0" cellpadding="3" border="0" width="100%">


                <tr class="ticketHeading"><th>Event</th><th class="startsort">Date</th><th>Venue</th><th class="unsortable">&nbsp;</th></tr>

        {section name=event loop=$EventsArray}
                <tr><td ><a href="{$EventsArray[event].event_url}" style="color: blue;">{$EventsArray[event].name}</a></td><td>{$EventsArray[event].date}<br />{$EventsArray[event].time}</td><td><a href="{$EventsArray[event].venue_url}" style="color: blue;">{$venueName}</a></td><td><a href="{$EventsArray[event].prod_url}"><img src="{$RootUrl}/Images/tickets_vb.gif" alt="{$EventsArray[event].name} {$EventsArray[event].date} {$venueName} seating map" /></a></td></tr>
        {/section}
	</table>
</div>
<br/>
<br/>
<br/>
<br/>
{else}
<br/>
<h2>No tickets available at {$venueName}.</h2>
{/if}


</div> <!-- end left_bar -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}


