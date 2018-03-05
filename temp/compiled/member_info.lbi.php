
<?php if ($this->_var['user_info']): ?>
您好
<span>
	<a href="<?php echo $this->_var['site_domain']; ?>user.php"><?php echo $this->_var['user_info']['nick_name']; ?></a>，欢迎来到<a alt="首页" title="首页" href="index.php">
    	<span><?php echo $this->_var['shop_name']; ?></span>
    </a>
</span>
<span>[<a href="<?php echo $this->_var['site_domain']; ?>user.php?act=logout"><?php echo $this->_var['lang']['user_logout']; ?></a>]</span>
<?php else: ?>
	<a href="<?php echo $this->_var['site_domain']; ?>user.php" class="link-login style-red">请登录</a>
	<?php if ($this->_var['shop_reg_closed'] != 1): ?>
    <a href="user.php?act=register" class="link-regist">注册</a>
    <?php endif; ?>
<?php endif; ?>