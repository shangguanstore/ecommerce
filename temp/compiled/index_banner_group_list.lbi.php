
<?php if ($this->_var['prom_ad']): ?>
<div class="panic-buying">
    <h3>限时抢购</h3>
    <div class="panic-buy-slide">
        <ul>
        	<?php $_from = $this->_var['prom_ad']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ad_0_93999300_1520229985');$this->_foreach['noad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['noad']['total'] > 0):
    foreach ($_from AS $this->_var['ad_0_93999300_1520229985']):
        $this->_foreach['noad']['iteration']++;
?>
            <li>
            	<div class="time" data-time="<?php echo $this->_var['ad_0_93999300_1520229985']['end_time']; ?>">
                    <span class="days">00</span>天<span class="hours">00</span>时<span class="minutes">00</span>分<span class="seconds">00</span>秒
                </div>
                <div class="buy-img"><a href="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_link']; ?>" target="_blank"><img src="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_code']; ?>" width="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_width']; ?>" height="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_height']; ?>" /></a></div>
                <div class="buy-name"><a href="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_link']; ?>" title="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_name']; ?>"><?php echo $this->_var['ad_0_93999300_1520229985']['goods_name']; ?></a></div>
                <div class="buy-price">
                    <span class="shop-price"><?php if ($this->_var['ad_0_93999300_1520229985']['goods_info']['promote_price']): ?><?php echo $this->_var['ad_0_93999300_1520229985']['goods_info']['promote_price']; ?><?php else: ?><?php echo $this->_var['ad_0_93999300_1520229985']['goods_info']['shop_price']; ?><?php endif; ?></span>
                    <span class="original-price"><?php echo $this->_var['ad_0_93999300_1520229985']['goods_info']['market_price']; ?></span>
                </div>
                <div class="buy-btn">
                    <a href="<?php echo $this->_var['ad_0_93999300_1520229985']['ad_link']; ?>" target="_blank" class="btn">立即抢 ></a>
                    <div class="buy-num"><?php echo empty($this->_var['ad_0_93999300_1520229985']['goods_info']['sales_volume']) ? '0' : $this->_var['ad_0_93999300_1520229985']['goods_info']['sales_volume']; ?>人已抢购</div>
                </div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <a href="javascript:void(0);" class="buy-prev"></a>
        <a href="javascript:void(0);" class="buy-next"></a>
    </div>
</div>
<?php endif; ?>