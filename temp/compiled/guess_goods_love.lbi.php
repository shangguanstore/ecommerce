<ul>
    <?php $_from = $this->_var['guess_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_42114500_1517185304');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_42114500_1517185304']):
?>
    <li>
        <div class="p-img"><a href="<?php echo $this->_var['goods_0_42114500_1517185304']['url']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/loadGoods.gif" width="134" height="134" class="lazy" data-original="<?php echo $this->_var['goods_0_42114500_1517185304']['goods_thumb']; ?>"></a></div>
        <div class="p-name"><a href="<?php echo $this->_var['goods_0_42114500_1517185304']['url']; ?>" target="_blank"><?php echo $this->_var['goods_0_42114500_1517185304']['short_name']; ?></a></div>
        <div class="p-price"><?php echo $this->_var['goods_0_42114500_1517185304']['shop_price']; ?></div>
        <a href="<?php echo $this->_var['goods_0_42114500_1517185304']['url']; ?>" target="_blank" class="btn6">去看看 &gt;</a>
    </li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
<script type="text/javascript">
	$(".clearance-content").find("img.lazy").lazyload({
		effect : "fadeIn"
	});
</script>