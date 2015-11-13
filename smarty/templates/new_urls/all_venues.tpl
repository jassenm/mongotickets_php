
<div id="content">
<div id="left_bar">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
{if $NumStates > 0}
<h1>{$h1}</h1>
<table class="category_event_list">
        {section name=state loop=$States}
                <tr><td><a href="{$States[state].url}">{$States[state].name}</a></td></tr>
        {/section}
</table>
{/if}


</div> <!-- end left_bar -->

{include file="right_bar.tpl"}
{include file="left_column.tpl"}

