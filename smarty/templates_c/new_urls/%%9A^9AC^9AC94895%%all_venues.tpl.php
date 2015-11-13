<?php /* Smarty version 2.6.18, created on 2008-02-24 15:19:12
         compiled from all_venues.tpl */ ?>

<div id="content">
<div id="left_bar">
<div id="breadcrumb_trail">
<?php echo $this->_tpl_vars['Breadcrumbs']; ?>

</div>
<?php if ($this->_tpl_vars['NumStates'] > 0): ?>
<h1><?php echo $this->_tpl_vars['h1']; ?>
</h1>
<table class="category_event_list">
        <?php unset($this->_sections['state']);
$this->_sections['state']['name'] = 'state';
$this->_sections['state']['loop'] = is_array($_loop=$this->_tpl_vars['States']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['state']['show'] = true;
$this->_sections['state']['max'] = $this->_sections['state']['loop'];
$this->_sections['state']['step'] = 1;
$this->_sections['state']['start'] = $this->_sections['state']['step'] > 0 ? 0 : $this->_sections['state']['loop']-1;
if ($this->_sections['state']['show']) {
    $this->_sections['state']['total'] = $this->_sections['state']['loop'];
    if ($this->_sections['state']['total'] == 0)
        $this->_sections['state']['show'] = false;
} else
    $this->_sections['state']['total'] = 0;
if ($this->_sections['state']['show']):

            for ($this->_sections['state']['index'] = $this->_sections['state']['start'], $this->_sections['state']['iteration'] = 1;
                 $this->_sections['state']['iteration'] <= $this->_sections['state']['total'];
                 $this->_sections['state']['index'] += $this->_sections['state']['step'], $this->_sections['state']['iteration']++):
$this->_sections['state']['rownum'] = $this->_sections['state']['iteration'];
$this->_sections['state']['index_prev'] = $this->_sections['state']['index'] - $this->_sections['state']['step'];
$this->_sections['state']['index_next'] = $this->_sections['state']['index'] + $this->_sections['state']['step'];
$this->_sections['state']['first']      = ($this->_sections['state']['iteration'] == 1);
$this->_sections['state']['last']       = ($this->_sections['state']['iteration'] == $this->_sections['state']['total']);
?>
                <tr><td><a href="<?php echo $this->_tpl_vars['States'][$this->_sections['state']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['States'][$this->_sections['state']['index']]['name']; ?>
</a></td></tr>
        <?php endfor; endif; ?>
</table>
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
