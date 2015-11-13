<?php /* Smarty version 2.6.18, created on 2008-01-16 21:45:49
         compiled from tickets.tpl */ ?>
<div id="content">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>

	<div class="eventVenueInfo"><h1><?php echo $this->_tpl_vars['Heading1']; ?>
</h1><div class="eventInfo"><?php echo $this->_tpl_vars['SubHeading']; ?>
<br/><?php echo $this->_tpl_vars['ShortDate']; ?>
<br />at <?php echo $this->_tpl_vars['VenueName']; ?>
 in <?php echo $this->_tpl_vars['City']; ?>
, <?php echo $this->_tpl_vars['RegionCode']; ?>
</div>
	<?php if ($this->_tpl_vars['NumTickets'] < 1): ?>
		<div id="no_tickets"><p><strong>Sorry, but <?php echo $this->_tpl_vars['EventName']; ?>
 tickets are currently not available for this date and time.</strong></p></div>
		</div>
	<?php endif; ?>
