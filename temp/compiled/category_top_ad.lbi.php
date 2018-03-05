
<?php if ($this->_var['ad_child']): ?>
<div class="category_adv w1390">
	<?php $_from = $this->_var['ad_child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'ad');$this->_foreach['noad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['noad']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['ad']):
        $this->_foreach['noad']['iteration']++;
?>
	<div <?php if ($this->_var['key'] % 2): ?>class="left"<?php else: ?>class="right"<?php endif; ?>><a href="<?php echo $this->_var['ad']['ad_link']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/loadGoods2.gif" style="max-width:<?php echo $this->_var['ad']['ad_width']; ?>px; max-height:<?php echo $this->_var['ad']['ad_height']; ?>px;" class="lazy" data-original="<?php echo $this->_var['ad']['ad_code']; ?>"/></a></div>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php endif; ?>