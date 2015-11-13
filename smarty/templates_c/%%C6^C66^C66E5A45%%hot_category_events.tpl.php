<?php /* Smarty version 2.6.18, created on 2008-01-26 09:25:30
         compiled from hot_category_events.tpl */ ?>
<div id="content">
<div id="breadcrumb_trail">
<?php echo $this->_tpl_vars['Breadcrumbs']; ?>

</div>
<?php if ($this->_tpl_vars['CategoryName'] != ""): ?>
	<h1><strong><?php echo $this->_tpl_vars['CategoryName']; ?>
 Tickets</strong></h1>
<?php endif; ?>
<div id="intro_text">
<p><?php echo $this->_tpl_vars['TextContent']; ?>
</p>
</div>
<div id="hot_category_table">
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
                        <div class="hot_category_table_column">
                                <div class="hot_category_events">
					<?php if ($this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['catimage'] != ""): ?>
                                		<a href="<?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['caturl']; ?>
">
						<img src="<?php echo $this->_tpl_vars['RootUrl']; ?>
/<?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['catimage']; ?>
" alt="<?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['catname']; ?>
" width="158" height="117"/>
						</a>
					<?php endif; ?>
                                <h1><strong><a href="<?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['caturl']; ?>
"><?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['catname']; ?>
</a></strong></h1>
					<ul>
                                                <?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['top_events']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['key']['show'] = true;
$this->_sections['key']['max'] = $this->_sections['key']['loop'];
$this->_sections['key']['step'] = 1;
$this->_sections['key']['start'] = $this->_sections['key']['step'] > 0 ? 0 : $this->_sections['key']['loop']-1;
if ($this->_sections['key']['show']) {
    $this->_sections['key']['total'] = $this->_sections['key']['loop'];
    if ($this->_sections['key']['total'] == 0)
        $this->_sections['key']['show'] = false;
} else
    $this->_sections['key']['total'] = 0;
if ($this->_sections['key']['show']):

            for ($this->_sections['key']['index'] = $this->_sections['key']['start'], $this->_sections['key']['iteration'] = 1;
                 $this->_sections['key']['iteration'] <= $this->_sections['key']['total'];
                 $this->_sections['key']['index'] += $this->_sections['key']['step'], $this->_sections['key']['iteration']++):
$this->_sections['key']['rownum'] = $this->_sections['key']['iteration'];
$this->_sections['key']['index_prev'] = $this->_sections['key']['index'] - $this->_sections['key']['step'];
$this->_sections['key']['index_next'] = $this->_sections['key']['index'] + $this->_sections['key']['step'];
$this->_sections['key']['first']      = ($this->_sections['key']['iteration'] == 1);
$this->_sections['key']['last']       = ($this->_sections['key']['iteration'] == $this->_sections['key']['total']);
?>
                                                        <li><a href="<?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['top_events'][$this->_sections['key']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['SubCategories'][$this->_sections['subcategory']['index']]['top_events'][$this->_sections['key']['index']]['name']; ?>
</a>
                                                        </li>
                                                <?php endfor; endif; ?>
					</ul>
                                </div>
                        </div>
         <?php endfor; endif; ?>
</div>
