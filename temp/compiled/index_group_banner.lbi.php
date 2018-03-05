
<?php if ($this->_var['ad_child']): ?>
<div class="shop-group">
    <ul>
    	<?php $_from = $this->_var['ad_child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ad_0_94357900_1520229985');$this->_foreach['noad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['noad']['total'] > 0):
    foreach ($_from AS $this->_var['ad_0_94357900_1520229985']):
        $this->_foreach['noad']['iteration']++;
?>
        <li>
            <a href="<?php echo $this->_var['ad_0_94357900_1520229985']['ad_link']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/loadGoods3.gif" style=" max-height:<?php echo $this->_var['ad_0_94357900_1520229985']['ad_height']; ?>px; max-width:<?php echo $this->_var['ad_0_94357900_1520229985']['ad_width']; ?>px;" class="lazy" data-original="<?php echo $this->_var['ad_0_94357900_1520229985']['ad_code']; ?>"/></a>
            <div class="group-ctr">
                <div class="time" data-time="<?php echo $this->_var['ad_0_94357900_1520229985']['end_time']; ?>">
                    <span class="days">00</span>天<span class="hours">00</span>时<span class="minutes">00</span>分<span class="seconds">00</span>秒
                </div>
            </div>
        </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <a href="javascript:void(0);" class="prev"></a>
    <a href="javascript:void(0);" class="next"></a>
    <i class="picon"></i>
</div>
<?php endif; ?>