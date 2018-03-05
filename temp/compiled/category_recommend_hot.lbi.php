
<?php if ($this->_var['hot_goods']): ?>
<div class="ecsc-hot-sales">
    <div class="hotsale w1390">
        <div class="hatsale-mt">热门推荐</div>
        <div class="bd">
            <ul>
            	<?php $_from = $this->_var['hot_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                <li>
                    <div class="item">
                        <div class="p-img"><a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumb']; ?>" width="112" height="112" /></a></div>
                        <div class="p-name"><a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" target="_blank"><?php echo $this->_var['goods']['short_style_name']; ?></a></div>
                        <div class="p-price">
                        	<?php if ($this->_var['goods']['promote_price'] != ''): ?>
                                <?php echo $this->_var['goods']['promote_price']; ?>
                            <?php else: ?>
                                <?php echo $this->_var['goods']['shop_price']; ?>
                            <?php endif; ?>
                        </div>
                        <a class="btn6" href="<?php echo $this->_var['goods']['url']; ?>">去抢购 ></a>
                    </div>
                </li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
            <a href="javascript:void(0);" class="prev"></a>
            <a href="javascript:void(0);" class="next"></a>
        </div>
    </div>
</div>
<?php endif; ?>