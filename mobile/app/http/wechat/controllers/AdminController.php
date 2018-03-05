<?php
namespace http\wechat\controllers;

use http\base\controllers\BackendController;

class AdminController extends BackendController
{

    protected $weObj = '';

    protected $wechat_id = 0;

    protected $page_num = 0;

    public function __construct()
    {
        parent::__construct();
        L(require(LANG_PATH  . C('shop.lang') . '/wechat.php'));
        $this->assign('lang', array_change_key_case(L()));
        // 默认微信公众号
        $this->wechat_id = 1;
        // 查找公众号
        $condition['id'] = $this->wechat_id;
        $mpInfo = $this->model->table('wechat')->field('*')->where($condition)->find();
        if (empty($mpInfo)) {
            $data = array(
                    'id' => $this->wechat_id,
                    'time' => gmtime(),
                    'type' => 2,
                    'status' => 1,
                    'default_wx' => 1
                    );
            $this->model->table('wechat')->data($data)->insert();
            $this->redirect(U('modify'));
        }
        // 获取配置信息
        $this->get_config();
        // 初始化 每页分页数量
        $this->page_num = 10;
        $this->assign('page_num', $this->page_num);
    }

    /**
     * 我的公众号
     */
    public function actionIndex()
    {
        $this->redirect(U('modify'));
        /*
         * $list = $this->model->table('wechat')
         * ->order('sort asc, id asc')
         * ->select();
         * if($list){
         * foreach($list as $key=>$val){
         * if($val['type'] == 2){
         * $list[$key]['manage_url'] = U('mass_message', array('wechat_id'=>$val['id']));
         * }
         * else{
         * $list[$key]['manage_url'] = U('reply_subscribe', array('wechat_id'=>$val['id']));
         * }
         * }
         * }
         * $l = sprintf(L('wechat_register'), '<a href=' . U('append') . '>');
         *
         * $this->assign('wechat_register', $l);
         * $this->assign('list', $list);
         * $this->display();
         */
    }

    /**
     * 设置公众号为默认
     */
    /*
     * public function set_default()
     * {
     * $id = I('get.id');
     * if (empty($id)) {
     * $this->message('请选择公众号', NULL, 'error');
     * }
     * // 取消默认
     * $data['default_wx'] = 0;
     * $this->model->table('wechat')
     * ->data($data)
     * ->where('1')
     * ->update();
     * // 设置默认
     * $data1['default_wx'] = 1;
     * $this->model->table('wechat')
     * ->data($data1)
     * ->where('id = ' . $id)
     * ->update();
     *
     * $this->redirect(U('index'));
     * }
     */

    /**
     * 新增公众号
     */
    public function actionAppend()
    {
        $this->redirect(U('index'));
        /*
         * if (IS_POST) {
         * $data = I('post.data', '', 'trim,htmlspecialchars');
         * $data['time'] = time();
         * // 验证数据
         * $result = Check::rule(array(
         * Check::must($data['name']),
         * L('must_name')
         * ), array(
         * Check::must($data['orgid']),
         * L('must_id')
         * ), array(
         * Check::must($data['token']),
         * L('must_token')
         * ));
         * if ($result !== true) {
         * $this->message($result, NULL, 'error');
         * }
         * // 更新数据
         * $this->model->table('wechat')
         * ->data($data)
         * ->insert();
         * $this->redirect(U('wechat/index'));
         * }
         * $this->display();
         */
    }

    /**
     * 修改公众号
     */
    public function actionModify()
    {
        $condition['id'] = $this->wechat_id;
        if (IS_POST) {
            $data = I('post.data', '', 'trim,htmlspecialchars');
            // 验证数据
            $form = new \libraries\Form();
            if(!$form->isEmpty($data['name'], 1)){
                $this->message(L('must_name'), NULL, 2);
            }
            if(!$form->isEmpty($data['orgid'], 1)){
                $this->message(L('must_id'), NULL, 2);
            }
            if(!$form->isEmpty($data['token'], 1)){
                $this->message(L('must_token'), NULL, 2);
            }
            // 更新数据
            $this->model->table('wechat')->data($data)->where($condition)->update();
            $this->message(L('wechat_editor') . L('success'), U('modify'));
        }
        $data = $this->model->table('wechat')->where($condition)->find();
        $data['url'] = '';
        if($data){
            $data['url'] = U('wechat/index/index', array('orgid'=>$data['orgid']), true);
        }

        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 删除公众号
     */
    /*
     * public function delete()
     * {
     * $condition['id'] = intval($_GET['id']);
     * $this->model->table('wechat')
     * ->where($condition)
     * ->delete();
     * $this->redirect(U('wechat/index'));
     * }
     */

    /**
     * 公众号菜单
     */
    public function actionMenuList()
    {
        $list = $this->model->table('wechat_menu')->where(array('wechat_id'=>$this->wechat_id))->order('sort asc')->select();
        $result = array();
        if (is_array($list)) {
            foreach ($list as $vo) {
                if ($vo['pid'] == 0) {
                    $vo['val'] = ($vo['type'] == 'click') ? $vo['key'] : $vo['url'];
                    $sub_button = array();
                    foreach ($list as $val) {
                        $val['val'] = ($val['type'] == 'click') ? $val['key'] : $val['url'];
                        if ($val['pid'] == $vo['id']) {
                            $sub_button[] = $val;
                        }
                    }
                    $vo['sub_button'] = $sub_button;
                    $result[] = $vo;
                }
            }
        }
        $this->assign('list', $result);
        $this->display();
    }

    /**
     * 编辑菜单
     */
    public function actionMenuEdit()
    {
        if (IS_POST) {
            $id = I('post.id');
            $data = I('post.data');
            $data['wechat_id'] = $this->wechat_id;
            if ('click' == $data['type']) {
                if (empty($data['key'])) {
                    exit(json_encode(array(
                                    'status' => 0,
                                    'msg' => L('menu_keyword') . L('empty')
                                    )));
                }
                $data['url'] = '';
            } else {
                if (empty($data['url'])) {
                    exit(json_encode(array(
                                    'status' => 0,
                                    'msg' => L('menu_url') . L('empty')
                                    )));
                }
                if(strpos($data['url'], 'http://') === false){
                    exit(json_encode(array(
                                    'status' => 0,
                                    'msg' => L('menu_url') . L('link_err')
                                    )));
            }
            $data['key'] = '';
            }
            // 编辑
            if (! empty($id)) {
                $this->model->table('wechat_menu')->data($data)->where(array('id'=>$id))->update();
            } else {
                // 添加
                $this->model->table('wechat_menu')->data($data)->insert();
            }

            exit(json_encode(array(
                            'status' => 1,
                            'msg' => L('attradd_succed')
                            )));
        }
        $id = I('get.id');
        $info = array();
        // 顶级菜单
        $top_menu = $this->model->table('wechat_menu')->where(array('pid'=>0, 'wechat_id'=>$this->wechat_id))->select();
        if (! empty($id)) {
            $info = $this->model->table('wechat_menu')->where(array('id'=>$id))->find();
            // 顶级菜单
            $top_menu = $this->model->query("SELECT * FROM {pre}wechat_menu WHERE id <> $id AND pid = 0 AND wechat_id = $this->wechat_id");
        }

        $this->assign('top_menu', $top_menu);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 删除菜单
     */
    public function actionMenuDel()
    {
        $id = I('get.id');
        if (empty($id)) {
            $this->message(L('menu_select_del'), NULL, 2);
        }
        $minfo = $this->model->table('wechat_menu')->field('id, pid')->where(array('id'=>$id))->find();
        // 顶级栏目
        if ($minfo['pid'] == 0) {
            $this->model->table('wechat_menu')->where(array('pid'=>$minfo['id']))->delete();
        }
        $this->model->table('wechat_menu')->where(array('id'=>$minfo['id']))->delete();
        $this->message(L('drop') . L('success'), U('menu_list'));
    }

    /**
     * 生成自定义菜单
     */
    public function actionSysMenu()
    {
        $list = $this->model->table('wechat_menu')->where(array('status'=>1, 'wechat_id'=>$this->wechat_id))->order('sort asc')->select();
        if (empty($list)) {
            $this->message('请至少添加一个自定义菜单', NULL, 2);
        }
        $data = array();
        if (is_array($list)) {
            foreach ($list as $val) {
                if ($val['pid'] == 0) {
                    $sub_button = array();
                    foreach ($list as $v) {
                        if ($v['pid'] == $val['id']) {
                            $sub_button[] = $v;
                        }
                    }
                    $val['sub_button'] = $sub_button;
                    $data[] = $val;
                }
            }
        }
        $menu_list = array();
        foreach ($data as $key => $val) {
            if (empty($val['sub_button'])) {
                $menu_list['button'][$key]['type'] = $val['type'];
                $menu_list['button'][$key]['name'] = $val['name'];
                if ('click' == $val['type']) {
                    $menu_list['button'][$key]['key'] = $val['key'];
                } else {
                    $menu_list['button'][$key]['url'] = html_out($val['url']);
                }
            } else {
                $menu_list['button'][$key]['name'] = $val['name'];
                foreach ($val['sub_button'] as $k => $v) {
                    $menu_list['button'][$key]['sub_button'][$k]['type'] = $v['type'];
                    $menu_list['button'][$key]['sub_button'][$k]['name'] = $v['name'];
                    if ('click' == $v['type']) {
                        $menu_list['button'][$key]['sub_button'][$k]['key'] = $v['key'];
                    } else {
                        $menu_list['button'][$key]['sub_button'][$k]['url'] = html_out($v['url']);
                    }
                }
            }
        }
        /*
         * $data = array( 'button'=>array( array('type'=>'click', 'name'=>"今日歌曲", 'key'=>'MENU_KEY_MUSIC'), array('type'=>'view', 'name'=>"歌手简介", 'url'=>'http://www.qq.com/'), array('name'=>"菜单", 'sub_button'=>array(array('type'=>'click', 'name'=>'hello world', 'key'=>'MENU_KEY_MENU'))) ) );
         */

        $rs = $this->weObj->createMenu($menu_list);
        if (empty($rs)) {
            $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
        }
        $this->message(L('menu_create') . L('success'), U('menu_list'));
    }

    /**
     * 关注用户列表
     */
    public function actionSubscribeList()
    {
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('subscribe_list', $filter), $this->page_num);

        $total = $this->model->table('wechat_user')->where(array('wechat_id'=>$this->wechat_id, 'subscribe'=>1))->order('subscribe_time desc')->count();
        $sql = 'SELECT u.*, g.name, us.user_name FROM {pre}wechat_user u LEFT JOIN {pre}wechat_user_group g ON u.groupid = g.group_id LEFT JOIN {pre}users us ON us.user_id = u.ect_uid where u.subscribe = 1 and u.wechat_id = ' . $this->wechat_id . ' group by u.uid order by u.subscribe_time desc limit ' . $offset;
        $list = $this->model->query($sql);
        if (empty($list)) {
            $list = array();
        }
        // 分组
        $where1['wechat_id'] = $this->wechat_id;
        $group_list = $this->model->table('wechat_user_group')->field('id, group_id, name, count')->where($where1)->order('id, sort desc')->select();


        $this->assign('page', $this->pageShow($total));
        $this->assign('list', $list);
        $this->assign('group_list', $group_list);
        $this->display();
    }

    /**
     * 关注用户列表搜索
     */
    public function actionSubscribeSearch()
    {
        $keywords = I('request.keywords','','trim');
        $group_id = I('request.group_id',0,'intval');
        $where = '';
        $where1 = '';
        if (! empty($keywords)) {
            $where .= ' and (u.nickname like "%' . $keywords . '%" or us.user_name like "%' . $keywords . '%")';
        }
        if (isset($group_id) && $group_id > 0) {
            $where .= ' and u.groupid = ' . $group_id;
        }

        // 分页
        $filter['page'] = '{page}';
        $filter['group_id'] = $group_id;
        $filter['keywords'] = $keywords;
        $offset = $this->pageLimit(U('subscribe_search', $filter), $this->page_num);

        $sql = 'SELECT count(*) as number FROM {pre}wechat_user u LEFT JOIN {pre}wechat_user_group g ON u.groupid = g.group_id LEFT JOIN {pre}users us ON us.user_id = u.ect_uid where u.subscribe = 1 and u.wechat_id = ' . $this->wechat_id . $where . ' order by u.subscribe_time desc';
        $total = $this->model->query($sql);

        $sql1 = 'SELECT u.*, g.name, us.user_name FROM {pre}wechat_user u LEFT JOIN {pre}wechat_user_group g ON u.groupid = g.group_id LEFT JOIN {pre}users us ON us.user_id = u.ect_uid where u.subscribe = 1 and u.wechat_id = ' . $this->wechat_id . $where . ' group by u.uid order by u.subscribe_time desc limit ' . $offset;
        $list = $this->model->query($sql1);

        // 分组
        $where2['wechat_id'] = $this->wechat_id;
        $group_list = $this->model->table('wechat_user_group')->field('id, group_id, name, count')->where($where2)->order('id, sort desc')->select();

        $this->assign('page', $this->pageShow($total[0]['number']));
        $this->assign('list', $list);
        $this->assign('group_id', $group_id); //分组内搜索
        $this->assign('group_list', $group_list);
        $this->display('admin_subscribelist');
    }

    /**
     * 移动关注用户分组
     */
    public function actionSubscribeMove()
    {
        if (IS_POST) {
            if (empty($this->wechat_id)) {
                $this->message(L('wechat_empty'), NULL, 2);
            }
            $group_id = I('post.group_id',0,'intval');
            $openid = I('post.id');
            if (is_array($openid)) {
                foreach ($openid as $v) {
                    // 微信端移动用户
                    $this->weObj->updateGroupMembers($group_id, $v);
                    // 数据处理
                    $this->model->table('wechat_user')->data(array('groupid'=>$group_id))->where(array('openid'=>$v))->update();
                }
                $this->message(L('sub_move_sucess'), U('subscribe_list'));
            } else {
                $this->message(L('select_please'), NULL, 2);
            }
        }
    }

    /**
     * 同步粉丝（直接插入数据，不能直接执行）
     */
    public function actionSysfans(){
        //微信用户
        $wechat_user = $this->weObj->getUserList();
        foreach($wechat_user['data']['openid'] as $v){
            $info = $this->weObj->getUserInfo($v);
            $info['wechat_id'] = $this->wechat_id;
            $this->model->table('wechat_user')->data($info)->insert();
        }
        $this->redirect(U('subscribe_list', array('wechat_id'=>$this->wechat_id)));
    }

    /**
     * 更新用户信息
     */
    public function actionSubscribeUpdate()
    {
        if (empty($this->wechat_id)) {
            $this->message(L('wechat_empty'), NULL, 2);
        }
        // 本地数据
        $where['wechat_id'] = $this->wechat_id;
        $local_user = $this->model->table('wechat_user')->field('openid')->where($where)->select();
        if (empty($local_user)) {
            $local_user = array();
        }
        $user_list = array();
        foreach ($local_user as $v) {
            $user_list[] = $v['openid'];
        }
        // 微信端数据
        $wechat_user = $this->weObj->getUserList();

        if ($wechat_user['total'] <= 10000) {
            $wechat_user_list = $wechat_user['data']['openid'];
        } else {
            $num = ceil($wechat_user['total'] / 10000);
            $wechat_user_list = $wechat_user['data']['openid'];
            for ($i = 0; $i <= $num; $i ++) {
                $wechat_user1 = $this->weObj->getUserList($wechat_user['next_openid']);
                $wechat_user_list = array_merge($wechat_user_list, $wechat_user1['data']['openid']);
            }
        }
        // 数据对比
        foreach ($local_user as $val) {
            // 数据在微信端存在
            if (in_array($val['openid'], $wechat_user_list)) {
                $info = $this->weObj->getUserInfo($val['openid']);
                $where1['openid'] = $val['openid'];
                $this->model->table('wechat_user')->data($info)->where($where1)->update();
            } else {
                $where2['openid'] = $val['openid'];
                $data['subscribe'] = 0;
                $this->model->table('wechat_user')->data($data)->where($where2)->update();
            }
        }
        // 数据不存在
        foreach ($wechat_user_list as $vs) {
            if (! in_array($vs, $user_list)) {
                $info = $this->weObj->getUserInfo($vs);
                $info['wechat_id'] = $this->wechat_id;
                $this->model->table('wechat_user')->data($info)->insert();
            }
        }

        $this->redirect(U('subscribe_list'));
    }

    /**
     * 发送客服消息
     */
    public function actionSendCustomMessage()
    {
        if (IS_POST) {
            $data = I('post.data');
            $openid = I('post.openid');
            $form = new \libraries\Form();
            if(!$form->isEmpty($openid, 1)){
                exit(json_encode(array('status' => 0, 'msg' => L('select_openid'))));
            }
            if(!$form->isEmpty($data['msg'], 1)){
                exit(json_encode(array('status' => 0, 'msg' => L('message_content') . L('empty'))));
            }
            $data['send_time'] = gmtime();
            $data['iswechat'] = 1;
            // 微信端发送消息
            $msg = array(
                    'touser' => $openid,
                    'msgtype' => 'text',
                    'text' => array(
                        'content' => $data['msg']
                        )
                    );
            $rs = $this->weObj->sendCustomMessage($msg);
            if (empty($rs)) {
                exit(json_encode(array(
                                'status' => 0,
                                'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
                                )));
            }
            // 添加数据
            $this->model->table('wechat_custom_message')
                ->data($data)
                ->insert();

            exit(json_encode(array(
                            'status' => 1
                            )));
        }
        $uid = I('get.uid');
        $openid = I('get.openid');
        if($openid){
            $where['openid'] = $openid;
        }
        else{
            $where['uid'] = $uid;
        }
        $info = $this->model->table('wechat_user')->field('uid, nickname, openid')->where($where)->find();

        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 客服消息列表
     */
    public function actionCustomMessageList()
    {
        $uid = I('get.uid');
        if (empty($uid)) {
            $this->message(L('select_openid'), NULL, 2);
        }
        $nickname = $this->model->table('wechat_user')->field('nickname')->where(array('uid'=>$uid))->one();
        // 分页
        $filter['page'] = '{page}';
        $filter['uid'] = $uid;
        $offset = $this->pageLimit(U('custom_message_list', $filter), $this->page_num);
        $total = $this->model->table('wechat_custom_message')->where(array('uid'=>$uid))->order('send_time desc')->count();
        $list = $this->model->table('wechat_custom_message')
            ->field('msg, send_time, iswechat')
            ->where(array('uid'=>$uid))
            ->order('send_time desc, id desc')
            ->limit($offset)
            ->select();

        $this->assign('page', $this->pageShow($total));
        $this->assign('list', $list);
        $this->assign('nickname', $nickname);
        $this->display();
    }

    /**
     * 分组管理
     */
    public function actionGroupsList()
    {
        $where['wechat_id'] = $this->wechat_id;
        $local_list = $this->model->table('wechat_user_group')
            ->where($where)
            ->order('id, sort desc')
            ->select();
        $this->assign('list', $local_list);
        $this->display();
    }

    /**
     * 同步分组
     */
    public function actionSysGroups()
    {
        if (empty($this->wechat_id)) {
            $this->message(L('wechat_empty'), NULL, 2);
        }
        // 微信端分组列表
        $list = $this->weObj->getGroup();
        if (empty($list)) {
            $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
        }
        // 本地分组
        $where['wechat_id'] = $this->wechat_id;
        $this->model->table('wechat_user_group')->where($where)->delete();
        foreach ($list['groups'] as $key => $val) {
            $data['wechat_id'] = $this->wechat_id;
            $data['group_id'] = $val['id'];
            $data['name'] = $val['name'];
            $data['count'] = $val['count'];
            $this->model->table('wechat_user_group')->data($data)->insert();
        }
        $this->redirect(U('subscribe_list'));
    }

    /**
     * 添加、编辑分组
     */
    public function actionGroupsEdit()
    {
        if (empty($this->wechat_id)) {
            $this->message(L('wechat_empty'), NULL, 2);
        }
        if (IS_POST) {
            $name = I('post.name');
            $id = I('post.id', 0, 'intval');
            $group_id = I('post.group_id');
            if (empty($name)) {
                exit(json_encode(array(
                                'status' => 0,
                                'msg' => L('group_name') . L('empty')
                                )));
            }
            $data['name'] = $name;
            if (! empty($id)) {
                // 微信端更新
                $rs = $this->weObj->updateGroup($group_id, $name);
                if (empty($rs)) {
                    exit(json_encode(array(
                                    'status' => 0,
                                    'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
                                    )));
                }
                // 数据更新
                $where['id'] = $id;
                $this->model->table('wechat_user_group')
                    ->data($data)
                    ->where($where)
                    ->update();
            } else {
                // 微信端新增
                $rs = $this->weObj->createGroup($name);
                if (empty($rs)) {
                    exit(json_encode(array(
                                    'status' => 0,
                                    'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
                                    )));
                }
                // 数据新增
                $data['wechat_id'] = $this->wechat_id;
                $this->model->table('wechat_user_group')
                    ->data($data)
                    ->insert();
            }
            exit(json_encode(array(
                            'status' => 1
                            )));
        }
        $id = I('get.id', 0, 'intval');
        $group = array();
        if (! empty($id)) {
            $where['id'] = $id;
            $group = $this->model->table('wechat_user_group')
                ->field('id, group_id, name')
                ->where($where)
                ->find();
        }

        $this->assign('group', $group);
        $this->display();
    }

    /**
     * 渠道二维码
     */
    public function actionQrcodeList()
    {
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('qrcode_list', $filter), $this->page_num);
        $total = $this->model->query("SELECT count(*) as count FROM {pre}wechat_qrcode WHERE username is null AND wechat_id = $this->wechat_id ORDER BY sort ASC");
        $list = $this->model->query("SELECT * FROM {pre}wechat_qrcode WHERE username is null AND wechat_id = $this->wechat_id ORDER BY sort ASC");

        $this->assign('page', $this->pageShow($total[0]['count']));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 编辑二维码
     */
    public function actionQrcodeEdit()
    {
        if (IS_POST) {
            $data = I('post.data');
            $data['wechat_id'] = $this->wechat_id;
            // 验证数据
            $form = new \libraries\Form();
            if(!$form->isEmpty($data['function'], 1)){
                $this->message(L('qrcode_function'). L('empty'), NULL, 2);
            }
            if(!$form->isEmpty($data['scene_id'], 1)){
                $this->message(L('qrcode_scene_value'). L('empty'), NULL, 2);
            }

            $rs = $this->model->table('wechat_qrcode')
                ->where(array('scene_id'=>$data['scene_id']))
                ->count();
            if ($rs > 0) {
                exit(json_encode(array(
                                'status' => 0,
                                'msg' => L('qrcode_scene_limit')
                                )));
            }
            $this->model->table('wechat_qrcode')
                ->data($data)
                ->insert();
            exit(json_encode(array(
                            'status' => 1
                            )));
        }
        $id = I('get.id', 0, 'intval');
        if (! empty($id)) {
            $status = I('get.status', 0, 'intval');
            $this->model->table('wechat_qrcode')
                ->data(array('status'=>$status))
                ->where(array('id'=>$id))
                ->update();
            $this->redirect(U('qrcode_list'));
        }
        $this->display();
    }

    /**
     * 扫码引荐
     */
    public function actionShareList()
    {
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('share_list', $filter), $this->page_num);
        $total = $this->model->query("SELECT count(*) as count FROM {pre}wechat_qrcode WHERE username is not null AND wechat_id = $this->wechat_id ORDER BY sort ASC");
        $list = $this->model->query("SELECT * FROM {pre}wechat_qrcode WHERE username is not null AND wechat_id = $this->wechat_id ORDER BY sort ASC");

        // 成交量
        if ($list) {
            foreach ($list as $key => $val) {
                $list[$key]['share_account'] = $this->model->table('affiliate_log')
                    ->field('sum(money)')
                    ->where(array('separate_type'=>0, 'user_id'=>$val['scene_id']))
                    ->one();
            }
        }
        $this->assign('page', $this->pageShow($total[0]['count']));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 编辑二维码
     */
    public function actionShareEdit()
    {
        if (IS_POST) {
            $data = I('post.data');
            $data['wechat_id'] = $this->wechat_id;
            // 验证数据
            $form = new \libraries\Form();
            if(!$form->isEmpty($data['username'], 1)){
                exit(json_encode(array(
                    'status' => 0,
                    'msg' => L('share_name'). L('empty')
                )));
            }
            if(!$form->isEmpty($data['scene_id'], 1)){
                exit(json_encode(array(
                    'status' => 0,
                    'msg' => L('share_userid'). L('empty')
                )));
            }

            $rs = $this->model->table('wechat_qrcode')
                ->where(array('scene_id'=>$data['scene_id']))
                ->count();
            if ($rs > 0) {
                exit(json_encode(array(
                                'status' => 0,
                                'msg' => L('qrcode_scene_limit')
                                )));
            }

            if (empty($data['expire_seconds'])) {
                $data['type'] = 1;
            } else {
                $data['type'] = 0;
            }
            $this->model->table('wechat_qrcode')
                ->data($data)
                ->insert();
            exit(json_encode(array(
                            'status' => 1
                            )));
        }
        $this->display();
    }

    /**
     * 删除二维码
     */
    public function actionQrcodeDel()
    {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->message(L('select_please') . L('qrcode'), NULL, 2);
        }
        $this->model->table('wechat_qrcode')
            ->where(array('id'=>$id))
            ->delete();
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('qrcode_list');
        $this->message(L('qrcode') . L('drop') . L('success'), $url);
    }

    /**
     * 更新并获取二维码
     */
    public function actionQrcodeGet()
    {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            exit(json_encode(array(
                            'status' => 0,
                            'msg' => L('select_please') . L('qrcode')
                            )));
        }
        $rs = $this->model->table('wechat_qrcode')
            ->field('type, scene_id, expire_seconds, qrcode_url, status')
            ->where(array('id'=>$id))
            ->find();
        if (empty($rs['status'])) {
            exit(json_encode(array(
                            'status' => 0,
                            'msg' => '二维码已禁用，请重新启用！'
                            )));
        }
        if (empty($rs['qrcode_url'])) {
            // 获取二维码ticket
            $ticket = $this->weObj->getQRCode((int) $rs['scene_id'], $rs['type'], $rs['expire_seconds']);
            if (empty($ticket)) {
                exit(json_encode(array(
                                'status' => 0,
                                'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
                                )));
            }
            $data['ticket'] = $ticket['ticket'];
            $data['expire_seconds'] = $ticket['expire_seconds'];
            $data['endtime'] = gmtime() + $ticket['expire_seconds'];
            // 二维码地址
            $qrcode_url = $this->weObj->getQRUrl($ticket['ticket']);
            $data['qrcode_url'] = $qrcode_url;

            $this->model->table('wechat_qrcode')
                ->data($data)
                ->where(array('id'=>$id))
                ->update();
        } else {
            $qrcode_url = $rs['qrcode_url'];
        }

        $this->assign('qrcode_url', $qrcode_url);
        $this->display();
    }

    /**
     * 图文回复(news)
     */
    public function actionArticle()
    {
        // 分页
        $filter['page'] = '{page}';
        $this->page_num = 15;
        $offset = $this->pageLimit(U('article', $filter), $this->page_num);
        $where['wechat_id'] = $this->wechat_id;
        $where['type'] = 'news';
        $total = $this->model->table('wechat_media')->where($where)->count();

        $list = $this->model->table('wechat_media')
            ->field('id, title, file, digest, content, add_time, sort, article_id')
            ->where($where)
            ->order('sort asc, add_time desc')
            ->limit($offset)
            ->select();
        foreach ((array) $list as $key => $val) {
            // 多图文
            if (! empty($val['article_id'])) {
                $id = explode(',', $val['article_id']);
                foreach ($id as $v) {
                    $list[$key]['articles'][] = $this->model->table('wechat_media')
                        ->field('id, title, file, add_time')
                        ->where(array('id'=>$v))
                        ->find();
                }
            }
            $list[$key]['content'] = empty($val['digest']) ? sub_str(strip_tags(html_out($val['content'])), 50) : $val['digest'];
        }
        $this->assign('page', $this->pageShow($total));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 图文回复编辑
     */
    public function actionArticleEdit()
    {
        if (IS_POST) {
            $id = I('post.id');
            $data = I('post.data');
            $data['content'] = I('post.content');
            $pic_path = I('post.file_path');


            $form = new \libraries\Form();
            if(!$form->isEmpty($data['title'], 1)){
                $this->message(L('title') . L('empty'), NULL, 2);
            }

            if(!$form->isEmpty($data['content'], 1)){
                $this->message(L('content') . L('empty'), NULL, 2);
            }
            /*if(!empty($data['link']) && !$form->isUrl($data['link'], 1)){
                $this->message(L('link_err'), NULL, 2);
            }*/
            // 封面处理
            if ($_FILES['pic']['name']) {
                $result = $this->ectouchUpload('pic', 'article');
                if ($result['error'] > 0) {
                    $this->message($result['message'], NULL, 2);
                }
                $data['file'] = str_replace(dirname(ROOT_PATH), '..', $result['message']['pic']['savepath']) . $result['message']['pic']['savename'];
                $data['file_name'] = $result['message']['pic']['name'];
                $data['size'] = $result['message']['pic']['size'];
            } else {
                $data['file'] = $pic_path;
            }
            if(!$form->isEmpty($data['file'], 1)){
                $this->message(L('please_upload'), NULL, 2);
            }
            $data['wechat_id'] = $this->wechat_id;
            $data['type'] = 'news';

            if (! empty($id)) {
                // 删除图片
                if ($pic_path != $data['file']) {
                    @unlink(dirname(ROOT_PATH) . $pic_path);
                }
                $data['edit_time'] = gmtime();
                $this->model->table('wechat_media')
                    ->data($data)
                    ->where(array('id'=>$id))
                    ->update();
            } else {
                $data['add_time'] = gmtime();
                $this->model->table('wechat_media')
                    ->data($data)
                    ->insert();
            }
            $this->message(L('wechat_editor') . L('success'), U('article'));
        }
        $id = I('get.id');
        if (! empty($id)) {
            $article = $this->model->table('wechat_media')
                ->where(array('id'=>$id))
                ->find();
            $this->assign('article', $article);
        }
        $this->display();
    }

    /**
     * 多图文回复编辑
     */
    public function actionArticleEditNews()
    {
        if (IS_POST) {
            $id = I('post.id');
            $article_id = I('post.article');
            $data['sort'] = I('post.sort');
            if (is_array($article_id)) {
                $data['article_id'] = implode(',', $article_id);
                $data['wechat_id'] = $this->wechat_id;
                $data['type'] = 'news';

                if (! empty($id)) {
                    $data['edit_time'] = gmtime();
                    $this->model->table('wechat_media')
                        ->data($data)
                        ->where(array('id'=>$id))
                        ->update();
                } else {
                    $data['add_time'] = gmtime();
                    $this->model->table('wechat_media')
                        ->data($data)
                        ->insert();
                }

                $this->redirect(U('article'));
            } else {
                $this->message('请重新添加', NULL, 2);
            }
        }
        $id = I('get.id');
        if (! empty($id)) {
            $rs = $this->model->table('wechat_media')
                ->field('article_id, sort')
                ->where(array('id'=>$id))
                ->find();
            if (! empty($rs['article_id'])) {
                $articles = array();
                $art = explode(',', $rs['article_id']);
                foreach ($art as $key => $val) {
                    $articles[] = $this->model->table('wechat_media')
                        ->field('id, title, file, add_time')
                        ->where(array('id'=>$val))
                        ->find();
                }
                $this->assign('articles', $articles);
            }
            $this->assign('sort', $rs['sort']);
        }

        $this->assign('id', $id);
        $this->display();
    }

    /**
     * 单图文列表供多图文选择
     */
    public function actionArticlesList()
    {
        // 分页
        $filter['page'] = '{page}';
        $this->page_num = 4;
        $offset = $this->pageLimit(U('articles_list', $filter), $this->page_num);
        $total = $this->model->query("SELECT count(*) as count  FROM {pre}wechat_media WHERE wechat_id =  $this->wechat_id  and type = 'news' and article_id is NULL");
        // 图文信息
        $article = $this->model->query("SELECT id, title, file, digest, content, add_time FROM {pre}wechat_media WHERE wechat_id =  $this->wechat_id  and type = 'news' and article_id is NULL ORDER BY sort ASC, add_time DESC limit $offset");
        if (! empty($article)) {
            foreach ($article as $k => $v) {
                $article[$k]['content'] = empty($v['digest']) ? sub_str(strip_tags(html_out($v['content'])), 50) : $v['digest'];
            }
        }

        $this->assign('page', $this->pageShow($total['count']));
        $this->assign('article', $article);
        $this->display();
    }

    /**
     * 多图文回复清空
     */
    public function actionArticleNewsDel()
    {
        $id = I('get.id');
        if (! empty($id)) {
            $this->model->table('wechat_media')
                ->data('article_id = 0')
                ->where(array('id'=>$id))
                ->update();
        }
        $this->redirect(U('article_edit_news'));
    }

    /**
     * 图文回复删除
     */
    public function actionArticleDel()
    {
        $id = I('get.id');
        $pic = $this->model->table('wechat_media')
            ->field('file')
            ->where(array('id'=>$id))
            ->one();
        if (empty($id)) {
            $this->message(L('select_please') . L('article'), NULL, 2);
        }
        $this->model->table('wechat_media')
            ->where(array('id'=>$id))
            ->delete();
        if (! empty($pic)) {
            @unlink(ROOT_PATH . $pic);
        }

        $this->redirect(U('article'));
    }

    /**
     * 图片管理(image)
     */
    public function actionPicture()
    {
        if (IS_POST) {
            if ($_FILES['pic']['name']) {
                $result = $this->ectouchUpload('pic', 'article');
                if ($result['error'] > 0) {
                    $this->message($result['message'], NULL, 2);
                }
                $data['file'] = str_replace(dirname(ROOT_PATH), '..', $result['message']['pic']['savepath']) . $result['message']['pic']['savename'];
                $data['thumb'] = str_replace(dirname(ROOT_PATH), '..', $result['message']['pic']['savepath']) . 'thumb_' . $result['message']['pic']['savename'];
                $data['file_name'] = $result['message']['pic']['name'];
                $data['size'] = $result['message']['pic']['size'];
                $data['type'] = 'image';
                $data['add_time'] = gmtime();
                $data['wechat_id'] = $this->wechat_id;

                $this->model->table('wechat_media')
                    ->data($data)
                    ->insert();

                $this->redirect(U('picture'));
            }
        }
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('picture', $filter), $this->page_num);

        $total = $this->model->query("SELECT count(*) as count FROM {pre}wechat_media WHERE wechat_id = $this->wechat_id and file is NOT NULL and (type = 'image' or type = 'news')");
        $list = $this->model->query("SELECT id, file, file_name, thumb, size FROM {pre}wechat_media WHERE wechat_id = $this->wechat_id and file is NOT NULL and (type = 'image' or type = 'news') order by add_time desc, sort asc limit $offset");
        if (empty($list)) {
            $list = array();
        }
        foreach ($list as $key => $val) {
            if ($val['size'] > (1024 * 1024)) {
                $list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
            } else {
                $list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
            }
        }
        $this->assign('page', $this->pageShow($total[0]['count']));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 语音
     */
    public function actionVoice()
    {
        if (IS_POST) {
            if ($_FILES['voice']['name']) {
                $result = $this->ectouchUpload('voice', 'voice');
                if ($result['error'] > 0) {
                    $this->message($result['message'], NULL, 2);
                }
                $data['file'] = str_replace(dirname(ROOT_PATH), '..', $result['message']['voice']['savepath']) . $result['message']['voice']['savename'];
                $data['file_name'] = $result['message']['voice']['name'];
                $data['size'] = $result['message']['voice']['size'];
                ;
                $data['type'] = 'voice';
                $data['add_time'] = gmtime();
                $data['wechat_id'] = $this->wechat_id;
                $this->model->table('wechat_media')
                    ->data($data)
                    ->insert();

                //$url = $_SERVER['HTTP_REFERER'];
                $url = U('voice');
                $this->redirect($url);
            }
        }
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('voice', $filter), $this->page_num);
        $total = $this->model->table('wechat_media')
            ->where(array('wechat_id'=>$this->wechat_id, 'type'=>'voice'))
            ->count();

        $list = $this->model->table('wechat_media')
            ->field('id, file, file_name, size')
            ->where(array('wechat_id'=>$this->wechat_id, 'type'=>'voice'))
            ->order('add_time desc, sort asc')
            ->limit($offset)
            ->select();
        if (empty($list)) {
            $list = array();
        }
        foreach ($list as $key => $val) {
            if ($val['size'] > (1024 * 1024)) {
                $list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
            } else {
                $list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
            }
        }
        $this->assign('page', $this->pageShow($total));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 视频
     */
    public function actionVideo()
    {
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('video', $filter), $this->page_num);
        $total = $this->model->table('wechat_media')
            ->where(array('wechat_id'=>$this->wechat_id, 'type'=>'video'))->count();

        $list = $this->model->table('wechat_media')
            ->field('id, file, file_name, size')
            ->where(array('wechat_id'=>$this->wechat_id, 'type'=>'video'))
            ->order('add_time desc, sort asc')
            ->limit($offset)
            ->select();
        if (empty($list)) {
            $list = array();
        }
        foreach ($list as $key => $val) {
            if ($val['size'] > (1024 * 1024)) {
                $list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
            } else {
                $list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
            }
        }

        $this->assign('page', $this->pageShow($total));
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 视频编辑
     */
    public function actionVideoEdit()
    {
        if (IS_POST) {
            $data = I('post.data');
            $id = I('post.id');

            if (empty($data['file']) || empty($data['file_name']) || empty($data['size'])) {
                $this->message('请上传视频', NULL, 2);
            }
            $size = round(($data['size'] / (1024 * 1024)), 1);
            if ($size > 5) {
                $this->message('视频大小超出最大限制，请重新上传', NULL, 2);
            }
            if (empty($data['title'])) {
                $this->message('请填写标题', NULL, 2);
            }
            $data['type'] = 'video';
            $data['wechat_id'] = $this->wechat_id;
            if (! empty($id)) {
                $data['edit_time'] = gmtime();
                $this->model->table('wechat_media')
                    ->data($data)
                    ->where(array('id'=>$id))
                    ->update();
            } else {
                $data['add_time'] = gmtime();
                $this->model->table('wechat_media')
                    ->data($data)
                    ->insert();
            }
             $this->message(L('upload_video') . L('success'), U('video'));
        }
        $id = I('get.id');
        if (! empty($id)) {
            $video = $this->model->table('wechat_media')
                ->field('id, file, file_name, size, title, content')
                ->where(array('id'=>$id))
                ->find();

            $this->assign('video', $video);
        }
        $this->display();
    }

    /**
     * 视频上传webuploader
     */
    public function actionVideoUpload()
    {
        if (IS_POST && ! empty($_FILES['file']['name'])) {
            $vid = I('post.vid');
            if (! empty($vid)) {
                $file = $this->model->table('wechat_media')
                    ->field('file')
                    ->where(array('id'=>$vid))
                    ->one();
                if (file_exists(dirname(ROOT_PATH) . $file)) {
                    @unlink(dirname(ROOT_PATH) . $file);
                }
            }
            $result = $this->ectouchUpload('file', 'video', 5);
            if ($result['error'] > 0) {
                $data['errcode'] = 1;
                $data['errmsg'] = $result['message'];
                echo json_encode($data);
                exit();
            }
            $data['errcode'] = 0;
            $data['file'] = str_replace(dirname(ROOT_PATH), '..', $result['message']['file']['savepath']) . $result['message']['file']['savename'];
            $data['file_name'] = $result['message']['file']['name'];
            $data['size'] = $result['message']['file']['size'];

            echo json_encode($data);
        }
    }

    /**
     * 素材编辑
     */
    public function actionMediaEdit()
    {
        if (IS_POST) {
            $id = I('post.id');
            $pic_name = I('post.file_name');
            $form = new \libraries\Form();
            if(!$form->isEmpty($id, 1)){
                $this->message('请选择', NULL, 2);
            }
            if(!$form->isEmpty($pic_name, 1)){
                $this->message('请输入名称', NULL, 2);
            }
            $data['file_name'] = $pic_name;
            $data['edit_time'] = gmtime();
            $num = $this->model->table('wechat_media')
                ->data($data)
                ->where(array('id'=>$id))
                ->update();

            exit(json_encode(array(
                            'status' => $num
                            )));
        }
        $id = I('get.id');
        $pic = $this->model->table('wechat_media')
            ->field('id, file_name')
            ->where(array('id'=>$id))
            ->find();
        if (empty($pic)) {
            $url = $_SERVER['HTTP_REFERER'];
            $this->redirect($url);
        }
        $this->assign('pic', $pic);
        $this->display();
    }

    /**
     * 素材删除
     */
    public function actionMediaDel()
    {
        $id = I('get.id');
        if (empty($id)) {
            $this->message('请选择', NULL, 2);
        }
        $pic = $this->model->table('wechat_media')
            ->field('file, thumb')
            ->where(array('id'=>$id))
            ->find();
        if (! empty($pic)) {
            $this->model->table('wechat_media')
                ->where(array('id'=>$id))
                ->delete();
        }
        if (file_exists(dirname(ROOT_PATH) . $pic['file'])) {
            @unlink(dirname(ROOT_PATH) . $pic['file']);
        }
        if (file_exists(dirname(ROOT_PATH) . $pic['thumb'])) {
            @unlink(dirname(ROOT_PATH) . $pic['thumb']);
        }
        $url = $_SERVER['HTTP_REFERER'];
        $this->redirect($url);
    }

    /**
     * 下载
     */
    public function actionDownload()
    {
        $id = I('get.id');
        $pic = $this->model->table('wechat_media')
            ->field('file, file_name')
            ->where(array('id'=>$id))
            ->find();
        $filename = dirname(ROOT_PATH) . $pic['file'];
        if (file_exists($filename)) {
            \libraries\Http::download($filename, $pic['file_name']);
        } else {
            $this->message('文件不存在', NULL, 2);
        }
    }

    /**
     * 群发消息列表
     */
    public function actionMassList()
    {
        // 分页
        $filter['page'] = '{page}';
        $offset = $this->pageLimit(U('mass_list', $filter), $this->page_num);
        $total = $this->model->table('wechat_mass_history')
            ->where(array('wechat_id'=>$this->wechat_id))
            ->count();
        $this->assign('page', $this->pageShow($total));

        $list = $this->model->table('wechat_mass_history')
            ->field('id, media_id, type, status, send_time, totalcount, sentcount, filtercount, errorcount')
            ->where(array('wechat_id'=>$this->wechat_id))
            ->order('send_time desc')
            ->limit($offset)
            ->select();
        foreach ((array) $list as $key => $val) {
            $media = $this->model->table('wechat_media')
                ->field('title, digest, content, file, article_id')
                ->where(array('id'=>$val['media_id']))
                ->find();
            if (! empty($media['article_id'])) {
                // 多图文
                $artids = explode(',', $media['article_id']);
                $artinfo = $this->model->table('wechat_media')
                    ->field('title, digest, content, file')
                    ->where(array('id'=>$artids[0]))
                    ->find();
            } else {
                $artinfo = $media;
            }
            if ('news' == $val['type']) {
                $artinfo['type'] = '图文消息';
            }
            $artinfo['content'] = empty($artinfo['digest']) ? sub_str(strip_tags(html_out($artinfo['content'])), 50) : $artinfo['digest'];
            $list[$key]['artinfo'] = $artinfo;
        }

        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 群发消息
     */
    public function actionMassMessage()
    {
        if (IS_POST) {
            $group_id = I('post.group_id', '', 'intval');
            $media_id = I('post.media_id');
            if ((empty($group_id) && $group_id !== 0) || empty($media_id)) {
                $this->message('请选择用户分组或者选择要发送的信息', NULL, 2);
            }

            $article = array();
            $article_info = $this->model->table('wechat_media')
                ->field('id, title, author, file, is_show, digest, content, link, type, article_id')
                ->where(array('id'=>$media_id))
                ->find();
            // 多图文
            if (! empty($article_info['article_id'])) {
                $articles = explode(',', $article_info['article_id']);
                foreach ($articles as $key => $val) {
                    $artinfo = $this->model->table('wechat_media')
                        ->field('title, author, file, is_show, digest, content, link')
                        ->where(array('id'=>$val))
                        ->find();
                    //$artinfo['content'] = strip_tags(html_out($artinfo['content']));
                    // 上传多媒体文件
                    $rs = $this->weObj->uploadMedia(array(
                                'media' => '@' . ROOT_PATH . $artinfo['file']
                                ), 'image');
                    if (empty($rs)) {
                        $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
                    }
                    // 重组数据
                    $article[$key]['thumb_media_id'] = $rs['media_id'];
                    $article[$key]['author'] = $artinfo['author'];
                    $article[$key]['title'] = $artinfo['title'];
                    $article[$key]['content_source_url'] = $artinfo['link'];
                    $article[$key]['content'] = html_out($artinfo['content']);
                    $article[$key]['digest'] = $artinfo['digest'];
                    $article[$key]['show_cover_pic'] = $artinfo['is_show'];
                }
            } else {
                // 单图文
                // 上传多媒体文件
                $rs = $this->weObj->uploadMedia(array(
                            'media' => '@' . ROOT_PATH . $article_info['file']
                            ), 'image');
                if (empty($rs)) {
                    $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
                }
                //$article_info['content'] = strip_tags(html_out($article_info['content']));
                // 重组数据
                $article[0]['thumb_media_id'] = $rs['media_id'];
                $article[0]['author'] = $article_info['author'];
                $article[0]['title'] = $article_info['title'];
                $article[0]['content_source_url'] = $article_info['link'];
                $article[0]['content'] = html_out($article_info['content']);
                $article[0]['digest'] = $article_info['digest'];
                $article[0]['show_cover_pic'] = $article_info['is_show'];
            }
            $article_list = array(
                    'articles' => $article
                    );
            // 图文消息上传
            $rs1 = $this->weObj->uploadArticles($article_list);
            if (empty($rs1)) {
                $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
            }
            // $rs1 = array('type'=>'image', 'media_id'=>'joUuDBc-9-sJp1U6vZpWYKiaS5XskqxJxGMm5HBf9q9Zs7DoKlSXVKUR3JIsfW_7', 'created_at'=>'1407482934');
            // 根据分组进行群发sendGroupMassMessage
            $massmsg = array(
                    'filter' => array(
                        'is_to_all' => false,
                        'group_id' => $group_id
                        ),
                    'mpnews' => array(
                        'media_id' => $rs1['media_id']
                        ),
                    'msgtype' => 'mpnews'
                    );
            $rs2 = $this->weObj->sendGroupMassMessage($massmsg);
            if (empty($rs2)) {
                $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
            }

            // 数据处理
            $msg_data['wechat_id'] = $this->wechat_id;
            $msg_data['media_id'] = $article_info['id'];
            $msg_data['type'] = $article_info['type'];
            $msg_data['send_time'] = gmtime();
            $msg_data['msg_id'] = $rs2['msg_id'];
            $id = $this->model->table('wechat_mass_history')
                ->data($msg_data)
                ->insert();

            $this->message('群发任务已启动，不过一般需要较长的时间才能全部发送完毕，请耐心等待', U('mass_message'));
        }
        // 分组信息
        $groups = $this->model->table('wechat_user_group')
            ->field('group_id, name')
            ->where(array('wechat_id'=> $this->wechat_id))
            ->order('group_id')
            ->select();
        // 图文信息
        $article = $this->model->table('wechat_media')
            ->field('id, title, file, content, article_id, add_time')
            ->where(array('wechat_id'=>$this->wechat_id, 'type'=>'news'))
            ->order('sort asc, add_time desc')
            ->select();
        foreach ((array) $article as $key => $val) {
            if (! empty($val['article_id'])) {
                $id = explode(',', $val['article_id']);
                foreach ($id as $v)
                    $article[$key]['articles'][] = $this->model->table('wechat_media')
                    ->field('id, title, file, add_time')
                    ->where(array('id'=>$v))
                    ->find();
            }
            $article[$key]['content'] = sub_str(strip_tags(html_out($val['content'])),100);
        }
        $this->assign('groups', $groups);
        $this->assign('article', $article);
        $this->display();
    }

    /**
     * 群发消息删除
     */
    public function actionMassDel()
    {
        $id = I('get.id');
        $msg_id = $this->model->table('wechat_mass_history')
            ->field('msg_id')
            ->where(array('id'=>$id))
            ->one();
        if (empty($msg_id)) {
            $this->message('消息不存在', NULL, 2);
        }
        $rs = $this->weObj->deleteMassMessage($msg_id);
        if (empty($rs)) {
            $this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 2);
        }

        $data['status'] = 'send success(已删除)';
        $this->model->table('wechat_mass_history')
            ->data($data)
            ->where(array('id'=>$id))
            ->update();
        $this->redirect(U('mass_list'));
    }

    /**
     * ajax获取图文信息
     */
    public function actionGetArticle()
    {
        if (IS_AJAX) {
            $data = I('post.article');
            $article = array();
            if (is_array($data)) {
                $id = implode(',', $data);
                $article = $this->model->query("SELECT id, title, file, link, digest, content, add_time FROM {pre}wechat_media WHERE id in ($id) ORDER BY sort asc, add_time desc");
                foreach ($article as $key => $val) {
                    $article[$key]['add_time'] = date('Y年m月d日', $val['add_time']);
                    $article[$key]['content'] = empty($val['digest']) ? sub_str(strip_tags(html_out($val['content'])), 50) : $val['digest'];
                }
            }
            echo json_encode($article);
        }
    }

    /**
     * 自动回复
     */
    public function actionAutoReply()
    {
        // 素材数据
        $type = I('get.type');
        if (! empty($type)) {
            // 分页
            $filter['page'] = '{page}';
            $filter['type'] = $type;
            $offset = $this->pageLimit(U('auto_reply', $filter), $this->page_num);
            if ('image' == $type) {
                $where = 'wechat_id = ' . $this->wechat_id . ' and file is NOT NULL and (type = "image" or type="news")';
                $list = $this->model->query('SELECT id, file, file_name, size, add_time, type FROM {pre}wechat_media WHERE '.$where.' ORDER BY add_time DESC limit '.$offset);
            } elseif ('voice' == $type) {
                $where = 'wechat_id = ' . $this->wechat_id . ' and file is NOT NULL and type = "voice"';
                $list = $this->model->query('SELECT id, file, file_name, size, add_time, type FROM {pre}wechat_media WHERE '.$where.' ORDER BY add_time DESC limit '.$offset);
            } elseif ('video' == $type) {
                $where = 'wechat_id = ' . $this->wechat_id . ' and file is NOT NULL and type = "video"';
                $list = $this->model->query('SELECT id, file, file_name, size, add_time, type FROM {pre}wechat_media WHERE '.$where.' ORDER BY add_time DESC limit '.$offset);
            } elseif ('news' == $type) {
                //$offset = $this->pageLimit(U('auto_reply', $filter), 6);
                // 只显示单图文
                $no_list = I('get.no_list', 0, 'intval');
                $this->assign('no_list', $no_list);
                if (! empty($no_list)) {
                    $where = 'wechat_id = ' . $this->wechat_id . ' and type="news" and article_id is NULL';
                } else {
                    $where = 'wechat_id = ' . $this->wechat_id . ' and type="news"';
                }
                $list = $this->model->query('SELECT id, title, file, file_name, size, digest, content, add_time, type, article_id FROM {pre}wechat_media WHERE '.$where.' ORDER BY add_time DESC limit '. $offset);
                foreach ((array) $list as $key => $val) {
                    if (! empty($val['article_id'])) {
                        $id = explode(',', $val['article_id']);
                        foreach ($id as $v)
                            $list[$key]['articles'][] = $this->model->table('wechat_media')
                            ->field('id, title, digest, file, add_time')
                            ->where(array('id'=>$v))
                            ->find();
                    }
                    $list[$key]['content'] = empty($val['digest']) ? sub_str(strip_tags(html_out($val['content'])), 50) : $val['digest'];
                }
            }
            foreach ((array) $list as $key => $val) {
                if ($val['size'] > (1024 * 1024)) {
                    $list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
                } else {
                    $list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
                }
            }

            $total = $this->model->query("SELECT count(*) as count FROM {pre}wechat_media WHERE $where ORDER BY add_time DESC");
              foreach ($total as $key => $value) {
                $num = $value['count'];
            }
            $this->assign('page', $this->pageShow($num));
            $this->assign('list', $list);
            $this->assign('type', $type);
            $this->display();
        }
    }


    /**
     * 关注回复(subscribe)
     */
    public function actionReplySubscribe()
    {
        if (IS_POST) {
            $content_type = I('post.content_type');
            if ($content_type == 'text') {
                $data['content'] = I('post.content');
                $data['media_id'] = 0;
            } else {
                $data['media_id'] = I('post.media_id');
                $data['content'] = '';
            }
            $data['type'] = 'subscribe';
            if (is_array($data) && (! empty($data['media_id']) || ! empty($data['content']))) {
                $where['type'] = $data['type'];
                $where['wechat_id'] = $this->wechat_id;
                $id = $this->model->table('wechat_reply')
                    ->field('id')
                    ->where($where)
                    ->one();
                if (! empty($id)) {
                    $this->model->table('wechat_reply')
                        ->data($data)
                        ->where($where)
                        ->update();
                } else {
                    $data['wechat_id'] = $this->wechat_id;
                    $this->model->table('wechat_reply')
                        ->data($data)
                        ->insert();
                }
                $this->message(L('wechat_editor') . L('success'), U('reply_subscribe'));
            } else {
                $this->message('请填写内容', NULL, 2);
            }
        }
        // 自动回复数据
        $subscribe = $this->model->table('wechat_reply')
            ->where(array('type'=>'subscribe', 'wechat_id'=>$this->wechat_id))
            ->find();
        if (! empty($subscribe['media_id'])) {
            $subscribe['media'] = $this->model->table('wechat_media')
                ->field('file, type, file_name')
                ->where(array('id'=>$subscribe['media_id']))
                ->find();
        }
        $this->assign('subscribe', $subscribe);
        $this->display();
    }

    /**
     * 消息回复(msg)
     */
    public function actionReplyMsg()
    {
        if (IS_POST) {
            $content_type = I('post.content_type');
            if ($content_type == 'text') {
                $data['content'] = I('post.content');
                $data['media_id'] = 0;
            } else {
                $data['media_id'] = I('post.media_id');
                $data['content'] = '';
            }
            $data['type'] = 'msg';
            if (is_array($data)) {
                $where['type'] = $data['type'];
                $where['wechat_id'] = $this->wechat_id;
                $id = $this->model->table('wechat_reply')
                    ->field('id')
                    ->where($where)
                    ->one();
                if (! empty($id)) {
                    $this->model->table('wechat_reply')
                        ->data($data)
                        ->where($where)
                        ->update();
                } else {
                    $data['wechat_id'] = $this->wechat_id;
                    $this->model->table('wechat_reply')
                        ->data($data)
                        ->insert();
                }
                $this->message(L('wechat_editor') . L('success'), U('reply_msg'));
            } else {
                $this->message('请填写内容', NULL, 2);
            }
        }
        // 自动回复数据
        $msg = $this->model->table('wechat_reply')
            ->where(array('type'=>'msg', 'wechat_id'=>$this->wechat_id))
            ->find();
        if (! empty($msg['media_id'])) {
            $msg['media'] = $this->model->table('wechat_media')
                ->field('file, type, file_name')
                ->where(array('id'=>$msg['media_id']))
                ->find();
        }
        $this->assign('msg', $msg);
        $this->display();
    }

    /**
     * 关键词自动回复
     */
    public function actionReplyKeywords()
    {
        $list = $this->model->table('wechat_reply')
            ->field('id, rule_name, content, media_id, reply_type')
            ->where(array('type'=>'keywords', 'wechat_id'=>$this->wechat_id))
            ->order('add_time desc')
            ->select();
        foreach ((array) $list as $key => $val) {
            // 内容不是文本
            if (! empty($val['media_id'])) {
                $media = $this->model->table('wechat_media')
                    ->field('title, file, file_name, type, digest, content, add_time, article_id')
                    ->where(array('id'=>$val['media_id']))
                    ->find();
                $media['content'] = empty($media['digest']) ? sub_str(strip_tags(html_out($media['content'])), 50) : $media['digest'];
                if (! empty($media['article_id'])) {
                    $artids = explode(',', $media['article_id']);
                    foreach ($artids as $v) {
                        $list[$key]['medias'][] = $this->model->table('wechat_media')
                            ->field('title, file, add_time')
                            ->where(array('id'=>$v))
                            ->find();
                    }
                } else {
                    $list[$key]['media'] = $media;
                }
            }
            $keywords = $this->model->table('wechat_rule_keywords')
                ->field('rule_keywords')
                ->where(array('rid'=>$val['id']))
                ->order('id desc')
                ->select();
            $list[$key]['rule_keywords'] = $keywords;
            // 编辑关键词时显示
            if (! empty($keywords)) {
                $rule_keywords = array();
                foreach ($keywords as $k => $v) {
                    $rule_keywords[] = $v['rule_keywords'];
                }
                $rule_keywords = implode(',', $rule_keywords);
                $list[$key]['rule_keywords_string'] = $rule_keywords;
            }
        }
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 关键词回复添加规则
     */
    public function actionRuleEdit()
    {
        if (IS_POST) {
            $id = I('post.id');
            $content_type = I('post.content_type');
            $rule_keywords = I('post.rule_keywords');
            // 主表数据
            $data['rule_name'] = I('post.rule_name');
            $data['media_id'] = I('post.media_id');
            $data['content'] = I('post.content');
            $data['reply_type'] = $content_type;
            if ($content_type == 'text') {
                $data['media_id'] = 0;
            } else {
                $data['content'] = '';
            }

            $form = new \libraries\Form();
            if(!$form->isEmpty($data['rule_name'], 1)){
                $this->message('请填写规则名称', NULL, 2);
            }
            if(!$form->isEmpty($rule_keywords, 1)){
                $this->message('请至少填写1个关键词', NULL, 2);
            }
            if (empty($data['content']) && empty($data['media_id'])) {
                $this->message('请填写或选择回复内容', NULL, 2);
            }
            $data['type'] = 'keywords';
            if (! empty($id)) {
                $this->model->table('wechat_reply')
                    ->data($data)
                    ->where(array('id'=>$id))
                    ->update();
                $this->model->table('wechat_rule_keywords')
                    ->where(array('rid'=>$id))
                    ->delete();
            } else {
                $data['add_time'] = gmtime();
                $data['wechat_id'] = $this->wechat_id;
                $id = $this->model->table('wechat_reply')
                    ->data($data)
                    ->insert();
            }
            // 编辑关键词
            $rule_keywords = explode(',', $rule_keywords);
            foreach ($rule_keywords as $val) {
                $kdata['rid'] = $id;
                $kdata['rule_keywords'] = $val;
                $this->model->table('wechat_rule_keywords')
                    ->data($kdata)
                    ->insert();
            }
            $this->message(L('wechat_editor') . L('success'), U('reply_keywords'));
        }
    }

    /**
     * 关键词回复规则删除
     */
    public function actionReplyDel()
    {
        $id = I('get.id');
        if (empty($id)) {
            $this->message('请选择', NULL, 2);
        }
        $this->model->table('wechat_reply')
            ->where(array('id'=>$id))
            ->delete();
        $this->redirect(U('reply_keywords'));
    }

    /**
     * 素材管理
     */
    public function actionMediaList()
    {
        $this->display();
    }

    /**
     * 提醒设置
     */
    public function actionRemind()
    {
        if (IS_POST) {
            $command = I('post.command');
            $data = I('post.data');
            $config = I('post.config');
            $info = Check::rule(array(
                        Check::must($command),
                        '关键词不正确'
                        ));
            if ($info !== true) {
                $this->message($info, NULL, 2);
            }
            if (! empty($config)) {
                $data['config'] = serialize($config);
            }
            $data['wechat_id'] = $this->wechat_id;
            $num = $this->model->table('wechat_extend')
                ->where('command = "' . $command . '" and wechat_id = ' . $this->wechat_id)
                ->count();
            if ($num > 0) {
                $this->model->table('wechat_extend')
                    ->data($data)
                    ->where('command = "' . $command . '" and wechat_id = ' . $this->wechat_id)
                    ->update();
            } else {
                $data['command'] = $command;
                $this->model->table('wechat_extend')
                    ->data($data)
                    ->insert();
            }

            $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $order_remind = $this->model->table('wechat_extend')
            ->field('name, enable, config')
            ->where('command = "order_remind" and wechat_id = ' . $this->wechat_id)
            ->find();
        if ($order_remind['config']) {
            $order_remind['config'] = unserialize($order_remind['config']);
        }
        $pay_remind = $this->model->table('wechat_extend')
            ->field('name, enable, config')
            ->where('command = "pay_remind" and wechat_id = ' . $this->wechat_id)
            ->find();
        if ($pay_remind['config']) {
            $pay_remind['config'] = unserialize($pay_remind['config']);
        }
        $send_remind = $this->model->table('wechat_extend')
            ->field('name, enable, config')
            ->where('command = "send_remind" and wechat_id = ' . $this->wechat_id)
            ->find();
        if ($send_remind['config']) {
            $send_remind['config'] = unserialize($send_remind['config']);
        }
        $register_remind = $this->model->table('wechat_extend')
            ->field('name, enable, config')
            ->where('command = "register_remind" and wechat_id = ' . $this->wechat_id)
            ->find();
        if ($register_remind['config']) {
            $register_remind['config'] = unserialize($register_remind['config']);
        }
        $this->assign('order_remind', $order_remind);
        $this->assign('pay_remind', $pay_remind);
        $this->assign('send_remind', $send_remind);
        $this->assign('register_remind', $register_remind);
        $this->display();
    }

    /**
     * 多客服设置
     */
    public function actionCustomerService()
    {
        $command = 'kefu';
        if (IS_POST) {
            $data = I('post.data');
            $config = I('post.config');

            if (! empty($config)) {
                $data['config'] = serialize($config);
            }
            $num = $this->model->table('wechat_extend')
                ->where(array('command'=>$command, 'wechat_id'=>$this->wechat_id))
                ->count();
            if ($num > 0) {
                $this->model->table('wechat_extend')
                    ->data($data)
                    ->where(array('command'=>$command, 'wechat_id'=>$this->wechat_id))
                    ->update();
            } else {
                $data['wechat_id'] = $this->wechat_id;
                $data['command'] = $command;
                $data['name'] = '多客服';
                $this->model->table('wechat_extend')
                    ->data($data)
                    ->insert();
            }

            $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $customer_service = $this->model->table('wechat_extend')
            ->field('name, enable, config')
            ->where(array('command'=>$command, 'wechat_id'=>$this->wechat_id))
            ->find();
        if ($customer_service['config']) {
            $customer_service['config'] = unserialize($customer_service['config']);
        }
        $this->assign('customer_service', $customer_service);
        $this->display();
    }

    /**
     * 添加多客服
     */
    public function actionAddKf(){
        $account = 'test@gh_1ca465561479';
        $nickname = 'test';
        $password = '123123';
        $rs = $this->weObj->addKFAccount($account, $nickname, $password);
        echo $this->weObj->errMsg;
        dump($rs);


    }

    /**
     * 模板消息
     */
    public function actionTemplate()
    {
        $list = $this->model->table('wechat_template')->order('id desc')->select();
        if ($list) {
            foreach ($list as $key => $val) {
                $list[$key]['add_time'] = local_date('Y-m-d H:i:s', $val['add_time']);
            }
        }
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 编辑模板消息
     */
    public function actionEditTemplate(){
        if(IS_AJAX){
            $id = I('post.id');
            $data = I('post.data');
            if(empty($data['title'])){
                exit(json_encode(array('status'=>0, 'msg'=>'模板消息标题或者模板编号不能为空')));
            }
            $un_code = $this->model->table('wechat_template')->where(array('code'=>$data['code']))->count();
            if($id){
                if(un_code > 1){
                    exit(json_encode(array('status'=>0, 'msg'=>'模板消息编号已存在,必须唯一')));
                }
                $this->db->table('wechat_template')->data($data)->where(array('id'=>$id))->update();
            }
            else{
                if(un_code > 0){
                    exit(json_encode(array('status'=>0, 'msg'=>'模板消息编号已存在,必须唯一')));
                }
                $data['add_time'] = gmtime();
                $data['status'] = 0;
                $data['wechat_id'] = $this->wechat_id;
                $id = $this->db->table('wechat_template')->data($data)->insert();
            }
            if($id){
                exit(json_encode(array('status'=>1)));
            }
            else{
                exit(json_encode(array('status'=>0, 'msg'=>'添加失败')));
            }
        }
        $id = I('get.id');
        if($id){
            $template = $this->model->table('wechat_template')->where(array('id'=>$id))->find();
            $this->assign('template', $template);
        }

        $this->display();
    }

    /**
     * 开关按钮
     */
    public function actionSwitch()
    {
        $id = I('get.id',0, 'intval');
        $status = I('get.status',0, 'intval');
        if(empty($id)){
            $this->message('请选择模板消息', NULL, 2);
        }
        //开启模板消息
        if(1 == $status){
            $template = $this->model->table('wechat_template')->field('template_id, code')->where(array('id'=>$id))->find();
            if(empty($template['template_id'])){
                $template_id = $this->weObj->addTemplateMessage($template['code']);
                if(!$template_id){
                    $this->message($this->weObj->errMsg, NULL, 2);
                }
                $this->model->table('wechat_template')->data(array('template_id'=>$template_id))->where(array('id'=>$id))->update();
            }
        }
        $this->model->table('wechat_template')->data(array('status'=>$status))->where(array('id'=>$id))->update();
        $this->redirect(U('template'));
    }

    /**
     * 获取配置信息
     */
    private function get_config()
    {
        $without = array(
                'index',
                'append',
                'modify',
                'delete',
                'set_default'
                );
        if (! in_array(ACTION_NAME, $without)) {
            $id = $this->wechat_id; // I('get.wechat_id', 0, 'intval');
            if (! empty($id)) {
                session('wechat_id', $id);
            } else {
                $id = session('wechat_id') ? session('wechat_id') : 0;
            }

            $status = $this->model->table('wechat')->field('status')->where(array('id'=>$id))->one();
            if (empty($status)) {
                $this->message(L('open_wechat'), U('wechat/admin/modify'), 2);
            }
            $this->wechat_id = session('wechat_id');
            if (! empty($this->wechat_id)) {
                // 公众号配置信息
                $where['id'] = $this->wechat_id;
                $wechat = $this->model->table('wechat')->field('token, appid, appsecret, type')->where($where)->find();
                if (empty($wechat)) {
                    $wechat = array();
                }
                $config = array();
                $config['token'] = $wechat['token'];
                $config['appid'] = $wechat['appid'];
                $config['appsecret'] = $wechat['appsecret'];

                $this->weObj = new \ectouch\wechat\Wechat($config);
                $this->assign('type', $wechat['type']);
            }
        }
    }
}
