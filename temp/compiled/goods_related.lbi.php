
<?php if ($this->_var['related_goods']): ?>
<div id="browse-browse-pop" class="m goods_related">
    <div class="mt"><h2><?php echo $this->_var['lang']['releate_goods']; ?></h2></div>
    <div class="mc">
        <ul>
        	<?php $_from = $this->_var['related_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_40649600_1517185304');$this->_foreach['related'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['related']['total'] > 0):
    foreach ($_from AS $this->_var['goods_0_40649600_1517185304']):
        $this->_foreach['related']['iteration']++;
?>
            <li <?php if (($this->_foreach['related']['iteration'] == $this->_foreach['related']['total'])): ?> class="last"<?php endif; ?>>
                <div class="p-img"><a href="<?php echo $this->_var['goods_0_40649600_1517185304']['url']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/loadGoods.gif" width="172" height="172" class="lazy" data-original="<?php echo $this->_var['goods_0_40649600_1517185304']['goods_thumb']; ?>"></a></div>
                <div class="p-name"><a href="<?php echo $this->_var['goods_0_40649600_1517185304']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods_0_40649600_1517185304']['goods_name']); ?>" target="_blank"><?php echo $this->_var['goods_0_40649600_1517185304']['short_name']; ?></a></div>
                <div class="p-price">
                	<?php if ($this->_var['goods_0_40649600_1517185304']['promote_price'] != ''): ?>
                        <?php echo $this->_var['goods_0_40649600_1517185304']['formated_promote_price']; ?>
                    <?php else: ?>
                        <?php echo $this->_var['goods_0_40649600_1517185304']['shop_price']; ?>
                    <?php endif; ?>
                </div>
                <a href="javascript:addToCartShowDiv(<?php echo $this->_var['goods_0_40649600_1517185304']['goods_id']; ?>);" class="btn">加入购物车</a>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
</div>
<?php endif; ?>