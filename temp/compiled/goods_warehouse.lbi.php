
<?php if ($this->_var['show_warehouse']): ?>
<div class="text-select" id="dis_warehouse">
    <div class="area_tit" id="dis_warehouse_name"><span><?php echo $this->_var['warehouse_name']; ?></span><i class="sc-icon-right"></i></div>
</div>
<div class="dis_warehouse_brand"></div>
<div class="warehouse" id="warehouse_li">
    <ul>
        <?php $_from = $this->_var['warehouse_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'warehouse');if (count($_from)):
    foreach ($_from AS $this->_var['warehouse']):
?>
            <?php if ($this->_var['warehouse']['region_name'] != $this->_var['warehouse_name']): ?>
        		<li onclick="warehouse(<?php echo $this->_var['warehouse']['region_id']; ?>,<?php echo $this->_var['goods_id']; ?>)"><?php echo $this->_var['warehouse']['region_name']; ?></li>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
</div>
<?php endif; ?>

<script type="text/javascript">
	$(function(){
		var width = $('#dis_warehouse').outerWidth();
		$("#dis_warehouse").click(function(e){
			$("#warehouse_li").show();
			$('.dis_warehouse_brand').css({"width":width-2,"display":"block"});
			$('#warehouse_li').css({"width":width-2});
			e.stopPropagation();
		});
		$(document).click(function(e){
			$("#warehouse_li").hide();
			$('.dis_warehouse_brand').hide();
			e.stopPropagation();
		})
	})
</script>