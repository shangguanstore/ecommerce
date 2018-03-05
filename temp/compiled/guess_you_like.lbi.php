
<div class="guess-love w1200">
    <div class="in-title"><i class="icon-r"></i><a href="#" target="_blank" class="bit">猜你喜欢</a></div>
    <div class="guess-love-warper">
        <dl class="love-tag-list">
            <dt class="love-title"><i class="love-i"></i>爱搜标签</dt>
            <dd class="love-content">
                <ul class="tag-name-list">
                    <?php $_from = $this->_var['searchkeywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'val_0_17699800_1515739839');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['val_0_17699800_1515739839']):
?> 
                    <?php if ($this->_var['key'] < 9): ?>
                    <li><a href="search.php?keywords=<?php echo urlencode($this->_var['val_0_17699800_1515739839']); ?>" target="_blank"><?php echo $this->_var['val_0_17699800_1515739839']; ?></a></li>
                    <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
            </dd>
        </dl>
        <dl class="love-product-list">
            <dt class="love-title"><i class="love-i"></i>喜欢的商品</dt>
            <dd class="love-content">
                <div class="love-product-items">
                    <?php $_from = $this->_var['guess_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_17729600_1515739839');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['goods_0_17729600_1515739839']):
        $this->_foreach['foo']['iteration']++;
?>
                    <div class="product-item">
                        <div class="jbox">
                            <div class="bg"></div>
                            <a href="<?php echo $this->_var['goods_0_17729600_1515739839']['url']; ?>" target="_blank"><h1><?php echo sub_str($this->_var['goods_0_17729600_1515739839']['short_name'],5); ?></h1><div class="new-price"><?php echo $this->_var['goods_0_17729600_1515739839']['shop_price']; ?></div></a>
                        </div>
                        <div class="p-img"><img src="themes/ecmoban_dsc/images/loadGoods.gif" width="110" height="110" class="lazy" data-original="<?php echo $this->_var['goods_0_17729600_1515739839']['goods_thumb']; ?>"></div>
                    </div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </dd>
        </dl>
        <dl class="love-store">
            <dt class="love-title"><i class="love-i"></i>推荐给你的店铺</dt>
            <dd class="love-content">
                <div class="love-store-list">
                    <?php $_from = $this->_var['guess_store']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['store']):
        $this->_foreach['foo']['iteration']++;
?>
                    <div class="love-store-item <?php if (($this->_foreach['foo']['iteration'] <= 1)): ?>mar_bott<?php endif; ?>">
                        <div class="item-left"><a href="<?php echo $this->_var['store']['store_url']; ?>" target="_blank" style=" text-align:center; display:block;"><img src="themes/ecmoban_dsc/images/loadGoods.gif" class="lazy" data-original="<?php echo $this->_var['store']['street_thumb']; ?>"/></a></div>
                        <div class="item-right">
                            <div class="store-logo"><img src="<?php echo $this->_var['store']['brand_thumb']; ?>" width="90"></div>
                            <div class="store-name"><?php echo sub_str($this->_var['store']['shop_name'],6); ?></div>
                            <div class="btn"><a href="<?php echo $this->_var['store']['store_url']; ?>" target="_blank">进入 ></a></div>
                        </div>
                    </div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </dd>
        </dl>
        <dl class="love-interest">
            <dt class="love-title"><i class="love-i"></i>可能感兴趣</dt>
            <dd class="love-content">
                <div class="love-interest-list">
                    <?php 
$k = array (
  'name' => 'ads',
  'id' => '127',
  'num' => '2',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
                </div>
            </dd>
        </dl>
    </div>
</div>