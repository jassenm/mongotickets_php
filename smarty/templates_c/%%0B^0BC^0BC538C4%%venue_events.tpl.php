<?php /* Smarty version 2.6.18, created on 2007-09-08 22:19:59
         compiled from venue_events.tpl */ ?>

<div id="content">
<div id="breadcrumb_trail">
<?php echo $this->_tpl_vars['Breadcrumbs']; ?>

</div>
<?php if ($this->_tpl_vars['NumEvents'] > 0): ?>
<h1><?php echo $this->_tpl_vars['venueName']; ?>
 Events</h1>
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
