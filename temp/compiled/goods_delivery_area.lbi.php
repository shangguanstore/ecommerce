
<div class="area_tit">
    <span><?php echo $this->_var['province_row']['region_name']; ?>&nbsp;<?php echo $this->_var['city_row']['region_name']; ?>&nbsp;<?php echo $this->_var['district_row']['region_name']; ?></span><i class="sc-icon-right"></i>
</div>
<div class="area_brand"></div>
<div class="area_list_style" id="area_list">
	<?php if ($this->_var['consignee_list']): ?>
	<div class="stock-add-used">
    	<div class="stock-top">
            <strong class="fl">常用地址</strong>
        </div>
        <div class="stock-con">
        	<ul class="area-list-used">
            	<?php $_from = $this->_var['consignee_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'consignee');$this->_foreach['noconsignee'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['noconsignee']['total'] > 0):
    foreach ($_from AS $this->_var['consignee']):
        $this->_foreach['noconsignee']['iteration']++;
?>
            	<li <?php if (($this->_foreach['noconsignee']['iteration'] == $this->_foreach['noconsignee']['total'])): ?>class="last"<?php endif; ?>><a href="javascript:;" onClick="get_region_change(<?php echo $this->_var['goods_id']; ?>, <?php echo $this->_var['consignee']['province_id']; ?>, <?php echo $this->_var['consignee']['city_id']; ?>, <?php echo $this->_var['consignee']['district_id']; ?>);" title="<?php echo $this->_var['consignee']['address']; ?>"><?php if ($this->_var['consignee']['address_id'] == $this->_var['address_id']): ?>（默认）<?php endif; ?><?php echo $this->_var['consignee']['consignee']; ?>&nbsp;&nbsp;<?php echo $this->_var['consignee']['city_name']; ?></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <div class="select_area_style" id="select_area">
        <div class="select_area_box">
            <div class="select_tab" onclick="region.selectArea(this, 1);" value="<?php echo $this->_var['province_row']['region_id']; ?>" id="province_li"><?php echo $this->_var['province_row']['region_name']; ?><i class="sc-icon-right"></i></div>
            <div class="select_tab" style="<?php if ($this->_var['district_row']['region_name'] == ''): ?>background:#fff;<?php endif; ?>" onclick="region.selectArea(this, 2);" value="<?php echo $this->_var['city_row']['region_id']; ?>" id="city_li"><?php echo $this->_var['city_row']['region_name']; ?><i class="sc-icon-right"></i></div>
            <div class="select_tab curr" style="<?php if ($this->_var['district_row']['region_name'] == ''): ?>display:none;<?php endif; ?> <?php if ($this->_var['district_row']['region_name']): ?>background:#fff<?php endif; ?>" onclick="region.selectArea(this, 3);" value="<?php echo $this->_var['city_district']['region_id']; ?>" id="district_type"><?php echo $this->_var['district_row']['region_name']; ?><i class="sc-icon-right"></i></div>
        </div>
    </div>
    <b onclick="region.closeRegion();" title="close" class="close_area_list"></b>
    <div class="house_list_style" id="house_list" style="display:none;" >
        <ul id="province_list">
             
            <?php $_from = $this->_var['province_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'province');$this->_foreach['noprovince'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['noprovince']['total'] > 0):
    foreach ($_from AS $this->_var['province']):
        $this->_foreach['noprovince']['iteration']++;
?>
                <li>
                    <a v="<?php echo $this->_var['province']['region_id']; ?>" title="<?php echo $this->_var['province']['region_name']; ?>" onclick="region.getRegion(<?php echo $this->_var['province']['region_id']; ?>, 2, city_list, this,<?php echo $this->_var['user_id']; ?>);" href="javascript:void(0);"><?php echo $this->_var['province']['region_name']; ?></a>
                </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            
        </ul>   
    </div>
    <div style="<?php if ($this->_var['district_row']['region_name'] == ''): ?>display: block;<?php else: ?>display: none;<?php endif; ?>" class="house_list_style" id="city_list_id">
        <ul id="city_list">    
                               	                             
            <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');$this->_foreach['nocity'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nocity']['total'] > 0):
    foreach ($_from AS $this->_var['city']):
        $this->_foreach['nocity']['iteration']++;
?>                                     
                <li>
                    <a v="<?php echo $this->_var['city']['region_id']; ?>" title="<?php echo $this->_var['city']['region_name']; ?>" onclick="region.getRegion(<?php echo $this->_var['city']['region_id']; ?>, 3, district_list, '<?php echo $this->_var['city']['region_name']; ?>',<?php echo $this->_var['user_id']; ?>);" href="javascript:void(0);"><?php echo $this->_var['city']['region_name']; ?></a>  
                </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>   
                              
        </ul>
    </div>
    <div style="<?php if ($this->_var['district_row']['region_name'] == ''): ?>display: none;<?php else: ?>display: block;<?php endif; ?>" class="house_list_style" id="district_list_id">
        <ul id="district_list">              
                
            <?php $_from = $this->_var['district_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'district');$this->_foreach['nodistrict'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nodistrict']['total'] > 0):
    foreach ($_from AS $this->_var['district']):
        $this->_foreach['nodistrict']['iteration']++;
?>
                <li>                     
                    <a v="<?php echo $this->_var['county']['region_id']; ?>" title="<?php echo $this->_var['district']['region_name']; ?>" onclick="region.changedDis(<?php echo $this->_var['district']['region_id']; ?>,<?php echo $this->_var['user_id']; ?>);" href="javascript:void(0);" id="district_<?php echo $this->_var['district']['region_id']; ?>"><?php echo $this->_var['district']['region_name']; ?></a>  
                </li>    
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>     
                   
        </ul>
    </div>
    <p class="mod_storage_state">商品暂时只支持配送至中国大陆地区</p>
</div>
 <input type="hidden" value="<?php echo $this->_var['province_row']['region_id']; ?>" id="province_id" name="province_region_id">
 <input type="hidden" value="<?php echo $this->_var['city_row']['region_id']; ?>" id="city_id" name="city_region_id">
 <input type="hidden" value="<?php if ($this->_var['district_row']['region_id']): ?><?php echo $this->_var['district_row']['region_id']; ?><?php else: ?>0<?php endif; ?>" id="district_id" name="district_region_id">         
 <input type="hidden" value="<?php echo $this->_var['merchant_id']; ?>" id="merchantId" name="merchantId">
 <input type="hidden" value="<?php echo $this->_var['user_id']; ?>" id="user_id" name="user_id">
 <input type="hidden" value="<?php echo $this->_var['goods_id']; ?>" id="good_id" name="good_id">
 
<script type="text/javascript">
function get_region_change(goods_id, province_id, city_id, district_id){
	Ajax.call(window.location.href, 'id=' + goods_id + '&act=in_stock' + '&province=' + province_id + "&city=" + city_id + "&district=" + district_id, ajax_is_inStock, "GET", "JSON");
}
function ajax_is_inStock(res){
	var t = '&t=' + parseInt(Math.random()*1000);
	var str_new = window.location.href.replace(/\&t\=\d+/g,t);
	location.href = str_new;
}
</script>
