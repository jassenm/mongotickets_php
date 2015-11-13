
<div id="content">
<table style="margin:0; padding:0; border:solid 0px black; border-collapse:collapse;">

	<tr>
       	 {section name=category loop=$Categories}
                <td valign="top">
			<div>
				<div>
					<h1><a style="font-size: 18px;text-align: left; valign: bottom;" href="{$Categories[category].url}">{$Categories[category].name} Tickets</a></h1>
				</div>
				<div style="border:solid 1px #e2d1a4">
						<ul style="list-style-type:none; padding-left: 0px;">
						{section name=key loop=$Categories[category].top_events}
							<li><a href="{$Categories[category].top_events[key].url}">{$Categories[category].top_events[key].name}</a>
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

