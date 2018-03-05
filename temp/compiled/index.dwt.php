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
<link rel="stylesheet" type="text/css" href="<?php echo $this->_var['ecs_css_path']; ?>" />
<link rel="stylesheet" type="text/css" href="themes/ecmoban_dsc/quickLinks.css" />
<link rel="stylesheet" type="application/rss+xml" title="RSS|<?php echo $this->_var['page_title']; ?>" href="<?php echo $this->_var['feed_url']; ?>" />
<link rel="stylesheet" href="themes/ecmoban_dsc/purebox.css" />

<?php echo $this->smarty_insert_scripts(array('files'=>'jquery-1.9.1.min.js,jquery.json.js,transport_jquery.js,cart_common.js,cart_quick_links.js')); ?>
<script type="text/javascript" src="themes/ecmoban_dsc/js/sc_common.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.SuperSlide.2.1.1.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.tabso_yeso.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.yomi.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/rotate3di.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/ecsc-nav.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/scroll_city.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/notLogin.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/asyLoadfloor.js"></script>

<style>.suspension{display:none;}</style>
</head>
<body>
<?php echo $this->fetch('library/page_header_index.lbi'); ?>
<div class="banner">
    <div class="banner-box">
        <div class="bd">
            <?php echo $this->fetch('library/index_ad.lbi'); ?>
        </div>
        <div class="ecsc-warp">
            <div class="hd"><ul></ul></div>
            <div class="focus-trigeminy">
                <div class="bd_lunbo">
                    <ul>
                        <li>
                            
<?php $this->assign('ads_id','92'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                            
<?php $this->assign('ads_id','94'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                            
<?php $this->assign('ads_id','95'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                        </li>
                        <li>
                            
<?php $this->assign('ads_id','96'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                            
<?php $this->assign('ads_id','97'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                            
<?php $this->assign('ads_id','98'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                        </li>
                        <li>
                            
<?php $this->assign('ads_id','99'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                            
<?php $this->assign('ads_id','100'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                            
<?php $this->assign('ads_id','101'); ?><?php $this->assign('ads_num','1'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 
                        </li>
                    </ul>
                </div>
                <a href="javascript:void(0);" class="tri_prev"></a>
                <a href="javascript:void(0);" class="tri_next"></a>
            </div>
            <div class="right-sidebar">
                <?php echo $this->fetch('library/index_banner_group_ad.lbi'); ?>
                <div class="proclamation">
                    <ul class="tabs-nav">
                        <li class="on">商城公告</li>
                        <li>招商入驻</li>
                    </ul>
                    <div class="tabs">
                        <div class="tabs-panel">
                        	
<?php $this->assign('articles',$this->_var['articles_13']); ?><?php $this->assign('articles_cat',$this->_var['articles_cat_13']); ?><?php echo $this->fetch('library/cat_articles.lbi'); ?>

                        </div>
                        <div class="tabs-panel" style=" display:none;">
                        	<div class="merSettled">
                            	<a href="merchants.php" target="_blank" class="store-join-btn" title="申请商家入驻；已提交申请，可查看当前审核状态。"></a>
                                <a href="merchants.php?id=57" target="_blank" class="store-join-help">查看开店协议</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="content">
    <div class="lazy-ecsc-warp">
        <div class="lazy-ecsc-done w1200">
            <div class="done-left">
                <div class="done-tabs">
                    <ul class="done-tabs-nav">
                        <li>热门推荐<i></i></li>
                        <li>疯狂抢购<i></i></li>
                        <li>商城推荐<i></i></li>
                        <li>热评商品<i></i></li>                            
                    </ul>
                </div>
                <div class="done-content">
                    <div class="done-item">
                        
<?php echo $this->fetch('library/recommend_hot.lbi'); ?>

                    </div>
                    <div class="done-item">
                    	
<?php echo $this->fetch('library/recommend_promotion.lbi'); ?>

                    </div>
                    <div class="done-item">
                        
<?php echo $this->fetch('library/recommend_best.lbi'); ?>

                    </div>
                    <div class="done-item">
                        
<?php echo $this->fetch('library/recommend_new.lbi'); ?>

                    </div>
                </div>
            </div>
            <div class="done-right">
                <?php echo $this->fetch('library/index_group_ad.lbi'); ?>
            </div>
        </div>
 
        <div id="goods_cat_level">
        
<?php $this->assign('cat_goods',$this->_var['cat_goods_8']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_8']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php $this->assign('cat_goods',$this->_var['cat_goods_6']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_6']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php $this->assign('cat_goods',$this->_var['cat_goods_12']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_12']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php $this->assign('cat_goods',$this->_var['cat_goods_3']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_3']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php echo $this->fetch('library/cat_goods.lbi'); ?>
<?php echo $this->fetch('library/cat_goods.lbi'); ?>

		</div>
        <div class="w1200 floor" id="floor_loading" style="padding:220px 0px;">
        	<div style="width:120px; height:100px; margin:auto;"><img src="themes/ecmoban_dsc/images/loading.gif"></div>
        </div>
        <?php echo $this->fetch('library/guess_you_like.lbi'); ?>
    </div>
</div>

<?php echo $this->fetch('library/page_footer.lbi'); ?>
<div id="elevator" class="elevator">
	<ul>
    	<li class="handler_floor1 curr"><i class="icon"></i></li>
        <li class="handler_floor2"><i class="icon"></i></li>
        <li class="handler_floor3"><i class="icon"></i></li>
        <li class="handler_floor4"><i class="icon"></i></li>
    </ul>
</div>
<div class="attached-search-container">
	<div class="attached_bg"></div>
	<div class="w1200">
        <div class="fp-iconfont"><img src="themes/ecmoban_dsc/images/logo_2015_2.png" height="58" /></div>
        <div class="mall-search">
            <form id="searchForm" name="searchForm" method="get" action="search.php" onSubmit="return checkSearchForm()" class="ecsc-search-form">
                <div class="ecsc-search-tabs">
                    <i class="sc-icon-right"></i>
                    <ul class="shop_search" id="shop_search2">
                    <?php if ($this->_var['search_type'] == 1): ?>
                        <li rev="1"><span>店铺</span></li>
                        <li rev="0" class="curr"><span>商品</span></li>
                    <?php else: ?>
                        <li rev="0"><span>商品</span></li>
                        <li rev="1" class="curr"><span>店铺</span></li>
                    <?php endif; ?>   
                    </ul>
                </div>
                <input autocomplete="off" name="keywords" type="text" id="keyword2" value="<?php if ($this->_var['search_keywords']): ?><?php echo $this->_var['search_keywords']; ?><?php else: ?><?php 
$k = array (
  'name' => 'rand_keyword',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?><?php endif; ?>" class="ecsc-search-input"/>
                <input type="hidden" name="store_search_cmt" value="<?php echo empty($this->_var['search_type']) ? '0' : $this->_var['search_type']; ?>">
                <button type="submit" class="ecsc-search-button"><i></i></button>
            </form>
        </div>
    </div>
</div>
<?php 
$k = array (
  'name' => 'user_menu_position',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>


<script type="text/javascript" src="themes/ecmoban_dsc/js/jfade.js"></script>
<script type="text/javascript" src="themes/ecmoban_dsc/js/jquery.purebox.js"></script>
<script type="text/javascript">
//首页幻灯片
$(".banner-box").slide({titCell:".hd ul",mainCell:".bd ul",effect:"fold",interTime:3500,delayTime:500,autoPlay:true,autoPage:true,trigger:"click",endFun:function(i,c,s){
	$(window).resize(function(){
        var width = $(window).width();
		s.find(".bd li").css("width",width);
    });
}});

//立即抢购滚动
$(".right-sidebar").slide({mainCell:".panic-buy-slide ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:1,prevCell:".buy-prev",nextCell:".buy-next"});

//商城公告和招商入驻切换
$(".proclamation").slide({titCell:".tabs-nav li",mainCell:".tabs"});

//首页新品热卖3条广告切换
$(".focus-trigeminy").slide({mainCell:".bd_lunbo ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,prevCell:".tri_prev",nextCell:".tri_next"});
$(".focus-trigeminy").hover(function(){
	$(this).children(".tri_prev,.tri_next").animate({'opacity':0.5});
},function(){
	$(this).children(".tri_prev,.tri_next").animate({'opacity':0});
});

$('.bd_lunbo a').jfade({
	start_opacity: "1",
	high_opacity: "1",
	low_opacity: "0.5",
	timing: "500"
});

//首页热门推荐,抢购,商城推荐,热评商品tab切换
$(".done-left").slide({titCell:".done-tabs-nav li",mainCell:".done-content"});
$("#dome1").slide({mainCell:".done-warp ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:5,prevCell:".done-prev",nextCell:".done-next"});
$("#dome2").slide({mainCell:".done-warp ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:5,prevCell:".done-prev",nextCell:".done-next"});
$("#dome3").slide({mainCell:".done-warp ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:5,prevCell:".done-prev",nextCell:".done-next"});
$("#dome4").slide({mainCell:".done-warp ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:5,prevCell:".done-prev",nextCell:".done-next"});

//首页团购左右滚动
$(".done-right").slide({mainCell:".shop-group ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:1});

//品牌街左右滚动
$(".brand-warp").slide({mainCell:".brand-warp-list",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:1});
$(".brand-adv").slide({titCell:".hd ul",mainCell:".bd ul",effect:"leftLoop",autoPlay:true,autoPage:true,scroll:1,vis:1});

//限时抢购倒计时
$(".time").each(function(){
	$(this).yomi();
});

//首页，顶级分类页广告栏按钮自适应宽度
$.liWidth(".floor");
$.liWidth(".brand-adv");

//首页悬浮栏
$(window).scroll(function(){
	var scrollTop = $(document).scrollTop();
	var content = $(".nav").offset().top;
	if(scrollTop>content){
		$(".attached-search-container").addClass("show");
	}else{
		$(".attached-search-container").removeClass("show");
	}
});

//楼层调用js
function load_js_content(key)
{		
	var Floor = $("#floor_" + key);
	
	//首页楼层内banner广告
	Floor.find(".floor-left-banner").slide({titCell:".hd ul",mainCell:".bd ul",effect:"left",autoPlay:true,autoPage:true,delayTime:300,scroll:1,vis:1});
	
	//首页楼层产品tab切换
	var tabsContent = Floor.find(".floor-tabs-content");
	Floor.find(".tab").tabso({cntSelect:tabsContent,tabEvent:"hover",tabStyle:"normal",onStyle:"on"});
	
	//首页楼层内品牌切换
	Floor.find(".floor-brand").slide({mainCell:".bd-brand-list",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,scroll:1,vis:1});
	
	//首页标准模块中间多图广告鼠标触及凸显
	Floor.find('.floor-new > ul > li').jfade({
		start_opacity: "0.9",
		high_opacity: "1",
		low_opacity: ".25",
		timing: "500"
	});
	
	Floor.find('.floor-tabs-content .ecsc-main > ul > li').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: "0.5",
		timing: "500"
	});
	
	Floor.find(".floor-title .tab li").hover(function(){
		var width =$(this).width();
		$(this).find("i").css({"width":width+2});
	});
	Floor.find(".floor-title").each(function(){
		var li1 = $(this).find("li");
		var width = li1.width();
		li1.find("i").css({"width":width+2});
	});
	
	//首页楼层分类
	Floor.find(".banner-nav").find("li").each(function(){
		$(this).mouseenter(function(){
			var index = $(this).index();
			$(this).parent().next().find("dd").eq(index).css({"background":"none"});
			$(this).children(".twoClass").show();
		});
		$(this).mouseleave(function(){
			var index = $(this).index();
			$(this).parent().next().find("dd").eq(index).css({"background":"#fff"});
			$(this).children(".twoClass").hide();
		})
	});
	
	//楼层新品
	var num = Floor.find(".floor-new").length;
	for(var i=0;i<num;i++){
		Floor.find(".floor-new").eq(i).find("li").eq(4).addClass("last");
	}
	Floor.find(".floor-new li").hover(function(){
		$(this).addClass("current");
	},function(){
		$(this).removeClass("current");
	});
	
	//图片延迟加载
	Floor.find("img.lazy").lazyload({
		effect : "fadeIn",
		threshold:5000
	});
	Floor.prev().find("img.lazy").lazyload({
		effect : "fadeIn",
		threshold:1000
	});
	Floor.find("a.lazy").lazyload({
		effect : "fadeIn",
		threshold:1000
	});
}

//楼层异步加载封装函数调用
$.homefloorLoad();

//首页楼层悬浮框
function indexfloor(){
	var elevator,winHeight,winWidth,elevatorHeight,elevatorWidth,top,left,disTop,floors,li,index;
	
	elevator =$("#elevator");
	floors = $(".floor");
	li = elevator.find("li");
	winHeight = $(window).height();
	winWidth = $(window).width();
	elevatorHeight = elevator.height();
	elevatorWidth = elevator.width();
	top = (winHeight-elevatorHeight)/2;
	left = (winWidth-1200)/2-elevatorWidth-40;
	
	disTop = $(".floor-container").offset().top - top;
	elevator.css({"position":"fixed","top":top,"left":left});
	
	$(window).resize(function(){
		winWidth = $(this).width();
		left = (winWidth-1200)/2-elevatorWidth-40;
		elevator.css({"left":left});
	});
	li.click(function(){
		index = $(this).index();
		top = parseInt(floors.eq(index).offset().top);
		$("body,html").stop().animate({scrollTop:top});
	});
	
	$(window).scroll(function(){
		var scrollTop = $(document).scrollTop();
		var guessloveTop = $(".guess-love").offset().top;
		if(scrollTop>disTop){
			elevator.stop().animate({"opacity":1});
			elevator.css({'*display':'block','display':'block '});
		}else{
			elevator.stop().animate({"opacity":0});
			elevator.css({'*display':'none','display':'none '});
		}
		
		if(scrollTop>guessloveTop-600){
			elevator.stop().animate({"opacity":0});
			elevator.css({'*display':'none','display':'none '});
		}
		
		for(var i=0;i<floors.length;i++){
			top =  parseInt(floors.eq(i).offset().top);
			if(scrollTop >= top-500){
				li.eq(i).addClass("curr").siblings().removeClass("curr");
			}
		}
	});
}
</script>

</body>
</html>
