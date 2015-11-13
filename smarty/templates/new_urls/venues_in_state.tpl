
<div id="content">
<div class="left_bar">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
{if $NumEvents > 0}
<h1>{$venueName}</h1>
<br/>
<table class="category_event_list">
        {section name=event loop=$EventsArray}
                <tr><td><a href="{$EventsArray[event].url}">{$EventsArray[event].name}</a></td></tr>
        {/section}
</table>
{/if}


</div> <!-- end left_bar -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}

