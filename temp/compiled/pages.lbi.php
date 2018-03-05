
<?php if ($this->_var['pager']['page_count'] > 1): ?>
<div class="tc">
    <form name="selectPageForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <?php if ($this->_var['pager']['styleid'] == 0): ?> 
        <div id="pager">
  <?php echo $this->_var['lang']['pager_1']; ?><?php echo $this->_var['pager']['record_count']; ?><?php echo $this->_var['lang']['pager_2']; ?><?php echo $this->_var['lang']['pager_3']; ?><?php echo $this->_var['pager']['page_count']; ?><?php echo $this->_var['lang']['pager_4']; ?> <span> <a href="<?php echo $this->_var['pager']['page_first']; ?>"><?php echo $this->_var['lang']['page_first']; ?></a> <a href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a> <a href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a> <a href="<?php echo $this->_var['pager']['page_last']; ?>"><?php echo $this->_var['lang']['page_last']; ?></a> </span>
    <?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_06910200_1515739694');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_06910200_1515739694']):
?>
      <?php if ($this->_var['key'] == 'keywords'): ?>
          <input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo urldecode($this->_var['item_0_06910200_1515739694']); ?>" />
        <?php else: ?>
          <input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item_0_06910200_1515739694']; ?>" />
      <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <select name="page" id="page" onchange="selectPage(this)">
    <?php echo $this->html_options(array('options'=>$this->_var['pager']['array'],'selected'=>$this->_var['pager']['page'])); ?>
    </select>
        </div>
        
        
		<?php else: ?>
            <div class="pages" id="pager">
                <ul>
                    <?php if ($this->_var['pager']['page_kbd']): ?>
                    <?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_07070900_1515739694');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_07070900_1515739694']):
?>
                        <?php if ($this->_var['key'] == 'keywords'): ?>
                        <input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo urldecode($this->_var['item_0_07070900_1515739694']); ?>" />
                        <?php else: ?>
                        <input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item_0_07070900_1515739694']; ?>" />
                        <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                    <?php endif; ?>
                    <?php if ($this->_var['pager']['page_first']): ?><li style="display:none;"><a href="<?php echo $this->_var['pager']['page_first']; ?>"><span>首页</span></a></li><?php endif; ?>
                    <li class="previous"><a href="<?php if ($this->_var['pager']['page_prev']): ?><?php echo $this->_var['pager']['page_prev']; ?><?php else: ?>#none<?php endif; ?>"><i><<</i>上一页</a></li>
                    
                        <?php if ($this->_var['pager']['page_count'] != 1): ?>
                        <?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_07271800_1515739694');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_07271800_1515739694']):
?>
                        <?php if ($this->_var['pager']['page'] == $this->_var['key']): ?>
                        <li class="current"><a href="#none"><?php echo $this->_var['key']; ?></a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $this->_var['item_0_07271800_1515739694']; ?>"><?php echo $this->_var['key']; ?></a></li>
                        <?php endif; ?>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        <?php endif; ?>
                   
                    <li class="nextious"><a href="<?php if ($this->_var['pager']['page_next']): ?><?php echo $this->_var['pager']['page_next']; ?><?php else: ?>#none<?php endif; ?>">下一页<i>>></i></a></li>
                    <?php if ($this->_var['pager']['page_last']): ?><li style="display:none"><a class="last" href="<?php echo $this->_var['pager']['page_last']; ?>"><span>末页</span></a></li><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </form>
</div>
<?php endif; ?>

<script type="Text/Javascript" language="JavaScript">
<!--

function selectPage(sel)
{
  sel.form.submit();
}

//-->
</script>
