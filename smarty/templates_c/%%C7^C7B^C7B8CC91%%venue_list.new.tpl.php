<?php /* Smarty version 2.6.18, created on 2008-01-22 22:58:17
         compiled from venue_list.new.tpl */ ?>

<div id="content">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>
	<h1><strong><?php echo $this->_tpl_vars['EventName']; ?>
</strong> Tickets</h1>
        <?php if ($this->_tpl_vars['EventImagePathname'] != ""): ?>
                <div class="event_image">
                <img src="/<?php echo $this->_tpl_vars['EventImagePathname']; ?>
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
                <p><?php echo $this->_tpl_vars['EventText']; ?>
</p>
        </div>
        <?php endif; ?>

	<div id="howtobuy">
	<h2>How To Buy <?php echo $this->_tpl_vars['EventName']; ?>
 Tickets</h2>
		<ol>
			<li>Select the theater where you would like to see <?php echo $this->_tpl_vars['EventName']; ?>
 by clicking on the venue name, city or state.</li>
			<li>Choose a date and time of the show you would like to see and click the View Tickets button.</li>
			<li>Choose the tickets you would like to buy and click the Buy Now button. You will be sent to the SECURE ordering page.</li>
			<li>Select the quantity of tickets you would like to buy and fill out payment information to complete the order.</li>
		</ol>
		<div id="event_text"><p>All <?php echo $this->_tpl_vars['EventName']; ?>
 tickets are 100% guaranteed. For more info about our <?php echo $this->_tpl_vars['EventName']; ?>
 ticket guarantee please click <a href="/policy.html">here</a>. We hope you enjoy your tickets to <?php echo $this->_tpl_vars['EventName']; ?>
 and that you come back and shop with MongoTickets.com in the future.</p><br/><p>If you are unable to find appropriate seats for <?php echo $this->_tpl_vars['EventName']; ?>
 on MongoTickets.com, we recommend you check back often as our inventory for <?php echo $this->_tpl_vars['EventName']; ?>
 changes regularly.</p>
	</div>
	</div>
