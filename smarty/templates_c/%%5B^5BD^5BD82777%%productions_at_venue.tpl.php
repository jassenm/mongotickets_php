<?php /* Smarty version 2.6.18, created on 2008-01-24 22:53:05
         compiled from productions_at_venue.tpl */ ?>

<div id="content">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>

<h1><strong><?php echo $this->_tpl_vars['EventName']; ?>
 Tickets <?php echo $this->_tpl_vars['City']; ?>
</strong></h1>
	<?php if ($this->_tpl_vars['EventImagePathname'] != ""): ?>
		<div class="event_image">
		<img src="/<?php echo $this->_tpl_vars['EventImagePathname']; ?>
" alt="<?php echo $this->_tpl_vars['EventName']; ?>
" class="left"  width="150" height="100" />
		</div>
	<?php endif; ?>
	<div id="event_text">
		<p><?php echo $this->_tpl_vars['EventIntroText']; ?>
</p>
	</div>

<?php if ($this->_tpl_vars['NumProductions'] > 0): ?>

<div class="production_table">
	<?php if ($this->_tpl_vars['DisplayHomeAwayOption'] > 0): ?>
		<form name="input" action="<?php echo $this->_tpl_vars['ScriptName']; ?>
" method="get">
			<?php if ($this->_tpl_vars['HomeOnlyFlag'] == 1): ?>
			<?php elseif ($this->_tpl_vars['HomeOnlyFlag'] == 0): ?>
			<?php elseif ($this->_tpl_vars['HomeOnlyFlag'] == 2): ?>
			<?php endif; ?>
		</form>
	<?php endif; ?>

	<table class="sortable" id="sortable_example" cellspacing="0" cellpadding="3" border="0" width="100%">

		<tr class="ticketHeading"><th class="startsort">Date</th><th>Venue</th><th>Event</th><th class="unsortable">&nbsp;</th></tr>

       	 	<?php unset($this->_sections['production']);
$this->_sections['production']['name'] = 'production';
$this->_sections['production']['loop'] = is_array($_loop=$this->_tpl_vars['Productions']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['production']['show'] = true;
$this->_sections['production']['max'] = $this->_sections['production']['loop'];
$this->_sections['production']['step'] = 1;
$this->_sections['production']['start'] = $this->_sections['production']['step'] > 0 ? 0 : $this->_sections['production']['loop']-1;
if ($this->_sections['production']['show']) {
    $this->_sections['production']['total'] = $this->_sections['production']['loop'];
    if ($this->_sections['production']['total'] == 0)
        $this->_sections['production']['show'] = false;
} else
    $this->_sections['production']['total'] = 0;
if ($this->_sections['production']['show']):

            for ($this->_sections['production']['index'] = $this->_sections['production']['start'], $this->_sections['production']['iteration'] = 1;
                 $this->_sections['production']['iteration'] <= $this->_sections['production']['total'];
                 $this->_sections['production']['index'] += $this->_sections['production']['step'], $this->_sections['production']['iteration']++):
$this->_sections['production']['rownum'] = $this->_sections['production']['iteration'];
$this->_sections['production']['index_prev'] = $this->_sections['production']['index'] - $this->_sections['production']['step'];
$this->_sections['production']['index_next'] = $this->_sections['production']['index'] + $this->_sections['production']['step'];
$this->_sections['production']['first']      = ($this->_sections['production']['iteration'] == 1);
$this->_sections['production']['last']       = ($this->_sections['production']['iteration'] == $this->_sections['production']['total']);
?>
        	        <tr><td><?php echo $this->_tpl_vars['Productions'][$this->_sections['production']['index']]['date']; ?>
</td><td><?php echo $this->_tpl_vars['Productions'][$this->_sections['production']['index']]['venuename']; ?>
</td><td><?php echo $this->_tpl_vars['Productions'][$this->_sections['production']['index']]['eventDescr']; ?>
</td><td><b><a href="<?php echo $this->_tpl_vars['Productions'][$this->_sections['production']['index']]['url']; ?>
"><img src="<?php echo $this->_tpl_vars['RootUrl']; ?>
/Images/ViewTicketsButton.gif" /></a></b></td></tr>
       	 	<?php endfor; endif; ?>

</table>
<?php if ($this->_tpl_vars['HomeOnlyFlag'] > 0): ?>
	<?php if ($this->_tpl_vars['NumProductions'] > 10): ?>
	<a href="<?php echo $this->_tpl_vars['ScriptName']; ?>
?home_only=0" style="color:blue; text-decoration:underline; font-size: 12px;" >View all available <?php echo $this->_tpl_vars['EventName']; ?>
 Tickets</a>
	<?php endif; ?>
<?php endif; ?>
</div>
<?php else: ?>
<div id="no_tickets">
<p>There are currently no tickets available for the <?php echo $this->_tpl_vars['EventName']; ?>
.</p>
</div>


<?php endif; ?>
	<?php if ($this->_tpl_vars['EventText'] != ""): ?>
	<h2><?php echo $this->_tpl_vars['EventName']; ?>
 History</h2>
       <div id="event_history_text">
                <p><?php echo $this->_tpl_vars['EventText']; ?>
</p>
        </div>
	<?php endif; ?>
	<h2>How To Buy <?php echo $this->_tpl_vars['EventName']; ?>
 Tickets</h2>
	<ol>
		<li>Choose a date and time for which you would like buy <?php echo $this->_tpl_vars['EventName']; ?>
 tickets and click the View Tickets button.</li>
		<li>Choose the tickets you would like to buy and click the Buy Now button. You will be sent to the SECURE ticket ordering page.</li>
		<li>Select the quantity of tickets you would like to buy and fill out payment information to complete the order.</li>
	</ol>
	<div style="margin: 0px 6px 4px 12px;"><p>All <?php echo $this->_tpl_vars['EventName']; ?>
 tickets are 100% guaranteed. For more information about our <?php echo $this->_tpl_vars['EventName']; ?>
 ticket guarantee please click <a href="/policy.html"> here</a>. We hope you enjoy your tickets to <?php echo $this->_tpl_vars['EventName']; ?>
 and that you come back and shop with MongoTickets.com in the future.</p><br/><p>If you are unable to find appropriate seats for <?php echo $this->_tpl_vars['EventName']; ?>
 on MongoTickets.com, we recommend you check back often as our inventory for <?php echo $this->_tpl_vars['EventName']; ?>
 changes regularly.</p></div>



