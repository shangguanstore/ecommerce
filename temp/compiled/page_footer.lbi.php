<div id="footer">
    <div class="global-footer-lazy">
        <div class="ecsc-service-flow">
            <div class="ecsc-service-item"><i class="ecsc-zp"></i><span>正品保障</span></div>
            <div class="ecsc-service-item"><i class="ecsc-qt"></i><span>七天包退</span></div>
            <div class="ecsc-service-item"><i class="ecsc-hp"></i><span>好评如潮</span></div>
            <div class="ecsc-service-item"><i class="ecsc-sd"></i><span>闪电发货</span></div>
            <div class="ecsc-service-item last"><i class="ecsc-ry"></i><span>权威荣誉</span></div>
        </div>
    </div>
    <div class="footer-desc">
        <div class="footer-desc-list">
        	<?php $_from = $this->_var['helps']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help_cat');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['help_cat']):
        $this->_foreach['no']['iteration']++;
?>
            <?php if ($this->_foreach['no']['iteration'] < 7): ?>
            <dl <?php if (($this->_foreach['no']['iteration'] == $this->_foreach['no']['total'])): ?>class="contact-us"<?php endif; ?>>
                <dt><?php echo $this->_var['help_cat']['cat_name']; ?></dt>
                <?php $_from = $this->_var['help_cat']['article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_10164200_1515739694');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_10164200_1515739694']):
?>
                <dd><a href="<?php echo $this->_var['item_0_10164200_1515739694']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['item_0_10164200_1515739694']['title']); ?>" target="_blank"><i>&gt;</i><?php echo $this->_var['item_0_10164200_1515739694']['short_title']; ?></a></dd>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </dl>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
       
        </div>
        <div class="footer-desc-copyright">
        	<?php if ($this->_var['navigator_list']['bottom']): ?> 
            <p class="footer-ecscinfo">
            	<?php $_from = $this->_var['navigator_list']['bottom']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav_0_10249500_1515739694');$this->_foreach['nav_bottom_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_bottom_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav_0_10249500_1515739694']):
        $this->_foreach['nav_bottom_list']['iteration']++;
?>
            	<a href="<?php echo $this->_var['nav_0_10249500_1515739694']['url']; ?>" <?php if ($this->_var['nav_0_10249500_1515739694']['opennew'] == 1): ?> target="_blank" <?php endif; ?>><?php echo $this->_var['nav_0_10249500_1515739694']['name']; ?></a> 
                <?php if (! ($this->_foreach['nav_bottom_list']['iteration'] == $this->_foreach['nav_bottom_list']['total'])): ?> 
                | 
                <?php endif; ?> 
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
            </p>
            <?php endif; ?>
            <?php if ($this->_var['img_links'] || $this->_var['txt_links']): ?>
            <p class="footer-otherlink">
            	<?php $_from = $this->_var['img_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
                <a href="<?php echo $this->_var['link']['url']; ?>" target="_blank" title="<?php echo $this->_var['link']['name']; ?>"><img src="<?php echo $this->_var['site_domain']; ?><?php echo $this->_var['link']['logo']; ?>" alt="<?php echo $this->_var['link']['name']; ?>" border="0" /></a>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php if ($this->_var['txt_links']): ?>
                <?php $_from = $this->_var['txt_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');$this->_foreach['nolink'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nolink']['total'] > 0):
    foreach ($_from AS $this->_var['link']):
        $this->_foreach['nolink']['iteration']++;
?>
                <a href="<?php echo $this->_var['link']['url']; ?>" target="_blank" title="<?php echo $this->_var['link']['name']; ?>"><?php echo $this->_var['link']['name']; ?></a>
                <?php if (! ($this->_foreach['nolink']['iteration'] == $this->_foreach['nolink']['total'])): ?> 
                | 
                <?php endif; ?> 
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php endif; ?>
            </p>
            <?php endif; ?>
            <?php if ($this->_var['icp_number']): ?> 
            <b><?php echo $this->_var['lang']['icp_number']; ?>:<a href="http://www.miibeian.gov.cn/" target="_blank"><?php echo $this->_var['icp_number']; ?></a></b>
            <?php endif; ?> 
            <p style="display:none"><?php 
$k = array (
  'name' => 'query_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></p>
            <p>&nbsp;</p>
            <?php if ($this->_var['stats_code']): ?>
            	<p style="text-align:right; display:none;"><?php echo $this->_var['stats_code']; ?></p>
            <?php endif; ?>
            <?php if ($this->_var['partner_img_links'] || $this->_var['partner_txt_links']): ?>
            <p class="footer-fp-img">
            	<?php $_from = $this->_var['partner_img_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
                <a href="<?php echo $this->_var['link']['url']; ?>" target="_blank" title="<?php echo $this->_var['link']['name']; ?>"><img src="<?php echo $this->_var['site_domain']; ?><?php echo $this->_var['link']['logo']; ?>" alt="<?php echo $this->_var['link']['name']; ?>" border="0" /></a>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php if ($this->_var['txt_links']): ?>
                <?php $_from = $this->_var['partner_txt_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');$this->_foreach['nolink'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nolink']['total'] > 0):
    foreach ($_from AS $this->_var['link']):
        $this->_foreach['nolink']['iteration']++;
?>
                <a href="<?php echo $this->_var['link']['url']; ?>" target="_blank" title="<?php echo $this->_var['link']['name']; ?>"><?php echo $this->_var['link']['name']; ?></a>
                <?php if (! ($this->_foreach['nolink']['iteration'] == $this->_foreach['nolink']['total'])): ?> 
                | 
                <?php endif; ?> 
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php endif; ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="pd_coupons" style="display: none;">
    <span class="success-icon m-icon"></span>
    <div class="item-fore">
        <h3>领取成功！感谢您的参与，祝您购物愉快~</h3>
        <div class="txt ftx-03">本活动为概率性事件，不能保证所有客户成功领取优惠券</div>
    </div>
</div>
<input name="seller_kf_IM" ru_id="<?php echo $_GET['merchant_id']; ?>" value="<?php echo $this->_var['shop_information']['is_IM']; ?>" type="hidden" rev="<?php echo $this->_var['site_domain']; ?>">
<input name="seller_kf_qq" value="<?php echo $this->_var['basic_info']['kf_qq']; ?>" type="hidden">
<input name="seller_kf_tel" value="<?php echo $this->_var['basic_info']['kf_tel']; ?>" type="hidden">
<?php if ($this->_var['suspension_two']): ?>
<script>var seller_qrcode='<img src="<?php echo $this->_var['site_domain']; ?><?php echo $this->_var['seller_qrcode_img']; ?>" alt="<?php echo $this->_var['seller_qrcode_text']; ?>" width="164" height="164">'; //by wu</script>
<?php echo $this->_var['suspension_two']; ?>
<?php endif; ?>
<script type="text/javascript">
$("#clear_price").click(function(){
	$("#price-min").val('');
	$("#price-max").val('');
});
	
$(".QR_code li").hover(function(){
	var index = $(this).index();
	$(this).addClass("current").siblings().removeClass("current");
	$(".QR_code .code_tp").eq(index).removeClass("hide").siblings().addClass("hide");
})

function checkSearchForm(){
	var keywords = $("input[name='keywords']").val();
	if(keywords == ''){
		
		divId = "keywords_html";
		var content = '<div id="' + divId + '">' + 
							'<div class="tip-box icon-box">' +
								'<span class="warn-icon m-icon"></span>' + 
								'<div class="item-fore">' +
									'<h3 class="rem ftx-04">您搜索内容的关键字不能空</h3>' +
								'</div>' +
							'</div>' +
						'</div>';
		
		pb({
			id:divId,
			title:'提示',
			width:445,
			height:58,
			content:content, 	//调取内容
			drag:false,
			foot:false
		});	
		
		return false;
	}
}
</script>
<!--[if lte IE 8]><?php echo $this->smarty_insert_scripts(array('files'=>'IE7Fix.js')); ?><![endif]-->

