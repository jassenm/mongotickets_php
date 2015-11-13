<?php /* Smarty version 2.6.18, created on 2012-06-20 11:02:18
         compiled from events_at_venue.tpl */ ?>

<div id="content">
<div class="left_bar">
<div id="breadcrumb_trail">
<?php echo $this->_tpl_vars['Breadcrumbs']; ?>

</div>
<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>
<br/>
<div style="margin: 0px 6px 4px 12px;">
<p>Address: <?php echo $this->_tpl_vars['Address']; ?>
<br/>
City: <?php echo $this->_tpl_vars['City']; ?>
<br/>
State: <?php echo $this->_tpl_vars['State']; ?>
<br/>
Zip: <?php echo $this->_tpl_vars['ZipCode']; ?>

</p>
</div>
<?php if ($this->_tpl_vars['NumEvents'] > 0): ?>
<div style="float: left; clear: none; width: 550px; margin: 5px 0px 10px 20px;"><a href="<?php echo $this->_tpl_vars['SeatingChartUrl']; ?>
" onclick="window.open('<?php echo $this->_tpl_vars['SeatingChartUrl']; ?>
','popup','width=500,height=500,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false" style="color: red"><img src="<?php echo $this->_tpl_vars['SeatingChartUrl']; ?>
" alt="<?php echo $this->_tpl_vars['venueName']; ?>
 Seating Chart"/><br /></a>
</div>

<h2><strong>Events at <?php echo $this->_tpl_vars['venueName']; ?>
</strong></h2>
<br/>
<div class="production_table">

        <table class="sortable" id="sortable_example" cellspacing="0" cellpadding="3" border="0" width="100%">


                <tr class="ticketHeading"><th>Event</th><th class="startsort">Date</th><th>Venue</th><th class="unsortable">&nbsp;</th></tr>

        <?php unset($this->_sections['event']);
$this->_sections['event']['name'] = 'event';
$this->_sections['event']['loop'] = is_array($_loop=$this->_tpl_vars['EventsArray']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['event']['show'] = true;
$this->_sections['event']['max'] = $this->_sections['event']['loop'];
$this->_sections['event']['step'] = 1;
$this->_sections['event']['start'] = $this->_sections['event']['step'] > 0 ? 0 : $this->_sections['event']['loop']-1;
if ($this->_sections['event']['show']) {
    $this->_sections['event']['total'] = $this->_sections['event']['loop'];
    if ($this->_sections['event']['total'] == 0)
        $this->_sections['event']['show'] = false;
} else
    $this->_sections['event']['total'] = 0;
if ($this->_sections['event']['show']):

            for ($this->_sections['event']['index'] = $this->_sections['event']['start'], $this->_sections['event']['iteration'] = 1;
                 $this->_sections['event']['iteration'] <= $this->_sections['event']['total'];
                 $this->_sections['event']['index'] += $this->_sections['event']['step'], $this->_sections['event']['iteration']++):
$this->_sections['event']['rownum'] = $this->_sections['event']['iteration'];
$this->_sections['event']['index_prev'] = $this->_sections['event']['index'] - $this->_sections['event']['step'];
$this->_sections['event']['index_next'] = $this->_sections['event']['index'] + $this->_sections['event']['step'];
$this->_sections['event']['first']      = ($this->_sections['event']['iteration'] == 1);
$this->_sections['event']['last']       = ($this->_sections['event']['iteration'] == $this->_sections['event']['total']);
?>
                <tr><td ><a href="<?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['event_url']; ?>
" style="color: blue;"><?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['name']; ?>
</a></td><td><?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['date']; ?>
<br /><?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['time']; ?>
</td><td><a href="<?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['venue_url']; ?>
" style="color: blue;"><?php echo $this->_tpl_vars['venueName']; ?>
</a></td><td><a href="<?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['prod_url']; ?>
"><img src="<?php echo $this->_tpl_vars['RootUrl']; ?>
/Images/tickets_vb.gif" alt="<?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['name']; ?>
 <?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['date']; ?>
 <?php echo $this->_tpl_vars['venueName']; ?>
 seating map" /></a></td></tr>
        <?php endfor; endif; ?>
	</table>
</div>
<br/>
<br/>
<br/>
<br/>
<?php else: ?>
<br/>
<h2>No tickets available at <?php echo $this->_tpl_vars['venueName']; ?>
.</h2>
<?php endif; ?>


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

