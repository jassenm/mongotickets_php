<?php /* Smarty version 2.6.18, created on 2012-06-19 23:46:00
         compiled from no_tickets.tpl */ ?>

<div id="content">
<div class="left_bar">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>

<div id="no_tickets">
<h1><?php echo $this->_tpl_vars['EventName']; ?>
 Tickets</h1>
<p><?php echo $this->_tpl_vars['EventName']; ?>
 tickets are currently not available</p>
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

