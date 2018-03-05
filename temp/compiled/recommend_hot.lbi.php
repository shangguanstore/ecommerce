
<?php if ($this->_var['hot_goods']): ?>
<div id="dome1">
    <div class="done-warp">
        <ul>
            <?php $_from = $this->_var['hot_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            <li>
                <div class="done-img"><a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumb']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" width="136" height="136"/></a></div> 
                <div class="done-name"><a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo $this->_var['goods']['short_style_name']; ?></a></div>
                <div class="done-price">
                    <span class="shop-price">
                        <?php if ($this->_var['goods']['promote_price'] != ''): ?>
                            <?php echo $this->_var['goods']['promote_price']; ?>
                        <?php else: ?>
                            <?php echo $this->_var['goods']['shop_price']; ?>
                        <?php endif; ?>
                    </span>
                    <span class="original-price"><?php echo $this->_var['goods']['market_price']; ?></span>
                </div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <a href="javascript:void(0);" class="done-prev"></a>
        <a href="javascript:void(0);" class="done-next"></a>
    </div>
</div>
<?php endif; ?>

