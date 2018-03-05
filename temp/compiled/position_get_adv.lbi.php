
<?php $_from = $this->_var['ad_posti']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'posti');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['posti']):
?>
<?php if ($this->_var['posti']['posti_type'] == 'floor_banner'): ?>
    <div class="floor-banner-body" <?php if ($this->_var['posti']['link_color']): ?>style="background:<?php echo $this->_var['posti']['link_color']; ?>;"<?php endif; ?>>
        <div class="floor-banner w1200">
            <a href="<?php echo $this->_var['posti']['ad_link']; ?>" target="_blank"><img style="max-width:<?php echo $this->_var['posti']['ad_width']; ?>px; max-height:<?php echo $this->_var['posti']['ad_height']; ?>px;" src="<?php echo $this->_var['posti']['ad_code']; ?>"/></a>
        </div>
    </div>
<?php elseif ($this->_var['posti']['posti_type'] == 'top_banner'): ?>
    <div class="top-banner" <?php if ($this->_var['posti']['link_color']): ?>style="background:<?php echo $this->_var['posti']['link_color']; ?>;"<?php endif; ?>>
        <div class="module w1200">
            <a href="<?php echo $this->_var['posti']['ad_link']; ?>" target="_blank"><img width="<?php echo $this->_var['posti']['ad_width']; ?>" height="<?php echo $this->_var['posti']['ad_height']; ?>" src="<?php echo $this->_var['posti']['ad_code']; ?>" /></a>
            <div class="close"></div>
        </div>
    </div>
<?php else: ?>
<a href="<?php echo $this->_var['posti']['ad_link']; ?>" target="_blank"><img width="<?php echo $this->_var['posti']['ad_width']; ?>" height="<?php echo $this->_var['posti']['ad_height']; ?>" src="<?php echo $this->_var['posti']['ad_code']; ?>" /></a>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>