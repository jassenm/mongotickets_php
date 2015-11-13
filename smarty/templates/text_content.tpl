<div id="content">
<div>
<table style="margin:0; padding:0; border:solid 1px #e2d1a4; border-collapse:collapse; ">
        <tr>
         {section name=subcategory loop=$SubCategories}
                <td valign="top" style="border-left: solid 1px #e2d1a4;">
                        <div style="margin:0; padding:0; border: 0px;">  {* #e2d1a4  *}
                                <div style="text-align: left; border: 0px;">
                                        <h1 style="text-align: left; padding: 4px;"><a style="font-size: 18px;" href="{$SubCategories[subcategory].caturl}">{$SubCategories[subcategory].catname}</a></h1>
                                </div>
                                <div style="margin:0;">
                                                <ul style="list-style-type:none;margin:0;padding-left: 0px;">
                                                {section name=key loop=$SubCategories[subcategory].top_events}
                                                        <li style="padding: 4px;margin:0; border-bottom: 0px;"><a href="{$SubCategories[subcategory].top_events[key].url}">{$SubCategories[subcategory].top_events[key].name}</a>
                                                        </li>
                                                {/section}
                                                </ul>
                                </div>
                        </div>
                </td>
         {/section}
        </tr>
</table>
</div>
