
<div id="content">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
<h1><strong>{$categoryName} Tickets</strong></h1>
{if $NumEvents > 0}
<table class="category_event_list">
        {section name=event loop=$EventsArray}
                <tr><td><a href="{$EventsArray[event].url}">{$EventsArray[event].name}</a></td></tr>
        {/section}
</table>
{/if}


{if $NumSubCategories > 0}
<div class="category_subcategory_list">
	<ul>
         {section name=subcategory loop=$SubCategories}
                        <li><a href="{$SubCategories[subcategory].url}">{$SubCategories[subcategory].name}</a> &nbsp;&nbsp;&nbsp;</li>
         {/section}
	</ul>
</div>
{/if}




