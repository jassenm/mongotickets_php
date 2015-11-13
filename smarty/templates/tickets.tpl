<div id="content">
   <div id="breadcrumb_trail">
   {$Breadcrumbs}
   </div>

	<div class="eventVenueInfo"><h1>{$Heading1}</h1><div class="eventInfo">{$SubHeading}<br/>{$ShortDate}<br />at {$VenueName} in {$City}, {$RegionCode}</div>
	{if $NumTickets < 1}
		<div id="no_tickets"><p><strong>Sorry, but {$EventName} tickets are currently not available for this date and time.</strong></p></div>
		</div>
	{/if}

