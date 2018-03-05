
<div class="preview">
    <div class="gallery_wrap">
        <a href="<?php if ($this->_var['pictures']['0']['img_url']): ?><?php echo $this->_var['pictures']['0']['img_url']; ?><?php else: ?><?php echo $this->_var['goods']['goods_img']; ?><?php endif; ?>" class="MagicZoomPlus" id="Zoomer" rel="hint-text: ; selectors-effect: false; selectors-class: img-hover; zoom-distance: 10;zoom-width: 400; zoom-height: 474;"><img src="themes/ecmoban_dsc/images/loadGoods3.gif" id="J_prodImg" alt="<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>" width="405" height="405" class="lazy" data-original="<?php if ($this->_var['pictures']['0']['img_url']): ?><?php echo $this->_var['pictures']['0']['img_url']; ?><?php else: ?><?php echo $this->_var['goods']['goods_img']; ?><?php endif; ?>"/></a>
    </div>
    <div class="spec-list">
        <a href="javascript:void(0);" class="spec-prev"></a>
        <a href="javascript:void(0);" class="spec-next"></a>
        <div class="spec-items">
            <ul>
            	<?php if (! $this->_var['pictures']['0']['img_url'] && $this->_var['goods']['goods_img']): ?>
                <li><a href="<?php echo $this->_var['goods']['goods_img']; ?>" rel="zoom-id: Zoomer" rev="<?php echo $this->_var['goods']['goods_img']; ?>"><img src="themes/ecmoban_dsc/images/loadGoods.gif" alt="<?php echo $this->_var['goods']['goods_name']; ?>" width="60" height="60" class="lazy" data-original="<?php echo $this->_var['goods']['goods_img']; ?>"/></a></li>
                <?php endif; ?>
            	<?php if ($this->_var['pictures']): ?> 
                <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['foo']['iteration']++;
?>
                <li><a href="<?php if ($this->_var['picture']['img_url']): ?><?php echo $this->_var['picture']['img_url']; ?><?php else: ?><?php echo $this->_var['picture']['thumb_url']; ?><?php endif; ?>" rel="zoom-id: Zoomer" rev="<?php if ($this->_var['picture']['img_url']): ?><?php echo $this->_var['picture']['img_url']; ?><?php else: ?><?php echo $this->_var['picture']['thumb_url']; ?><?php endif; ?>"><img src="themes/ecmoban_dsc/images/loadGoods.gif" alt="<?php echo $this->_var['goods']['goods_name']; ?>" width="60" height="60" class="lazy" data-original="<?php if ($this->_var['picture']['thumb_url']): ?><?php echo $this->_var['picture']['thumb_url']; ?><?php else: ?><?php echo $this->_var['picture']['img_url']; ?><?php endif; ?>"/></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
              	<?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="short-share">
        <div class="short-share-r"><?php if ($this->_var['cfg']['show_goodssn']): ?><?php echo $this->_var['lang']['bar_code']; ?><em id="bar_code"></em><?php endif; ?></div>
    	<div id="compare">
          <a href="javascript:;" class="btn-compare" id="compareLink" style=" margin-left:10px;">
       	  	<input id="<?php echo $this->_var['goods']['goods_id']; ?>" type="checkbox" onClick="Compare.add(this, <?php echo $this->_var['goods']['goods_id']; ?>,'<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>','<?php echo $this->_var['goods']['goods_type']; ?>', '<?php echo $this->_var['goods']['goods_thumb']; ?>', '<?php echo $this->_var['goods']['shop_price']; ?>', '<?php echo $this->_var['goods']['market_price']; ?>')" style=" vertical-align:middle; margin-left:8px;"/>&nbsp;&nbsp;<label for="<?php echo $this->_var['goods']['goods_id']; ?>" style=" line-height:25px; height:25px;">对比</label>
          </a>
        </div>
        <div class="collecting"><a href="javascript:void(0);" class="choose-btn-coll <?php if ($this->_var['goods']['is_collect']): ?>selected<?php endif; ?>"  data-dialog="goods_collect_dialog" data-divid="goods_collect" data-url="user.php?act=collect" data-goodsid="<?php echo $this->_var['goods']['goods_id']; ?>" data-width="455" data-height="58" data-title="提示"><b></b>收藏<em id="collect_count">(<?php echo $this->_var['goods']['collect_count']; ?>)</em></a></div>
        <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare" style="float:right;"><a class="bds_more" href="#none" style="background:url(themes/ecmoban_dsc/images/fx.png) 0px 10px no-repeat !important;color: #666;line-height: 25px;height: 25px;margin: 4px 15px 4px 10px;padding-left: 20px; padding-top:5px;display: block; float:right;">分享</a></div>
    </div>
</div>

<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=692785" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
