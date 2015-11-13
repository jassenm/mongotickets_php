
<div id="content">
<div class="left_bar">

   <div id="breadcrumb_trail">
   {$Breadcrumbs}
   </div>

<h1><strong>{$h1}</strong></h1>


{if $NumEvents > 0}
	<h2>Events</h2>
	<table class="search_results">
       		{section name=event loop=$Events}
               		<tr><td align="left" valign="top"><a href="{$Events[event].url}">{$Events[event].name}</a></td></tr>
       		{/section}
	</table>
{/if}

{if $NumVenues > 0}
	<h2>Venues</h2>
	<table class="search_results">
       		{section name=venue loop=$Venues}
               		<tr><td align="left" valign="top"><a href="{$Venues[venue].url}">{$Venues[venue].name}</a></td></tr>
       		{/section}
	</table>
{/if}
{if ($NumEvents == 0) and ($NumVenues == 0)}
	<p style="margin: 0px 0px 0px 12px;">Your search returned <strong>0</strong> results</p>

{/if}

</div> <!-- end left_bar -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}

