
<?php if ($this->_var['category_topic']): ?>
<?php $_from = $this->_var['category_topic']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'topic');if (count($_from)):
    foreach ($_from AS $this->_var['topic']):
?>
<a href="<?php echo $this->_var['topic']['topic_link']; ?>" target="_blank"><?php echo $this->_var['topic']['topic_name']; ?><i>&gt;</i></a>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php endif; ?>