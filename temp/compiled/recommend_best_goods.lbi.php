
<?php if ($this->_var['best_goods']): ?>
<div class="charts-item" style="display:none;">
    <ul>
    	<?php $_from = $this->_var['best_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_40463300_1517185304');$this->_foreach['best'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['best']['total'] > 0):
    foreach ($_from AS $this->_var['goods_0_40463300_1517185304']):
        $this->_foreach['best']['iteration']++;
?>
        <li>
            <b class="sales-num sales-num-<?php if ($this->_foreach['best']['iteration'] < 4): ?>1<?php else: ?>2<?php endif; ?>" id="<?php echo $this->_foreach['best']['iteration']; ?>"><?php echo $this->_foreach['best']['iteration']; ?></b>
            <div class="sales-product-img"><a href="<?php echo $this->_var['goods_0_40463300_1517185304']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods_0_40463300_1517185304']['name']); ?>"><img src="<?php echo $this->_var['goods_0_40463300_1517185304']['thumb']; ?>" width="52" height="52"></a></div>
            <div class="p-name"><a href="<?php echo $this->_var['goods_0_40463300_1517185304']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods_0_40463300_1517185304']['name']); ?>"><?php echo $this->_var['goods_0_40463300_1517185304']['short_style_name']; ?></a></div>
            <div class="p-price">
            	<?php if ($this->_var['goods_0_40463300_1517185304']['promote_price'] != ''): ?>
                    <?php echo $this->_var['goods_0_40463300_1517185304']['promote_price']; ?>
                <?php else: ?>
                    <?php echo $this->_var['goods_0_40463300_1517185304']['shop_price']; ?>
                <?php endif; ?> 
            </div>
        </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
</div>
<?php endif; ?> 