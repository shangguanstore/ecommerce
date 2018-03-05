<?php if ($this->_var['comments']): ?>
<ul>
	<?php $_from = $this->_var['comments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'comment');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['comment']):
        $this->_foreach['no']['iteration']++;
?>
    <li class="ratelist-item">
        <div class="user-info-block">
            <div class="avatar-wrapper">
            	<?php if ($this->_var['comment']['user_picture']): ?>
            	<img src="<?php echo $this->_var['comment']['user_picture']; ?>" width="85" height="85" />
                <?php else: ?>
                <img src="themes/ecmoban_dsc/images/touxiang.jpg" width="85" height="85" />
                <?php endif; ?>
            </div>
            <p class="name-wrapper"><?php echo htmlspecialchars($this->_var['comment']['username']); ?></p>
        </div>
        <div class="review-content-wrapper">
            <div class="info">
                <div class="rate-status">
                    <span class="common-rating"><span class="rate-stars" style="width:<?php if ($this->_var['comment']['rank'] == 1): ?>20<?php elseif ($this->_var['comment']['rank'] == 2): ?>40<?php elseif ($this->_var['comment']['rank'] == 3): ?>60<?php elseif ($this->_var['comment']['rank'] == 4): ?>80<?php elseif ($this->_var['comment']['rank'] == 5): ?>100<?php endif; ?>%"></span></span>
                </div>
            </div>
            <div class="normal-view">
            	<div class="comment-content">
                    <p class="content">
                        <?php echo $this->_var['comment']['content']; ?><b class="hqrate-badge"><?php echo $this->_var['comment']['add_time']; ?></b>
                    </p>
                    <dl>
                        <dd class="p-bfc">
                            <?php $_from = $this->_var['comment']['goods_tag']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'tag');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['tag']):
        $this->_foreach['no']['iteration']++;
?>
                            <p class="conmm-tags"><span><?php echo $this->_var['tag']['txt']; ?></span></p>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </dd>
                    </dl>
                </div>
                <?php if ($this->_var['comment']['img_list']): ?>
                <div class="comment-content">
                    <div class="p-show-img clearfix">
                        <ul>
                        	<?php $_from = $this->_var['comment']['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
                            <li><img src="<?php echo $this->_var['img']['img_thumb']; ?>" width="65"></li>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </ul>
                        <span class="show-number">共 <?php echo $this->_var['comment']['img_cont']; ?> 张图片</span>
                        <a href="#none" class="view-show">点击查看</a>
                    </div>
                    <div class="p-photos hide">
                        <div class="p-photos-viewer">
                            <div class="p-photos-wrap">
                                <div class="bd">
                                    <ul>
                                    	<?php $_from = $this->_var['comment']['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
                                        <li><img src="<?php echo $this->_var['img']['comment_img']; ?>" width="292"></li>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </ul>
                                </div>
                                <div class="hd">
                                    <ul>
                                    	<?php $_from = $this->_var['comment']['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
                                        <li><img src="<?php echo $this->_var['img']['comment_img']; ?>" width="65"></li>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </ul>
                                </div>
                                <a href="javascript:void(0);" class="photo_prev"></a>
                                <a href="javascript:void(0);" class="photo_next"></a>
                            </div>
                        </div>
                        <span class="show-number">共 <?php echo $this->_var['comment']['img_cont']; ?> 张图片</span>
                        <a href="#none" class="view-hide">点击收起</a>
                    </div>
                </div>        
                <?php endif; ?>                                 
                <?php if ($this->_var['comment']['re_content']): ?>
                <div class="hf comment-content">
                	
                    <span class="ftx-01"><?php echo $this->_var['comment']['shop_name']; ?>：</span>
                    <span class="qingse"><?php echo $this->_var['comment']['re_content']; ?><b class="hqrate-badge"><?php echo $this->_var['comment']['re_add_time']; ?></b></span>
                    
                </div>
                <?php endif; ?>
                
                <div class="comment-content comment-reply<?php echo $this->_var['comment']['id']; ?>" id="reply_comment_ECS_COMMENT<?php echo $this->_var['comment']['id']; ?>">
                	<?php $_from = $this->_var['comment']['reply_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'reply');if (count($_from)):
    foreach ($_from AS $this->_var['reply']):
?>
                    <div class="comment-reply-item">                    
                        <div class="reply-infor">                        
                            <a href="javascript:;" class="user-name"><?php echo $this->_var['reply']['user_name']; ?></a>：
                            <span class="words"><?php echo $this->_var['reply']['content']; ?></span>
                            <span class="time">&#12288;<?php echo $this->_var['reply']['add_time']; ?></span>                    
                        </div>                            
                    </div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <?php if ($this->_var['comment']['reply_count'] > $this->_var['comment']['reply_size']): ?>
                    <div class="pages26 hide">
                        <div class="pages">
                            <div class="pages-it">
                        		<?php echo $this->_var['comment']['reply_pager']; ?>
                    		</div>
                    	</div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="comment-content comment-operate">
                    <span class="reply<?php if ($this->_var['comment']['reply_count'] > 0): ?> red<?php endif; ?>" ><i class="icon"></i>(<font class="reply-count<?php echo $this->_var['comment']['id']; ?>"><?php echo $this->_var['comment']['reply_count']; ?></font>)</span>
                    <span class="nice comment_nice <?php if ($this->_var['comment']['useful'] > 0): ?> red<?php endif; ?>" data-commentid="<?php echo $this->_var['comment']['id']; ?>" data-idvalue="<?php echo $this->_var['comment']['id_value']; ?>"><i class="icon"></i>(<font class="reply-nice<?php echo $this->_var['comment']['id']; ?>" ><?php echo $this->_var['comment']['useful']; ?></font>)</span>
                </div>
                <div class="comment-content reply-textarea hide" id="reply-textarea<?php echo $this->_var['comment']['id']; ?>">
                    <div class="reply-arrow"><b class="layer"></b></div>
                    <div class="inner">
                        <textarea class="reply-input" name="reply_content<?php echo $this->_var['comment']['id']; ?>" id="reply_content<?php echo $this->_var['comment']['id']; ?>" placeholder="<?php echo htmlspecialchars($this->_var['comment']['username']); ?>："></textarea>
                    </div>
                    <div class="btnbox">
                    	<span id="reply-error<?php echo $this->_var['comment']['id']; ?>"></span>
                    	<input name="comment_goods<?php echo $this->_var['comment']['id']; ?>" id="comment_goods<?php echo $this->_var['comment']['id']; ?>" type="hidden" value="<?php echo $this->_var['comment']['id_value']; ?>">
                    	<input name="comment_user<?php echo $this->_var['comment']['id']; ?>" id="comment_user<?php echo $this->_var['comment']['id']; ?>" type="hidden" value="<?php echo $this->_var['comment']['user_id']; ?>">
                    	<button type="button" class="reply-submit" value="<?php echo $this->_var['comment']['id']; ?>">提交</button>
                    </div>
                </div>
                <?php if ($this->_var['comment']['reply_count'] > 0): ?>
                <a href="comment_repay.php?act=repay&comment_id=<?php echo $this->_var['comment']['id']; ?>" class="fr ftx-05 mt15">查看全部回复>></a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($this->_var['comment']['buy_goods']['goods_attr']): ?>
        <div class="ratelist-history">
            <span class="history-title">
            	<?php echo $this->_var['comment']['buy_goods']['goods_attr']; ?>
            </span>
            <span class="history-add-time"><?php echo $this->_var['comment']['buy_goods']['add_time']; ?>&nbsp;购买</span>
        </div> 
        <?php endif; ?>
    </li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
<?php endif; ?>

<?php if ($this->_var['count'] > $this->_var['size']): ?>
<div class="pages26">
    <div class="pages">
        <div class="pages-it">
        <?php echo $this->_var['pager']; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<script type="text/javascript">
	$(function(){
		$('.reply-submit').click(function(){
			var T = $(this);
			var comment_id = T.val();
			var reply_content = $("#reply_content" + comment_id).val();
			var user_id = $("#comment_user" + comment_id).val();
			var goods_id = $("#comment_goods" + comment_id).val();
			
			if(reply_content == ''){
				$("#reply-error" + comment_id).html('请您填写留言信息.');
				return false;
			}
			
			Ajax.call('goods.php', 'act=comment_reply&comment_id=' + comment_id + '&reply_content=' + reply_content + '&goods_id=' + goods_id + '&user_id=' + user_id, commentReplyResponse, 'POST', 'JSON');
		});
		
		$('.comment_nice').click(function(){
			var T = $(this);
			var comment_id = T.data('commentid');
			var goods_id = T.data('idvalue');
			var type = 'comment';
			
			Ajax.call('goods.php', 'act=add_useful&id=' + comment_id + '&goods_id=' + goods_id + '&type=' + type, niceResponse, 'GET', 'JSON');
		});
	});
	
	function commentReplyResponse(res){
		if(res.err_no == 1){
			var back_url = res.url;
			$.notLogin("get_ajax_content.php?act=get_login_dialog",back_url);
			return false;
		}else if(res.err_no == 2){
			$("#reply-error" + res.comment_id).html('您已评价过.');
		}else{
			$("#reply-error" + res.comment_id).html('添加成功.');
			$("#reply_content" + res.comment_id).val('');
			$("#reply-textarea" + res.comment_id).addClass('hide');
                        $(".reply-count").addClass('red');
		}
		$(".comment-reply" + res.comment_id).html(res.content);
		$(".reply-count" + res.comment_id).html(res.reply_count);
	}
	
	function niceResponse(res){
		if(res.err_no == 1){
			var back_url = res.url;
			$.notLogin("get_ajax_content.php?act=get_login_dialog",back_url);
			return false;
		}else if(res.err_no == 0){
			$(".reply-nice" + res.id).html(res.useful);
                        $(".comment_nice").addClass('red');
		}
	}
</script>


