
<?php if ($this->_var['promotion_goods']): ?>
<div id="dome2">
    <div class="done-warp">
        <ul>
            <?php $_from = $this->_var['promotion_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_17028500_1515739839');$this->_foreach['promotion_foreach'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['promotion_foreach']['total'] > 0):
    foreach ($_from AS $this->_var['goods_0_17028500_1515739839']):
        $this->_foreach['promotion_foreach']['iteration']++;
?>
            <li>
                <div class="done-img"><a href="<?php echo $this->_var['goods_0_17028500_1515739839']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods_0_17028500_1515739839']['thumb']; ?>" title="<?php echo htmlspecialchars($this->_var['goods_0_17028500_1515739839']['name']); ?>" width="136" height="136" /></a></div> 
                <div class="done-name"><a href="<?php echo $this->_var['goods_0_17028500_1515739839']['url']; ?>" target="_blank" title="<?php echo htmlspecialchars($this->_var['goods_0_17028500_1515739839']['name']); ?>"><?php echo htmlspecialchars($this->_var['goods_0_17028500_1515739839']['short_name']); ?></a></div>
                <div class="done-price">
                    <span class="shop-price">
                        <?php if ($this->_var['goods_0_17028500_1515739839']['promote_price'] != ''): ?>
                            <?php echo $this->_var['goods_0_17028500_1515739839']['promote_price']; ?>
                        <?php else: ?>
                            <?php echo $this->_var['goods_0_17028500_1515739839']['shop_price']; ?>
                        <?php endif; ?>
                    </span>
                    <span class="original-price"><?php echo $this->_var['goods_0_17028500_1515739839']['market_price']; ?></span>
                </div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <a href="javascript:void(0);" class="done-prev"></a>
        <a href="javascript:void(0);" class="done-next"></a>
    </div>
</div>
<?php endif; ?>