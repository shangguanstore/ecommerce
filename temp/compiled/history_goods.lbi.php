<?php if ($this->_var['history_goods']): ?>
<div class="history" id="history">
    <div class="mt">
        <h2>最近浏览</h2>
        <a onclick="clear_history()" class="clear_history ftx-05" href="javascript:void(0);">清空</a>
    </div>
    <div class="mc" id="history_list">
    	<ul>
        	<?php $_from = $this->_var['history_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_43737500_1517185304');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_43737500_1517185304']):
?>
        	<li>
            	<div class="item">
                    <div class="p-img"><a href="<?php echo $this->_var['goods_0_43737500_1517185304']['url']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/loadGoods.gif" width="72" height="72" class="lazy" data-original="<?php echo $this->_var['goods_0_43737500_1517185304']['goods_thumb']; ?>"/></a></div>
                    <div style="width:90px; float:left;">
                    	<div class="p-name"><a href="<?php echo $this->_var['goods_0_43737500_1517185304']['url']; ?>" target="_blank"><?php echo $this->_var['goods_0_43737500_1517185304']['short_name']; ?></a></div>
                        <div class="p-price">
                            <?php if ($this->_var['releated_goods_data']['promote_price'] != ''): ?>
                                <?php echo $this->_var['goods_0_43737500_1517185304']['formated_promote_price']; ?>
                            <?php else: ?>
                                <?php echo $this->_var['goods_0_43737500_1517185304']['shop_price']; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-num">售出：<strong> <?php echo empty($this->_var['goods_0_43737500_1517185304']['sales_volume']) ? '0' : $this->_var['goods_0_43737500_1517185304']['sales_volume']; ?></strong>笔</div>
                    </div>
                </div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>    
</div>
<?php endif; ?>
<script type="text/javascript">
if(document.getElementById('history_list')){
	if (document.getElementById('history_list').innerHTML.replace(/\s/g,'').length<1)
	{
		document.getElementById('history').style.display='none';
	}
	else
	{
		document.getElementById('history').style.display='block';
	}
}
function clear_history()
{
Ajax.call('user.php', 'act=clear_history',clear_history_Response, 'GET', 'TEXT',1,1);
}
function clear_history_Response(res)
{
document.getElementById('history_list').innerHTML = '<div class="history_tishi"><?php echo $this->_var['lang']['no_history']; ?><br /><a href="index.php" class="ftx-05">去购物</a></div>';
}
</script>