<?php exit;?>0015203164140f969c3b8fe89b9298fd46de108fddb5s:3006:"a:2:{s:8:"template";s:2942:"<?php $__Template->display($this->getTpl("page_header")); ?>
<div class="con b-color-f">
    <section class="user-center user-login margin-lr">
        <form class="login-form validation" action="<?php echo U('index');?>" method="post">
            <div class="text-all dis-box j-text-all" name="usernamediv">
                <label>账 号</label>
                <div class="box-flex input-text">
                    <input class="j-input-text" name="username" datatype="*" nullmsg="请输入用户名"
                           type="text" placeholder="用户名/手机号/邮箱"/>
                    <i class="iconfont icon-guanbi1 is-null j-is-null"></i>
                </div>
            </div>
            <div class="text-all dis-box j-text-all" name="passworddiv">
                <label>密 码</label>
                <div class="box-flex input-text">
                    <input class="j-input-text" name="password" type="password" datatype="*" nullmsg="请输入密码" placeholder="请输入密码"/>
                    <i class="iconfont icon-guanbi1 is-null j-is-null"></i>
                </div>
                <i class="iconfont icon-yanjing is-yanjing j-yanjing disabled"></i>
            </div>
            <input type="hidden" name="back_act" value="<?php echo $back_act; ?>"/>
            <a class="fr t-remark" href="<?php echo U('get_password');?>">忘记密码？</a>
            <button type="submit" class="btn-submit">登录</button>

        </form>
        <a class="a-first u-l-register" href="<?php echo U('register');?>">新用户注册</a>

        <?php if($oauth_list) { ?>
        <div class="other-login">
            <h4 class="title-hrbg"><span>第三方登录</span>
                <hr/>
            </h4>
            <ul class="dis-box">
                <?php $n=1;if(is_array($oauth_list)) foreach($oauth_list as $vo) { ?>
                    <li class="box-flex"><a href="<?php echo U('oauth/index/index', array('type' => $vo['type'],'back_url' => $back_act));?>">
                        <img src="<?php echo __TPL__;?>/img/oauth/sns_<?php echo $vo['type']; ?>.png" alt="" width="64">
                        </a>
                    </li>
                <?php $n++;}unset($n); ?>
            </ul>
        </div>
        <?php } ?>
    </section>
</div>
<script>
    $(function () {
        $.Tipmsg.r = null;
        $(".validation").Validform({
            tiptype: function (msg) {
                d_messages(msg);
            },
            tipSweep: true,
            ajaxPost: true,
            callback: function (data) {
                // {"info":"demo info","status":"y"}
                if (data.status === 'y') {
                    window.location.href = data.url;
                } else {
                    d_messages(data.info);
                }
            }
        });
    })
</script>
</body>

</html>";s:12:"compile_time";i:1520230014;}";