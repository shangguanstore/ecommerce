<div class="car_goods_items">
	<div class="category_left" <?php if (! $this->_var['best_goods']): ?>style="width:100%"<?php endif; ?>>
		<?php if ($this->_var['script_name'] == 'search'): ?>
		<?php echo $this->fetch('/library/search_filter.lbi'); ?>
		<?php else: ?>
		<?php echo $this->fetch('/library/category_filter.lbi'); ?>
		<?php endif; ?>
    	<div class="car_goods_list goods_network_list" style="width:100%">
            <?php if ($this->_var['category'] > 0): ?>
            <form name="compareForm" action="compare.php" method="post" onSubmit="return compareGoods(this);" class="goodslistForm" data-state="0">
            <?php endif; ?>
            <div class="goods-list">
                <?php if ($this->_var['goods_list']): ?>
                <ul class="gl-warp" ectype="items">
                    <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                    <?php if ($this->_var['goods']['goods_id']): ?>
                    <li class="gl-item" <?php if (! $this->_var['best_goods']): ?>style="width:231px;"<?php endif; ?>>
                        <div class="gl-i-wrap network-wrap">
                            <div class="p-img">
                                <a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" width="216" height="216"/></a>
                                <div class="<?php if ($this->_var['goods']['is_new'] == 1): ?>new_picon pi1<?php elseif ($this->_var['goods']['is_hot'] == 1): ?>new_picon pi2<?php elseif ($this->_var['goods']['is_best'] == 1): ?>new_picon pi3<?php endif; ?>"></div>
                            </div>
                            <?php if ($this->_var['goods']['pictures']): ?>
                            <div class="sider">
                            	<ul>
                                	<?php $_from = $this->_var['goods']['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['picture'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['picture']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['picture']['iteration']++;
?>           
                                	<li <?php if (($this->_foreach['picture']['iteration'] - 1) == 0): ?> class="curr"<?php endif; ?>><img src="<?php if ($this->_var['picture']['thumb_url']): ?><?php echo $this->_var['picture']['thumb_url']; ?><?php else: ?><?php echo $this->_var['picture']['img_url']; ?><?php endif; ?>" width="26" height="26" /></li>
                                	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                </ul>
                                <!--<a href="javascript:void(0);" class="goods_prev"></a>
                                <a href="javascript:void(0);" class="goods_next"></a>-->
                            </div>
                            <?php endif; ?>
                            <div class="p-price">
                                <strong>
                                    <?php if ($this->_var['goods']['promote_price'] != ''): ?>
                                        <?php echo $this->_var['goods']['promote_price']; ?>
                                    <?php else: ?>
                                        <?php echo $this->_var['goods']['shop_price']; ?>
                                    <?php endif; ?>
                                </strong>
                            </div>
                            <div class="p-num">销量：<?php echo $this->_var['goods']['sales_volume']; ?></div>
                            <div class="p-name"><a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></div>
                            <div class="p-commit">
                            	<div class="p-commit-left">
                                	<div class="p-eva">已有<em><?php echo $this->_var['goods']['review_count']; ?> +</em>评价</div>
                                	<div class="p-store"><a href="<?php echo $this->_var['goods']['store_url']; ?>" target="_blank" class="p-brand" title="<?php echo $this->_var['goods']['rz_shopName']; ?>"><i class="icon"></i><?php echo $this->_var['goods']['rz_shopName']; ?></a></div>
                                </div>
                                <div class="p-commit-right">
                                	<?php if ($this->_var['goods']['prod'] == 1): ?>
                                        <?php if ($this->_var['goods']['goods_number'] > 0): ?>
                                            <a onClick="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,event,this,'flyItem');" class="addcart" rev="<?php echo $this->_var['goods']['goods_thumb']; ?>" data-dialog="addCart_dialog" data-id="" data-divid="addCartLog" data-url="" data-title="请选择属性">
                                               <i></i>
                                               购买
                                            </a>
                                        <?php else: ?>
                                            <a href="javascript:;" class="addcartWu">
                                                <i></i>
                                                无货
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                    <a onClick="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,event,this,'flyItem');" class="addcart" rev="<?php echo $this->_var['goods']['goods_thumb']; ?>">
                                        <i></i>
                                        购买
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($this->_var['goods']['prod'] == 1): ?>
                                <?php if (! $this->_var['goods']['goods_number'] > 0): ?>
                                    <div class="p-focus"><span>无货</span></div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="p-operation">
                                <a href="javascript:;" id="compareLink"><input id="<?php echo $this->_var['goods']['goods_id']; ?>" type="checkbox" onClick="Compare.add(this, <?php echo $this->_var['goods']['goods_id']; ?>,'<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>','<?php echo $this->_var['goods']['type']; ?>', '<?php echo $this->_var['goods']['goods_thumb']; ?>', '<?php echo $this->_var['goods']['shop_price']; ?>', '<?php echo $this->_var['goods']['market_price']; ?>')"/><label for="<?php echo $this->_var['goods']['goods_id']; ?>">对比</label></a>
                                <a href="javascript:collect(<?php echo $this->_var['goods']['goods_id']; ?>);" class="choose-btn-coll <?php if ($this->_var['goods']['is_collect']): ?>selected<?php endif; ?>"><b></b>收藏</a>
                                
                                <?php if ($this->_var['goods']['is_IM'] == 1 || $this->_var['goods']['is_dsc']): ?>
                                <a id="IM" onclick="openWin(this)" href="javascript:;" goods_id="<?php echo $this->_var['goods']['goods_id']; ?>"  class="seller-btn custom"><i class="icon"></i>客服</a>
                                <?php else: ?>
                                <?php if ($this->_var['goods']['kf_type'] == 1): ?>
                                <a href="http://www.taobao.com/webww/ww.php?ver=3&touid=<?php echo $this->_var['goods']['kf_ww']; ?>&siteid=cntaobao&status=1&charset=utf-8" class="seller-btn custom" target="_blank"><i class="icon"></i>客服</a>
                                <?php else: ?>
                                <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->_var['goods']['kf_qq']; ?>&site=qq&menu=yes" class="seller-btn custom" target="_blank"><i class="icon"></i>客服</a>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                            </div>
                            <?php if ($this->_var['dwt_filename'] == 'history_list'): ?> 
                            <div class="history_close">
                                <a href="javascript:delHistory(<?php echo $this->_var['goods']['goods_id']; ?>)"><img src="themes/ecmoban_dsc/images/p-del.png"></a>
                            </div>
                            <?php endif; ?> 
                        </div>
                    </li>
                    <?php endif; ?> 
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                </ul>
                <?php else: ?>
                <div class="notContent">抱歉，没有找到符合条件的数据</div>
                <?php endif; ?>
            </div>
            <div class="floor_loading" style="display:none">
                <div style="width:120px; height:100px; margin:auto;"><img src="themes/ecmoban_dsc/images/loading.gif"></div>
            </div>
            <?php if ($this->_var['category'] > 0): ?>
            </form>
            <?php endif; ?>
            <div id="flyItem" class="fly_item"><img src="" width="40" height="40"></div>
            <input type="hidden" value="<?php echo $this->_var['region_id']; ?>" id="region_id" name="region_id">
            <input type="hidden" value="<?php echo $this->_var['area_id']; ?>" id="area_id" name="area_id">
    	</div>
    	<div class="car_goods_list goods_switch_list" style="display:none;">
            <?php if ($this->_var['category'] > 0): ?>
            <form name="compareForm_cat" id="compareForm_cat" action="compare.php" method="post" onSubmit="return compareGoods(this);" class="goodslistForm" data-state="1">
            <?php endif; ?>
            <div class="goods-list">
            <?php if ($this->_var['goods_list']): ?>
            <div class="items clearfix" ectype="items">
                <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                <?php if ($this->_var['goods']['goods_id']): ?>
                <div class="item clearfix">
                    <div class="col col-1">
                        <div class="pic-img"><a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" width="98" height="98"/></a></div>
                    </div>
                    <div class="col col-2" <?php if (! $this->_var['best_goods']): ?>style="width:541px;"<?php endif; ?>>
                        <div class="title"><a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></div>
                        <div class="desc">
                        	<div class="lie">
                                <span class="p-eva">评价：<?php echo $this->_var['goods']['review_count']; ?></span>
                                <span class="p-num">销量：<?php echo $this->_var['goods']['sales_volume']; ?></span>
                                <span class="p-collect"><a href="javascript:collect(<?php echo $this->_var['goods']['goods_id']; ?>);" class="choose-btn-coll <?php if ($this->_var['goods']['is_collect']): ?>selected<?php endif; ?>"><b></b>收藏</a></span>
                            </div>
                            <div class="lie">
                            	<div class="p-store"><a href="<?php echo $this->_var['goods']['store_url']; ?>" target="_blank" class="p-brand"><i class="icon"></i><?php echo $this->_var['goods']['rz_shopName']; ?></a></div>
                            	<div class="p-customer">
                                	<?php if ($this->_var['goods']['kf_type'] == 1): ?>
                                    <a href="http://www.taobao.com/webww/ww.php?ver=3&touid=<?php echo $this->_var['goods']['kf_ww']; ?>&siteid=cntaobao&status=2&charset=utf-8" target="_blank" class="custom"><i class="icon"></i></a>
                                    <?php else: ?>
                                    <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->_var['goods']['kf_qq']; ?>&site=qq&menu=yes" target="_blank" class="custom"><i class="icon"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="p-price">
                            <?php if ($this->_var['goods']['promote_price'] != ''): ?>
                                <?php echo $this->_var['goods']['promote_price']; ?>
                            <?php else: ?>
                                <?php echo $this->_var['goods']['shop_price']; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-ship">运费：<?php echo $this->_var['goods']['shipping_fee_formated']; ?></div>
                    </div>
                    <div class="col col-4">
                    	<div class="p-number">
                            <span class="tit">数量：</span>
                            <div class="query">
                                <a class="btn-reduce" href="javascript:;" onclick="setAmount('reduce','<?php echo $this->_var['goods']['goods_id']; ?>')">-</a>
                                <input class="buy-num" id="product_num_<?php echo $this->_var['goods']['goods_id']; ?>" onchange="setAmount('edit','<?php echo $this->_var['goods']['goods_id']; ?>')" value="1" name="number" defaultnumber="1" autocomplete="off">
                                <a class="btn-adds" href="javascript:;" onclick="setAmount('add','<?php echo $this->_var['goods']['goods_id']; ?>')">+</a>
                            </div>
                        </div>
                        <div class="p-stock">
                        	<span class="tit">库存：</span>
                            <div class="goods_num" id="goods_num"><?php echo $this->_var['goods']['goods_number']; ?> 件</div>
                        </div>
                    </div>
                    <div class="col col-5">
                        <div class="buttons">
                            <a onClick="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,event,this,'');" data-confirm_type="3" class="btn-buynow">立即购买</a>
                            <?php if ($this->_var['goods']['prod'] == 1): ?>
                                <?php if ($this->_var['goods']['goods_number'] > 0): ?>
                                    <a onClick="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,event,this,'flyItem2');" class="btn" rev="<?php echo $this->_var['goods']['goods_thumb']; ?>" data-dialog="addCart_dialog" data-id="" data-divid="addCartLog" data-url="" data-title="请选择属性">
                                        <i class="icon-shopping-cart"></i>
                                        加入购物车
                                    </a>
                                <?php else: ?>
                                    <a href="javascript:;" class="btn end_btn">
                                        <i class="icon-shopping-cart"></i>
                                        暂时缺货
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                            <a onClick="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,event,this,'flyItem2');" class="btn" rev="<?php echo $this->_var['goods']['goods_thumb']; ?>">
                                <i class="icon-shopping-cart"></i>
                                加入购物车
                            </a>
                            <?php endif; ?>
                            <div class="red_bg"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?> 
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
            </div>
            <?php else: ?>
            <div class="notContent">抱歉，没有找到符合条件的数据</div>
            <?php endif; ?>
            </div>
            <div class="floor_loading" style="display:none">
                <div style="width:120px; height:100px; margin:auto;"><img src="themes/ecmoban_dsc/images/loading.gif"></div>
            </div>
            <?php if ($this->_var['category'] > 0): ?>
            </form>
            <?php endif; ?>
            <div id="flyItem2" class="fly_item2"><img src="" width="40" height="40"></div>
    	</div>
    </div>
    <?php echo $this->fetch('/library/category_recommend_best.lbi'); ?>
</div>
<script type="Text/Javascript" language="JavaScript">
<!--

function selectPage(sel)
{
  sel.form.submit();
}

//-->
</script> 
<script type="text/javascript">
window.onload = function()
{
  Compare.init();
  fixpng();
}
<?php $_from = $this->_var['lang']['compare_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['key'] != 'button_compare'): ?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php else: ?>
var button_compare = '';
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var compare_no_goods = "<?php echo $this->_var['lang']['compare_no_goods']; ?>";
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";


/*切换商品数量   by kong*/
function setAmount(i,j)
{
	var a = $("#product_num_"+j).val();
	if($.isNumeric(a))
	{
		if(i == "add")
		{
			a++;
			$("#product_num_"+j).val(a);
		}
		else if(i == "reduce")
		{
			a--;
			if(a >= 1)
			{
				$("#product_num_"+j).val(a);
			}
			else
			{   
				$("#product_num_"+j).val(1);
				alert("商品数量不能少于1件~");
			}
		}
		else
		{
			if(a < 1)
			{
				$("#product_num_"+j).val(1);
				alert("商品数量不能少于1件~");	
			}else{
                            $("#product_num_"+j).val(a);
                        }
		}
	}
	else
	{       
		$("#product_num_"+j).val(1);
		alert("商品数量必须为数字~");
	}
	
}
</script>