<?php /* Smarty version 2.6.18, created on 2012-06-18 11:21:26
         compiled from error_page.tpl */ ?>
<div id="content">
<div class="left_bar">
   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>

	<div id="no_tickets">
	<p><?php echo $this->_tpl_vars['ErrorMessage']; ?>
</p>
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
