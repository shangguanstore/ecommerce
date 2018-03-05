<?php exit;?>00151607334048649be1b4cfd9e9827fff1029a706c2s:17364:"a:2:{s:8:"template";s:17299:"<?php $__Template->display($this->getTpl("page_header-nav")); ?>
<body>
		<div class="con">
			<div class="category">
				<section class="search">
                    <div class="text-all dis-box j-text-all text-all-back">
                        <a class="a-icon-back j-close-search" href="javascript:history.go(-1);"><i class="iconfont icon-back"></i></a>
						<div class="box-flex input-text n-input-text i-search-input">
							<a class="a-search-input j-search-input" href="javascript:void(0)"></a>
                            <i class="iconfont icon-sousuo"></i>
							<input class="j-input-text" type="text" placeholder="商品/店铺搜索" />
							<i class="iconfont icon-guanbi1 is-null j-is-null"></i>
						</div>
                        <?php if($cat_id) { ?>
						<a href="javascript:void(0)" class="s-filter j-s-filter">筛选</a>
                        <?php } ?>
					</div>
				</section>
				<section class="product-sequence dis-box">
                    <?php if($order == 'ASC' && $sort == 'goods_id') { ?>
					<a class="box-flex a-change active" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'goods_id', 'order'=>'DESC', 'keyword'=>$keywords));?>#goods_list">综合<i class="iconfont icon-xiajiantou"></i></a>
                    <?php } elseif ($order == 'DESC' && $sort == 'goods_id') { ?>
                    <a class="box-flex active" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'goods_id', 'order'=>'ASC', 'keyword'=>$keywords));?>#goods_list">综合<i class="iconfont icon-xiajiantou"></i></a>
                    <?php } else { ?>
                    <a class="box-flex" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'goods_id', 'order'=>'DESC', 'keyword'=>$keywords));?>#goods_list">综合<i class="iconfont icon-xiajiantou"></i></a>
                    <?php } ?>
					<a class="box-flex <?php if($sort == 'last_update') { ?>active<?php } ?>" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'last_update', 'order'=>'DESC', 'keyword'=>$keywords));?>#goods_list">新品</a>
					<a class="box-flex <?php if($sort == 'sales_volume') { ?>active<?php } ?>" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'sales_volume', 'order'=>'DESC', 'keyword'=>$keywords));?>#goods_list">销量</a>
                    <?php if($order == 'ASC' && $sort == 'shop_price') { ?>
					<a class="box-flex a-change active" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'shop_price', 'order'=>'DESC', 'keyword'=>$keywords));?>#goods_list">价格<i class="iconfont icon-xiajiantou"></i></a>
                    <?php } elseif ($order == 'DESC' && $sort == 'shop_price') { ?>
                    <a class="box-flex active" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'shop_price', 'order'=>'ASC', 'keyword'=>$keywords));?>#goods_list">价格<i class="iconfont icon-xiajiantou"></i></a>
                    <?php } else { ?>
                    <a class="box-flex" href="<?php echo U('products', array('id'=>$category, 'intro'=>$intro, 'display'=>$display, 'brand'=>$brand_id, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>'shop_price', 'order'=>'DESC', 'keyword'=>$keywords));?>#goods_list">价格<i class="iconfont icon-xiajiantou"></i></a>
                    <?php } ?>
					<a class="a-sequence j-a-sequence"><i class="iconfont icon-viewlist" data="2"></i></a>
				</section>
                <!-- 内容 -->
				<section class="product-list j-product-list  product-list-small" data="2">
                    <script id="j-product" type="text/html">
                    <%if totalPage > 0%>
					<ul>
                        <%each list as goods%>
                        <li>
                            <div class="product-div">
                                <a href="<%goods.url%>"><img class="product-list-img" src="<%goods.goods_thumb%>" /></a>
                                <div class="product-text">
                                    <a href="<%goods.url%>"><h4><%goods.goods_name%></h4></a>
                                    <p class="dis-box p-t-remark"><span class="box-flex">库存:<%goods.goods_number%><%goods.measure_unit%></span><span class="box-flex">销量:<%goods.sales_volume%><%goods.measure_unit%></span></p>
                                    <p>
                                    <span class="p-price t-first ">
                                        <%if goods.promote_price%>
                                        <%#goods.promote_price%>
                                        <%else%>
                                        <%#goods.shop_price%>
                                        <%/if%>
                                        <small><del><%#goods.market_price%></del></small>
                                    </span>
                                    </p>
                                    <a href="javascript:void(0);" class="icon-flow-cart fr j-goods-attr" onclick="addToCart(<%goods.goods_id%>, 0)"><i class="iconfont icon-gouwuche" ></i></a>
                                </div>
                            </div>
                            </section>
                        </li>
                        <%/each%>
                    </ul>
                    <%else%>
                    <div class="no-div-message">
                        <i class="iconfont icon-biaoqingleiben"></i>
                        <p>亲，此处没有内容～！</p>
                    </div>
                    <%/if%>
                    </script>
				</section>
			</div>
            </div>
            <!-- 筛选 -->
			<div class="j-filter-div filter-div ts-5 c-filter-div" id="j-filter-div">
				<div class="mask-filter-div"></div>
                <section class="close-filter-div j-close-filter-div">
                    <div class="close-f-btn">
                        <i class="iconfont icon-fanhui"></i>
                        <span>关闭</span>
                    </div>
                </section>
                <form action="<?php echo U('products', array('id'=>$category, 'display'=>$display, 'sort'=>$sort, 'order'=>$order));?>" method="post" id="filter">
                <section class="con-filter-div ">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
					<div class="radio-switching padding-all j-radio-switching <?php if($isself) { ?>active<?php } ?>" data="<?php echo $isself; ?>">
						<label class="fl">自营产品</label> <span class="fr"><em class="ts-1"></em><hr class="ts-1" /></span>
					</div>
                    <div class="filter-city padding-all j-filter-city">
						<label class="fl">配送城市</label>
                        <span class="fr t-jiantou"><span class="text-all-span j-city-scolor fl">
                            <?php echo $province_row['region_name']; ?> <?php echo $city_row['region_name']; ?> <?php echo $district_row['region_name']; ?>
                        </span><i class="iconfont icon-jiantou tf-180"></i></span>
					</div>
					<div class="padding-all select-one">
						<ul class="dis-box j-get-more">
							<!--<li class="ect-select box-flex">
								<label class="ts-1 active dis-block">货到付款</label>
							</li>-->
							<li class="ect-select box-flex list-select hasgoods">
								<label class="ts-1 dis-block">仅看有货</label>
							</li>
							<li class="ect-select box-flex list-select promotion">
								<label class="ts-1 dis-block">促销</label>
							</li>
						</ul>
					</div>
					<div class="price-range padding-all">
						<label class="price-range-label" data-min="<?php echo $price_range['min']; ?>" data-max="<?php echo $price_range['max']; ?>">价格区间</label>
						<div class="price-slider">
							<div id="slider-range" class="slider"></div>
							<div class="slider-info">
								<span id="slider-range-amount"></span>
							</div>
						</div>
					</div>
					<div class="select-two">
						<a class="select-title padding-all j-menu-select">
                        <label class="fl">品牌</label>
                        <span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><em class="fl"><?php echo $brand_name; ?></em><i class="iconfont icon-jiantou tf-180 ts-2"></i></span>
                    </a>
                        <ul class="j-sub-menu padding-all j-get-limit brand" data-istrue="true">
                            <?php $n=1;if(is_array($brands)) foreach($brands as $brand) { ?>
                            <li class="ect-select <?php if($brand['brand_id'] == 0) { ?>j-checkbox-all<?php } ?>" data-brand="<?php echo $brand['brand_id']; ?>">
                                <label class="ts-1 <?php if($brand['selected']) { ?>active<?php } ?>"><?php echo $brand['brand_name']; ?><i class="fr iconfont icon-gou ts-1"></i></label>
                            </li>
                            <?php $n++;}unset($n); ?>
                        </ul>
                        <?php $n=1;if(is_array($filter_attr_list)) foreach($filter_attr_list as $k=>$v) { ?>
                        <a class="select-title padding-all j-menu-select">
                            <label class="fl"><?php echo $v['filter_attr_name']; ?></label>
                            <span class="fr t-jiantou j-t-jiantou"><em class="fl"><?php echo $v['select_attr_name']; ?></em><i class="iconfont icon-jiantou tf-180 ts-2"></i></span>
                        </a>
                        <ul class="j-sub-menu padding-all j-get-limit filter_attr" data-key="<?php echo $k; ?>">
                            <?php $n=1;if(is_array($v['attr_list'])) foreach($v['attr_list'] as $attr) { ?>
                            <li class="ect-select <?php if($attr['attr_id'] == 0) { ?>j-checkbox-all<?php } ?>" data-attr="<?php echo $attr['attr_id']; ?>">
                                <label class="ts-1 <?php if($attr['selected']) { ?>active<?php } ?>"><?php echo $attr['attr_value']; ?><i class="fr iconfont icon-gou ts-1"></i></label>
                            </li>
                            <?php $n++;}unset($n); ?>
                        </ul>
                        <?php $n++;}unset($n); ?>
					</div>
					<div class="ect-button-more dis-box padding-all">
                        <input type="hidden" name="isself" value="<?php echo $isself; ?>" />
                        <input type="hidden" name="brand" value="<?php echo $brand_id; ?>" />
                        <input type="hidden" name="price_min" value="<?php echo $price_min; ?>" />
                        <input type="hidden" name="price_max" value="<?php echo $price_max; ?>" />
                        <input type="hidden" name="filter_attr" value="<?php echo $filter_attr; ?>"/>
                        <input type="hidden" name="keyword" value="<?php echo $keywords; ?>"/>
                        <input type="hidden" name="hasgoods" value="0"/>
                        <input type="hidden" name="promotion" value="0"/>
                        <input type="hidden" name="region_id" value="<?php echo $region_id; ?>" id="region_id"/>
                        <input type="hidden" name="area_id" value="<?php echo $area_id; ?>" id="area_id"/>
						<a class="box-flex btn-reset j-filter-reset" type="button">清空选项</a>
						<a class="box-flex btn-submit" type="submit" onclick="$('#filter').submit()">确定</a>
					</div>
                    </div>
                </div>
				</section>
                </form>
			</div>
        <!--城市筛选s-->
        <div class="c-filter-div c-city-div filter-city-div cate-filter-city">
            <section class="con-filter-div">
                <aside>
                    <div class="menu-left j-city-left scrollbar-none" id="sidebar">
                        <ul>
                            <!-- 省、直辖市 -->
                            <?php $n=1;if(is_array($province_list)) foreach($province_list as $province) { ?>
                            <li <?php if($province_row['region_id'] == $province['region_id']) { ?>class="active"<?php } ?> onclick="region.selectRegion(0, <?php echo $province['region_id']; ?>, 2)"><?php echo $province['region_name']; ?></li>
                            <?php $n++;}unset($n); ?>
                        </ul>
                    </div>
                </aside>
                <section class="menu-right j-city-right fr">
                    <div class="select-two j-get-city-one">
                        <?php $n=1;if(is_array($city_list)) foreach($city_list as $city) { ?>
                        <?php if($city['district_list']) { ?>
                        <a class="select-title padding-all j-menu-select">
                            <label class="fl"><?php echo $city['region_name']; ?></label>
                            <span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span>
                        </a>
                        <ul class="padding-all j-sub-menu">
                            <?php $n=1;if(is_array($city['district_list'])) foreach($city['district_list'] as $district) { ?>
                            <li class="ect-select">
                                <label onclick="region.selectDis(<?php echo $district['region_id']; ?>, 1)" class="ts-1 <?php if($district_row['region_id'] == $district['region_id']) { ?>active<?php } ?>"><?php echo $district['region_name']; ?><i class="fr iconfont icon-gou ts-1"></i></label>
                            </li>
                            <?php $n++;}unset($n); ?>
                        </ul>
                        <?php } else { ?>
                        <a class="select-title padding-all">
                            <label class="fl" onclick="region.selectDis(<?php echo $city['region_id']; ?>, 0)"><?php echo $city['region_name']; ?></label>
                            <span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span>
                        </a>
                        <?php } ?>
                        <?php $n++;}unset($n); ?>
                    </div>
                </section>
            </section>
        </div>
        <input type="hidden" name="warehouse_id" value="<?php echo $warehouse_id; ?>">
        <input type="hidden" name="area_id" value="<?php echo $area_id; ?>">
        <!--城市筛选e-->
		<script type="text/javascript">
            <?php if($cat_id) { ?>
			var Sliders = function() {
				// 筛选价格区间 js
				$("#slider-range").slider({
					range: true,
					min: <?php echo $price_range['min']; ?>,
					max: <?php echo $price_range['max']; ?>,
					step: 100,
					values: [<?php echo $price_min; ?>, <?php if($price_max) { ?><?php echo $price_max; ?><?php } else { ?><?php echo $price_range['max']; ?><?php } ?>],
					slide: function(event, ui) {
						$("#slider-range-amount").text(ui.values[0] + " ~ " + ui.values[1]);
                        $("input[name=price_min]").val(ui.values[0]);
                        $("input[name=price_max]").val(ui.values[1]);
					}
				});
				$("#slider-range-amount").text($("#slider-range").slider("values", 0) + " ~ " + $("#slider-range").slider("values", 1));
			}();
            <?php } ?>
            //异步数据
            $(function(){
                var url = "<?php echo U('products', array('id'=>$category, 'display'=>$display, 'brand'=>$brand_id, 'intro'=>$intro, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'page'=>$page, 'sort'=>$sort, 'order'=>$order, 'keyword'=>$keywords, 'isself'=>$isself, 'hasgoods'=>$hasgoods, 'promotion'=>$promotion));?>#goods_list";
                //$('#J_list').more({'address': url, 'trigger':'.get_more', 'amount':'<?php echo $size; ?>'});
                //订单列表
                $('.product-list').infinite({url: url, template:'j-product'});
            })
		</script>
		<?php $__Template->display($this->getTpl("float_nav")); ?>
	</body>

</html>";s:12:"compile_time";i:1515986940;}";