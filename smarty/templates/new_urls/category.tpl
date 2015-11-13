<div id="content">
<table style="margin:0; padding:0; border:solid 0px black; border-collapse:collapse;">
         {section name=subcategory loop=$SubCategories}
        <tr>
                <td valign="top"><a style="font-size: 18px;text-align: left; valign: bottom;" href="{$SubCategories[subcategory].url}">{$SubCategories[subcategory].name}</a>
                </td>
        </tr>
         {/section}
</table>
</div>

