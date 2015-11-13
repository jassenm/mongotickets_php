<?php /* Smarty version 2.6.18, created on 2012-06-18 11:56:12
         compiled from main_related_events.tpl */ ?>
<?php if ($this->_tpl_vars['NumRelatedCategories'] > 0): ?>

<div id="related_events_table">
	<?php $_from = $this->_tpl_vars['RelatedCategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['category_name'] => $this->_tpl_vars['categoryArray']):
?>
                        <div class="related_events_column">
                                <div class="related_events_column_heading">Related <?php echo $this->_tpl_vars['category_name']; ?>
 Events</div>
                                <div class="related_events_column_list">
                                                <ul>
							<?php unset($this->_sections['index']);
$this->_sections['index']['name'] = 'index';
$this->_sections['index']['loop'] = is_array($_loop=$this->_tpl_vars['categoryArray']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['index']['show'] = true;
$this->_sections['index']['max'] = $this->_sections['index']['loop'];
$this->_sections['index']['step'] = 1;
$this->_sections['index']['start'] = $this->_sections['index']['step'] > 0 ? 0 : $this->_sections['index']['loop']-1;
if ($this->_sections['index']['show']) {
    $this->_sections['index']['total'] = $this->_sections['index']['loop'];
    if ($this->_sections['index']['total'] == 0)
        $this->_sections['index']['show'] = false;
} else
    $this->_sections['index']['total'] = 0;
if ($this->_sections['index']['show']):

            for ($this->_sections['index']['index'] = $this->_sections['index']['start'], $this->_sections['index']['iteration'] = 1;
                 $this->_sections['index']['iteration'] <= $this->_sections['index']['total'];
                 $this->_sections['index']['index'] += $this->_sections['index']['step'], $this->_sections['index']['iteration']++):
$this->_sections['index']['rownum'] = $this->_sections['index']['iteration'];
$this->_sections['index']['index_prev'] = $this->_sections['index']['index'] - $this->_sections['index']['step'];
$this->_sections['index']['index_next'] = $this->_sections['index']['index'] + $this->_sections['index']['step'];
$this->_sections['index']['first']      = ($this->_sections['index']['iteration'] == 1);
$this->_sections['index']['last']       = ($this->_sections['index']['iteration'] == $this->_sections['index']['total']);
?>
								<li><a href="<?php echo $this->_tpl_vars['categoryArray'][$this->_sections['index']['index']]['caturl']; ?>
"><?php echo $this->_tpl_vars['categoryArray'][$this->_sections['index']['index']]['catname']; ?>
</a></li>
							<?php endfor; endif; ?>
						</ul>
				</div> <!-- end related_events_column_list -->
				</div> <!-- end related_events_column -->
	<?php endforeach; endif; unset($_from); ?>
</div> <!-- end related_events_table -->
<?php endif; ?>
</div> <!-- end left_bar -->
