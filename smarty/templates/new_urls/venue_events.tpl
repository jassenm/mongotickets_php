
<div id="content">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
{if $NumEvents > 0}
<h1>{$venueName} Tickets</h1>
<table class="category_event_list">
        {section name=event loop=$EventsArray}
                <tr><td><a href="{$EventsArray[event].url}">{$EventsArray[event].name}</a></td></tr>
        {/section}
</table>
{/if}

