<?php /* Smarty version 2.6.18, created on 2012-06-18 16:33:37
         compiled from productions.tpl */ ?>

<div id="content">
<div class="left_bar">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>

<h1><strong><?php echo $this->_tpl_vars['EventName']; ?>
 Tickets</strong></h1>
	<?php if ($this->_tpl_vars['EventImagePathname'] != ""): ?>
		<div class="event_image">
		<img src="<?php echo $this->_tpl_vars['RootUrl']; ?>
/<?php echo $this->_tpl_vars['EventImagePathname']; ?>
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
/Images/tickets_vb.gif"  alt="<?php echo $this->_tpl_vars['Productions'][$this->_sections['production']['index']]['ticket_page_title']; ?>
"/></a></b></td></tr>
       	 	<?php endfor; endif; ?>

</table>
<?php if ($this->_tpl_vars['HomeOnlyFlag'] > 1): ?>
	<?php if ($this->_tpl_vars['NumProductions'] > 10): ?>
<div style="text-align: center; padding: 5px 0px 5px 0px;">
	<a href="<?php echo $this->_tpl_vars['ScriptName']; ?>
?home_only=0" style="color:blue; text-decoration:underline; font-size: 12px;" >View all available <?php echo $this->_tpl_vars['EventName']; ?>
 Tickets</a>
</div>
	<?php endif; ?>
<?php endif; ?>
</div> <!-- end production_table -->
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
                <?php echo $this->_tpl_vars['EventText']; ?>

        </div>
	<?php endif; ?>
	<h2>How To Buy</h2>
	<ol>
		<li>Choose a date and time, and click the View Tickets button.</li>
		<li>Choose the tickets you would like to buy and click the Buy Tickets button. You will be sent to the SECURE ticket ordering page.</li>
		<li>Select the quantity of tickets you would like to buy and fill out payment information to complete the order.</li>
	</ol>
	<div style="margin: 0px 6px 4px 12px;"><p>All tickets are 100% guaranteed. For more information about our guarantee please click <a href="/policy.html"> here</a>. We hope you enjoy yourself and that you come back and shop with MongoTickets.com in the future.</p><br/><p>If you are unable to find appropriate seats on MongoTickets.com, we recommend you check back often as our inventory for <?php echo $this->_tpl_vars['EventName']; ?>
 changes regularly. You could also visit <a href="http://www.ticketmaster.com">Ticketmaster.com</a></p><br/></div>



</div> <!-- end left_bar -->

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "right_bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left_column.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
