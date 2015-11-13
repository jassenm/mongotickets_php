<?php /* Smarty version 2.6.18, created on 2012-06-18 15:08:11
         compiled from related_events.tpl */ ?>
<?php if ($this->_tpl_vars['NumRelatedCategories'] > 0): ?>
	<div class="related_events_column">
		<div class="related_events_column_heading">Related Events</div>
		<div class="related_events_column_list">
			<ul>
				<?php unset($this->_sections['category']);
$this->_sections['category']['name'] = 'category';
$this->_sections['category']['loop'] = is_array($_loop=$this->_tpl_vars['RelatedCategories']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['category']['show'] = true;
$this->_sections['category']['max'] = $this->_sections['category']['loop'];
$this->_sections['category']['step'] = 1;
$this->_sections['category']['start'] = $this->_sections['category']['step'] > 0 ? 0 : $this->_sections['category']['loop']-1;
if ($this->_sections['category']['show']) {
    $this->_sections['category']['total'] = $this->_sections['category']['loop'];
    if ($this->_sections['category']['total'] == 0)
        $this->_sections['category']['show'] = false;
} else
    $this->_sections['category']['total'] = 0;
if ($this->_sections['category']['show']):

            for ($this->_sections['category']['index'] = $this->_sections['category']['start'], $this->_sections['category']['iteration'] = 1;
                 $this->_sections['category']['iteration'] <= $this->_sections['category']['total'];
                 $this->_sections['category']['index'] += $this->_sections['category']['step'], $this->_sections['category']['iteration']++):
$this->_sections['category']['rownum'] = $this->_sections['category']['iteration'];
$this->_sections['category']['index_prev'] = $this->_sections['category']['index'] - $this->_sections['category']['step'];
$this->_sections['category']['index_next'] = $this->_sections['category']['index'] + $this->_sections['category']['step'];
$this->_sections['category']['first']      = ($this->_sections['category']['iteration'] == 1);
$this->_sections['category']['last']       = ($this->_sections['category']['iteration'] == $this->_sections['category']['total']);
?>
					<li><a href="<?php echo $this->_tpl_vars['RelatedCategories'][$this->_sections['category']['index']]['caturl']; ?>
"><?php echo $this->_tpl_vars['RelatedCategories'][$this->_sections['category']['index']]['catname']; ?>
</a></li>
				<?php endfor; endif; ?>
			</ul>
		</div> <!-- end related_events_column_list -->
	</div> <!-- related_events_column -->
<?php endif; ?>
</div> <!-- end left_bar -->
