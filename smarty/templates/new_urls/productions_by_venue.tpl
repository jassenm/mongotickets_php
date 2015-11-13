
<div id="content">
<h1>{$EventName}</h1>
       	 {section name=production loop=$Productions}
        	        <b><a href="{$Productions[production].url}">{$Productions[production].date}&nbsp;{$Productions[production].venuename}&nbsp;&nbsp;{$Productions[production].eventDescr}</a> &nbsp;&nbsp;&nbsp;</b><br>
       	 {/section}
	<br>

</div>
