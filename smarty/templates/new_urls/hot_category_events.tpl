<div id="content">
<div class="left_bar">
<div id="breadcrumb_trail">
{$Breadcrumbs}
</div>
{if $CategoryName != ""}
	<h1><strong>{$CategoryName} Tickets</strong></h1>
{/if}
<div id="intro_text">
<p>{$TextContent}</p>
</div>
<div id="hot_category_table">
         {section name=subcategory loop=$SubCategories}
                        <div class="hot_category_table_column">
                                <div class="hot_category_events">
					{if $SubCategories[subcategory].catimage != ""}
                                		<a href="{$SubCategories[subcategory].caturl}">
						<img src="{$RootUrl}/{$SubCategories[subcategory].catimage}" alt="{$SubCategories[subcategory].catname}" width="158" height="117"/>
						</a>
					{/if}
                                <h1><strong><a href="{$SubCategories[subcategory].caturl}">{$SubCategories[subcategory].catname}</a></strong></h1>
					<ul>
                                                {section name=key loop=$SubCategories[subcategory].top_events}
                                                        <li><a href="{$SubCategories[subcategory].top_events[key].url}">{$SubCategories[subcategory].top_events[key].name}</a>
                                                        </li>
                                                {/section}
					</ul>
                                </div> <!-- end hot_category_events -->
                        </div> <!-- end hot_category_table_column -->
         {/section}
</div> <!-- end hot_category_table -->
