
<?php echo $this->_var['get_adv']; ?>

<div class="floor w1200" data-title="<?php echo $this->_var['goods_cat']['name']; ?>" data-idx="<?php echo $this->_var['goods_cat']['floor_sort_order']; ?>" id="floor_<?php echo $this->_var['goods_cat']['floor_sort_order']; ?>">
    <div class="floor-container">
        <div class="floor-title">
            <h2><i class="floor-icon"><?php echo $this->_var['goods_cat']['floor_sort_order']; ?>F</i><span rev='<?php echo $this->_var['goods_cat']['id']; ?>'><?php echo $this->_var['goods_cat']['name']; ?></span></h2>
            <ul class="tab">
            	<?php $_from = $this->_var['goods_cat']['goods_level2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cat');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['cat']):
        $this->_foreach['foo']['iteration']++;
?>
                <?php if ($this->_var['key'] < 2): ?>
                <li <?php if ($this->_foreach['foo']['iteration'] == 1): ?>class="on"<?php endif; ?>><?php echo $this->_var['cat']['name']; ?><i></i></li>
                <?php endif; ?>
  				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </div>
        <div class="floor-content">
            <div class="floor-left">
                <div class="floor-left-banner">
                    <div class="bd">
                    	<?php echo $this->_var['cat_goods_banner']; ?>
                    </div>
                    <div class="hd"><ul></ul></div>
                    <i class="flip-icon-top"></i>
                    <i class="flip-icon-bottom"></i>
                </div>
                <div class="banner-nav">
                    <ul class="oneClass">
                    	<?php $_from = $this->_var['goods_cat']['goods_level2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['cat']):
        $this->_foreach['foo']['iteration']++;
?>
                        <li>
                            <a href="<?php echo $this->_var['cat']['url']; ?>" target="_blank" class="oneClass-name"><?php echo $this->_var['cat']['name']; ?> <i>></i></a>
                            <div class="twoClass">
                            	<?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child_0_05555900_1515857582');$this->_foreach['foochild'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foochild']['total'] > 0):
    foreach ($_from AS $this->_var['child_0_05555900_1515857582']):
        $this->_foreach['foochild']['iteration']++;
?>
                                <div class="item"><a href="<?php echo $this->_var['child_0_05555900_1515857582']['url']; ?>" target="_blank"><?php echo $this->_var['child_0_05555900_1515857582']['name']; ?></a></div>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </div>
                        </li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                    <dl>
                    	<?php $_from = $this->_var['goods_cat']['goods_level2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['cat']):
        $this->_foreach['foo']['iteration']++;
?>
                    	<dd></dd>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                        <dd></dd>
                    </dl>
                </div>
            
            </div>
            <div class="floor-right">
                <div class="floor-tabs-content">
                	<?php $_from = $this->_var['goods_cat']['goods_level3']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'goods_level3');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['goods_level3']):
        $this->_foreach['foo']['iteration']++;
?>
                    <?php if ($this->_var['key'] < 2): ?>
                    <div class="ecsc-main" <?php if (! ($this->_foreach['foo']['iteration'] <= 1)): ?>style="display:none"<?php endif; ?>>
                        <ul class="p-list">
                        	<?php $_from = $this->_var['goods_level3']['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['foo']['iteration']++;
?>
                            <li>
                                <div class="p-img"><a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" width="140" height="140"></a></div>
                                <div class="p-name"><a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></a></div>
                                <div class="p-price">
                                	<span class="shop-price">
                                    	<?php if ($this->_var['goods']['promote_price'] != ''): ?>
                                            <?php echo $this->_var['goods']['promote_price']; ?>
                                        <?php else: ?>
                                            <?php echo $this->_var['goods']['shop_price']; ?>
                                        <?php endif; ?>
                                    </span>
                                    <span class="original-price"><?php echo $this->_var['goods']['market_price']; ?></span>
                                </div>
                            </li>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </div>
        </div>
        <?php echo $this->_var['cat_goods_hot']; ?>
    </div>
</div>