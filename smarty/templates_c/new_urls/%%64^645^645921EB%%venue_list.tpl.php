<?php /* Smarty version 2.6.18, created on 2012-06-20 00:09:07
         compiled from venue_list.tpl */ ?>

<div id="content">
<div class="left_bar">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>
	<h1><strong><?php echo $this->_tpl_vars['EventName']; ?>
</strong> Tickets</h1>
        <?php if ($this->_tpl_vars['EventImagePathname'] != ""): ?>
                <div class="event_image">
                <img src="<?php echo $this->_tpl_vars['RootUrl']; ?>
/<?php echo $this->_tpl_vars['EventImagePathname']; ?>
" alt="<?php echo $this->_tpl_vars['EventName']; ?>
" class="left"  width="150" height="100" />
                </div>
        <?php endif; ?>
	<?php if ($this->_tpl_vars['EventIntroText'] != ""): ?>
	        <div id="event_text">
                	<p><?php echo $this->_tpl_vars['EventIntroText']; ?>
</p>
        	</div>
	<?php endif; ?>


	<h2>Please <strong>SELECT A THEATER</strong> from the list below:</h2>
	<div id="venues">
	<table class="sortable" id="venue_list" cellspacing="0"  width="100%" cellpadding="3" border="0">
	        <tr><th>Venue</th><th>City</th><th>State</th></tr>

		<?php $_from = $this->_tpl_vars['Venues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['venue_name'] => $this->_tpl_vars['venue_info']):
?>
                        <tr><td><a href="<?php echo $this->_tpl_vars['venue_info']['url']; ?>
"><?php echo $this->_tpl_vars['venue_name']; ?>
</a></td><td><a href="<?php echo $this->_tpl_vars['venue_info']['url']; ?>
"><?php echo $this->_tpl_vars['venue_info']['city']; ?>
</a></td><td><a href="<?php echo $this->_tpl_vars['venue_info']['url']; ?>
"><?php echo $this->_tpl_vars['venue_info']['region_code']; ?>
</a></td></tr>
		<?php endforeach; endif; unset($_from); ?>
	</table>
	</div>
        <?php if ($this->_tpl_vars['EventText'] != ""): ?>
        <h2><?php echo $this->_tpl_vars['EventName']; ?>
 History</h2>
       <div id="event_history_text">
                <?php echo $this->_tpl_vars['EventText']; ?>

        </div>
        <?php endif; ?>

	<div id="howtobuy">
	<h2>How To Buy</h2>
		<ol>
			<li>Select the theater by clicking on the venue name, city or state.</li>
			<li>Choose a date and time, and click the View Tickets button.</li>
			<li>Choose the tickets you would like to buy and click the Buy Tickets button. You will be sent to the SECURE ordering page.</li>
			<li>Select the quantity of tickets you would like to buy and fill out payment information to complete your order.</li>
		</ol>
		<div id="event_text"><p>All tickets are 100% guaranteed. For more info about our guarantee please click <a href="/policy.html">here</a>. We hope you enjoy your tickets and that you come back and shop with MongoTickets.com in the future.</p><br/><p>If you are unable to find appropriate seats on MongoTickets.com, we recommend you check back often as our inventory changes regularly. You could also visit <a href="http://www.ticketmaster.com">Ticketmaster.com</a></p><br/>
	</div>
	</div>

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
