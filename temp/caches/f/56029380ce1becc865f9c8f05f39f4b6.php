<?php exit;?>0015173771260ba2551c0407512537e7b65d4e3e02a1s:16296:"a:2:{s:8:"template";s:16231:"<?php $__Template->display($this->getTpl("page_header-nav")); ?>
<body>
<div class="con mb-7">

    <div class="index-banner swiper-container box position-rel banner-first">
        <div class="swiper-wrapper">
        <?php echo insert_ads(array('id'=>256, 'num'=>6));?>
        </div>
        <!-- 分页器 -->
        <div class="swiper-pagination br-half text-c banner-first-pagination"></div>
        <!--search-->
        <section class="search box banner-search">
            <div class="text-all position-rel dis-box j-text-all">
                <?php if(C('shop.use_lbs')) { ?>
                <a href="<?php echo U('location/index/index');?>"><div class="dis-box search-logo"><div class="location-left-cont onelist-hidden"><?php echo $current_city['region_name']; ?></div><div class="box-flex"><i class="iconfont icon-moreunfold"></i></div></div></a>
                <?php } ?>
                <div class="box-flex input-text n-input-text i-search-input">
                    <a class="a-search-input j-search-input" href=""></a>
                    <!--#open_search-->
                    <i class="iconfont icon-sousuo"></i>
                    <div class="j-input-text nav-soso">商品/店铺搜索</div>
                    <i class="iconfont icon-guanbi is-null j-is-null"></i>
                </div>
                <?php if(C('shop.use_lbs')) { ?>
                <div class="search-left index-xiaoxi position-rel">
                    <a href="<?php echo U('user/index/messagelist');?>"><i class="iconfont icon-xiaoxi1"></i><em class="position-abo active"></em></a>
                </div>
                <?php } ?>
            </div>
        </section>
        <!--linear-->
        <div class="linear"></div>
    </div>
    <!--menu-->
    <nav class="bg-white ptb-1 index-nav">
        <ul class="box ul-5 text-c bg-white">

            <?php $n=1;if(is_array($nav)) foreach($nav as $key=>$list) { ?>
            <li>
                <a href="<?php echo $list['url']; ?>">
                    <img src="<?php echo __TPL__;?>img/<?php echo $list['pic']; ?>">
                    <p class="m-top04"><?php echo $list['name']; ?></p>
                </a>
			</li>
            <?php $n++;}unset($n); ?>

        </ul>
    </nav>
    <!--index-new-->
				<section class="dis-box index-new mb-1 position-rel">
					<div class="index-new-left">
						<div class="new-icon">
							<img src="<?php echo __TPL__;?>img/new-icon.png">
						</div>
					</div>
					<div class="box-flex">
						<!-- Swiper -->
						<div class="swiper-container index-new-list">
							<div class="swiper-wrapper">
                                <?php $n=1;if(is_array($article)) foreach($article as $vo) { ?>
								<div class="swiper-slide swiper-no-swiping">
									<a class="product-div-link" href="<?php echo $vo['url']; ?>"></a>
									<div class="index-new-cont">
										<h4 class="col-3 f-05 onelist-hidden"><?php echo $vo['title']; ?></h4>
										<p class="col-9 f-02"><?php echo $vo['add_time']; ?></p>
									</div>
								</div>
                                <?php $n++;}unset($n); ?>
							</div>
						</div>
					</div>
					<div class="index-new-right">
						<span class="t-jiantou t-remark fr"><a href="<?php echo U('article/index/index');?>">更多<i class="iconfont icon-more f-2"></i></a></span>
					</div>
				</section>
    <script type="text/javascript">
        var gmt_end_time = <?php if($end_time) { ?><?php echo $end_time; ?><?php } else { ?>0<?php } ?>;
        var day = '<em class="color-whie hour">';
        var hour = '</em> : <em class="color-whie mini">';
        var minute = '</em> : <em class="color-whie sec">';
        var second = '</em>';
        var end = '';
        onload = function(){
            try {onload_leftTime();}
            catch (e) {}
        }
    </script>
    <!--discount-->
    <div class="box mb-1 title inx-ms">
        <div class="box padding-all b-color-f">
            <h3 class="color-red position-rel" >
                <span class="fl inx-ms-time">限时<i class="iconfont">&#xe60b;</i>秒杀</span>
                <span class="color-dark" id="leftTime"></span>
                <span class="t-jiantou t-remark fr"><a href="<?php echo U('category/index/products',array('intro'=>'promotion'));?>">更多<i class="iconfont icon-more"></i></a></span>
            </h3>
        </div>

        <div class="b-color-f">
            <div class="swiper-container recom-container">
                <div class="swiper-wrapper">
                    <ul class="swiper-slide recom-list">
                        <!-- <?php $n=1; if(is_array($promotion_goods)) foreach($promotion_goods as $key => $promotion) { ?> -->
                        <li>
                            <a href="<?php echo $promotion['url']; ?>">
                                <img src="<?php echo $promotion['goods_thumb']; ?>" />
                                <p class="index-price text-c"><?php echo $promotion['current_price']; ?><br><del><?php echo $promotion['shop_price']; ?></del></p>
                            </a>
                        </li>
                        <!-- <?php if(($key+1)%3 == 0 && $key !== (count($promotion_goods)-1)) { ?> -->
                    </ul><ul class="swiper-slide recom-list">
                    <!-- <?php } ?> -->
                    <!-- <?php $n++;}unset($n); ?> -->
                </ul>
                </div>
            </div>
        </div>
    </div>

    <!--banner2-->
    <div class="index-banner swiper-container box mb-1 banner-second">
        <div class="swiper-wrapper">
            <?php echo insert_ads(array('id'=>257, 'num'=>3));?>
        </div>
    </div>

    <!--recom-->
    <div class="box mb-1 title inx-ms">
        <div class="box padding-all b-color-f">
            <h3 class="recom-title">
                精品推荐
                <span>精品商品尽收眼底赶快行动吧</span>
                <span class="t-jiantou t-remark fr"><a href="<?php echo U('category/index/products',array('intro'=>'best'));?>">更多<i class="iconfont icon-more"></i></a></span>
            </h3>
        </div>

        <section class="product-list-medium">
            <ul>
              <!-- <?php $n=1; if(is_array($best_goods)) foreach($best_goods as $key => $best) { ?> -->
              <li>
                <div class="product-div">
                  <a class="product-div-link" href="<?php echo $best['url']; ?>"></a>
                  <img class="product-list-img" src="<?php echo $best['goods_thumb']; ?>" />
                  <div class="product-text">
                      <h4><?php echo $best['goods_name']; ?></h4>
                      <p><span class="p-price t-first "><?php echo $best['current_price']; ?></span></p>
                  </div>
                </div>
              </li>
              <!-- <?php $n++;}unset($n); ?> -->
            </ul>
        </section>
    </div>

    <!--hot-->
    <div class="box mb-1 b-color-f" style="display:none;">
        <div class="box padding-all  wallet-bt">
            <h3 class="recom-title">
                热门大牌
                <span>最全最时尚的大牌汇集</span>
                <span class="t-jiantou t-remark fr">更多<i class="iconfont icon-more"></i></span>
            </h3>
        </div>
        <div class="hot-container-div">
            <i class="iconfont icon-back"></i>
            <div class="swiper-container hot-container">
                <ul class="swiper-wrapper">
                    <?php $n=1;if(is_array($brand_list)) foreach($brand_list as $vo) { ?>
                    <li class="swiper-slide ">
                        <a href="javascript:; ">
                            <!--此处报重定向过多错误，目前无法找到原因，先注释掉 2018/01/12-->
                            <!--<img src="<?php echo $vo['brand_logo']; ?>" alt="">-->
                            <img src="" alt="">
                        </a>
                    </li>
                    <?php $n++;}unset($n); ?>
                </ul>
                <!-- 导航按钮 -->
            </div>	<i class="iconfont icon-more"></i>
        </div>
    </div>

    <!--banner3-->
    <div class="index-banner swiper-container box mb-1 banner-third">
        <div class="swiper-wrapper">
        <?php echo insert_ads(array('id'=>258, 'num'=>3));?>
        </div>
    </div>
    <!--brand-->

    <?php if($store) { ?>
    <div class="pb-2 idx-padding b-color-f">
        <div class="box padding-all">
            <h3 class="recom-title">
                店铺街
                <span>精品店铺一条街</span>
                <span class="t-jiantou t-remark fr"><a href="<?php echo U('store/index/index');?>">更多<i class="iconfont icon-more"></i></a></span>
            </h3>
        </div>
        <div class="swiper-container brand-container b-color-f">
            <div class="swiper-wrapper">
                <ul class="brand-list swiper-slide">
                    <!--<?php $n=1; if(is_array($store)) foreach($store as $key => $val) { ?>-->
                    <li>
                        <a href="<?php echo $val['url']; ?>">
                            <div class="brand-img">
                                <img src="<?php echo $val['street_thumb']; ?>" />
                                <div class="brand-logo">
                                    <img src="<?php echo $val['brand_thumb']; ?>"/>
                                </div>
                            </div>
                            <ul class="ul-3">
                                <!--<?php $n=1; if(is_array($val['goods_list'])) foreach($val['goods_list'] as $k => $v) { ?>-->
                                <li>
                                    <img src="<?php echo $v['goods_thumb']; ?>" />
                                </li>
                                <!--<?php $n++;}unset($n); ?>-->
                            </ul>
                        </a>
                    </li>
                    <!-- <?php if(($key+1) % 3 == 0 && $key !== (count($store)-1)) { ?> -->
                </ul><ul class="brand-list swiper-slide">
                <!-- <?php } ?> -->
                <!--<?php $n++;}unset($n); ?>-->
            </ul>
            </div>
            <!-- 分页器 -->
            <div class="swiper-pagination brand-pagination"></div>
        </div>
    </div>
    <?php } ?>
    <!--like-->
    <p class="index-title padding-all text-c">猜你喜欢</p>
    <section class="product-list j-product-list product-list-medium" data="1">
		<script id="j-product" type="text/html">
		<ul>
			<%each list as goods%>
			<li>
				<div class="product-div">
					<a class="product-div-link" href="<%goods.url%>"></a>
					<img class="product-list-img" src="<%goods.goods_thumb%>" />
					<div class="product-text">
              <h4><%goods.goods_name%></h4>
              <p><span class="p-price t-first "><%#goods.current_price%><small></small></span></p>
              <a href="javascript:void(0)" class="icon-flow-cart fr j-goods-attr"><i class="iconfont icon-gouwuche"></i></a>
          </div>
				</div>
			</li>
			<%/each%>
		</ul>
		</script>
    </section>

    <div class="n-footer-box" style="display: none;">
        <ul class="n-footer-minbox">
            <li>
                <a href="http://m.ecmoban.com/index.php?m=touch&amp;c=index&amp;a=download">
                    <div class="n-footer-img-box"><img src="<?php echo __TPL__;?>/img/footer-a.png"></div>
                    <p class="footer-tit1">客户端</p>
                </a>
            </li>
            <li>
                <a href="javascript:;">
                    <div class="n-footer-img-box"><img src="<?php echo __TPL__;?>/img/footer-c.png"></div>
                    <p class="footer-tit">触屏版</p>
                </a>
            </li>
            <li>
                <a href="<?php echo __HOST__;?>">
                    <div class="n-footer-img-box"><img src="<?php echo __TPL__;?>/img/footer-b.png"></div>
                    <p class="footer-tit1">电脑版</p>
                </a>
            </li>
        </ul>
    </div>

    <footer class="padding-all text-center t-remark" style="display: none;">
        <?php echo $copyright; ?>
        &copy; <?php echo date('Y');?> <?php echo C('shop.shop_name');?> 版权所有，并保留所有权利。
    </footer>
	<div class="footer-cont"><span><img src='<?php echo __TPL__;?>img/loading1.gif'>正在加载……</span></div>
	<div class="filter-top filter-top-index" id="scrollUp">
		<i class="iconfont icon-jiantou"></i>
	</div>

	<footer class="footer-nav dis-box">
		<a href="<?php echo U('site/index/index');?>" class="box-flex nav-list active">
			<i class="nav-box i-home"></i><span>首页</span>
		</a>
		<a href="<?php echo U('category/index/index');?>" class="box-flex nav-list">
			<i class="nav-box i-cate"></i><span>分类</span>
		</a>
		<a href="javascript:;" class="box-flex nav-list j-search-input">
			<i class="nav-box i-shop"></i><span>搜索</span>
		</a>
		<a href="<?php echo U('cart/index/index');?>" class="box-flex position-rel nav-list">
			<i class="nav-box i-flow"></i><span>购物车222</span>
		</a>
		<?php if($filter) { ?>
		<a href="<?php echo U('drp/user/index');?>" class="box-flex nav-list">
			<i class="nav-box i-user"></i><span><?php echo $custom; ?>中心</span>
		</a>
		<?php } elseif ($community) { ?>
		<a href="<?php echo U('community/index/index');?>" class="box-flex nav-list">
			<i class="nav-box i-user"></i><span>社区</span>
		</a>
		<?php } else { ?>
		<a href="<?php echo U('user/index/index');?>" class="box-flex nav-list">
			<i class="nav-box i-user"></i><span>我</span>
		</a>
		<?php } ?>
	</footer>
	<!--悬浮菜单e-->
</div>

<script type="text/javascript ">
    $(function($) {
    				var swiper = new Swiper('.index-new-list', {
					pagination: '.swiper-pagination',
					paginationClickable: true,
					direction: 'vertical',
					loop: true,
					grabCursor: true,
					autoplayDisableOnInteraction: false,
					autoplay: 4000,
					speed: 700,
					freeMode: true,
					noSwiping : true,
				});
        var mySwiper = new Swiper('.banner-first', {
            autoplay: 4000,
            loop: true,
            pagination: '.banner-first-pagination'
        });
        var mySwiper = new Swiper('.banner-second', {
            autoplay: 5000,
            pagination: '.banner-second-pagination'
        });
        var mySwiper = new Swiper('.banner-third', {
            autoplay: 5000,
            pagination: '.banner-third-pagination'
        });
        var mySwiper = new Swiper('.recom-container', {
            pagination: '.recom-pagination',
            slidesPerView: 1,
            paginationClickable: true
        });
        var mySwiper = new Swiper('.brand-container', {
            pagination: '.brand-pagination',
            slidesPerView: 1,
            paginationClickable: true
        });
        var mySwiper = new Swiper('.hot-container', {
            slidesPerView: 4,
            paginationClickable: true,
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev'
        });
    });
	var url = "<?php echo U('async_list');?>";
	$('.product-list').infinite({url: url, template:'j-product'});
			$(document).ready(function() {
            $(window).scroll(function() {
                if ($(document).scrollTop() >= 0<$(document).height() - $(window).height()) {
                       $(".footer-cont").html("<span><img src='<?php echo __TPL__;?>img/loading1.gif'>正在加载……</span>")
                }
                if ($(document).scrollTop() >= $(document).height() - $(window).height()) {

			        $(".footer-cont").html("<span>没有更多了</span>")
			     
                }
            });
        });
</script>
</body>
</html>
";s:12:"compile_time";i:1517290726;}";