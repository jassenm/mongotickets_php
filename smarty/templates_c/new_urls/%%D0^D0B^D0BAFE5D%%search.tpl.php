<?php /* Smarty version 2.6.18, created on 2012-06-20 01:07:13
         compiled from search.tpl */ ?>

<div id="content">
<div class="left_bar">

   <div id="breadcrumb_trail">
   <?php echo $this->_tpl_vars['Breadcrumbs']; ?>

   </div>

<h1><strong><?php echo $this->_tpl_vars['h1']; ?>
</strong></h1>


<?php if ($this->_tpl_vars['NumEvents'] > 0): ?>
	<h2>Events</h2>
	<table class="search_results">
       		<?php unset($this->_sections['event']);
$this->_sections['event']['name'] = 'event';
$this->_sections['event']['loop'] = is_array($_loop=$this->_tpl_vars['Events']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
               		<tr><td align="left" valign="top"><a href="<?php echo $this->_tpl_vars['Events'][$this->_sections['event']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['Events'][$this->_sections['event']['index']]['name']; ?>
</a></td></tr>
       		<?php endfor; endif; ?>
	</table>
<?php endif; ?>

<?php if ($this->_tpl_vars['NumVenues'] > 0): ?>
	<h2>Venues</h2>
	<table class="search_results">
       		<?php unset($this->_sections['venue']);
$this->_sections['venue']['name'] = 'venue';
$this->_sections['venue']['loop'] = is_array($_loop=$this->_tpl_vars['Venues']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['venue']['show'] = true;
$this->_sections['venue']['max'] = $this->_sections['venue']['loop'];
$this->_sections['venue']['step'] = 1;
$this->_sections['venue']['start'] = $this->_sections['venue']['step'] > 0 ? 0 : $this->_sections['venue']['loop']-1;
if ($this->_sections['venue']['show']) {
    $this->_sections['venue']['total'] = $this->_sections['venue']['loop'];
    if ($this->_sections['venue']['total'] == 0)
        $this->_sections['venue']['show'] = false;
} else
    $this->_sections['venue']['total'] = 0;
if ($this->_sections['venue']['show']):

            for ($this->_sections['venue']['index'] = $this->_sections['venue']['start'], $this->_sections['venue']['iteration'] = 1;
                 $this->_sections['venue']['iteration'] <= $this->_sections['venue']['total'];
                 $this->_sections['venue']['index'] += $this->_sections['venue']['step'], $this->_sections['venue']['iteration']++):
$this->_sections['venue']['rownum'] = $this->_sections['venue']['iteration'];
$this->_sections['venue']['index_prev'] = $this->_sections['venue']['index'] - $this->_sections['venue']['step'];
$this->_sections['venue']['index_next'] = $this->_sections['venue']['index'] + $this->_sections['venue']['step'];
$this->_sections['venue']['first']      = ($this->_sections['venue']['iteration'] == 1);
$this->_sections['venue']['last']       = ($this->_sections['venue']['iteration'] == $this->_sections['venue']['total']);
?>
               		<tr><td align="left" valign="top"><a href="<?php echo $this->_tpl_vars['Venues'][$this->_sections['venue']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['Venues'][$this->_sections['venue']['index']]['name']; ?>
</a></td></tr>
       		<?php endfor; endif; ?>
	</table>
<?php endif; ?>
<?php if (( $this->_tpl_vars['NumEvents'] == 0 ) && ( $this->_tpl_vars['NumVenues'] == 0 )): ?>
	<p style="margin: 0px 0px 0px 12px;">Your search returned <strong>0</strong> results</p>

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
