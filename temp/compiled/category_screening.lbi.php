
<?php if ($this->_var['brands']['1'] || $this->_var['price_grade']['1'] || $this->_var['filter_attr_list'] || $this->_var['color_search'] || $this->_var['get_bd'] || $this->_var['g_price'] || $this->_var['g_array'] || $this->_var['c_array'] || $this->_var['parray']): ?>
<div class="right-extra" rewrite=<?php echo $this->_var['rewrite']; ?>>
      <div class="u_cloose">
        <dl>
          <dt>已选条件：</dt>
          <dd>
          
          	<?php if (! $this->_var['get_bd']['bd'] && ! $this->_var['g_price'] && ! $this->_var['parray'] && ! $this->_var['c_array']['attr_value'] && ! $this->_var['g_array']): ?>
            &nbsp;
            <?php endif; ?>
          
            <?php if ($this->_var['get_bd']['bd']): ?>
                <div class="get_item" title="<?php echo $this->_var['get_bd']['bd']; ?>">
                <b>品牌：</b>
                <em><?php echo $this->_var['get_bd']['bd']; ?></em>
                <a href="<?php echo $this->_var['get_bd']['br_url']; ?>"></a>
              </div>
            <?php endif; ?>

             <?php if ($this->_var['g_price']): ?>
               <?php $_from = $this->_var['g_price']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'price');if (count($_from)):
    foreach ($_from AS $this->_var['price']):
?>
                <div class="get_item" title="<?php echo $this->_var['price']['price_range']; ?>">
                  <b>价格：</b>
                  <em><?php echo $this->_var['price']['price_range']; ?></em>
                  <a href="<?php echo $this->_var['price']['url']; ?>"></a>
                </div>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              <?php endif; ?>

              <?php if ($this->_var['parray']): ?>
                <div class="get_item" title="{$parray.min_max">
                  <b>价格：</b>
                  <em><?php echo $this->_var['parray']['min_max']; ?></em>
                  <a href="<?php echo $this->_var['parray']['purl']; ?>"></a>
                </div>
              <?php endif; ?>
  
             <?php if ($this->_var['c_array']['attr_value']): ?>
                <div class="get_item" title="<?php echo $this->_var['c_array']['attr_value']; ?>">
                  <b><?php echo $this->_var['c_array']['filter_attr_name']; ?>：</b>
                  <em><?php echo $this->_var['c_array']['attr_value']; ?></em>
                  <a href="<?php echo $this->_var['c_array']['url']; ?>"></a>
                </div>
             <?php endif; ?>

            

            <?php if ($this->_var['g_array']): ?>  
                <?php $_from = $this->_var['g_array']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'garray');if (count($_from)):
    foreach ($_from AS $this->_var['garray']):
?>
                <div class="get_item" title="<?php echo $this->_var['garray']['g_name']; ?>">
                  <span class="brand_t"><?php echo $this->_var['garray']['filter_attr_name']; ?>：</span>
                  <em><?php echo $this->_var['garray']['g_name']; ?></em>
                  <a href="<?php echo $this->_var['garray']['g_url']; ?>"></a>
                </div>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
          </dd>
          <dd class="give_up_all"><a href="category.php?id=<?php echo $this->_var['category']; ?>">全部撤销</a></dd>
          
        </dl>
      </div>
      <div class="goods_list">
          <ul class="attr_father">
          
            <?php if ($this->_var['brands']): ?>
            <li>
              <div class="brand_img attr_list">
                <div class="brand_name_l">品牌：</div>
                <div class="brand_select_more">
                  <div class="all_a_z">
                      <ul class="a_z">
                          <li class="all_brand curr">所有品牌</li>
                          <?php $_from = $this->_var['letter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'letter_0_01079000_1515739694');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['letter_0_01079000_1515739694']):
?>
                            <li data-key="<?php echo $this->_var['letter_0_01079000_1515739694']; ?>"><?php echo $this->_var['letter_0_01079000_1515739694']; ?></li>
                          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                          <li class="other_brand">其他</li>
                      </ul>
                  </div>
                <div class="wrap_brand">  
                  <div class="brand_div">
                   <?php $_from = $this->_var['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                    <div class="brand_img_word" brand ="<?php echo $this->_var['brand']['brand_letters']; ?>">
                      <?php if ($this->_var['brand']['brand_logo']): ?>
                      <a href="<?php echo $this->_var['brand']['url']; ?>"><img src="<?php echo $this->_var['brand']['brand_logo']; ?>" alt="<?php echo $this->_var['brand']['brand_name']; ?>" title="<?php echo $this->_var['brand']['brand_name']; ?>"> 
                      <strong><?php echo $this->_var['brand']['brand_name']; ?></strong></a>
                      <?php else: ?>
                      <a href="<?php echo $this->_var['brand']['url']; ?>"><b><?php echo $this->_var['brand']['brand_name']; ?></b></a> 
                      <?php endif; ?>
                    </div>
                   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  </div>
                    
                  </div>  
                  <div class="zimu_list">
                      <ul class="get_more" >
                        <?php $_from = $this->_var['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                          <li class="is_no" brand ="<?php echo $this->_var['brand']['brand_letters']; ?>" url_id="<?php echo $this->_var['brand']['brand_id']; ?>"><span class="choose_ico"></span><a class="goods_brand_name" data-url="<?php echo $this->_var['brand']['url']; ?>"><?php echo $this->_var['brand']['brand_name']; ?></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                      </ul>
                  </div>
                  <div class="enter_yes_no">
                    <div class="ct_auto">
                        <span class="yes_bt botton disabled">确定</span>
                        <span class="no_bt botton">关闭</span>
                    </div>
                  </div>
                </div>
                <div class="sl-ext">
                	<div class="choose_open"><i class="icon_all"></i></div>
                    <div class="choose_more"><i class="icon"></i>多选</div>
                </div>
              </div>
            </li>
            <?php endif; ?>
            
            <?php if ($this->_var['price_grade']['1']): ?>
            <li>
              <dl>
                <div class="filter_attr_name"><?php echo $this->_var['lang']['price']; ?>：</div>
                <div class="attr_son">
                	<div class="price_auto fl">
                    <?php $_from = $this->_var['price_grade']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'grade');if (count($_from)):
    foreach ($_from AS $this->_var['grade']):
?>
                    <?php if ($this->_var['grade']['price_range']): ?>
                      <div class="price_range"><a href="<?php echo $this->_var['grade']['url']; ?>"><?php echo $this->_var['grade']['price_range']; ?></a></div>
                    <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </div>
                    <div class="price_auto price_bottom fl">
                          <input type="text" title="最低价" name="price_min" class="price_class price_min">
                          <span class="price_class span_price_class">-</span>
                          <input type="text" title="最高价" name="price_max" class="price_class price_max">
                          <button class="price_ok price_class">确定</button>
                    </div>
                </div>
              </dl>
            </li>
            <?php endif; ?> 

            
              <?php if ($this->_var['color_search']): ?>
              <li>
                <dl class="attr_list">
                  	<div class="filter_attr_name"><?php echo $this->_var['color_search']['filter_attr_name']; ?>：</div>
                    <div class="attr_son attr_color">
                        <div class="item_list color_list_color">
                            <?php $_from = $this->_var['color_search']['attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'color_se');if (count($_from)):
    foreach ($_from AS $this->_var['color_se']):
?>
                             <?php if ($this->_var['color_se']['selected']): ?>
                                  <span class="u_get"></span>
                                  <?php else: ?>
                                  <div class="color_divdd">
                                      <dd url_id="<?php echo $this->_var['color_se']['goods_id']; ?>">
                                        <a title="<?php echo $this->_var['color_se']['attr_value']['c_value']; ?>" href="<?php echo $this->_var['color_se']['url']; ?>" data-url="<?php echo $this->_var['color_se']['url']; ?>">
                                        <span style="background:<?php echo $this->_var['color_se']['attr_value']['c_url']; ?>"></span>
                                        <b></b>
                                        </a>
                                      </dd>
								   </div>
                             <?php endif; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </div>
                        <div class="tw_buttom">
                            <span class="sure sure_color disabled">确定</span>
                            <span class="no_btn">取消</span>
                        </div>
                    </div>
                    <div class="sl-ext">
                  		<div class="choose_more"><i class="icon"></i>多选</div>
					</div>
                </dl>
              </li>
              <?php endif; ?> 
            

            <?php $_from = $this->_var['filter_attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'filter_attr_0_01638400_1515739694');if (count($_from)):
    foreach ($_from AS $this->_var['filter_attr_0_01638400_1515739694']):
?>
            <li class="same_li">
              <dl class="attr_list">
                <div class="filter_attr_name"><?php echo htmlspecialchars($this->_var['filter_attr_0_01638400_1515739694']['filter_attr_name']); ?>：</div>
                <div class="attr_son">
                  <div class="item_list">
                       <?php $_from = $this->_var['filter_attr_0_01638400_1515739694']['attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr');if (count($_from)):
    foreach ($_from AS $this->_var['attr']):
?>
                        <?php if ($this->_var['attr']['selected']): ?>
                        <span class="u_get"><?php echo $this->_var['attr']['attr_value']; ?></span>
                         <?php else: ?>
                        <dd url_id="<?php echo $this->_var['attr']['goods_id']; ?>"><a href="<?php echo $this->_var['attr']['url']; ?>" data-url="<?php echo $this->_var['attr']['url']; ?>"><span></span><strong><?php echo $this->_var['attr']['attr_value']; ?></strong></a></dd>
                         <?php endif; ?>
                      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  </div>
                  <div class="tw_buttom">
                    <span class="sure sure_I disabled">确定</span>
                    <span class="no_btn">取消</span>
                  </div>
                </div>
                <div class="sl-ext">
                    <div class="choose_open"><i class="icon_all"></i></div>                
                    <div class="choose_more"><i class="icon"></i>多选</div>
                </div>
              </dl>
            </li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </ul>
      </div>

      <div class="click_more"><span><strong>更多选项</strong><i></i></span></div>

  </div>
<?php endif; ?>
