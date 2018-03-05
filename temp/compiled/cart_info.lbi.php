
<div class="ecsc-icon">
    <i class="ecsc-left"></i>
    <i class="ecsc-right">></i>
    <i class="ecsc-count cart_num"><?php echo $this->_var['str']; ?></i>
    <a href="flow.php">购物车</a>
</div>
<?php if ($this->_var['goods']): ?>
<div class="ecscup-content">
    <div class="ecscmc">
        <ul>
        	<?php $_from = $this->_var['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_17913100_1515739838');$this->_foreach['goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods']['total'] > 0):
    foreach ($_from AS $this->_var['goods_0_17913100_1515739838']):
        $this->_foreach['goods']['iteration']++;
?>
            <li>
            	<?php if ($this->_var['goods_0_17913100_1515739838']['rec_id'] > 0): ?>
                <div class="ecsc-img"><a href="<?php echo $this->_var['goods_0_17913100_1515739838']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods_0_17913100_1515739838']['goods_thumb']; ?>" /></a></div>
                <?php endif; ?>
                <div class="ecsc-way">
                	<?php if ($this->_var['goods_0_17913100_1515739838']['rec_id'] > 0 && $this->_var['goods_0_17913100_1515739838']['extension_code'] == 'package_buy'): ?>
                    <a href="javascript:void(0);" class="title"><?php echo $this->_var['goods_0_17913100_1515739838']['short_name']; ?><span style="color:#FF0000">（<?php echo $this->_var['lang']['remark_package']; ?>）</span></a>
                    <?php elseif ($this->_var['goods_0_17913100_1515739838']['rec_id'] > 0 && $this->_var['goods_0_17913100_1515739838']['is_gift'] != 0): ?>
                    <a href="javascript:void(0);" class="title"><?php echo $this->_var['goods_0_17913100_1515739838']['short_name']; ?><span style="color:#FF0000">（<?php echo $this->_var['lang']['largess']; ?>）</span></a>
                    <?php else: ?>
                    <a href="<?php echo $this->_var['goods_0_17913100_1515739838']['url']; ?>" target="_blank" class="title"><?php echo $this->_var['goods_0_17913100_1515739838']['short_name']; ?></a>
                    <?php endif; ?>
                    <div class="deal">
                    	<?php if ($this->_var['goods_0_17913100_1515739838']['rec_id'] > 0 && $this->_var['goods_0_17913100_1515739838']['extension_code'] != 'package_buy' && $this->_var['goods_0_17913100_1515739838']['is_gift'] == 0): ?>
                        <div class="count">
                            <a href="javascript:void(0);" id="min_number" onclick="changenum(<?php echo $this->_var['goods_0_17913100_1515739838']['rec_id']; ?>, -1, <?php echo $this->_var['goods_0_17913100_1515739838']['warehouse_id']; ?>, <?php echo $this->_var['goods_0_17913100_1515739838']['area_id']; ?>)" class="count-subtract"><s></s></a><span class="num" id="goods_number_<?php echo $this->_var['goods_0_17913100_1515739838']['rec_id']; ?>"><?php echo empty($this->_var['goods_0_17913100_1515739838']['goods_number']) ? '1' : $this->_var['goods_0_17913100_1515739838']['goods_number']; ?></span><a href="javascript:void(0);" onclick="changenum(<?php echo $this->_var['goods_0_17913100_1515739838']['rec_id']; ?>,1, <?php echo $this->_var['goods_0_17913100_1515739838']['warehouse_id']; ?>, <?php echo $this->_var['goods_0_17913100_1515739838']['area_id']; ?>)" class="count-add"><s></s><b></b></a>
                        </div>
                        <div class="price"><?php echo $this->_var['goods_0_17913100_1515739838']['goods_price']; ?></div>
                        <?php else: ?>
                        <div class="price" style=" margin:0px;"><?php echo $this->_var['goods_0_17913100_1515739838']['goods_price']; ?></div>
                        <?php endif; ?>
                        <a href="javascript:void(0);" onClick="deleteCartGoods(<?php echo $this->_var['goods_0_17913100_1515739838']['rec_id']; ?>,0)" class="close">删除</a>
                    </div>
                </div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
    <div class="ecscmb">
    	<input name="cart_value" id="cart_value" value="<?php echo $this->_var['cart_value']; ?>" type="hidden" />
        <div class="total"><span class="total-num cart_num"><?php echo $this->_var['str']; ?></span>件共计：<span class="total-price"><?php echo $this->_var['cart_info']['amount']; ?></span></div>
        <a href="flow.php" class="ecsc-cart">去购物车 <i class="ecsc-right">></i></a>
    </div>
</div>
<?php endif; ?>

<script type="text/javascript">

function changenum(rec_id, diff, warehouse_id, area_id)
{
	var cValue = $('#cart_value').val();
                var goods_number =Number($('#goods_number_' + rec_id).text()) + Number(diff);
 
	if(goods_number < 1)
	{
		return false;	
	}
	else
	{
		change_goods_number(rec_id,goods_number, warehouse_id, area_id, cValue);
	}
}
function change_goods_number(rec_id, goods_number, warehouse_id, area_id, cValue)
{
	if(cValue != '' || cValue == 'undefined'){
	   var cValue = $('#cart_value').val(); 
	}   
	
	Ajax.call('flow.php?step=ajax_update_cart', 'rec_id=' + rec_id +'&goods_number=' + goods_number +'&cValue=' + cValue +'&warehouse_id=' + warehouse_id +'&area_id=' + area_id, change_goods_number_response, 'POST','JSON');                
}
function change_goods_number_response(result)
{    
	var rec_id = result.rec_id;           
    if (result.error == 0)
    {
       $('#goods_number_' +rec_id).val(result.goods_number);//更新数量
       $('#goods_subtotal_' +rec_id).html(result.goods_subtotal);//更新小计
       if (result.goods_number <= 0)
        {// 数量为零则隐藏所在行
            $('#tr_goods_' +rec_id).style.display = 'none';
            $('#tr_goods_' +rec_id).innerHTML = '';
        }
        $('#total_desc').html(result.flow_info);//更新合计
        if($('ECS_CARTINFO'))
        {//更新购物车数量
            $('#ECS_CARTINFO').html(result.cart_info);
        }

		if(result.group.length > 0){
			for(var i=0; i<result.group.length; i++){
				$("#" + result.group[i].rec_group).html(result.group[i].rec_group_number);//配件商品数量
				$("#" + result.group[i].rec_group_talId).html(result.group[i].rec_group_subtotal);//配件商品金额
			
			}
		}

		$("#goods_price_" + rec_id).html(result.goods_price);
		
		//ecmoban模板堂 --zhuo 优惠活动 start
		$('#favourable_list').html(result.favourable_list_content);
		$('#your_discount').html(result.your_discount);
		if(result.discount){
			$('#cart_discount').css({"display" : ""});
		}else{
			$('#cart_discount').css({"display" : "none"});
		}
		//ecmoban模板堂 --zhuo 优惠活动 end
		                }
		                else if (result.message != '')
		                {
		$('#goods_number_' +rec_id).val(result.cart_Num);//更新数量
		                    alert(result.message);
		                }                
}


function deleteCartGoods(rec_id,index)
{
	Ajax.call('delete_cart_goods.php', 'id='+rec_id+'&index='+index, deleteCartGoodsResponse, 'POST', 'JSON');
}

/**
 * 接收返回的信息
 */
function deleteCartGoodsResponse(res)
{
  if (res.error)
  {
    alert(res.err_msg);
  }
  else if(res.index==1)
  {
		Ajax.call('get_ajax_content.php?act=get_content', 'data_type=cart_list', return_cart_list, 'POST', 'JSON');
  }
  else
  {
	  $("#ECS_CARTINFO").html(res.content);
	  $(".cart_num").html(res.cart_num);
  }
}

function return_cart_list(result)
{
	$(".cart_num").html(result.cart_num);
	$(".pop_panel").html(result.content);
}
</script>