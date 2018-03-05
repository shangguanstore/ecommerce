<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />

<title><?php echo $this->_var['page_title']; ?></title>



<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />
<link rel="stylesheet" type="text/css" href="themes/ecmoban_dsc/base.css" />
<link href="<?php echo $this->_var['ecs_css_path']; ?>" rel="stylesheet" type="text/css" />
<link rel="alternate" type="application/rss+xml" title="RSS|<?php echo $this->_var['page_title']; ?>" href="<?php echo $this->_var['feed_url']; ?>" />
<link rel="stylesheet" type="text/css" href="themes/ecmoban_dsc/quickLinks.css" />
<link href="themes/ecmoban_dsc/area_stock.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="themes/ecmoban_dsc/purebox.css">
<link rel="stylesheet" href="themes/ecmoban_dsc/goods_fitting.css">
<link rel="stylesheet" href="themes/ecmoban_dsc/js/jquery-ui/jquery-ui.min.css">

<?php echo $this->smarty_insert_scripts(array('files'=>'jquery-1.9.1.min.js,jquery.json.js,transport_jquery.js,common.js,compare.js,utils.js,cart_common.js,cart_quick_links.js,warehouse_area.js')); ?>


<script type="text/javascript" src="themes/ecmoban_dsc/js/sc_memake.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/sc_common.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.SuperSlide.2.1.1.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.tabso_yeso.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.yomi.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/rotate3di.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/magiczoomplus.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/scroll_city.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/floor.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/notLogin.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery-ui/jquery-ui.min.js"></script>
<style>.suspension{display:none;}</style>
</head>

<body>

<?php if ($this->_var['area_htmlType'] != 'goods' && $this->_var['area_htmlType'] != 'exchange' && $this->_var['area_htmlType'] != 'presale' && $this->_var['area_htmlType'] != 'group_buy'): ?>
	<?php echo $this->smarty_insert_scripts(array('files'=>'warehouse_area.js')); ?>
<?php else: ?>
	<?php echo $this->smarty_insert_scripts(array('files'=>'warehouse.js')); ?>
<?php endif; ?>

<?php echo $this->fetch('library/page_header.lbi'); ?>
<div class="ecsc-breadcrumb w1200">
    <?php echo $this->fetch('library/ur_here.lbi'); ?>
</div>
<div id="p-box">
    <div class="w1200">
        <div class="product-intro">
            <?php echo $this->fetch('library/goods_gallery.lbi'); ?>
            <div class="m-item-inner">
                <div class="itemInfo">
                    <form action="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" method="post" name="ECS_FORMBUY" id="ECS_FORMBUY" >
                        <div class="name">
                            <h1><?php echo $this->_var['goods']['goods_style_name']; ?></h1>
                            <?php if ($this->_var['goods']['goods_brief']): ?>
                            <h2><?php echo $this->_var['goods']['goods_brief']; ?></h2>
                            <?php endif; ?>
                        </div>
                        <div class="summary clearfix">
                            
                            <div class="summary-list">
                            	<div class="summary-price">
                                	<div class="dt"><?php if ($this->_var['goods']['gmt_end_time']): ?><?php echo $this->_var['lang']['promote_price']; ?><?php else: ?><?php echo $this->_var['lang']['shop_price']; ?><?php endif; ?>：</div>
                                	<div class="dd"><strong class="p-price" id="ECS_SHOPPRICE"></strong>&nbsp;<a href="#none" user_id="<?php echo $this->_var['user_id']; ?>" goods_id="<?php echo $this->_var['goods']['goods_id']; ?>" class="price_notify">(降价通知)</a></div>
                                </div>
                                <?php if ($this->_var['cfg']['show_marketprice']): ?>
                            	<div class="summary-item">
                                	<div class="dt"><?php echo $this->_var['lang']['market_prices']; ?>：</div>
                                    <div class="dd"><span class="m-price" id="ECS_MARKETPRICE"><?php echo $this->_var['goods']['market_price']; ?></span></div>
                                </div>
                                <?php endif; ?>
                                <div class="summary-info">
                                	<div id="comment-count" class="comment-count item fl">
                                        <p class="comment">累计评价</p>
                                        <a class="count" href="#comment"><?php echo $this->_var['comment_all']['allmen']; ?></a>
                                    </div>
                                    <div id="comment-number" class="comment-count item fl">
                                        <p class="comment">累计销量</p>
                                        <a class="count" href="#comment"><?php echo $this->_var['goods']['sales_volume']; ?></a>
                                    </div>
                                </div>
                                <?php if ($this->_var['goods']['gmt_end_time']): ?>
                                <div class="summary-promotion-time"<?php if ($this->_var['promo_count'] > 1): ?> style="display:none;"<?php endif; ?>>
                                	<i class="clock"></i>
                                    <span class="label">剩余：</span>
                                    <div class="time" data-time="<?php echo $this->_var['goods']['promote_end_time']; ?>">
                                        <span class="days">00</span><i>天</i><span class="hours">00</span><i>时</i><span class="minutes">00</span><i>分</i><span class="seconds">00</span><i>秒</i>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($this->_var['two_code']): ?>
                                <div class="summary-phone">
                                	<div class="dt">手机购买：</div>
                                    <div class="dd">
                                    	<div class="qrcode-wrap">
                                            <div class="qrcode_tit">
                                                <span class="qrcode_span">手机扫描二维码</span>
                                                <span class="icon"><s></s><b></b></span>
                                            </div>
                                            <div id="summary-mbuy">
                                                <i></i>
                                                 <div class="qrcode"><img src="<?php echo $this->_var['weixin_img_url']; ?>" alt="商品二维码" title="<?php echo $this->_var['weixin_img_text']; ?>"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<?php endif; ?>
                                <?php if ($this->_var['promotion'] || $this->_var['goods']['consumption']): ?>
                                <div class="summary-item summary-promotion<?php if ($this->_var['goods']['gmt_end_time'] && $this->_var['promo_count'] == 1): ?> summaryEndTimeW<?php endif; ?>">
                                    <div class="dt">促　　销：</div>
                                    <div class="dd p-promotions-wrap">
                                    	<div class="p-promotions">
                                        	<?php if ($this->_var['promo_count'] > 1): ?>
                                        	<div class="prom-item">
                                            	<em class="ftx-01">以下促销可在购物车中使用</em>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($this->_var['goods']['consumption']): ?>
                                            <div class="prom-mj">
                                                <em class="h1_red_bg">满减</em>
                                                <em class="h1_red">
                                                <?php $_from = $this->_var['goods']['consumption']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'con');$this->_foreach['nocon'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nocon']['total'] > 0):
    foreach ($_from AS $this->_var['con']):
        $this->_foreach['nocon']['iteration']++;
?>
                                                    满<?php echo $this->_var['con']['cfull']; ?>减<?php echo $this->_var['con']['creduce']; ?>
                                                    <?php if (! ($this->_foreach['nocon']['iteration'] == $this->_foreach['nocon']['total'])): ?>，<?php endif; ?>
                                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                                </em>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php $_from = $this->_var['promotion']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['nopromotion'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nopromotion']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['nopromotion']['iteration']++;
?>
                                            	<?php if ($this->_var['item']['type'] == "favourable"): ?>
                                                <div class="prom-item">
                                                    <?php if ($this->_var['item']['act_type'] == 0): ?>
                                                    <em class="h1_red_bg">满赠</em>
                                                    <?php elseif ($this->_var['item']['act_type'] == 1): ?>
                                                    <em class="h1_red_bg">立减</em>
                                                    <?php elseif ($this->_var['item']['act_type'] == 2): ?>
                                                    <em class="h1_red_bg">折扣</em>
                                                    <?php endif; ?>
                                                    <em class="h1_red" title="<?php echo $this->_var['item']['act_name']; ?>"><?php echo $this->_var['item']['act_name']; ?></em>
                                                </div>
                                                
                                                <?php elseif ($this->_var['item']['type'] == "group_buy"): ?>
                                                <div class="prom-item">
                                                    <a href="group_buy.php" title="<?php echo $this->_var['lang']['group_buy']; ?>" class="hl_red_bg" title="<?php echo $this->_var['lang']['group_buy']; ?>"><?php echo $this->_var['lang']['group_buy']; ?></a>
                                                </div>
                                                <?php elseif ($this->_var['item']['type'] == "auction"): ?>
                                                <div class="prom-item">
                                                    <a href="auction.php" title="<?php echo $this->_var['lang']['auction']; ?>" class="hl_red_bg" title="<?php echo $this->_var['lang']['auction']; ?>"><?php echo $this->_var['lang']['auction']; ?></a>
                                                </div>
                                                <?php elseif ($this->_var['item']['type'] == "snatch"): ?>
                                                <div class="prom-item">
                                                	<a href="snatch.php" title="<?php echo $this->_var['lang']['snatch']; ?>" class="hl_red_bg" title="<?php echo $this->_var['lang']['snatch']; ?>"><?php echo $this->_var['lang']['snatch']; ?></a>
                                                </div>
                                                <?php endif; ?>
                                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                            
                                            <?php if ($this->_var['promo_count'] > 1): ?>
                                            	<div class="view-all-promotions">
                                                	<span class="prom-sum">共<em class="prom-number"><?php echo $this->_var['promo_count']; ?></em>项促销</span>
                                                    <a href="#none" class="view-link"><i class="i-arrow"></i></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div> 
                                </div>       
                                <?php endif; ?>

                                <?php if ($this->_var['goods_coupons']): ?>
                                <div class="summary-quan<?php if ($this->_var['goods']['gmt_end_time']): ?> summaryEndTimeW<?php endif; ?>">
                                	<div class="dt">领　　券：</div>
                                    <div class="dd">
                                    	<dl>
                                        	<dt class="fl"></dt>
                                            <?php $_from = $this->_var['goods_coupons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'vo');if (count($_from)):
    foreach ($_from AS $this->_var['vo']):
?>
                                            <dd class="lh">
                                                <a class="J-open-tb" href="#none" data-goodsid="<?php echo $this->_var['goods_id']; ?>">
                                                    <span class="quan-item">
                                                        <s></s><b></b>
                                                        <span class="text"><?php echo $this->_var['vo']['cou_man']; ?>-<?php echo $this->_var['vo']['cou_money']; ?></span>
                                                    </span>
                                                </a>
                                            </dd>
                                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                        </dl>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($this->_var['volume_price_list'] && $this->_var['regular']): ?>
                            <div class="summary-service">
                                <div class="dt">优惠阶梯：</div>
                                <div class="dd relative">
                                	<a href="javascript:void(0);" class="view_priceLadder">查看价格阶梯</a>
                                    <table width="60%" class="priceLadder">
                                        <tr>
                                        	<th align="center" width="35%"><?php echo $this->_var['lang']['number_to']; ?></th>
                                        	<th align="center" width="65%"><?php echo $this->_var['lang']['preferences_price']; ?></th>
                                        </tr>
                                        <?php $_from = $this->_var['volume_price_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('price_key', 'price_list');if (count($_from)):
    foreach ($_from AS $this->_var['price_key'] => $this->_var['price_list']):
?>
                                        <tr>
                                            <td align="center"><?php echo $this->_var['price_list']['number']; ?></td>
                                            <td align="center"><?php echo $this->_var['price_list']['format_price']; ?></td>
                                        </tr>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </table>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="summary-stock">
                            	<div class="dt">配　　送：</div>
                                <div class="dd" style="overflow:inherit">
                                    <div class="store-selector">
                                        <div class="text-select" id="area_address">
                                        </div>
    								</div>
                                    <div class="store-warehouse">
                                    	<div class="store-warehouse-info"></div>
                                        <div id="isHas_warehouse_num" class="store-prompt"></div>
                                    </div>
                                </div>
                                <input type="hidden" value="<?php echo $this->_var['region_id']; ?>" id="region_id" name="region_id">
                                <input type="hidden" value="<?php echo $this->_var['area_id']; ?>" id="area_id" name="area_id">
                            </div>
                            <div class="summary-service">
                                <div class="dt">服　　务：</div>
								<div class="dd">
									<?php if ($this->_var['goods']['user_id'] > 0): ?>
									由&nbsp;<a href="<?php echo $this->_var['goods']['store_url']; ?>" target="_blank"><?php echo $this->_var['goods']['rz_shopName']; ?></a>&nbsp;发货并提供售后服务。
									<?php else: ?>
									由&nbsp;<a href="javascript:void(0)" class="ftx-05"><?php echo $this->_var['basic_info']['shop_name']; ?></a>&nbsp;发货并提供售后服务。
									<?php endif; ?>
									
									<?php if ($this->_var['shippingFee']['is_shipping'] != 1): ?>
									<span style="color:#999;">[ 该地区不支持配送 ]</span>
									<?php else: ?>
									<span style="color:#999;">[ 快递：<?php echo $this->_var['shippingFee']['shipping_fee_formated']; ?> ]</span>
									<?php endif; ?>	
																		
								</div>
                            </div>
                        </div>
                        
                        <div class="choose p-choose-wrap">
                        	<?php if ($this->_var['cfg']['show_brand'] && $this->_var['goods']['user_id']): ?>
                            <div class="summary-service">
                                <div class="dt"><?php echo $this->_var['lang']['goods_brand']; ?></div>
								<div class="dd">
									<a style="margin-left:0px;" href="<?php echo $this->_var['goods']['goods_brand_url']; ?>" ><?php echo $this->_var['goods']['goods_brand']; ?></a>
								</div>
                            </div>
							<?php endif; ?>
                        	
                            <?php if ($this->_var['cfg']['use_integral']): ?>
                            <div class="summary-service">
                                <div class="dt"><?php echo $this->_var['lang']['goods_integral']; ?></div>
								<div class="dd"><?php echo $this->_var['goods']['integral']; ?> <?php echo $this->_var['points_name']; ?></div>
                            </div>
                            <?php endif; ?>
                            
                        	<?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
?>
                            <?php if ($this->_var['spec']['values']): ?>
                            <div class="choose-version <?php if ($this->_var['spec']['attr_type'] == 1): ?>attr-radio<?php else: ?>attr-check<?php endif; ?> goods_info_attr li">
                                <div class="dt"><?php echo $this->_var['spec']['name']; ?>：</div>
                                <?php if ($this->_var['cfg']['goodsattr_style'] == 1): ?>
                                    <div class="dd">
                                        <?php if ($this->_var['spec']['is_checked'] > 0): ?>
                                        <ul>
                                        <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');$this->_foreach['attrvalues'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['attrvalues']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
        $this->_foreach['attrvalues']['iteration']++;
?>  
                                        <li class="item<?php if ($this->_var['value']['checked'] == 1 && $this->_var['cfg']['add_shop_price'] == 1): ?> selected<?php endif; ?>">
                                            <b></b>
                                            <a rev="<?php echo $this->_var['value']['img_site']; ?>" name="<?php echo $this->_var['value']['id']; ?>" href="javascript:;" onclick="get_url(this)" >
                                                <?php if ($this->_var['value']['img_flie']): ?>
                                                <img src="<?php echo $this->_var['value']['img_flie']; ?>" width="25" height="25" />
                                                <?php echo $this->_var['value']['label']; ?>
                                                <?php else: ?>
                                                <?php echo $this->_var['value']['label']; ?>
                                                <?php endif; ?>
                                                <input style="display:none" id="spec_value_<?php echo $this->_var['value']['id']; ?>" type="<?php if ($this->_var['spec']['attr_type'] == 2): ?>checkbox<?php else: ?>radio<?php endif; ?>" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" />
                                                <?php if ($this->_var['value']['checked'] == 1): ?>
												<script type="text/javascript">
													$(function(){
														<?php if ($this->_var['cfg']['add_shop_price'] == 1): ?>
														$("#spec_value_<?php echo $this->_var['value']['id']; ?>").prop("checked", true);
														<?php else: ?>
														$("#spec_value_<?php echo $this->_var['value']['id']; ?>").prop("checked", false);
														<?php endif; ?>
													});
                                                </script>
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                        </ul>
                                        <?php else: ?>
                                        <ul>
                                            <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');$this->_foreach['attrvalues'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['attrvalues']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
        $this->_foreach['attrvalues']['iteration']++;
?>  
                                                <li class="item<?php if ($this->_var['key'] == 0 && $this->_var['cfg']['add_shop_price'] == 1): ?> selected<?php endif; ?>">
                                                    <b></b>
                                                    <a href="javascript:;" name="<?php echo $this->_var['value']['id']; ?>" class="noimg">
                                                        <?php echo $this->_var['value']['label']; ?>
                                                        <input style="display:none" id="spec_value_<?php echo $this->_var['value']['id']; ?>" type="<?php if ($this->_var['spec']['attr_type'] == 2): ?>checkbox<?php else: ?>radio<?php endif; ?>" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" /></a> 
                                                        <?php if ($this->_var['key'] == 0): ?>
														<script type="text/javascript">
                                                            $(function(){
																<?php if ($this->_var['cfg']['add_shop_price'] == 1): ?>
                                                                $("#spec_value_<?php echo $this->_var['value']['id']; ?>").prop("checked", true);
																<?php else: ?>
																$("#spec_value_<?php echo $this->_var['value']['id']; ?>").prop("checked", false);
																<?php endif; ?>
                                                            });
                                                        </script>
                                                        <?php endif; ?>  
                                                    </a>
                                                </li>
                                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                        </ul>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                
                                <?php endif; ?>  
                                <input type="hidden" name="spec_list" value="<?php echo $this->_var['spec_key']; ?>" />    
                            </div>
                            <?php endif; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            
                            <?php if ($this->_var['goods']['is_xiangou'] == 1): ?>
                            <div class="choose-num li">
                                <div class="dt">限　　购：</div>
                                <div class="dd">
                                <?php echo $this->_var['goods']['xiangou_num']; ?>&nbsp;<?php echo $this->_var['goods']['measure_unit']; ?>(已购买：<?php echo empty($this->_var['orderG_number']) ? '0' : $this->_var['orderG_number']; ?>&nbsp;<?php echo $this->_var['goods']['measure_unit']; ?>)
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            
                            <?php if ($this->_var['goods']['store_count'] > 0): ?>
                            <div class="choose-store li">
                                <div class="dt">门店服务：</div>
                                <div class="dd">
                                    <a href="javascript:void(0);" onclick="seller_store(<?php echo $this->_var['goods']['goods_id']; ?>)"><i></i><span>门店自提</span></a>&nbsp;&nbsp;·&nbsp;&nbsp;选择有现货的门店下单，可立即提货
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            
                            
                            <?php if ($this->_var['stages']): ?>
                            <div class="choose-stages li" id="chooseStages">
                            	<div class="dt">白条分期：</div>
                                <div class="dd">
                                	<ul>
                                    	<?php $_from = $this->_var['stages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'vo');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['vo']):
?>
                                    	<?php if ($this->_var['k'] == 1): ?>
                                    	<li class="item" flag="0" val="<?php echo $this->_var['k']; ?>">
                                        	<b></b>
                                            <a href="javascript:;">
                                                <strong>30天免息</strong>
                                                <span class="stages_intro">无手续费</span>
                                            </a>
                                        </li>
                                        <?php else: ?>
                                        <li class="item" flag="0" val="<?php echo $this->_var['k']; ?>">
                                        	<b></b>
                                            <a href="javascript:;">
                                                <strong>￥<?php echo $this->_var['vo']['stages_one_price']; ?>×<?php echo $this->_var['k']; ?>期</strong>
                                                <span class="stages_intro">含手续费：费率<?php echo $this->_var['goods']['stages_rate']; ?>%，￥<?php echo $this->_var['vo']['stages_one_price']; ?>×<?php echo $this->_var['k']; ?>期</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                        <input type="hidden" name="stages_qishu" value=""/>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="choose-num choose-xznum li">
                                <div class="dt">数　　量：</div>
                                <div class="dd">
                                    <a class="btn-reduce" href="javascript:;">-</a>
                                    <input class="text buy-num" id="quantity" onblur="changePrice()" value="1" name="number" defaultnumber="1">
                                    <input type="hidden" id="perNumber" value="1000">
                                    <input type="hidden" id="perMinNumber" value="1">
                                    <a class="btn-add" href="javascript:;">+</a>
                                    <?php if ($this->_var['cfg']['show_goodsnumber']): ?>
                                    <span>库存：<em id="goods_attr_num"></em>&nbsp;<?php echo $this->_var['goods']['measure_unit']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="choose-btns">
                            	<input name="confirm_type" id="confirm_type" type="hidden" value="3" />
                                <?php if ($this->_var['goods']['review_status'] <= 2 || $this->_var['goods']['is_on_sale'] == 0): ?>
                                    <a id="sold_out" class="ajax_buy" href="javascript:;"></a>
                                <?php else: ?>
                                    <?php if ($this->_var['goods_area'] == 1): ?>
                                    	<a href="<?php if ($this->_var['user_id'] <= 0 && $this->_var['one_step_buy']): ?>#none<?php else: ?>javascript:bool=0;addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)<?php endif; ?>" class="buynow btn-buynow">立即购买</a>
                                        <?php if (! $this->_var['one_step_buy']): ?>
                                        <a href="javascript:bool=0;addToCartShowDiv(<?php echo $this->_var['goods']['goods_id']; ?>)" class="btn-append"><i class="icon"></i>加入购物车</a>
                                        <?php endif; ?>
                                    	
                                        <?php if ($this->_var['stages']): ?>
                                        <a class="btn-stages btn_disabled" href="javascript:;" title="">分期购</a>
                                        <?php endif; ?>
                                    	
                                    <?php else: ?>
                                    <a id="no_addToCart" class="ajax_buy" href="javascript:;"></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($this->_var['goods']['goods_extends']): ?>
                                    <div class="choose-desc">
                                        <?php if ($this->_var['goods']['goods_extends']['is_reality']): ?><div class="choose-item choose-zp"><i class="icon"></i>正品</div><?php endif; ?>
                                        <?php if ($this->_var['goods']['goods_extends']['is_return']): ?><div class="choose-item choose-bt"><i class="icon"></i>包退</div><?php endif; ?>
                                        <?php if ($this->_var['goods']['goods_extends']['is_fast']): ?><div class="choose-item choose-ss"><i class="icon"></i>闪速</div><?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
            <?php echo $this->fetch('library/goods_merchants.lbi'); ?>
        </div>
    </div>
</div>
<div class="w1200">
    <?php echo $this->fetch('library/goods_fittings.lbi'); ?>
    <div class="ecsc-goods-layout">
        <div class="ecsc-layout-210">
          <?php if ($this->_var['basic_info']['kf_type'] == 1): ?>
              <?php if ($this->_var['basic_info']['kf_ww_all']): ?>
              <div class="m service_list">
                <div class="mt"><h2>店内客服</h2></div>
                <div class="mc">
                    <ul>
                        <?php $_from = $this->_var['basic_info']['kf_ww_all']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'kf_ww');if (count($_from)):
    foreach ($_from AS $this->_var['kf_ww']):
?>
                        <li><a href="http://www.taobao.com/webww/ww.php?ver=3&touid=<?php echo $this->_var['kf_ww']['1']; ?>&siteid=cntaobao&status=1&charset=utf-8" target="_blank"><i class="icon_service_ww"></i><span><?php echo $this->_var['kf_ww']['0']; ?></span></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
              </div>
              <?php endif; ?>
          <?php else: ?>
          	  <?php if ($this->_var['basic_info']['kf_qq_all']): ?>
              <div class="m service_list">
                <div class="mt"><h2>店内客服</h2></div>
                <div class="mc">
                    <ul>
                        <?php $_from = $this->_var['basic_info']['kf_qq_all']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'kf_qq');if (count($_from)):
    foreach ($_from AS $this->_var['kf_qq']):
?>
                        <li><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->_var['kf_qq']['1']; ?>&site=qq&menu=yes" target="_blank"><i class="icon icon_service_qq"></i><span><?php echo $this->_var['kf_qq']['0']; ?></span></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
              </div>
              <?php endif; ?>
          <?php endif; ?>
          <?php if ($this->_var['goods']['user_id']): ?>
          <form method="GET" action="merchants_store.php" class="sort" name="listform">
            <div id="sp-search" class="m">
                <div class="mt"><h2>店内搜索</h2></div>
                <div class="mc">
                    <p class="sp-form-item1"><input type="text" name="keyword" id="sp-keyword" value="" placeholder="关键字"></p>
                    <p class="sp-form-item2"><input type="text" id="sp-price" name="price_min" class="sp-price" value="" placeholder="价格"><span>~</span><input type="text" name="price_max" class="sp-price" id="sp-price1" value="" placeholder="价格"></p>
                    <p class="sp-form-item3"><i class="icon"></i><input type="submit" value="搜索" id="btnShopSearch"></p>
                    <input type="hidden" name="merchant_id" value="<?php echo $this->_var['goods']['user_id']; ?>" />
                </div>
            </div>
            </form>
            <?php if ($this->_var['goods_store_cat']): ?>
            <div id="sp-category" class="m">
                <div class="mt"><h2>店内分类</h2></div>
                <div class="mc">
                    <h2>查看全部商品</h2>
                    <?php $_from = $this->_var['goods_store_cat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['cat']):
?>
                    <dl class="<?php if ($this->_var['key'] == 0): ?>open<?php endif; ?>">
                        <dt><s class="icon"></s><span class="cate_name"><?php echo $this->_var['cat']['name']; ?></span></dt>
                        <?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cat');$this->_foreach['nocat'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nocat']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['cat']):
        $this->_foreach['nocat']['iteration']++;
?>
                        <dd><a href="<?php echo $this->_var['cat']['url']; ?>" target="_blank"><?php echo $this->_var['cat']['name']; ?></a></dd>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </dl>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($this->_var['goods_related_cat']): ?>
            <div class="m goods_related_cat">
            	<div class="mt"><h2>相关分类</h2></div>
                <div class="mc">
                	<ul class="lh">
                    	<?php $_from = $this->_var['goods_related_cat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['cat']):
?>
                    	<li><a href="<?php echo $this->_var['cat']['url']; ?>" target="_blank"><?php echo $this->_var['cat']['cat_name']; ?></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($this->_var['goods_brand']): ?>
            <div class="m goods_brand">
            	<div class="mt"><h2>同类其他品牌</h2></div>
                <div class="mc">
                	<ul class="lh">
                    	<?php $_from = $this->_var['goods_brand']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                    	<li><a href="<?php echo $this->_var['brand']['url']; ?>" target="_blank"><?php echo $this->_var['brand']['brand_name']; ?></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <div id="charts" class="m">
                <div class="mt"><h2>最新排行榜</h2></div>
                <div class="mc">
                    <ul class="charts-tab">
                        <li class="first on">新品<i></i></li>
                        <li>推荐<i></i></li>
                        <li class="last">热销<i></i></li>
                    </ul>
                    <div class="charts-list">
                    	
<?php echo $this->fetch('library/recommend_new_goods.lbi'); ?>
 
                        
<?php echo $this->fetch('library/recommend_best_goods.lbi'); ?>
 
                        
<?php echo $this->fetch('library/recommend_hot_goods.lbi'); ?>
 
                    </div>
                </div>
            </div>
            
<?php echo $this->fetch('library/goods_related.lbi'); ?>

            
<?php echo $this->fetch('library/goods_article.lbi'); ?>
       
<?php 
$k = array (
  'name' => 'history_goods',
  'goods_id' => '0',
  'warehouse_id' => $this->_var['region_id'],
  'area_id' => $this->_var['area_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
        </div>
        <div class="ecsc-goods-main">
            <div class="ecsc-single-desc desc">
                <div class="goods-detail-title goods-detail-mt">
                    <ul class="tabs">
                        <li class="tab_item first current">商品详情</li>
                        <li class="tab_item">商品评论</li>
                        <li class="tab_item">网友讨论圈</li>
                    </ul>
                </div>
                
                <div class="floors">
                    <div class="ecsc-goods-item fment">
                    	<dl class="goods-para">
                            <dd class="column"><span>商品名称：<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></span></dd>
                            <dd class="column"><span>商品编号：<?php echo $this->_var['goods']['goods_sn']; ?></span></dd>
                            <dd class="column"><span>店铺：<a href="<?php echo $this->_var['goods']['store_url']; ?>" title="<?php echo $this->_var['goods']['rz_shopName']; ?>" target="_blank"><?php echo $this->_var['goods']['rz_shopName']; ?></a></span></dd>
                            <?php if ($this->_var['cfg']['show_goodsweight']): ?>
                            <dd class="column"><span>重量：<?php echo $this->_var['goods']['goods_weight']; ?></span></dd>
                            <?php endif; ?>
                            <?php if ($this->_var['cfg']['show_addtime']): ?>
                            <dd class="column"><span>上架时间：<?php echo $this->_var['goods']['add_time']; ?></span></dd>
                            <?php endif; ?>
                        </dl>
                        <?php if ($this->_var['properties']): ?>
                        <dl class="goods-para" style="margin-top:0px;">
                            <?php $_from = $this->_var['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'property_group');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['property_group']):
?>	
                            <dt class="hide"><?php echo $this->_var['key']; ?></dt>
                            <?php $_from = $this->_var['property_group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'property');if (count($_from)):
    foreach ($_from AS $this->_var['property']):
?>
                            <dd class="column"><span title="<?php echo $this->_var['property']['value']; ?>"><?php echo htmlspecialchars($this->_var['property']['name']); ?>：<?php echo $this->_var['property']['value']; ?></span></dd>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </dl>
                        <?php endif; ?>
                        <div class="goods-detail-banner">
                            <?php echo $this->_var['goods']['goods_desc']; ?>
                        </div>
                    </div>
                    <div class="ecsc-goods-item fment pinglun">
                        <div class="goods-detail-title">
                            <h2>商品评价(<?php echo $this->_var['comment_all']['allmen']; ?>)</h2>
                            <a href="user.php?act=comment_list" class="btn-comment" target="_blank">发表评论</a>
                        </div>
                        <div class="overview-detail">
                            <div class="rate">
                                <strong><?php echo $this->_var['comment_all']['goodReview']; ?><span>%</span></strong>
                                <span class="hp">好评率</span>
                            </div>
                            <div class="percent">
                                <dl>
                                    <dt>好评<span>(<?php echo $this->_var['comment_all']['goodReview']; ?>%)</span></dt>
                                    <dd><div style="width: <?php echo $this->_var['comment_all']['goodReview']; ?>px;"></div></dd>
                                </dl>
                                <dl>
                                    <dt>中评<span>(<?php echo $this->_var['comment_all']['middlReview']; ?>%)</span></dt>
                                    <dd><div style="width: <?php echo $this->_var['comment_all']['middlReview']; ?>px;"></div></dd>
                                </dl>
                                <dl>
                                    <dt>差评<span>(<?php echo $this->_var['comment_all']['badReview']; ?>%)</span></dt>
                                    <dd><div style="width: <?php echo $this->_var['comment_all']['badReview']; ?>px;"></div></dd>
                                </dl>
                            </div>
                            <?php if ($this->_var['goods']['impression_list']): ?>
                            <div class="actor-new">
                                <dl>
                                    <dt>买家印象：</dt>
                                    <dd class="p-bfc">
                                        <?php $_from = $this->_var['goods']['impression_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'tag');if (count($_from)):
    foreach ($_from AS $this->_var['tag']):
?>
                                        <p class="conmm-tags"><span><?php echo $this->_var['tag']['txt']; ?></span><em>(<?php echo $this->_var['tag']['num']; ?>)</em></p>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </dd>
                                </dl>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="comments-list">
                            <?php 
$k = array (
  'name' => 'goods_comment_title',
  'goods_id' => $this->_var['goods']['goods_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
                            <div class="ratelist-content">
                                <?php echo $this->fetch('library/comments.lbi'); ?> 	
                            </div>
                        </div>
                    </div>
                    <div class="ecsc-goods-item fment tiezi">
                        <?php 
$k = array (
  'name' => 'goods_discuss_title',
  'goods_id' => $this->_var['goods']['goods_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
                        <div class="table" id="discuss_list_ECS_COMMENT">
                            <div class="thead">
                                <div class="th td1">主题</div>
                                <div class="th td2">回复/浏览</div>
                                <div class="th td3">作者</div>
                                <div class="th td4">时间</div>
                            </div>
                            <div class="tbody">
                                <?php $_from = $this->_var['discuss_list']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
                                <div class="tr">
                                    <div class="td td1">
                                        <?php if ($this->_var['list']['dis_type'] == 1): ?>
                                        <i class="tie_icon tao"></i>
                                        <?php elseif ($this->_var['list']['dis_type'] == 2): ?>
                                            <i class="tie_icon wen"></i>
                                        <?php elseif ($this->_var['list']['dis_type'] == 3): ?>
                                            <i class="tie_icon quan"></i>
                                        <?php elseif ($this->_var['list']['dis_type'] == 4): ?>
                                            <i class="tie_icon shai"></i>
                                        <?php endif; ?>
                                        <div class="s1">
                                        	<?php if ($this->_var['list']['dis_type'] == 4): ?>
                                            <a href="single_sun.php?act=discuss_show&did=<?php echo $this->_var['list']['dis_id']; ?>&dis_type=4" target="_blank"><?php echo $this->_var['list']['dis_title']; ?></a>
                                            <img src="themes/ecmoban_dsc/images/image_s.jpg">
                                            <?php else: ?>
                                            <a href="single_sun.php?act=discuss_show&did=<?php echo $this->_var['list']['dis_id']; ?>" target="_blank"><?php echo $this->_var['list']['dis_title']; ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="td td2"><?php echo $this->_var['list']['reply_num']; ?>/<?php echo $this->_var['list']['dis_browse_num']; ?></div>
                                    <div class="td td3"><a href="user.php"><?php echo $this->_var['list']['user_name']; ?></a></div>
                                    <div class="td td4"><?php echo $this->_var['list']['add_time']; ?></div>
                                </div>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </div>
                            <div class="tfoot">
                                <div class="fr"><span>共<?php echo $this->_var['all_count']; ?>个话题</span><a href="category_discuss.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" target="_blank">点击浏览全部 ></a></div>
                            </div>
                        </div>
                    </div>
            	</div>
            </div>  
        </div>
    </div>
    <div class="ecsc-goods-love ecsc-goods-love-new">
        <div class="ec-title"><h3>猜你喜欢</h3><a href="javascript:void(0)" class="ec-huan" data-changepage="1"><i class="icon"></i>换一组</a></div>
        <div class="clearance-content" id="goodsLove_content">
            <?php echo $this->fetch('library/guess_goods_love.lbi'); ?>
        </div>
    </div>
</div>
<?php echo $this->fetch('library/goods_fittings_cnt.lbi'); ?> 

<?php 
$k = array (
  'name' => 'user_menu_position',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>

	 
<?php echo $this->fetch('library/duibi.lbi'); ?>


<?php echo $this->fetch('library/page_footer.lbi'); ?>

<div class="thickdiv"></div>
<div id="notify_box" class="thickbox" >
	<div class="thickwrap" style="width: 520px;">
		<div style="width:500" id="notify_title" class="thicktitle"><span>降价通知</span></div>
		<div style="width: 500px; height: 260px; padding-left: 10px; padding-right: 10px;" id="notify_con" class="thickcon">
			<div id="sale-notice">
			    <p>一旦商品在30日内降价，您将收到邮件、短信和手机推送消息！通过手机客户端消息提醒，购买更便捷~</p>
			    <div class="form-item">
			        <label style="padding-right: 5px;" for="price-notice">价格低于￥</label><input type="text" id="price-notice" name="price-notice">
			        <em>时，通知我</em>
			        <div style="color: Red; display: inline;" id="priceError"></div>
			        <div style=" display: none;" id="price"></div>
			    </div>
			    <div class="form-item">
			        <label style="padding-right: 5px;" for="cellphone">手机号码：</label><input type="text" class="input-email" id="cellphone" name="cellphone">
			        <div style="color: Red; display: inline;" id="phoneError"></div>
			    </div>
			    <div class="form-item">
			        <label style="padding-right: 5px;" for="email">邮箱地址：</label><input type="text" class="input-email" id="user_email_notice" name="email">
			        <em style="color: red;">*</em>
			        <div style="color: Red; display: inline;" id="MailError"></div>
			    </div>
			    <div class="form-submit">
			        &nbsp;<input type="button" value="提交" id="submit_notice" name="submit">
			        <span style="color: Red; display: inline;" id="notProduce"></span>
			    </div>
			</div>
		</div>
		<a id="" class="thickclose" href="#">×2</a>
	</div>
</div>

<div class="ecsc-cart-popup" id="addtocartdialog">
    <div class="loading-mask"></div>
    <div class="loading">
        <div class="center_pop_txt">
            <div class="title"><h3>提示</h3><a href="javascript:loadingClose();" title="关闭" class="loading-x">X</a></div>
        </div>
        <div class="btns">
            <a href="flow.php" class="ecsc-btn-mini ecsc-btn-orange">去付款</a>
            <a href="javascript:loadingClose();" class="ecsc-btn-mini">继续购物</a>
        </div>
    </div>
</div>

<?php echo $this->fetch('library/common_html.lbi'); ?>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.purebox.js"></script>
<script type="text/javascript">

/*商品语言JS start*/
<?php $_from = $this->_var['lang']['goods_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
/*商品语言JS end*/

var goods_id = <?php echo $this->_var['goods_id']; ?>;
var goodsattr_style = <?php echo empty($this->_var['cfg']['goodsattr_style']) ? '1' : $this->_var['cfg']['goodsattr_style']; ?>;
var gmt_end_time = <?php echo empty($this->_var['promote_end_time']) ? '0' : $this->_var['promote_end_time']; ?>;
var goodsId = <?php echo $this->_var['goods_id']; ?>;
var now_time = <?php echo $this->_var['now_time']; ?>;


$(function(){
	changePrice('onload');
    change_group();
	Compare.init();
	fixpng();
	goods_delivery_area();
	try {onload_leftTime();}
	catch (e) {}
	
	<?php if ($this->_var['cfg']['add_shop_price'] == 0): ?>
		$(".attr_list").find("li.item :input").prop("checked", false);
		$(".attr_list").find("li.item").removeClass("selected");
	<?php endif; ?>
});

/*by kong 门店 弹窗  20160721 start*/
function seller_store(goods_id){
    var spec_arr = '';
    var formBuy      = document.forms['ECS_FORMBUY'];
    if (formBuy)
    {
        spec_arr = getSelectedAttributes(formBuy);
    }
    var back_url = "goods.php?id="+goods_id;
    var user_id = <?php echo $this->_var['user_id']; ?>
    /*未登录 跳转登陆，登陆选择门店*/
    if(user_id > 0){
        Ajax.call("get_ajax_content.php?act=get_store_list&goods_id="+goods_id+ '&spec_arr=' + spec_arr,'back_act='+ back_url, function(data){
            pb({
                    id:"storeDialogBody",
                    title:"查看门店",
                    width:670,
                    height:320,
                    content:data.content, 	//调取内容
                    drag:false,
                    foot:false
            });
        }, 'POST','JSON');
    }else{
        $.notLogin("get_ajax_content.php?act=get_login_dialog",back_url);
        return false;
    }
}
/*by kong 门店 弹窗  20160721 end*/

/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function changePrice(onload)
{
  
  var qty = document.forms['ECS_FORMBUY'].elements['number'].value;

  //ecmoban模板堂 --zhuo start 限购
  if(onload != 'onload'){
	  
	  var goods_attr_id = getSelectedAttributes(document.forms['ECS_FORMBUY']);
	  
	  <?php if ($this->_var['xiangou'] == 1): ?>
		 <?php if ($this->_var['goods']['is_xiangou'] == 1 && $this->_var['goods']['xiangou_num'] > 0): ?>
			var xuangou_num = <?php echo $this->_var['goods']['xiangou_num']; ?>;
			var xiangou = <?php echo $this->_var['xiangou']; ?>;
			if(qty > xuangou_num && xuangou_num > 0 && xiangou == 1){
				
				var add_cart_divId = 'flow_add_cart';
				var content = '<div id="flow_add_cart">' + 
									'<div class="tip-box icon-box">' +
										'<span class="warn-icon m-icon"></span>' + 
										'<div class="item-fore">' +
											'<h3 class="rem ftx-04">超过限购数量</h3>' +
										'</div>' +
									'</div>' +
								'</div>';
				pb({
					id:add_cart_divId,
					title:'标题',
					width:455,
					height:58,
					content:content, 	//调取内容
					drag:false,
					foot:false
				});
					
				qty = 1;
			}
		 <?php endif; ?>
	   <?php endif; ?>
	   
	   var goods_attr = '';
	   <?php if ($this->_var['cfg']['add_shop_price'] == 0): ?>
	   		var attr_id = goods_attr = getSelectedAttributesGroup(document.forms['ECS_FORMBUY']);
			goods_attr = '&goods_attr=' + attr_id;
	   <?php endif; ?>
	   
	   Ajax.call('goods.php', 'act=price&id=' + goodsId + '&attr=' + goods_attr_id + goods_attr + '&number=' + qty + '&warehouse_id=' + <?php echo empty($this->_var['region_id']) ? '0' : $this->_var['region_id']; ?> + '&area_id=' + <?php echo empty($this->_var['area_id']) ? '0' : $this->_var['area_id']; ?>, changePriceResponse, 'GET', 'JSON');
  }else{
	  
	  <?php if ($this->_var['cfg']['add_shop_price'] == 1): ?>	
	  	var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);
		attr = '&attr=' + attr;
	  <?php else: ?>
	  	var attr = '';
	  <?php endif; ?>
  
	  Ajax.call('goods.php', 'act=price&id=' + goodsId + attr + '&number=' + qty + '&warehouse_id=' + <?php echo empty($this->_var['region_id']) ? '0' : $this->_var['region_id']; ?> + '&area_id=' + <?php echo empty($this->_var['area_id']) ? '0' : $this->_var['area_id']; ?> + '&onload=' + onload, changePriceResponse, 'GET', 'JSON');
  }
   //ecmoban模板堂 --zhuo end 限购
}

/**
 * 接收返回的信息
 */
function changePriceResponse(res)
{

  if (res.err_msg.length > 0)
  {
    get_user_prompt_message(res.err_msg);
  }
  else
  {
	
	if (document.getElementById('bar_code')){
		document.getElementById('bar_code').innerHTML = res.bar_code;
	}
	  
	document.forms['ECS_FORMBUY'].elements['number'].value = res.qty;
	
	$('#cost-price').html(res.marketPrice_amount);

	//ecmoban模板堂 --zhuo satrt
	if (document.getElementById('goods_attr_num')){
	  document.getElementById('goods_attr_num').innerHTML = res.attr_number;  
	  document.getElementById('perNumber').value = res.attr_number;  
	}
	
	if(document.getElementById('ECS_SHOPPRICE')){ 
		if(document.getElementById('ECS_MARKETPRICE')){
			document.getElementById('ECS_MARKETPRICE').innerHTML = res.result_market;
		}
		
		if(res.onload == 'onload'){
			document.getElementById('ECS_SHOPPRICE').innerHTML = res.result;
		}else{
			<?php if ($this->_var['cfg']['add_shop_price'] == 1): ?>	
				document.getElementById('ECS_SHOPPRICE').innerHTML = res.result;
			<?php else: ?>
				if(res.show_goods == 1){
					document.getElementById('ECS_SHOPPRICE').innerHTML = res.spec_price;
				}else{
					document.getElementById('ECS_SHOPPRICE').innerHTML = res.result;
				}
			<?php endif; ?>
		}
		
		var combo_shop = document.getElementsByName('combo_shopPrice[]'); 
		var combo_mark = document.getElementsByName('combo_markPrice[]');  
		
		for(var i=0; i<combo_shop.length; i++){
			combo_shop[i].innerHTML = res.shop_price;
		}
		
		for(var i=0; i<combo_mark.length; i++){
			combo_mark[i].innerHTML = res.market_price;
		}
	}
	
	if(res.err_no == 2){
		$('#isHas_warehouse_num').html("该地区暂不支持配送");
	}else{
		
		if (document.getElementById('isHas_warehouse_num')){
		  var isHas;
		  if(res.attr_number > 0){
			  $('a.btn-append').attr('href','javascript:addToCartShowDiv(<?php echo $this->_var['goods']['goods_id']; ?>)').removeClass('btn_disabled');
			  $('a.buynow').attr('href','<?php if ($this->_var['user_id'] <= 0 && $this->_var['one_step_buy']): ?>#none<?php else: ?>javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)<?php endif; ?>').removeClass('btn_disabled');
			  $('a').remove('#quehuo');
			  isHas = '<strong>有货</strong>，下单后立即发货';
		  }else{
			  isHas = '<strong>无货</strong>，此商品暂时售完';
			  
				$('a.btn-append').attr('href','#none').addClass('btn_disabled');
				$('a.buynow').attr('href','#none').addClass('btn_disabled');
				
				<?php if ($this->_var['goods']['review_status'] >= 3): ?>
					if(!document.getElementById('quehuo')){
						<?php if ($this->_var['goods']['is_on_sale'] != 0): ?>
						$('div.choose-desc').before('<a id="quehuo" class="ajax_buy" href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>);" class="fl"></a>');
						<?php endif; ?>
					}
				<?php endif; ?>
		  }
		  document.getElementById('isHas_warehouse_num').innerHTML = isHas;
		}
	}
	
	if(res.fittings_interval){
		for(var i=0; i<res.fittings_interval.length; i++){
			$("#m_goods_" + res.fittings_interval[i].groupId).html(res.fittings_interval[i].fittings_minMax);
			$("#m_goods_save_" + res.fittings_interval[i].groupId).html(res.fittings_interval[i].save_minMaxPrice);
			$("#m_goods_reference_" + res.fittings_interval[i].groupId).html(res.fittings_interval[i].market_minMax);
		}
	}
	
	if(res.onload == 'onload'){
		document.getElementById('ECS_SHOPPRICE').innerHTML = res.result;
	}
	
	<?php if ($this->_var['cfg']['add_shop_price'] == 1): ?>	
		$('.ECS_fittings_interval').html(res.shop_price);
	<?php else: ?>
		if(res.show_goods == 1){
			$('.ECS_fittings_interval').html(res.spec_price);
		}else{
			$('.ECS_fittings_interval').html(res.shop_price);
		}
	<?php endif; ?>
	//ecmoban模板堂 --zhuo end


      //@author-bylu 更新白条分期购每期的价格 start
	  if(res.stages){
		  var i=0;
		  $.each(res.stages,function(k,v){
			  if(k!=1) {
				  $('#chooseStages dd strong').eq(i).html('￥' + v + '×' + k + '期');
				  $('#chooseStages dd strong').eq(i).next('span').html('含手续费：费率<?php echo $this->_var['goods']['stages_rate']; ?>%，￥' + v + '×' + k + '期');
			  }
			  i++;
		  });
	  }
      //author-bylu end
  }
}

function goods_delivery_area(){
	var area = new Object();

	area.province_id = <?php echo empty($this->_var['area']['province_id']) ? '0' : $this->_var['area']['province_id']; ?>;
	area.city_id = <?php echo empty($this->_var['area']['city_id']) ? '0' : $this->_var['area']['city_id']; ?>;
	area.district_id = <?php echo empty($this->_var['area']['district_id']) ? '0' : $this->_var['area']['district_id']; ?>;
	area.goods_id = <?php echo empty($this->_var['area']['goods_id']) ? '0' : $this->_var['area']['goods_id']; ?>;
	area.user_id = <?php echo empty($this->_var['area']['user_id']) ? '0' : $this->_var['area']['user_id']; ?>;
	area.region_id = <?php echo empty($this->_var['area']['region_id']) ? '0' : $this->_var['area']['region_id']; ?>;
	area.area_id = <?php echo empty($this->_var['area']['area_id']) ? '0' : $this->_var['area']['area_id']; ?>;
	area.merchant_id = <?php echo empty($this->_var['area']['merchant_id']) ? '0' : $this->_var['area']['merchant_id']; ?>;
	
	Ajax.call('ajax_dialog.php?act=goods_delivery_area', 'area=' + $.toJSON(area), goods_delivery_areaResponse, 'POST', 'JSON'); 
}

function goods_delivery_areaResponse(result){
	$("#area_address").html(result.content);
	$(".store-warehouse-info").html(result.warehouse_content);
}

//处理添加商品到组合购买购物车
function get_cart_combo_open_list(rev, fitt_goods){
  
  
  var group        	= new Object();
  group.rev   		= rev;
  
  if(fitt_goods){
  	group.fitt_goods  = fitt_goods;
  }

  Ajax.call('flow.php?step=add_cart_combo_list', 'group=' + $.toJSON(group), get_cart_combo_openResponse, 'POST', 'JSON'); //兼容jQuery by mike
}
//处理添加商品到组合购买购物车的反馈信息
function get_cart_combo_openResponse(result)
{
	if(result.list_select != 1){
		$("#fittings_minMax_top").html(result.fittings_minMax);
		$("#list_select").html(result.null_money);
	}else{
		$(".fittings_minMax").html(result.fittings_minMax);
	}
	
	$('.J_ComboDialog').find(".fitts_body").html(result.content_type);
	
	$(".save_minMaxPrice").html(result.save_minMaxPrice);
	$(".market_minMax").html(result.market_minMax);
	$(".collocation_number").html(result.collocation_number);
	
	$(".fittings_minMax").html(result.fittings_minMax);
	$('strong[name="combo_savePrice[]"]').html(result.save_minMaxPrice);
	$('span[name="combo_shopPrice[]"]').html(result.fittings_minMax);
	$('span[name="combo_markPrice[]"]').html(result.market_minMax);
}

$(".fitting-tab").tabso({cntSelect:".fitting-list",tabEvent:"click",tabStyle:"normal",onStyle:"on"});
$(".charts-tab").tabso({cntSelect:".charts-list",tabEvent:"click",tabStyle:"normal",onStyle:"on"});
$(".spec-list").slide({mainCell:".spec-items ul",effect:"left",trigger:"click",pnLoop:false,autoPage:true,scroll:1,vis:5,prevCell:".spec-prev",nextCell:".spec-next"});
$(".fitting-content").slide({mainCell:".fitting-wrap ul",effect:"left",trigger:"click",pnLoop:false,autoPage:true,scroll:1,vis:5,prevCell:".fitting-prev",nextCell:".fitting-next"});
$(".p-photos-wrap").slide({mainCell:".bd ul",effect:"left",autoPlay:false,prevCell:".photo_prev",nextCell:".photo_next"});
$(".ecsc-single-desc").jfloor(43,0);

<?php if ($this->_var['goods']['gmt_end_time']): ?>
	$(".time").each(function(){
		$(this).yomi();
	});
<?php endif; ?>
/**
 * 猜你喜欢-换一组
 */
function change_group(){
	var page = 1;
	$(".ecsc-goods-love .ec-huan").click(function(){
		page++;
		if(page == 4){
			page = 1;
		}
		Ajax.call('goods.php?act=guess_goods', 'page=' + page , guessGoodsResponse, 'GET', 'JSON');
	});
}
function guessGoodsResponse(data){
    $("#goodsLove_content").html(data.result);
}

//数量选择
function quantity(){

	$(".btn-reduce").click(function(){
		var quantity = Number($("#quantity").val());
		var perNumber = Number($("#perNumber").val());
		var perMinNumber = Number($("#perMinNumber").val());
	
		if(quantity>perMinNumber){
			quantity-=1;
			$("#quantity").val(quantity);
            changePrice('onload');//@author bylu 数量减少后获取白条分期新价格;

		}else{
			$("#quantity").val(perMinNumber);
		}
	});
	
	$(".btn-add").click(function(){
		var quantity = Number($("#quantity").val());
		var perNumber = Number($("#perNumber").val());
		var perMinNumber = Number($("#perMinNumber").val());
		var err = 0;
	
		if(quantity < perNumber){
			
			quantity+=1;
			
			//限购
			<?php if ($this->_var['xiangou'] == 1): ?>
				<?php if ($this->_var['goods']['is_xiangou'] == 1 && $this->_var['goods']['xiangou_num'] > 0): ?>
					var xuangou_num = <?php echo $this->_var['goods']['xiangou_num']; ?>;
					var xiangou = <?php echo $this->_var['xiangou']; ?>;
					
					if (<?php echo $this->_var['orderG_number']; ?> >= xuangou_num){
						  err = 1;
						  var message = '已购买<?php echo $this->_var['orderG_number']; ?>件商品达到限购条件,无法再购买';
						  quantity = 1;
					}else if(quantity > xuangou_num && xuangou_num > 0 && xiangou == 1){
						  err = 1;
						  var message = '超过限购数量';
						  quantity = 1;
					}
					
					if(err == 1){
						get_user_prompt_message(message);
					}
				<?php endif; ?>
			<?php endif; ?>
			
			if(quantity == 1){
				err = 0;
			}
			$("#quantity").val(quantity);
            changePrice('onload');//@author bylu 数量增加后获取白条分期新价格;
		}else{
			$("#quantity").val(perNumber);
		}

	})
}
quantity();


function get_url(t){
	if(t.rev != ''){
		location.href = t.rev;
	}else{
		 //var spec_value = $(t).find("input:checked").val();
		 var spec_value = $(t).children('input').attr('value');
		 Ajax.call('goods.php?act=getInfo', 'id='+goods_id+'&attr_id='+spec_value,getImgUrl,'POST', 'JSON');
	}
}

function getImgUrl(result)
{
	if(result.t_img != ''){
		$('#Zoomer').attr({href:"" +result.t_img+ ""});
		//$('#J_prodImg').attr({href:"" +result.t_img+ "", src:"" +result.t_img+ ""});
		$('#J_prodImg').attr({src:"" +result.t_img+ ""});
		$('.MagicBoxShadow').eq(0).find('img').eq(0).attr({src:"" +result.t_img+ ""});
		$('.MagicThumb-expanded').find('img').attr({src:"" +result.t_img+ ""});
	}
} 

//降价通知
$(function(){
	$(".price_notify").click(function(){
		var user_id = $(this).attr("user_id");
		if(user_id == 0){
			location.href="user.php";return false;
		}
		$(".thickdiv").show();
		$("#notify_box").show();
	});

	//关闭弹框
	$(".thickclose").click(function(){
		$(".thickdiv").hide();
		$("#notify_box").hide();
	})

	$("#submit_notice").click(function(){
		var user_id = $(".price_notify").attr("user_id");
		var goods_id = $(".price_notify").attr("goods_id");
		var hopeDiscount = $("#price-notice").val();
		var cellphone = $("#cellphone").val();
		var email = $("#user_email_notice").val();

		var res = checkform(hopeDiscount,cellphone,email);

		if(!res){
			return false;
		}

		jQuery.ajax({
			url: 'goods.php?act=price_notice',
			type: 'post',
			dataType: 'json',
			data: {
                                    'user_id': user_id,
                                    'goods_id': goods_id,
                                    'hopeDiscount': hopeDiscount,
                                    'cellphone':cellphone,
                                    'email': email
			},
                        cache: false,
                        success: function (result) {
                            if (result.status == 0) {
                                    alert(result.msg);
                                    $(".thickdiv").hide();
                                    $("#notify_box").hide();
                            }else{
                                    alert(result.msg);
                            }
			},
			error: function () {
			}
		})

	});

    //未登录团购弹出登录框
    $('.btn-buynow').click(function(){
        var user_id = Number(<?php echo $this->_var['user_id']; ?>);
        var one_step_buy = Number(<?php echo $this->_var['one_step_buy']; ?>);
        if(user_id <= 0 && one_step_buy == 1)
        {
            var back_url = "goods.php?id=" + <?php echo $this->_var['goods']['goods_id']; ?>;
            $.notLogin("get_ajax_content.php?act=get_login_dialog",back_url);
            return false;
        }
    });
	function checkform(hopeDiscount,cellphone,email){
		//期望价
		if (hopeDiscount.length == 0){
			document.getElementById("priceError").style.display = "inline";
			document.getElementById("priceError").innerHTML = "价格不能为空！";
			return false;
		}
		else{
			var myreg = /^[+]?(([1-9]\d*[.]?)|(0.))(\d{0,2})?$/;
			if (!myreg.test(hopeDiscount)) {
				document.getElementById("priceError").style.display = "inline";
				document.getElementById("priceError").innerHTML = "价格输入格式不正确！";
				return false;
			} else {
				document.getElementById("priceError").innerHTML = "";
			}
		}
		//手机号码
		if (cellphone.length > 0 ) {
			var rg = /^(0|86|17951)?(1[0-9])[0-9]{9}$/
			if (!rg.test(cellphone)) {
				document.getElementById("phoneError").style.display = "inline";
				document.getElementById("phoneError").innerHTML = "&nbsp;&nbsp;手机格式不正确！";
				$("#cellphone").attr("class", "input-email input-error");
				return false;
			}
			else {
				document.getElementById("phoneError").innerHTML = "";
			}
		} else {
			document.getElementById("phoneError").innerHTML = "";
		}
		//邮箱
		if (email.length == 0) {
			document.getElementById("MailError").style.display = "inline";
			document.getElementById("MailError").innerHTML = "邮箱不能为空！";
			$("#user_email").attr("class", "input-email input-error");
			return false;
		} else {
			var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if (!reg.test(email)) {
					document.getElementById("MailError").style.display = "inline";
					document.getElementById("MailError").innerHTML = "邮箱格式不正确！";
					$("#user_email").attr("class", "input-email input-error");
					return false;
				}
			else {
				document.getElementById("MailError").innerHTML = "";
			}
		}

		return true;
	}

});

//@author-bylu 白条分期js start
$('#chooseStages .item').click(function(){
    if($(this).attr('flag') == 0){
        $(this).addClass('selected').siblings('.item').removeClass('selected');
        $(this).attr('flag',1);
		$(this).siblings('.item').attr('flag',0);
        $('input[name="stages_qishu"]').val($(this).attr('val'));
    }else{
        $(this).removeClass('selected');
        $(this).attr('flag',0);
        $('input[name="stages_qishu"]').val('');

    }
    //这里是分期按钮(选中分期期数才给点击);
    if($('input[name="stages_qishu"]').val()==''){
        $('.btn-stages').addClass("btn_disabled");
        $('.btn-stages').attr('href','javascript:;');
    }else{
        $('.btn-stages').removeClass("btn_disabled");
        $('.btn-stages').attr('href','javascript:bool=1;addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)');
    }
});
//@author-bylu  end

/*多个促销活动展开*/
$(".summary-promotion").hover(function(){
	$(this).addClass("z-promotions-all-show");
},function(){
	$(this).removeClass("z-promotions-all-show");
});

<?php $_from = $this->_var['lang']['compare_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['key'] != 'button_compare'): ?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php else: ?>
var button_compare = '';
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</script>


    <script type="text/javascript">

        $(function(){
            $('.J-open-tb,.mpbtn_yhq').click(function(){
                setTimeout(function(){
                    var goods_id = <?php echo $_GET['id']; ?>;
                    $.post("get_ajax_content.php?act=get_content&goods_id="+goods_id,{'data_type':'mpbtn_coupons'},function(data){
                        $('.pop_panel').html(data.content)
                    },'json');
                },10)
            })
        })

    </script>
 
</body>
</html>
