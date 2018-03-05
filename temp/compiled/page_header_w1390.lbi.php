<link rel="stylesheet" type="text/css" href="themes/ecmoban_dsc/suggest.css" />
<?php echo $this->smarty_insert_scripts(array('files'=>'suggest.js,jquery.lazyload.js')); ?>
<?php 
$k = array (
  'name' => 'get_adv',
  'logo_name' => $this->_var['top_banner'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>

<div id="site-nav">
    <div class="w1390 dorpdown">
        <?php 
$k = array (
  'name' => 'header_region',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
        <div class="ecsc-login" id="ECS_MEMBERZONE">
        	<?php 
$k = array (
  'name' => 'member_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
        </div>
        <ul class="quick-menu fr">
        	<?php if ($this->_var['navigator_list']['top']): ?>
            <?php $_from = $this->_var['navigator_list']['top']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'nav');$this->_foreach['nav_top_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_top_list']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['nav']):
        $this->_foreach['nav_top_list']['iteration']++;
?>
            <?php if (($this->_foreach['nav_top_list']['iteration'] - 1) < 4): ?>
            <li>
            	<div class="dt"><a href="<?php echo $this->_var['nav']['url']; ?>" <?php if ($this->_var['nav']['opennew']): ?>target="_blank"<?php endif; ?>><?php echo $this->_var['nav']['name']; ?></a></div>
            </li>
            <li class="spacer"></li>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
            <?php if ($this->_var['navigator_list']['top']): ?>
            <li class="li_dorpdown">
            	<div class="dt"><a href="javascript:;" >网站导航</a><i class="ci-right"><s>◇</s></i></div>
                <div class="dd dorpdown-layer">
                	<div class="dd-spacer"></div>
                    <?php $_from = $this->_var['navigator_list']['top']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'nav');$this->_foreach['nav_top_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_top_list']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['nav']):
        $this->_foreach['nav_top_list']['iteration']++;
?>
            			<?php if (($this->_foreach['nav_top_list']['iteration'] - 1) >= 4): ?>
                    		<div class="item"><a href="<?php echo $this->_var['nav']['url']; ?>" <?php if ($this->_var['nav']['opennew']): ?>target="_blank"<?php endif; ?>><?php echo $this->_var['nav']['name']; ?></a></div>
                    	<?php endif; ?>
            		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </li>
            <?php endif; ?>
        </ul>
        <div class="shopcart-2015" id="ECS_CARTINFO">
        	<?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
        </div>
    </div>
</div>
<div class="header w1390">
    <div class="ecsc-logo"><a href="<?php echo $this->_var['url_index']; ?>" class="logo"><img src="themes/ecmoban_dsc/images/logo.gif" /></a></div>
    <div class="ecsc-join"><a href="<?php echo $this->_var['url_merchants']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/ecsc-join.gif" /></a></div>
    <div class="ecsc-search">
        <form id="searchForm" name="searchForm" method="get" action="search.php" onSubmit="return checkSearchForm()" class="ecsc-search-form">
            <div class="ecsc-search-tabs">
                <i class="sc-icon-right"></i>
                <ul class="shop_search" id="shop_search">
                <?php if ($this->_var['search_type'] == 1): ?>
                    <li rev="1"><span>店铺</span></li>
                    <li rev="0" class="curr"><span>商品</span></li>
                <?php else: ?>
                    <li rev="0"><span>商品</span></li>
                    <li rev="1" class="curr"><span>店铺</span></li>
                <?php endif; ?>   
                </ul>
            </div>
            <input autocomplete="off" onKeyUp="lookup(this.value);" name="keywords" type="text" id="keyword" value="<?php if ($this->_var['search_keywords']): ?><?php echo $this->_var['search_keywords']; ?><?php else: ?><?php 
$k = array (
  'name' => 'rand_keyword',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?><?php endif; ?>" class="ecsc-search-input"/>
            <input type="hidden" name="store_search_cmt" value="<?php echo empty($this->_var['search_type']) ? '0' : $this->_var['search_type']; ?>">
            <button type="submit" class="ecsc-search-button"><i></i></button>
        </form>
        <div class="keyword">
        <?php if ($this->_var['searchkeywords']): ?>
            <ul>
                <?php $_from = $this->_var['searchkeywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?> <li><a href="search.php?keywords=<?php echo urlencode($this->_var['val']); ?>" target="_blank"><?php echo $this->_var['val']; ?></a></li> <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
            <?php endif; ?>
        </div>
        
            <div class="suggestions_box" id="suggestions" style="display:none;">
                <div class="suggestions_list" id="auto_suggestions_list">
                &nbsp;
                </div>
            </div>
        
    </div>
    <div class="site-commitment">
        <div class="site-commitment-front"><a href="#" target="_blank"></a></div>
        <div class="site-commitment-back"><a href="#" target="_blank"></a></div>
    </div>
</div>
<div class="nav ecsc-zoom">
    <div class="w1390">
        <div class="categorys site-mast">
            <div class="dt"><a href="<?php echo $this->_var['url_categoryall']; ?>" target="_blank">全部商品分类</a><i class="sc-icon-right"></i></div>
            <div class="dd" style="display:none">
                <div class="cata-nav" id="cata-nav">
					<?php $_from = $this->_var['categories_pro']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');$this->_foreach['categories_pro'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['categories_pro']['total'] > 0):
    foreach ($_from AS $this->_var['cat']):
        $this->_foreach['categories_pro']['iteration']++;
?>
                    <div class="item fore<?php echo $this->_foreach['categories_pro']['iteration']; ?>">
                        <div class="item-left">
                            <div class="cata-nav-name" data-id="<?php echo $this->_var['cat']['id']; ?>" eveval="0">
                                <h3>
                                    <?php if ($this->_var['cat']['category_link'] == 1): ?>
                                    <?php echo $this->_var['cat']['name']; ?>
                                    <?php else: ?>
                                    <a href="<?php echo $this->_var['cat']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['cat']['name']); ?>"><?php echo htmlspecialchars($this->_var['cat']['name']); ?></a>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            <b>></b>
                        </div>
                        <div class="cata-nav-layer">
                            <div class="cata-nav-left">
                                <div class="item-channels">
                                    <div class="channels" id="channels_<?php echo $this->_var['cat']['id']; ?>">
                                    </div>
                                </div>
                                <div class="subitems" id="subitems_<?php echo $this->_var['cat']['id']; ?>">
                                </div>
                            </div> 
                            <div class="cata-nav-rigth" id="item_brands<?php echo $this->_var['cat']['id']; ?>">   
                                
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </div>
        </div>
        <div class="navitems" id="nav">
            <ul>
                <li><a href="index.php" <?php if ($this->_var['navigator_list']['config']['index'] == 1): ?>class="selected"<?php endif; ?>>首页</a></li>
                <?php $_from = $this->_var['navigator_list']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['nav_middle_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_middle_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['nav_middle_list']['iteration']++;
?>
                <li><a href="<?php echo $this->_var['nav']['url']; ?>" <?php if ($this->_var['nav']['active'] == 1): ?>class="selected"<?php endif; ?> <?php if ($this->_var['nav']['opennew']): ?>target="_blank"<?php endif; ?>><?php echo $this->_var['nav']['name']; ?></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </div>
    </div>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'suggest.js')); ?>

<script type="text/javascript">
/*首页分类树*/
$(function(){
	$('#cata-nav .item').unbind('mouseenter').bind('mouseenter',function(){
		var T = $(this);
		var cat_id = T.children('.item-left').children('.cata-nav-name').data('id');
		var eveval = T.children('.item-left').children('.cata-nav-name').attr('eveval');
		
		if(eveval != 1){
			T.children('.item-left').children('.cata-nav-name').attr('eveval', '1');
			/*加载中by wu*/
			$('#subitems_' + cat_id).html('<img src="themes/ecmoban_dsc/images/loadGoods.gif" width="200" height="200" class="lazy" style="margin:100px 281px">');
			$.ajax({
			   type: "GET",
			   url: "ajax_dialog.php",
			   data: "act=getCategoryCallback&cat_id=" + cat_id,
			   dataType:'json',
			   success: function(data){
				   	var strLength = Number(data.topic_content.length)
				   	if(strLength == 2 || strLength == 0){
						$('#channels_' + data.cat_id).parent('.item-channels').hide();
					}
				 	$('#channels_' + data.cat_id).html(data.topic_content);
				 	$('#subitems_' + data.cat_id).html(data.cat_content);
				 	$('#item_brands' + data.cat_id).html(data.brands_ad_content);
			   }
			});
		}
		
		T.addClass("selected");
		T.children('.cata-nav-layer').show();
	});
	
	$('#cata-nav .item').unbind('mouseleave').bind('mouseleave',function(){
		$(this).removeClass("selected");
        $(this).children(".cata-nav-layer").hide();
	});
});
</script>



