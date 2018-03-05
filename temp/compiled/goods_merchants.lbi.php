
<div class="seller-pop">
    <div class="seller-logo">
    	<?php if ($this->_var['goods']['user_id']): ?>
        	<?php if ($this->_var['goods']['shopinfo']['brand_thumb']): ?>
    		<a href="<?php echo $this->_var['goods']['store_url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['shopinfo']['brand_thumb']; ?>" height="45" /></a>
            <?php else: ?>
            <a href="<?php echo $this->_var['goods']['goods_brand_url']; ?>" target="_blank"><?php echo $this->_var['goods']['goods_brand']; ?></a>
            <?php endif; ?>
        <?php else: ?>
        	<?php if ($this->_var['goods']['brand']['brand_logo']): ?>
        	<a href="<?php echo $this->_var['goods']['brand']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['brand']['brand_logo']; ?>" height="45" /></a>
            <?php else: ?>
            <a href="<?php echo $this->_var['goods']['goods_brand_url']; ?>" target="_blank"><?php echo $this->_var['goods']['goods_brand']; ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="seller-infor"><a href="<?php echo $this->_var['goods']['store_url']; ?>" title="<?php echo $this->_var['goods']['rz_shopName']; ?>" target="_blank" class="name"><?php echo $this->_var['goods']['rz_shopName']; ?></a><i class="icon arrow-show-more"></i></div>
    <?php if ($this->_var['goods']['user_id']): ?>
    <?php if ($this->_var['goods']['grade_name']): ?>
    <dl class="seller-zf seller_grade_name">
        <dt>商家等级：</dt>
        <dd>
        	<span title="<?php echo $this->_var['goods']['grade_introduce']; ?>"><?php echo $this->_var['goods']['grade_name']; ?></span><img src="<?php echo $this->_var['goods']['grade_img']; ?>" width="20" title="<?php echo $this->_var['goods']['grade_introduce']; ?>"/>
        </dd>
    </dl>
    <?php endif; ?>
    <dl class="seller-zf">
        <dt>店铺总分：</dt>
        <dd>
            <span class="heart-white">
                <span class="heart-red h10" style="width:<?php echo $this->_var['merch_cmt']['cmt']['all_zconments']['allReview']; ?>%;" title="综合好评(<?php echo $this->_var['merch_cmt']['cmt']['all_zconments']['allReview']; ?>%)"> </span>
            </span>
            <em class="evaluate-grade"><strong title="10"><a target="_blank" href="#"><?php echo $this->_var['merch_cmt']['cmt']['all_zconments']['score']; ?></a></strong>分</em>
        </dd>
    </dl>
    <div class="seller-pop-box">
        <dl class="pop-score-detail">
            <dt class="score-title">
                <span class="col1">评分明细</span>
                <span class="col2">与行业相比</span>
            </dt>
            <dd class="score-infor">
                <div class="score-part">
                    <span class="score-desc">商品<em title="<?php echo $this->_var['merch_cmt']['cmt']['commentRank']['zconments']['score']; ?>" class="number"><?php echo $this->_var['merch_cmt']['cmt']['commentRank']['zconments']['score']; ?></em></span>
                    <span class="score-change"><em class="score-percent"><?php echo $this->_var['merch_cmt']['cmt']['commentRank']['zconments']['up_down']; ?>%</em></span>
                	<span class="score-trend"><i class="sprite-<?php if ($this->_var['merch_cmt']['cmt']['commentRank']['zconments']['is_status'] == 1): ?>up<?php elseif ($this->_var['merch_cmt']['cmt']['commentRank']['zconments']['is_status'] == 2): ?>average<?php else: ?>down<?php endif; ?>"></i></span>
                </div>
                <div class="score-part">
                    <span class="score-desc">服务<em title="<?php echo $this->_var['merch_cmt']['cmt']['commentServer']['zconments']['score']; ?>" class="number"><?php echo $this->_var['merch_cmt']['cmt']['commentServer']['zconments']['score']; ?></em></span>
                    <span class="score-change"><em class="score-percent"><?php echo $this->_var['merch_cmt']['cmt']['commentServer']['zconments']['up_down']; ?>%</em></span>
                	<span class="score-trend"><i class="sprite-<?php if ($this->_var['merch_cmt']['cmt']['commentServer']['zconments']['is_status'] == 1): ?>up<?php elseif ($this->_var['merch_cmt']['cmt']['commentServer']['zconments']['is_status'] == 2): ?>average<?php else: ?>down<?php endif; ?>"></i></span>
                </div>
                <div class="score-part">
                    <span class="score-desc">时效<em title="<?php echo $this->_var['merch_cmt']['cmt']['commentDelivery']['zconments']['score']; ?>" class="number"><?php echo $this->_var['merch_cmt']['cmt']['commentDelivery']['zconments']['score']; ?></em></span>
                    <span class="score-change"><em class="score-percent"><?php echo $this->_var['merch_cmt']['cmt']['commentDelivery']['zconments']['up_down']; ?>%</em></span>
                	<span class="score-trend"><i class="sprite-<?php if ($this->_var['merch_cmt']['cmt']['commentDelivery']['zconments']['is_status'] == 1): ?>up<?php elseif ($this->_var['merch_cmt']['cmt']['commentDelivery']['zconments']['is_status'] == 2): ?>average<?php else: ?>down<?php endif; ?>"></i></span>
                </div>
            </dd>
        </dl>
    </div>
    <?php endif; ?>
    <div class="seller-address">
        <div class="item">
        	<?php if ($this->_var['goods']['user_id']): ?>
            <span class="label">公&nbsp;&nbsp;&nbsp;&nbsp;司：</span>
            <?php else: ?>
            <span class="label">品&nbsp;&nbsp;&nbsp;&nbsp;牌：</span>
            <?php endif; ?>
            <span class="text">
            <?php if ($this->_var['goods']['user_id']): ?>
            	<?php if ($this->_var['shop_info']['companyName']): ?>
                    <?php echo $this->_var['shop_info']['companyName']; ?>
                <?php else: ?>
                	<?php echo $this->_var['basic_info']['shop_name']; ?>
                <?php endif; ?>  
            <?php else: ?>
                <?php echo $this->_var['goods']['brand']['brand_name']; ?>
            <?php endif; ?>    
            </span>
        </div>
        <div class="item">
            <span class="label">所在地：</span>
            <span class="text">
            	<?php if ($this->_var['adress']['province'] && $this->_var['adress']['city']): ?>
                    <?php echo $this->_var['adress']['province']; ?>&nbsp;<?php echo $this->_var['adress']['city']; ?>
                <?php else: ?>
                    <?php echo $this->_var['basic_info']['province']; ?>&nbsp;<?php echo $this->_var['basic_info']['city']; ?>
                <?php endif; ?>   
            </span>
        </div>
    </div>
    <div class="seller-kefu">
        
        <?php if ($this->_var['shop_information']['is_IM'] == 1 || $this->_var['shop_information']['is_dsc']): ?>

            <a id="IM" onclick="openWin(this)" href="javascript:;" goods_id="<?php echo $this->_var['goods']['goods_id']; ?>" class="seller-btn"><i class="icon"></i>在线客服</a>
        <?php else: ?>
            <?php if ($this->_var['basic_info']['kf_type'] == 1): ?>
            <a href="http://www.taobao.com/webww/ww.php?ver=3&touid=<?php echo $this->_var['basic_info']['kf_ww']; ?>&siteid=cntaobao&status=1&charset=utf-8" class="seller-btn" target="_blank"><i class="icon"></i>联系客服</a>
            <?php else: ?>
            <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->_var['basic_info']['kf_qq']; ?>&site=qq&menu=yes" class="seller-btn" target="_blank"><i class="icon"></i>联系客服</a>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>
    <?php if ($this->_var['goods']['user_id']): ?>
    <div class="pop-shop-enter">
        <a href="<?php echo $this->_var['goods']['store_url']; ?>" class="btn-gray btn-shop-access">进店逛逛</a>
        <a href="javascript:;" onClick="get_collect_store(2, <?php echo $this->_var['goods']['user_id']; ?>)" class="btn-gray btn-shop-followey">关注店铺</a>
        <input type="hidden" name="error" value="<?php echo $this->_var['goods']['error']; ?>" id="error"/>
    </div>
    <?php endif; ?>
</div>