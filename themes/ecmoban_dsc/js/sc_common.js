$(function(){
	//top_banner关闭
	$(".top-banner .close").click(function(){
		$(this).parents(".top-banner").hide();
	});
	
	//城市选择
	$("#city-choice,.f-store,.li_dorpdown").hover(function(){
		$(this).addClass("hover");
		var width = $(this).find(".sc-choie").outerWidth();
		$(this).find(".dd-spacer").css("width",width-2);
	},function(){
		$(this).removeClass("hover");
	});
	
	$(".li_dorpdown").hover(function(){
		$(this).addClass("hover");
		var width = $(this).find(".dt").outerWidth();
		$(this).find(".dd-spacer").css({"width":width-2,"right":0});
	},function(){
		$(this).removeClass("hover");
	});
	
	$("#site-nav").jScroll();
	
	//首页购物车展开
	$(".shopcart-2015").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});
	
	//全部分类
	$(".quanbu").hover(function(){
		$(this).addClass("hover");
		$(this).children("#categorys-mini-main").show();
	},function(){
		$(this).removeClass("hover");
		$(this).children("#categorys-mini-main").hide();
	});
	
	$(".navitems ul").mouseleave(function(){
		$(this).next().children().animate({left:15},400);
	});
	
	//导航栏子分类展开
	$("#cata-nav .item").mouseenter(function(){
		$(this).addClass("selected");
		$(this).children(".cata-nav-layer").show();
	});
	$("#cata-nav .item").mouseleave(function(){
		$(this).removeClass("selected");
		$(this).children(".cata-nav-layer").hide();
	});
	
	//首页立即抢购隐藏显示
	$(".panic-buy-slide").hover(function(){
		$(this).children(".buy-prev,.buy-next").animate({"opacity":0.4},500);
	},function(){
		$(this).children(".buy-prev,.buy-next").animate({"opacity":0},500);
	});

	//团购分类区域筛选
	$(".filter-strip-all").mouseenter(function(){
		$(".geo-more-placeholder").remove();
		$(this).parents(".content-cell").addClass("site-fs-cell-geowrap");
		$(this).parents(".content-cell").after("<div class='geo-more-placeholder'></div>");
	});
	$(".gd").mouseleave(function(){
		$(this).parents(".content-cell").removeClass("site-fs-cell-geowrap");
		$(".geo-more-placeholder").remove();
	});
	
	$(".group-floor .mc li").mouseenter(function(){
		$(this).addClass("current");
	});
	$(".group-floor .mc li").mouseleave(function(){
		$(this).removeClass("current");
	});
	
	$(".ziji").mouseenter(function(){
		$(this).addClass("hover");
		var width =$(this).width();
		$(this).children(".dorpdown-layer").children(".dd-spacer").css("width",width-2);
	});
	$(".ziji").mouseleave(function(){
		$(this).removeClass("hover");
		$(this).children(".dorpdown-layer").children(".dd-spacer").css("width",0);
	});
	
	$(".reply").click(function(){
		if($(this).parent(".comment-operate").next().hasClass("hide")){
			$(this).parent(".comment-operate").next().removeClass("hide");
		}else{
			$(this).parent(".comment-operate").next().addClass("hide");
		}
	});
	
	$(".biz-info").hover(function(){
		$(this).addClass("biz-info-open").siblings().removeClass("biz-info-open");
	});
	
	$(".choose .attr-radio .item").click(function(){
		$(this).addClass("selected").siblings().removeClass("selected");
		$(this).find("input[type='radio']").prop("checked",true);
		changePrice();
	});
	
	$(".choose .attr-check .item").click(function(){
		var len = $(this).parent().find(".selected").length;
		if($(this).hasClass("selected")){
			if(len<=1)return;
			$(this).removeClass("selected");
			$(this).find("input[type='checkbox']").prop("checked",false);
		}else{
			$(this).addClass("selected");
			$(this).find("input[type='checkbox']").prop("checked",true);			
		}
		changePrice();
	});
	
	$("#menu dt i.icon,#menu dt span").click(function(){
		var dl = $(this).parents("dl");
		if(dl.hasClass("selected")){
			dl.removeClass("selected");
		}else{
			dl.addClass("selected");
		}
	});
	
	$(".disComment").click(function(){
		$(this).parent().next().show();
	});
	
	var outTimer;
	$(".site-mast").hover(function(){
		var $this = $(this);
		clearTimeout(outTimer);
		outTimer = setTimeout(function(){
			$this.find(".dt").addClass("up");
			$this.find(".dd").show();
		},100);
	},function(){
		var $this = $(this);
		clearTimeout(outTimer);
		outTimer = setTimeout(function(){
			$this.find(".dt").removeClass("up");
			$this.find(".dd").hide();
		},100);
	});

	//网友评论话题类型单选切换
	$("div.value-item").click(function(){
		$(this).addClass("selected").siblings().removeClass("selected");
	});
	
	//购物提交订单
	$(".sku-props-selector .item").click(function(){
		$(this).addClass("selected").siblings().removeClass("selected");
	});
	
	//购物车促销优惠效果
	$(".sales-promotion").click(function(){
		$(this).next().slideDown();
	});
	$(".promotion-tit").click(function(){
		$(this).parent().slideUp();
	});
	
	//确认提交订单页面
	$("*[data-dialog='dialog_checkout']").click(function(){
		var ok_title,cl_title;
		var url = $(this).data('url'); //弹框内容信息
		var title = $(this).data('title'); //弹框标题
		var width = $(this).data('width'); //弹框宽度
		var height = $(this).data('height'); //弹框高度
		var divId = $(this).data('divid'); //点击类型
		var address_id = $(this).data('id'); //收货地址ID

		ok_title = "确定";
		cl_title = "取消";
		
		if(divId == 'del_address'){
			$('#' + divId + ' .ftx-04').css({'padding': '11px 0px 0px 10px'});
			$('#' + divId + ' .tip-box').css({
				'width': '330px',
				'height': '50px',
				'padding': '0px 0px 10px 0px'
			});
			$('#' + divId + ' .item-fore').css({
				'margin': '0px 0px 0px 47px'
			});
			
			$('#' + divId + ' .pb-bd').css({
				'padding-left': '65px'
			});
		}
		
		if(divId == 'new_address' || divId == 'edit_address'){ //添加收货地址信息
			Ajax.call(url, 'address_id=' + address_id, function(data){
				
				pb({
					id:divId,
					title:title,
					width:width,
					height:height,
					ok_title:ok_title, 	//按钮名称
					cl_title:cl_title, 	//按钮名称
					content:data.content, 	//调取内容
					drag:false,
					foot:false
				});
				
			}, 'POST','JSON');             
		}else if(divId == 'del_address'){ //删除收货地址信息
			var content = $('#del_address').html();
			pb({
				id:divId,
				title:title,
				width:width,
				height:height,
				ok_title:ok_title, 	//按钮名称
				cl_title:cl_title, 	//按钮名称
				content:content, 	//调取内容
				drag:false,
				foot:true,
				onOk:function(){
					Ajax.call('flow.php?step=delete_Consignee', 'address_id=' + address_id, function(data){
						
						if(data.error == 2){
							$('.dialog_checkout').hide();
							$('#consignee_new').css({
								'overflow' : 'inherit'
							});
							$('#consignee_new').html(data.content);
						}else{
							$('#consignee_list').html(data.content);
						}
						
						$('#goods_inventory').html(data.goods_list);//送货清单
                		$('#ECS_ORDERTOTAL').html(data.order_total);//费用汇总
					}, 'POST','JSON');
				}
			});
		}
	});
	
	//发票信息
	$(document).on("click","*[data-dialog='editInvoice_dialog']",function(){
		var ok_title,cl_title;
		var url = $(this).data('url'); //删除连接地址
		var title = $(this).data('title');
		var width = 675; 
		var height = 278;
		var divId = $(this).data('divid');
		
		ok_title = "保存发票信息";
		cl_title = "取消";

		Ajax.call(url,'',invoiceResponse, 'POST', 'JSON');
                
		function invoiceResponse(data){
			if(data.error == 0){
				pb({
					id:divId,
					title:title,
					width:width,
					height:height,
					ok_title:ok_title, 	//按钮名称
					cl_title:cl_title, 	//按钮名称
					content:data.content, 	//调取内容
					drag:false,
					foot:true,
					onOk:function(){
						var invoice_val = $("#edit_invoice .selected").find("input[type='radio']").val();
						var inv_content = $("#edit_invoice .item-selected").find("input[type='radio']").val();
						/*获取配送方式 by kong */
						var arr =[];
						$(".shopping-list").each(function(k,v){
							var arr2 = [];
							var ru_id = $(this).find("input[name='ru_id[]']").val();
							var shipping = $(this).find("input[name='shipping[]']").val(); 
							arr2.push(ru_id);
							arr2.push(shipping);
							arr[k] = arr2;
						});
						if(typeof invoice_val == 'undefined' || invoice_val == ""){
							alert("请选择或填写发票抬头部分！");return false;
						}
										  
						Ajax.call('ajax_dialog.php?act=gotoInvoice','inv_content='+encodeURIComponent(inv_content)+'&invoice_id='+invoice_val + '&shipping_id=' + $.toJSON(arr),gotoInvoiceResponse, 'POST', 'JSON');
						
						function gotoInvoiceResponse(result){
							if(result.error != ""){
								alert(result.error);return false;
							}else{
								$("#inv_content .inv_payee").html(result.inv_payee);
								$("#inv_content .inv_content").html(result.inv_content);
								$("#inv_content").find("input[name=inv_payee]").val(result.inv_payee);
								$("#inv_content").find("input[name=inv_content]").val(result.inv_content);
								$("#ECS_ORDERTOTAL").html(result.content);
							}
						}
					}
				});
			}
			
			$("#" + divId + " .cboxContent .pb-bd .tip-box").css({
				"position" : "relative",
				"top" : "-30px"
			});
		}
	});
	
	//商品详情页店铺展开收起
	$(".arrow-show-more").click(function(){
		$(".seller-pop-box,.seller-address").stop(true,false).slideToggle();
	});
	//推荐搭配
	$(".fitting-wrap li").click(function(){
		var goods_id = $(this).attr('id');
		if($(this).hasClass("checked")){
			$("input[name='goods_list_" + goods_id + "']").click();
			$(this).removeClass("checked");
		}else{
			$("input[name='goods_list_" + goods_id + "']").click();
			$(this).addClass("checked");
		}
	});
	
	//商品晒单图片展开
	$(".view-show").click(function(){
		$(this).parent().addClass("hide");
		$(this).parent().next().removeClass("hide");
		
		function commentsImg(){
			var wrap = $(".p-photos-wrap");
			var li = wrap.find(".bd li");
			var index = wrap.find(".on").index();
			var height = li.eq(index).find("img").height();
			
			li.css("height",height);
			$(".photo_next,.photo_prev").click(function(){
				index = $(this).parents(".p-photos-wrap").find(".on").index();
				height = li.eq(index).find("img").height();
				li.css({'height':height});
			});
		}
		commentsImg();
	});
	$(".view-hide").click(function(){
		$(this).parent().addClass("hide");
		$(this).parent().prev().removeClass("hide");
	});
	
	/*
	 * 购物结算页面
	 */
	//收货人地址选择
	$("#consignee_new .consignee-cont li").click(function(){
		var address_id = $(this).data('addressid');
		$(this).addClass("item-selected").siblings().removeClass("item-selected");
		chose_Consignee(address_id);
	});
	
	function chose_Consignee(address_id){
		var consignee = document.getElementById('radio_' + address_id);
                var store_id = document.getElementById('store_id').value;
                (store_id > 0) ? store_id : 0;
		if(consignee){
			consignee.checked = true;
		}

		Ajax.call('flow.php?step=edit_consignee_checked', 'address_id=' + address_id+ '&store_id=' +store_id, edit_consignee_checkedResponse, 'POST','JSON');
	}

	function edit_consignee_checkedResponse(result){
		if(result.error > 0){
			if(result.error == 1){
				var back_url = "flow.php";
				$.notLogin("get_ajax_content.php?act=get_login_dialog",back_url);
				return false;
			}else{
				alert(result.msg);
				return false;
			}
		}else{
			$('#consignee_list').html(result.content);
			$('#goods_inventory').html(result.goods_list);//送货清单
			$('#ECS_ORDERTOTAL').html(result.order_total);//费用汇总
		}
	}
	
	//验证支付密码
	if(document.getElementById('pwd_error')){
		var payment_code = $(".checkout .payment-list .ul-payment-list li.item-selected").data('type');
		
		if(payment_code == 'onlinepay'){
			$("#qt_balance").hide();
			$("#qt_onlinepay").show();
	
			$("form[name='doneTheForm'] :input[name='pay_pwd_error']").val(1);
		}else{
			
			var sueplus = $("#ECS_SURPLUS").val();
			var integral = $("#ECS_INTEGRAL").val();
			
			if(sueplus > 0 || integral > 0){
				$("#qt_balance").hide();
				$("#qt_onlinepay").show();
	
				$("form[name='doneTheForm'] :input[name='pay_pwd_error']").val(1);
			}else{
				$("#qt_balance").show();
				$("#qt_onlinepay").hide();
				
				$("form[name='doneTheForm'] :input[name='pay_pwd_error']").val(0);
			}
		}
	}
	
	//支付方式选择
	$(".checkout .payment-list .ul-payment-list li").click(function(){
		var type = $(this).data('type');
		$(this).addClass("item-selected").siblings().removeClass("item-selected");
		$(this).find('input').prop("checked", true);
		if(type=="onlinepay"){
			$("#qt_balance").hide();
			
			$("#qt_onlinepay").show();
	  		$("form[name='doneTheForm'] :input[name='pay_pwd_error']").val(1);
			
			$("#ECS_SURPLUS").val(0);
			changeSurplus(0);
		}else{
			
			var surplus = $("#ECS_SURPLUS").val();
			var integral = $("#ECS_INTEGRAL").val();
			
			if(!(surplus > 0 || integral > 0)){
				$("#qt_onlinepay").hide();
				$("form[name='doneTheForm'] :input[name='pay_pwd_error']").val(0);
			}
			
			$("#qt_balance").show();
		}
		
		$("#ECS_PAY_PAYPWD").html('');
		$("form[name='doneTheForm'] :input[name='pay_pwd']").val('');
	});
        
	//产品列表筛选
	$(".store-checkbox label").click(function(){
		var check = $(this).prev();
		if(check.prop("checked") == true){
			var input_url = ($(this).nextAll('#input-i2').attr('rev'));
			check.addClass("checkbox-checked");
		}else{
			var input_url = ($(this).nextAll("#input-i1").attr('rev'));
			check.addClass("checkbox-checked");
		}
		
		location.href = input_url;
	});
	
	//店铺分类展开
	$("#sp-category dt").click(function(){
		if($(this).parent().hasClass("open")){
			$(this).parent().removeClass("open");
		}else{
			$(this).parent().addClass("open");
			$(this).parent().siblings().removeClass("open");
		}
	});
	
	
	//价格筛选
	$(".fP-box input").click(function(){
		$('.price-button-strip > .fP-expand').show();
	});
	
	$(document).click(function(e){
		if(e.target.id !='price-min' && e.target.id !='price-max'){
			$('.price-button-strip > .fP-expand').hide();
		}
	})
	
	//评论筛选
	$(".m-tab-trigger li").click(function(){
		var rev = $(this).attr('rev');
		var comment;
		var goods_id = $("input[name='good_id']").val()
		$(this).addClass("on").siblings().removeClass("on");
		
		if(rev == 1){
			comment = 'comment_good';	
		}else if(rev == 2){
			comment = 'comment_middle';
		}else if(rev == 3){
			comment = 'comment_short';
		}else{
			comment = 'comment_all';
		}
		
		goods_id = goods_id + "|" + rev;
		
		Ajax.call('comment.php?act=' + comment, 'id=' + goods_id, get_commentResponse, 'GET', 'JSON');
	});
	
	//网友讨论圈
	$('.dis_type').click(function(){
		var T = $(this);
		var rev = $(this).attr('rev');
		var dis_sort = $(this).attr('sort');
		var revType = $(this).attr('revType');
		var goods_id = $("input[name='good_id']").val();
		
		$(this).parent('li').addClass('current').siblings().removeClass('current');
		
		if(!revType){
			revType = 0; //加载模板
		}
		
		if(dis_sort){
			dis_sort = "|" + dis_sort;
			rev = $("input[name='dis_class']").val();
		}else{
			dis_sort = '';
			$("input[name='dis_class']").val(rev);
		}
		
		goods_id = goods_id + "|" + rev + "|" + revType + dis_sort;
		
		Ajax.call('comment_discuss.php?act=discuss', 'id=' + goods_id, comment_discussResponse, 'GET', 'JSON');
	});
	
	
	$(window).resize(function(){
		var winWidth =$(window).width();
		$.resize(winWidth,'.banner-box');//首页轮播自动缩小
		$.resize(winWidth,'.classify-banner');//首页轮播自动缩小
	});
	
	jQuery.resize = function(width,obj){
		var obj = $(obj);
		var auto = (width-1200)/2;
		obj.children(".bd").find('ul,li').css({"width":width});
		if(width>1200){
			obj.find(".banner-width").css({"margin-left":auto,"margin-right":auto});
		}else{
			obj.find(".banner-width").css({"margin-left":0,"margin-right":0});
		}
	}
	
	//订单状态筛选
	function order_select(){
		var $this = $(".user-item-dl");
		$this.hover(function(){
			$(this).children(".user-select-main").show();
		},function(){
			$(this).children(".user-select-main").hide();
		});
		
		$this.find(".item").hover(
			function(){
				$(this).addClass('h1_red');
			},
			function(){
				$(this).removeClass('h1_red');
			}
		);
		
		$this.find(".item").on("click",function(){
			var idtxt = $(this).data('idtxt');
			var action = $(this).data('action');
			var type = $(this).data('type');
		
			var index = $(this).parents(".user-item-dl").index();
			var text = $(this).text();
			$this.find(".user-select-main").hide();
			$this.eq(index).children(".user-item-select").find("span").text(text);
			$(this).addClass("selected").siblings().removeClass("selected");
			
			get_OrderSearch(idtxt, action, type,$(this));
		});
	}
	order_select();
	
	//配送地区 start
	function areaAddress(){
		var $this = $("#area_address");
		var width=0;
		$this.hover(function(){
			width = $(this).outerWidth();
			$(this).find('.area_list_style').show();
			$(this).find(".area_brand").show();
			$(this).find(".area_brand").css({'width':width-2});
			$(this).find('.flow_area_list_style').show();
		},function(){
			$(this).find('.area_list_style').hide();
			$(this).find(".area_brand").hide();
			
			$(this).find('.flow_area_list_style').hide();
		});
	}
	areaAddress();
	//配送地区 end
	
	//预售详情 预售规则
	$(".sp-rule").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});
	
	//购物车未登录结算弹出登录框
	$("#go_pay").click(function(){
		var back_url=$(this).data("url");
		$.notLogin("get_ajax_content.php?act=get_login_dialog",back_url);
		return false;
	});
	
	//头部购物车数量加减
	function query(){
		var num = 1;
		var subtract =$(".count-subtract");
		var add =$(".count-add");
		
		$(".count .num").each(function(){
            if(Number($(this).text())>1){
				$(this).prev().removeClass("ecsc-minusOff");
			}
        });
		
		subtract.click(function(){
			num = $(this).next(".num");
			var number = Number(num.text());
			if(number>1){
				num.text(number-1);
			}
			if(Number(num.text())== 1){
				$(this).addClass("ecsc-minusOff");
			}
		});
		
		add.click(function(){
			num = $(this).prev(".num");
			var number = Number(num.text());
			num.text(number+1);
			if(Number(num.text())>1){
				$(this).prevAll(".count-subtract").removeClass("ecsc-minusOff");
			}
		});
	}
	query();
	
	$(document).ready(function(e) {
        var bodylayer = $("body");
		var div = $("<div class='support'></div>")
			.append("");
		bodylayer.append(div);
		
		bodylayer.css('cssText', 'position: relative !important;');
		$(".support").css('cssText', 'position: absolute !important; bottom:92px !important; width:100% !important; text-align: center !important; z-index:2147483647 !important; font-family: "Microsoft YaHei";');
    });
	
	
	//手机扫一扫
	$(".qrcode-wrap").hover(function(){
		$(this).addClass("mob-buy-curr");
		$(this).find("#summary-mbuy").css({"opacity":1});
	},function(){
		$(this).removeClass("mob-buy-curr");
		$(this).find("#summary-mbuy").css({"opacity":0});
	});

	//默认顶级分类页分类筛选
	$(".ctm_items").find(".item").click(function(){
		$(this).addClass("current").siblings().removeClass("current");
	});
	
	//商品详情页价格阶梯
	var hoverTimer, outTimer;
	$('.view_priceLadder').hover(function(){
		clearTimeout(outTimer);
		hoverTimer = setTimeout(function(){$(".priceLadder").show()},200);
	},function(){
		clearTimeout(hoverTimer);
		outTimer = setTimeout(function(){$(".priceLadder").hide()},200);
	});
	
	//登录切换微信扫描登录
	$(".qrcode-target").click(function(){
		if($(this).hasClass("btn-2qrcode")){
			$(this).removeClass("btn-2qrcode").addClass("btn-2login");
			$(this).prevAll(".qrcode-login").show();
			$(this).prevAll(".login-form").hide();
		}else{
			$(this).removeClass("btn-2login").addClass("btn-2qrcode");
			$(this).prevAll(".qrcode-login").hide();
			$(this).prevAll(".login-form").show();
		}
	});
	
	//众筹页面js
	$("#parent_catagory li a").on("click",function(){
		var textTypeIndex = $(this).parent().index();
		var vsecondlist = $(".v-second-list");
		$(this).parent().addClass("current").siblings().removeClass("current");
		$(this).parents(".v-fold").next().show();
		var index = textTypeIndex-1;
		if(index >= 0){
			vsecondlist.show();
			vsecondlist.children(".s-list").eq(index).show().siblings().hide();
		}else{
			vsecondlist.hide();
			vsecondlist.children(".s-list").hide();
		}
	});
	$("#sort li").click(function(){
		$(this).addClass("current").siblings().removeClass("current");
	});

	$(".v-option").click(function(){
		if($(this).hasClass('slidedown')){
			$(this).removeClass('slidedown').addClass('v-close');
			$(this).html("<b></b><span>收起</span>");
			$(this).next().css("height","auto");
		}else{
			$(this).removeClass('v-close').addClass('slidedown');
			$(this).html("<b></b><span>更多</span>");
			$(this).next().css("height","26px");
		}
	});
	
	$.inputPrompt("#keyword",true,$('#keyword').val());
	$.inputPrompt("#keyword2",true,$('#keyword2').val());
	$.ecscSearch("#shop_search");
	$.ecscSearch("#shop_search2");
})


function get_commentResponse(result){
	$("#ECS_COMMENT").html(result.content);
}

function comment_discussResponse(result){
	$("#discuss_list_ECS_COMMENT").html(result.content);
}  

//头部随时退、先行赔偿、过期退切换
$(function(){
	$('.site-commitment .site-commitment-back').hide();
	function mySideChange(front) {
		if (front) {
			$(this).parent().find('div.site-commitment-front').show();
			$(this).parent().find('div.site-commitment-back').hide();
		} else {
			$(this).parent().find('div.site-commitment-front').hide();
			$(this).parent().find('div.site-commitment-back').show();
		}
	}
	$('.site-commitment').mouseenter(function(){
		$(this).find('div').stop().rotate3Di('flip', 250, {direction: 'clockwise', sideChange: mySideChange});
	}).mouseleave(function(){
		$(this).find('div').stop().rotate3Di('unflip', 500, {sideChange: mySideChange});
	});
});

//input文本框 提示文字
jQuery.inputPrompt = function(s,c,v){
	var s = $(s);
	s.focus(function(){
		if($(this).val() == v){
			$(this).val("");
			if(c==true){
				$(this).css("color","#666");
			}
		}
	});
	s.blur(function(){
		if($(this).val()==''){
			$(this).val(v);
			if(c==true){
				$(this).css("color","#999");
			}
		}else{
			if(c==true){
				$(this).css("color","#666")
			}
		}
	});
}

$(document).click(function(){
	$(".suggestions_box").hide();
});

$(".suggestions_box").click(function(e){//自己要阻止
	e.stopPropagation();//阻止冒泡到body
});

//ie6、ie7去除a标签点击后虚线
function ie67(){
	var b_v = navigator.appVersion;
	var IE6 = b_v.search(/MSIE 6/i) != -1;
	var IE7 = b_v.search(/MSIE 7/i) != -1;
	if (IE6||IE7)
	{
		$("a").ready(function(){
			$("a").attr("hidefocus",true);
		});
	}
}
ie67();

//头部商品店铺搜索切换
jQuery.ecscSearch = function(a){
	$(a).hover(function(){
		$(this).css({"height":"auto","overflow":"visible"});
	},function(){
		$(this).css({"height":35,"overflow":"hidden"});
	});
	
	$(a).prev().hover(function(){
		$(this).next().css({"height":"auto","overflow":"visible"});
	});
	
	$(a).find('li').each(function(index, element){
		if($(this).hasClass("curr")){
			$(element).click(function(){
				var html_1 = $(this).html();
				var attr_1 = $(this).attr('rev');
				if(index == 0){
					$(this).html($(this).next().html());
					$(this).next().html(html_1);
					
					var nextAttr = $(this).next().attr("rev");
					$(this).attr({rev : "" +nextAttr+ ""});
					$(this).next().attr({rev : "" +attr_1+ ""});	
				}else if(index == 1){
					$(this).html($(this).prev().html());
					$(this).prev().html(html_1);
					
					var prevAttr = $(this).prev().attr("rev");
					$(this).attr({rev : "" +prevAttr+ ""});
					$(this).prev().attr({rev : "" +attr_1+ ""});
					
					$(this).parent().css({"height":35,"overflow":"hidden"});
				}
				$(this).parents(".ecsc-search-tabs").nextAll("input[name='store_search_cmt']").val(attr_1);
			});
		}
	});
}
function dialogPrompt(i,m){
   var content = '<div id="'+i+'">' + 
                        '<div class="tip-box icon-box">' +
                                '<span class="warn-icon m-icon"></span>' + 
                                '<div class="item-fore">' +
                                        '<h3 class="rem ftx-04">' + m + '</h3>' +
                                '</div>' +
                        '</div>' +
                '</div>';
        pb({
                id:i,
                title:'标题',
                width:455,
                height:58,
                content:content, 	//调取内容
                drag:false,
                foot:false
        });

        $('#' + i + ' .tip-box').css({
                'width' : '350px'
        });
}
//首页，顶级分类页广告栏自适应宽度
jQuery.liWidth = function(obj){
	$(obj).each(function(){
		var li = $(this).find(".hd li")
		if(li.length==1){
			li.parents(".hd").addClass("hd_li1");
		}else if(li.length==2){
			li.parents(".hd").addClass("hd_li2");
		}else if(li.length==3){
			li.parents(".hd").addClass("hd_li3");
		}else if(li.length==4){
			li.parents(".hd").addClass("hd_li4");
		}
	});
}

//jq仿select
jQuery.divselect = function(divselectid,inputselectid) {
	var inputselect = $(inputselectid);
	$(divselectid+" .cite").click(function(event){
		event.stopImmediatePropagation();
		var ul = $(divselectid+" ul");
		if(ul.css("display")=="none"){
			ul.css("display","block");
		}else{
			ul.css("display","none");
		}

	});
	$(divselectid+" ul li a").click(function(event){
		event.stopImmediatePropagation();
		var txt = $(this).text();
		$(divselectid+" .cite").html(txt);
		var value = $(this).attr("selectid");
		inputselect.val(value);
		$(divselectid+" ul").hide();
	});
	$(document).bind("click",function(){
		$(divselectid+" ul").hide();
	})
};

/*
 *	顶级分类页几套模板js(默认、女装、家电、食品）
 */

$(function(){
	//二级分类栏js
	$('#parent-cata-nav .item').unbind('mouseenter').bind('mouseenter',function(){
		var T = $(this);
		var cat_id = T.children('.item-left').children('.cata-nav-name').data('parentcat');
		var eveval = T.children('.item-left').children('.cata-nav-name').attr('parent_eveval');
		
		if(eveval != 1){
			T.children('.item-left').children('.cata-nav-name').attr('parent_eveval', '1');
			/*加载中by wu*/
			$('#brands_' + cat_id).html('<img src="themes/ecmoban_dsc/images/loadGoods.gif" width="200" height="200" class="lazy" style="margin:0 112px">');					
			$.ajax({
			   type: "GET",
			   url: "get_ajax_content.php",
			   data: "act=getCategotyParentTree&cat_id=" + cat_id,
			   dataType:'json',
			   success: function(data){
				 $('#brands_' + data.cat_id).html(data.brands_content);
			   }
			});
		}
		
		T.addClass("selected");
		T.children('.cata-nav-layer').show();
	});
	
	$('#parent-cata-nav .item').unbind('mouseleave').bind('mouseleave',function(){
		$(this).removeClass("selected");
		$(this).children(".cata-nav-layer").hide();
	});
	
	//随便瞧瞧换一组（家电顶级分类页模板）
	$(".have-a-look").find(".ec-huan").click(function(){
		var load_num = 0;
		var region_id = $("input[name='region_id']").val();
		var area_id = $("input[name='area_id']").val();
		var prent_id = $("input[name='cat_id']").val();
		$.ajax({
			type:'get',
			url:'get_ajax_content.php',
			data:"act=changeShow&type=1&tpl=2&cat_id={$cate_info.cat_id}&rome_key=" + load_num + "&prent_id=" + prent_id + "&region_id=" + region_id + "&area_id=" + area_id,
			dataType:'json',
			success:function(data)
			{
				$(".ecsc-ps-list").html(data.page);
				$(".ecsc-ps-list").find("img.lazy").lazyload({
					effect : "fadeIn"
				});
			}
		})
	});
});

//分类商品换一组
function changeShow(cat_id,tpl)
{
	var load_num = 0;
	var region_id = $("input[name='region_id']").val();
	var area_id = $("input[name='area_id']").val();
	var prent_id = $("input[name='cat_id']").val();
	$.ajax({
		type:'get',
		url:'get_ajax_content.php',
		data:"act=changeShow&type=3&tpl="+ tpl +"&cat_id=" + cat_id + "&rome_key=" + load_num + "&prent_id=" + prent_id + "&region_id=" + region_id + "&area_id=" + area_id,
		dataType:'json',
		success:function(data)
		{
			$("[cat_id="+cat_id+"]").html(data.page);
			$("[cat_id="+cat_id+"]").find("img.lazy").lazyload({
				effect : "fadeIn"
			});
		}
	})
}

//异步加载每个楼层的分类切换
jQuery.tabs = function (){
	var li = $(".tab").find("li");
	var index = 0;
	var floors ='';
	li.hover(function(){
		$(this).addClass("on");
		$(this).siblings().removeClass("on");
		index = $(this).index();
		floors = $(this).parents(".floor-container");
		floors.find(".ecsc-cp-tabs").hide();
		floors.find(".ecsc-cp-tabs").eq(index).show();
	});
}
/*顶级分类页几套模板js(默认、女装、家电、食品）end*/


//出现广告位提示
jQuery.adpos = function(){
	$("*[ecdscType='adPos']").each(function(i,e){
		var _this = $(this);
		var div = _this.find('img');
		var text = _this.data("adposname");
	
		if(!div.length>0){
			_this.addClass('adPos_hint');
			_this.html('<section>请去后台广告位置" '+text+'" 里面设置广告！</section>');
		}
	});
	$("*[ecdscType='Text']").each(function(i,e){
		var _this = $(this);
		var div = _this.find('div');
		var text = _this.data("adposname");
	
		if(!div.length>0){
			_this.addClass('adPos_hint');
			_this.html('<section>请去" '+text+'"设置！</section>');
		}
	});
}
//出现广告位提示end

//轮播广告hd只适应left
$.slidehd = function(bd,hd){
	var length = $(bd).length;
	var width = length*37;
	$(hd).css({"margin-left":-width/2});
}
//轮播广告hd只适应left end

//会员中心提示错误信息
function get_user_prompt_message(text){
	var ok_title = "确定";
	var cl_title = "取消";
	var title = '提示信息';
	var width = 455; 
	var height = 58;
	var divId = "email_div";
	
	var content = '<div id="' + divId + '">' +
						'<div class="tip-box icon-box">' +
							'<span class="warn-icon m-icon"></span>' +
							'<div class="item-fore">' +
								'<h3 class="ftx-04">' + text + '</h3>' + 
							'</div>' +
						'</div>' +
					'</div>';
	
	pb({
		id:divId,
		title:title,
		width:width,
		height:height,
		ok_title:ok_title, 	//按钮名称
		cl_title:cl_title, 	//按钮名称
		content:content, 	//调取内容
		drag:false,
		foot:true,
		onOk:function(){              
		},
		onCancel:function(){
		}
	});
	
	$('.pb-ok').addClass('color_df3134');
	$('#' + divId + ' .pb-ct .item-fore').css({
		'height' : '58px'
	});
	
	if(text.length <= 15){
		$('#' + divId + ' .pb-ct .item-fore').css({
			"padding-top" : '10px'
		});
	}
}
function sildeImg(num){
	//$(".network-wrap:eq("+num+")").slide({mainCell:".sider ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,trigger:"click",scroll:1,vis:6,prevCell:".goods_prev",nextCell:".goods_next",});
	//$(".network-wrap:gt("+num+")").slide({mainCell:".sider ul",effect:"left",pnLoop:false,autoPlay:false,autoPage:true,trigger:"click",scroll:1,vis:6,prevCell:".goods_prev",nextCell:".goods_next",});

	$(".sider li").hover(function(){
		var src = $(this).find('img').attr("src");
		$(this).parents(".sider").prev().find("img").attr("src",src);
		$(this).addClass("curr").siblings().removeClass("curr");
	});
}