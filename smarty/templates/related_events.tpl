{if $NumRelatedCategories> 0}
	<div class="related_events_column">
		<div class="related_events_column_heading">Related Events</div>
		<div class="related_events_column_list">
			<ul>
				{section name=category loop=$RelatedCategories}
					<li><a href="{$RelatedCategories[category].caturl}">{$RelatedCategories[category].catname}</a></li>
				{/section}
			</ul>
		</div>
	</div>
{/if}
