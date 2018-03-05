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
<?php if ($this->_var['cat_style']): ?>
<link href="<?php echo $this->_var['cat_style']; ?>" rel="stylesheet" type="text/css" />
<?php endif; ?>
<link href="themes/ecmoban_dsc/select.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="themes/ecmoban_dsc/quickLinks.css" />
<link rel="alternate" type="application/rss+xml" title="RSS|<?php echo $this->_var['page_title']; ?>" href="<?php echo $this->_var['feed_url']; ?>" />
<link rel="stylesheet" href="themes/ecmoban_dsc/purebox.css">
<link rel="stylesheet" href="themes/ecmoban_dsc/js/perfect-scrollbar/perfect-scrollbar.min.css">

<?php echo $this->smarty_insert_scripts(array('files'=>'jquery-1.9.1.min.js,jquery.json.js,transport_jquery.js,cart_common.js,cart_quick_links.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,global.js,utils.js,compare.js,search_category_menu.js,jd_choose.js,pinyin.js,warehouse.js,parabola.js')); ?>
<script type="text/javascript" src="themes/ecmoban_dsc/js/sc_common.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.SuperSlide.2.1.1.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.tabso_yeso.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.yomi.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/rotate3di.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/scroll_city.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/notLogin.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/asyLoadfloor.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<style>.suspension{display:none;}</style>
</head>

<body>
<?php echo $this->fetch('library/page_header_w1390.lbi'); ?>

<?php echo $this->fetch('library/category_recommend_hot.lbi'); ?>

<?php 
$k = array (
  'name' => 'get_adv_child',
  'ad_arr' => $this->_var['category_top_ad'],
  'id' => $this->_var['cate_info']['cat_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>

<div class="ecsc-breadcrumb w1390">
    <?php echo $this->fetch('library/ur_here.lbi'); ?>
    <div class="pre-num"><?php echo $this->_var['lang']['total']; ?><?php echo $this->_var['pager']['record_count']; ?><?php echo $this->_var['lang']['jian_goods']; ?></div>
</div>
<div id="filter">
    <div class="filter-section-wrapper mt-component-2 w1390">
		<?php echo $this->fetch('library/category_screening.lbi'); ?>
    </div>

</div>
<div id="content" class="w1390">
		
<?php echo $this->fetch('library/goods_list.lbi'); ?>
 
<?php if (! $this->_var['category_load_type']): ?>

<?php echo $this->fetch('library/pages.lbi'); ?>

<?php endif; ?>
</div>
<div class="guess-love goods-list-love w1390">
    <div class="ec-title"><h3><?php echo $this->_var['lang']['guess_love']; ?></h3></div>
    <div class="goods-list">
        <ul class="gl-warp">
            <?php $_from = $this->_var['guess_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            <li class="gl-item">
                <div class="gl-i-wrap">
                    <div class="p-img">
                        <a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="themes/ecmoban_dsc/images/loadGoods.gif" width="200" height="200" class="lazy" data-original="<?php echo $this->_var['goods']['goods_thumb']; ?>"></a>
                    </div>
                    <div class="p-name"><a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank" title="<?php echo $this->_var['goods']['goods_name']; ?>"><?php echo $this->_var['goods']['short_name']; ?></a></div>
                    <div class="p-price">
                    	<?php if ($this->_var['goods']['promote_price'] != ''): ?>
                            <?php echo $this->_var['goods']['promote_price']; ?>
                        <?php else: ?>
                            <?php echo $this->_var['goods']['shop_price']; ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-num"><?php echo $this->_var['lang']['sale_amount']; ?>：<?php echo $this->_var['goods']['sales_volume']; ?></div>
                    <div class="p-commit"><span class="p-nice"><i class="icon"></i><span class="nice-stars"><?php echo $this->_var['goods']['comment_percent']; ?>%</span></span></div>
                </div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
</div>


 

	 
<?php echo $this->fetch('library/duibi.lbi'); ?>


<?php 
$k = array (
  'name' => 'user_menu_position',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> 

<?php echo $this->fetch('library/page_footer.lbi'); ?>
<input name="script_name" value="<?php echo $this->_var['script_name']; ?>" type="hidden" />
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.purebox.js"></script>
<script type="text/javascript">
$(function(){
	//价格筛选提交
	$('.ui-btn-submit').click(function(){
		var min_price = Number($(".price-min").val());
		var max_price = Number($(".price-max").val());
		
		if(min_price == '' && max_price == ''){
			alert('<?php echo $this->_var['lang']['screen_price']; ?>');
			return false;
		}else if(min_price == ''){
			alert('<?php echo $this->_var['lang']['screen_price_left']; ?>');
			return false;
		}else if(max_price == ''){
			alert('<?php echo $this->_var['lang']['screen_price_right']; ?>');
			return false;
		}else if(min_price > max_price || min_price == max_price){
			alert('<?php echo $this->_var['lang']['screen_price_dy']; ?>');
			return false;
		}
		$("form[name='listform']").submit();
	});
	
	$('.ui-btn-clear').click(function(){
		$("input[name='price_min']").val('');
		$("input[name='price_max']").val('');
	});
	
	$(".zimu_list").hover(function(){
		$(".zimu_list").perfectScrollbar();
	});
	
	$(".wrap_brand").hover(function(){
		$(".extend .wrap_brand").perfectScrollbar();
	});
});

function selectStoreTab(a){
	var li =$(".tab").find("li").eq(a);
	if(!li.hasClass("curr")){
		li.addClass("curr").siblings().removeClass("curr");
	}
	$("#stock_list").find(".mc").eq(a).removeClass("hide").siblings(".mc").addClass("hide");
}

$(".hotsale").slide({mainCell:".bd ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:4});
$(".share-content").slide({mainCell:".bd ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:7});

//异步加载更多商品 by wu start	
<?php if ($this->_var['category_load_type']): ?>
	var query_string = '<?php echo $this->_var['query_string']; ?>';
	$.goodsLoad('.goods_network_list .goods-list','.gl-item','.goods-spread',query_string,0);
<?php endif; ?>
//异步加载更多商品 by wu end

//商品列表页 商品小图轮播 图片数量大于6个
sildeImg(0);

$(function(){
	var obj = $("#filter-sortbar .styles").find(".item");
	var imtes = $(".category_left");
	obj.click(function(){
		var index = $(this).index();
		$(this).addClass("current").siblings().removeClass("current");
		imtes.find(".car_goods_list").eq(index).show().siblings(".car_goods_list").hide();
		//需要开启异步加载
		<?php if ($this->_var['category_load_type']): ?>
			if(index == 1){
				$.goodsLoad('.goods_switch_list .goods-list','.item','.goods-spread',query_string,1);
			}
		<?php endif; ?>
	});
});
</script>
</body>
</html>
