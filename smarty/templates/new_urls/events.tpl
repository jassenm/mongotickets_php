
<div id="content">
<div class="left_bar">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
<h1><strong>{$categoryName}</strong></h1>
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
</div> <!-- end category_subcategory_list -->
{/if}

</div> <!-- end left -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}


