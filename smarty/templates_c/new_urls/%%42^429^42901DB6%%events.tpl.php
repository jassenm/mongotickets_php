<?php /* Smarty version 2.6.18, created on 2012-06-18 16:33:54
         compiled from events.tpl */ ?>

<div id="content">
<div class="left_bar">
<div id="breadcrumb_trail">
<?php echo $this->_tpl_vars['Breadcrumbs']; ?>

</div>
<h1><strong><?php echo $this->_tpl_vars['categoryName']; ?>
</strong></h1>
<?php if ($this->_tpl_vars['NumEvents'] > 0): ?>
<table class="category_event_list">
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
                <tr><td><a href="<?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['EventsArray'][$this->_sections['event']['index']]['name']; ?>
</a></td></tr>
        <?php endfor; endif; ?>
</table>
<?php endif; ?>


<?php if ($this->_tpl_vars['NumSubCategories'] > 0): ?>
<div class="category_subcategory_list">
	<ul>
         <?php unset($this->_sections['subcategory']);
$this->_sections['subcategory']['name'] = 'subcategory';
$this->_sections['subcategory']['loop'] = is_array($_loop=$this->_tpl_vars['SubCategories']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['subcategory']['show'] = true;
$this->_sections['subcategory']['max'] = $this->_sections['subcategory']['loop'];
$this->_sections['subcategory']['step'] = 1;
$this->_sections['subcategory']['start'] = $this->_sections['subcategory']['step'] > 0 ? 0 : $this->_sections['subcategory']['loop']-1;
if ($this->_sections['subcategory']['show']) {
    $this->_sections['subcategory']['total'] = $this->_sections['subcategory']['loop'];
    if ($this->_sections['subcategory']['total'] == 0)
        $this->_sections['subcategory']['show'] = false;
} else
    $this->_sections['subcategory']['total'] = 0;
if ($this->_sections['subcategory']['show']):

            for ($this->_sections['subcategory']['index'] = $this->_sections['subcategory']['start'], $this->_sections['subcategory']['iteration'] = 1;
                 $this->_sections['subcategory']['iteration'] <= $this->_sections['subcategory']['total'];
                 $this->_sections['subcategory']['index'] += $this->_sections['subcategory']['step'], $this->_sections['subcategory']['iteration']++):
$this->_sections['subcategory']['rownum'] = $this->_sections['subcategory']['iteration'];
$this->_sections['subcategory']['index_prev'] = $this->_sections['subcategory']['index'] - $this->_sections['subcategory']['step'];
$this->_sections['subcategory']['index_next'] = $this->_sections['subcategory']['index'] + $this->_sections['subcategory']['step'];
$this->_sections['subcategory']['first']      = ($this->_sections['subcategory']['iteration'] == 1);
$this->_sections['subcategory']['last']       = ($this->_sections['subcategory']['iteration'] == $this->_sections['subcategory']['total']);
?>
                        <li><a href="<?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['name']; ?>
</a> &nbsp;&nbsp;&nbsp;</li>
         <?php endfor; endif; ?>
	</ul>
</div> <!-- end category_subcategory_list -->
<?php endif; ?>

</div> <!-- end left -->

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

