{if $NumRelatedCategories> 0}
<div id="related_events_table">
	{foreach from=$RelatedCategories key=category_name item=categoryArray}
                        <div class="related_events_column">
                                <div class="related_events_column_heading">Related {$category_name} Events</div>
                                <div class="related_events_column_list">
                                                <ul>
							{section name=index loop=$categoryArray}
								<li><a href="{$categoryArray[index].caturl}">{$categoryArray[index].catname}</a></li>
							{/section}
						</ul>
				</div>
			</div>
	{/foreach}
</div>
{/if}
