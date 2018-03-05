
<?php if ($this->_var['site_domain']): ?>
<?php if ($this->_var['area_htmlType'] != 'goods' && $this->_var['area_htmlType'] != 'exchange'): ?>
<script type="text/javascript" src="<?php echo $this->_var['site_domain']; ?>js/warehouse_area.js"></script>
<?php else: ?>
<script type="text/javascript" src="<?php echo $this->_var['site_domain']; ?>js/warehouse.js"></script>
<?php endif; ?>
<?php else: ?>
<?php if ($this->_var['area_htmlType'] != 'goods' && $this->_var['area_htmlType'] != 'exchange'): ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'warehouse_area.js')); ?>
<?php else: ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'warehouse.js')); ?>
<?php endif; ?>
<?php endif; ?>


<ul class="fl">
    <li class="dorpdown" id="city-choice">
        <dt class="sc-icon">
            <span>送货至：</span>
        <div class="sc-choie"><i class="sc-icon-right"></i><span class="ui-areamini-text" data-id="2" title=""><?php echo $this->_var['region_name']; ?></span></div>
        </dt>
        <?php if ($this->_var['pin_region_list']): ?>
        <dd class="dorpdown-layer">
            <div class="dd-spacer"></div>
            <div class="ui-areamini-content-wrap" id="ui-content-wrap">
                <dl>
        <dt>热门</dt>
        <dd><a href="javascript:get_district_list(52, 0);" <?php if ($this->_var['city_top'] == 52): ?>class="city_selected"<?php endif; ?>>北京</a></dd>
        <dd><a href="javascript:get_district_list(321, 0);" <?php if ($this->_var['city_top'] == 321): ?>class="city_selected"<?php endif; ?>>上海</a></dd>
        <dd><a href="javascript:get_district_list(76, 0);" <?php if ($this->_var['city_top'] == 76): ?>class="city_selected"<?php endif; ?>>广州</a></dd>
        <dd><a href="javascript:get_district_list(77, 0);" <?php if ($this->_var['city_top'] == 77): ?>class="city_selected"<?php endif; ?>>深圳</a></dd>
        <dd><a href="javascript:get_district_list(322, 0);" <?php if ($this->_var['city_top'] == 322): ?>class="city_selected"<?php endif; ?>>成都</a></dd>
        <dd><a href="javascript:get_district_list(311, 0);" <?php if ($this->_var['city_top'] == 311): ?>class="city_selected"<?php endif; ?>>西安</a></dd>
        <dd><a href="javascript:get_district_list(343, 0);" <?php if ($this->_var['city_top'] == 343): ?>class="city_selected"<?php endif; ?>>天津</a></dd>
        <dd><a href="javascript:get_district_list(180, 0);" <?php if ($this->_var['city_top'] == 180): ?>class="city_selected"<?php endif; ?>>武汉</a></dd>
        <dd><a href="javascript:get_district_list(120, 0);" <?php if ($this->_var['city_top'] == 120): ?>class="city_selected"<?php endif; ?>>海口</a></dd>
        <dd><a href="javascript:get_district_list(220, 0);" <?php if ($this->_var['city_top'] == 220): ?>class="city_selected"<?php endif; ?>>南京</a></dd>
        </dl>
        <div class="search-first-letter">
            <?php $_from = $this->_var['pin_region_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('letter', 'pin');if (count($_from)):
    foreach ($_from AS $this->_var['letter'] => $this->_var['pin']):
?>
            <a href="javascript:void(0);" data-letter="<?php echo $this->_var['letter']; ?>"><?php echo $this->_var['letter']; ?></a>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <div class="scrollBody" id="scrollBody">
            <div class="all-list" id="scrollMap">
                <ul id="ul">
                    <?php $_from = $this->_var['pin_region_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('letter', 'pin_region');$this->_foreach['reg'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['reg']['total'] > 0):
    foreach ($_from AS $this->_var['letter'] => $this->_var['pin_region']):
        $this->_foreach['reg']['iteration']++;
?>
                    <li data-id="<?php echo $this->_foreach['reg']['iteration']; ?>" data-name="<?php echo $this->_var['letter']; ?>">
                        <em><?php echo $this->_var['letter']; ?></em>
                        <div class="itme-city">
                            <?php $_from = $this->_var['pin_region']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'region');if (count($_from)):
    foreach ($_from AS $this->_var['region']):
?>
                            <?php if ($this->_var['region']['is_has']): ?>
                            <a href="javascript:get_district_list(<?php echo $this->_var['region']['region_id']; ?>, 0);" <?php if ($this->_var['city_top'] == $this->_var['region']['region_id']): ?>class="city_selected"<?php endif; ?>><?php echo $this->_var['region']['region_name']; ?></a>
                            <?php else: ?>
                            <a href="javascript:void(0);" class="is_district"><?php echo $this->_var['region']['region_name']; ?></a>
                            <?php endif; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </div>
                    </li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
            </div>
            <div class="scrollBar" id="scrollBar">
                <p id="city_bar"></p>
            </div>
            <input name="area_phpName" type="hidden" id="phpName" value="<?php echo $this->_var['area_phpName']; ?>">
        </div>
        </div>
        </dd>
        <?php endif; ?>
    </li>
</ul>