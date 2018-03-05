<?php
namespace http\wechat\controllers;

use http\base\controllers\FrontendController;

class IndexController extends FrontendController
{
    private $weObj = '';
    private $orgid = '';
    private $wechat_id = 1;

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        // 获取公众号配置
        $this->orgid = I('get.orgid', '');
        if ($this->orgid) {
            $this->load_helper('passport');
            $wxinfo = $this->get_config($this->orgid);
            $config['token'] = $wxinfo['token'];
            $config['appid'] = $wxinfo['appid'];
            $config['appsecret'] = $wxinfo['appsecret'];
            $config['encodingaeskey'] = $wxinfo['encodingaeskey'];
            $config['debug'] = APP_DEBUG;
            $this->weObj = new \ectouch\wechat\Wechat($config);
            $this->weObj->valid();
        }
    }

    /**
     * 执行方法
     */
    public function actionIndex()
    {
        // 事件类型
        $type = $this->weObj->getRev()->getRevType();
        $wedata = $this->weObj->getRev()->getRevData();
        $keywords = '';
        if ($type == \ectouch\wechat\Wechat::MSGTYPE_TEXT) {
            $keywords = $wedata['Content'];
        } elseif ($type == \ectouch\wechat\Wechat::MSGTYPE_EVENT) {
            if ('subscribe' == $wedata['Event']) {
                $scene_id = 0;
                // 用户扫描带参数二维码(未关注)
                if (isset($wedata['Ticket']) && !empty($wedata['Ticket'])) {
                    $scene_id = $this->weObj->getRevSceneId();
                    $flag = true;
                    // 关注
                    $this->subscribe($wedata['FromUserName'], $scene_id);
                    // 关注时回复信息
                    $this->msg_reply('subscribe');
                } else {
                    // 关注
                    $this->subscribe($wedata['FromUserName']);
                    // 关注时回复信息
                    $this->msg_reply('subscribe');
                }
            } elseif ('unsubscribe' == $wedata['Event']) {
                // 取消关注
                $this->unsubscribe($wedata['FromUserName']);
                exit();
            } elseif ('MASSSENDJOBFINISH' == $wedata['Event']) {
                // 群发结果
                $data['status'] = $wedata['Status'];
                $data['totalcount'] = $wedata['TotalCount'];
                $data['filtercount'] = $wedata['FilterCount'];
                $data['sentcount'] = $wedata['SentCount'];
                $data['errorcount'] = $wedata['ErrorCount'];
                // 更新群发结果
                $this->db->table('wechat_mass_history')
                    ->data($data)
                    ->where(array('msg_id' => $wedata['MsgID']))
                    ->update();
                exit();
            } elseif ('CLICK' == $wedata['Event']) {
                // 点击菜单
                $keywords = $wedata['EventKey'];
            } elseif ('VIEW' == $wedata['Event']) {
                $this->redirect($wedata['EventKey']);
            } elseif ('SCAN' == $wedata['Event']) {
                $scene_id = $this->weObj->getRevSceneId();
            } elseif ('kf_create_session' == $wedata['Event']) {
                //多客服接入
            } elseif ('kf_close_session' == $wedata['Event']) {
                //多客服关闭
            } elseif ('kf_switch_session' == $wedata['Event']) {
                //多客服转接
            }
        } else {
            $this->msg_reply('msg');
            exit();
        }
        //扫描二维码
        if (!empty($scene_id)) {
            $qrcode_fun = model()->table('wechat_qrcode')->field('function')->where(array('scene_id' => $scene_id))->one();
            //扫码引荐
            if (isset($flag)) {
                //增加扫描量
                $this->db->query("UPDATE {pre}wechat_qrcode SET scan_num = scan_num + 1 WHERE scene_id = $scene_id");
            }
            $keywords = $qrcode_fun;
        }
        // 回复
        if (!empty($keywords)) {
            //记录用户操作信息
            $this->record_msg($wedata['FromUserName'], $keywords);
            // 多客服
            $rs = $this->customer_service($wedata['FromUserName'], $keywords);
            if (empty($rs)) {
                // 功能插件
                $rs1 = $this->get_function($wedata['FromUserName'], $keywords);
                if (empty($rs1)) {
                    // 关键词回复
                    $rs2 = $this->keywords_reply($keywords);
                    if (empty($rs2)) {
                        // 消息自动回复
                        $this->msg_reply('msg');
                    }
                }
            }
        }
    }

    /**
     * 关注处理
     *
     * @param array $info
     */
    private function subscribe($openid = '', $scene_id = 0)
    {
        if (!empty($openid)) {
            // 获取用户信息
            $info = $this->weObj->getUserInfo($openid);
            if (empty($info)) {
                exit('null');
            }
            // 组合数据
            $data['wechat_id'] = $this->wechat_id;
            $data['subscribe'] = $info['subscribe'];
            $data['openid'] = $info['openid'];
            $data['nickname'] = $info['nickname'];
            $data['sex'] = $info['sex'];
            $data['language'] = $info['language'];
            $data['city'] = $info['city'];
            $data['province'] = $info['province'];
            $data['country'] = $info['country'];
            $data['headimgurl'] = $info['headimgurl'];
            $data['subscribe_time'] = $info['subscribe_time'];
            $data['remark'] = $info['remark'];
            $data['groupid'] = isset($info['groupid']) ? $info['groupid'] : $this->weObj->getUserGroup($openid);
            $data['unionid'] = isset($info['unionid']) ? $info['unionid'] : '';
            // 公众号启用微信开发者平台，检查unionid
            $unionmode = empty($data['unionid']) ? false : true;
            $identify = 'wechat_' . ($unionmode ? $data['unionid'] : $data['openid']);
            // 已关注用户基本信息
            $condition = array('openid' => $data['openid']);
            $result = $this->db->table('wechat_user')->field('ect_uid, unionid')->where($condition)->find();
            // 查找用户是否存在
            if (isset($result['ect_uid'])) {
                $users = $this->db->table("users")->where(array('user_id' => $result['ect_uid']))->find();
                if (empty($users) || empty($result['ect_uid'])) {
                    $this->db->table('wechat_user')->where($condition)->delete();
                    $result = array();
                }
            }
            // 未关注
            if (empty($result)) {
                // 其他平台是否注册
                $condition = array('aite_id' => $identify);
                $userinfo = $this->db->table('users')->field('user_id, parent_id')->where($condition)->find();
                if(empty($userinfo)){
                    // 设置的用户注册信息
                    $username = substr(md5($identify), -2) . time() . rand(100, 999);
                    $password = mt_rand(100000, 999999);
                    $email = $username . '@' . get_top_domain();
                    // 推荐人ID
                    if (!empty($scene_id)) {
                        $scene_user = $this->db->table("users")->field('user_id')->where(array('user_id' => $scene_id))->find();
                        $scene_id = empty($scene_user) ? 0 : $scene_user['user_id'];
                    }
                    // 用户注册
                    $extend = array(
                        'parent_id' => $scene_id,
                        'nick_name' => $data['nickname'],
                        'aite_id' => $identify,
                        'sex' => $data['sex'],
                        'user_picture' => $data['headimgurl']
                    );
                    if (!register($username, $password, $email, $extend) !== false) {
                        exit('null');
                    }
                    // 注册微信资料
                    $data['ect_uid'] = $_SESSION['user_id'];
                    $data['parent_id'] = $scene_id;
                }else{
                    // 更新微信资料
                    $data['ect_uid'] = $userinfo['user_id'];
                    $data['parent_id'] = $userinfo['parent_id'];
                }
                $this->db->table('wechat_user')->data($data)->insert();
                // 新用户红包优惠
                $data1['user_id'] = $_SESSION['user_id'];
                $bonus_num = $this->db->table('user_bonus')->where($data1)->count();
                if ($bonus_num <= 0) {
                    $content = $this->send_message($openid, 'bonus', $this->weObj, 1);
                    $bonus_msg = empty($content) ? '' : $content['content'];
                    if (!empty($bonus_msg)) {
                        // 微信端发送消息
                        $msg = array(
                            'touser' => $openid,
                            'msgtype' => 'text',
                            'text' => array(
                                'content' => $bonus_msg
                            )
                        );
                        $this->weObj->sendCustomMessage($msg);
                    }
                }
            } else {
                // 同步微信资料
                $condition = array('openid' => $data['openid']);
                $this->db->table('wechat_user')->data($data)->where($condition)->update();
            }
        }
    }

    /**
     * 取消关注
     *
     * @param string $openid
     */
    public function unsubscribe($openid = '')
    {
        // 未关注
        $where['openid'] = $openid;
        $rs = $this->db->table('wechat_user')->where($where)->count();
        // 修改关注状态
        if ($rs > 0) {
            $data['subscribe'] = 0;
            $this->db->table('wechat_user')->data($data)->where($where)->update();
        }
    }

    /**
     * 被动关注，消息回复
     *
     * @param string $type
     * @param string $return
     */
    private function msg_reply($type, $return = 0)
    {
        $replyInfo = $this->db->table('wechat_reply')
            ->field('content, media_id')
            ->where(array('type' => $type, 'wechat_id' => $this->wechat_id))
            ->find();
        if (!empty($replyInfo)) {
            if (!empty($replyInfo['media_id'])) {
                $replyInfo['media'] = $this->db->table('wechat_media')
                    ->field('title, content, file, type, file_name')
                    ->where(array('id' => $replyInfo['media_id']))
                    ->find();
                if ($replyInfo['media']['type'] == 'news') {
                    $replyInfo['media']['type'] = 'image';
                }
                // 上传多媒体文件
                $rs = $this->weObj->uploadMedia(array(
                    'media' => '@' . ROOT_PATH . $replyInfo['media']['file']
                ), $replyInfo['media']['type']);
                // 回复数据重组
                if ($rs['type'] == 'image' || $rs['type'] == 'voice') {
                    $replyData = array(
                        'ToUserName' => $this->weObj->getRev()->getRevFrom(),
                        'FromUserName' => $this->weObj->getRev()->getRevTo(),
                        'CreateTime' => time(),
                        'MsgType' => $rs['type'],
                        ucfirst($rs['type']) => array(
                            'MediaId' => $rs['media_id']
                        )
                    );
                } elseif ('video' == $rs['type']) {
                    $replyData = array(
                        'ToUserName' => $this->weObj->getRev()->getRevFrom(),
                        'FromUserName' => $this->weObj->getRev()->getRevTo(),
                        'CreateTime' => time(),
                        'MsgType' => $rs['type'],
                        ucfirst($rs['type']) => array(
                            'MediaId' => $rs['media_id'],
                            'Title' => $replyInfo['media']['title'],
                            'Description' => strip_tags($replyInfo['media']['content'])
                        )
                    );
                }
                if ($return) {
                    return array('type' => 'media', 'content' => $replyData);
                }
                $this->weObj->reply($replyData);
                //记录用户操作信息
                $this->record_msg($this->weObj->getRev()->getRevTo(), '图文信息', 1);
            } else {
                // 文本回复
                $replyInfo['content'] = html_out($replyInfo['content']);
                if ($return) {
                    return array('type' => 'text', 'content' => $replyInfo['content']);
                }
                $this->weObj->text($replyInfo['content'])->reply();
                //记录用户操作信息
                $this->record_msg($this->weObj->getRev()->getRevTo(), $replyInfo['content'], 1);
            }
        }
    }

    /**
     * 关键词回复
     *
     * @param string $keywords
     * @return boolean
     */
    private function keywords_reply($keywords)
    {
        $endrs = false;
        $sql = 'SELECT r.content, r.media_id, r.reply_type FROM {pre}wechat_reply r LEFT JOIN {pre}wechat_rule_keywords k ON r.id = k.rid WHERE k.rule_keywords = "' . $keywords . '" and r.wechat_id = ' . $this->wechat_id . ' order by r.add_time desc LIMIT 1';
        $result = $this->db->query($sql);
        if (!empty($result)) {
            // 素材回复
            if (!empty($result[0]['media_id'])) {
                $mediaInfo = $this->db->table('wechat_media')
                    ->field('id, title, digest, content, file, type, file_name, article_id, link')
                    ->where(array('id' => $result[0]['media_id']))
                    ->find();
                // 回复数据重组
                if ($result[0]['reply_type'] == 'image' || $result[0]['reply_type'] == 'voice') {
                    // 上传多媒体文件
                    $rs = $this->weObj->uploadMedia(array(
                        'media' => '@' . ROOT_PATH . $mediaInfo['file']
                    ), $result[0]['reply_type']);
                    $replyData = array(
                        'ToUserName' => $this->weObj->getRev()->getRevFrom(),
                        'FromUserName' => $this->weObj->getRev()->getRevTo(),
                        'CreateTime' => time(),
                        'MsgType' => $rs['type'],
                        ucfirst($rs['type']) => array(
                            'MediaId' => $rs['media_id']
                        )
                    );
                    // 回复
                    $this->weObj->reply($replyData);
                    $endrs = true;
                } elseif ('video' == $result[0]['reply_type']) {
                    // 上传多媒体文件
                    $rs = $this->weObj->uploadMedia(array(
                        'media' => '@' . ROOT_PATH . $mediaInfo['file']
                    ), $result[0]['reply_type']);
                    $replyData = array(
                        'ToUserName' => $this->weObj->getRev()->getRevFrom(),
                        'FromUserName' => $this->weObj->getRev()->getRevTo(),
                        'CreateTime' => time(),
                        'MsgType' => $rs['type'],
                        ucfirst($rs['type']) => array(
                            'MediaId' => $rs['media_id'],
                            'Title' => $replyInfo['media']['title'],
                            'Description' => strip_tags($replyInfo['media']['content'])
                        )
                    );
                    // 回复
                    $this->weObj->reply($replyData);
                    $endrs = true;
                } elseif ('news' == $result[0]['reply_type']) {
                    // 图文素材
                    $articles = array();
                    if (!empty($mediaInfo['article_id'])) {
                        $artids = explode(',', $mediaInfo['article_id']);
                        foreach ($artids as $key => $val) {
                            $artinfo = $this->db->table('wechat_media')
                                ->field('id, title, file, digest, content, link')
                                ->where(array('id' => $val))
                                ->find();
                            $artinfo['content'] = sub_str(strip_tags(html_out($artinfo['content'])), 100);
                            $articles[$key]['Title'] = $artinfo['title'];
                            $articles[$key]['Description'] = empty($artinfo['digest']) ? $artinfo['content'] : $artinfo['digest'];
                            $articles[$key]['PicUrl'] = dirname(__URL__) . str_replace('..', '', $artinfo['file']);
                            $articles[$key]['Url'] = empty($artinfo['link']) ? U('article/index/wechat_news_info', array('id' => $artinfo['id']), true) : strip_tags(html_out($artinfo['link']));
                        }
                    } else {
                        $articles[0]['Title'] = $mediaInfo['title'];
                        $articles[0]['Description'] = empty($mediaInfo['digest']) ? sub_str(strip_tags(html_out($mediaInfo['content'])), 100) : $mediaInfo['digest'];
                        $articles[0]['PicUrl'] = dirname(__URL__) . str_replace('..', '', $mediaInfo['file']);
                        $articles[0]['Url'] = empty($mediaInfo['link']) ? U('article/index/wechat_news_info', array('id' => $mediaInfo['id']), true) : strip_tags(html_out($mediaInfo['link']));
                    }
                    // 回复
                    $this->weObj->news($articles)->reply();
                    //记录用户操作信息
                    $this->record_msg($this->weObj->getRev()->getRevTo(), '图文信息', 1);
                    $endrs = true;
                }
            } else {
                // 文本回复
                $result[0]['content'] = html_out($result[0]['content']);
                $this->weObj->text($result[0]['content'])->reply();
                //记录用户操作信息
                $this->record_msg($this->weObj->getRev()->getRevTo(), $result[0]['content'], 1);
                $endrs = true;
            }
        }
        return $endrs;
    }

    /**
     * 功能变量查询
     *
     * @param unknown $tousername
     * @param unknown $fromusername
     * @param unknown $keywords
     * @return boolean
     */
    public function get_function($fromusername, $keywords)
    {
        $return = false;
        $rs = $this->db->query("SELECT name, keywords, command, config FROM {pre}wechat_extend WHERE keywords like '%$keywords%' and enable = 1 and wechat_id = $this->wechat_id ORDER BY id ASC LIMIT 6");
        if (empty($rs)) {
            $rs = $this->db->query("SELECT name, keywords, command, config FROM {pre}wechat_extend WHERE command = 'search' and enable = 1 and wechat_id = $this->wechat_id ORDER BY id ASC LIMIT 6");
        }
        $info = reset($rs);
        $info['user_keywords'] = $keywords;
        /*if($rs){
            $key = explode(',', $rs['keywords']);
            if(!in_array($keywords, $key)){
                return $return;
            }
        }*/
        $file = ADDONS_PATH . 'wechat/' . $info['command'] . '/' . $info['command'] . '.class.php';
        if (file_exists($file)) {
            require_once($file);
            $wechat = new $info['command']();
            $data = $wechat->show($fromusername, $info);
            if (!empty($data)) {
                // 数据回复类型
                if ($data['type'] == 'text') {
                    $this->weObj->text($data['content'])->reply();
                    //记录用户操作信息
                    $this->record_msg($fromusername, $data['content'], 1);
                } elseif ($data['type'] == 'news') {
                    $this->weObj->news($data['content'])->reply();
                    //记录用户操作信息
                    $this->record_msg($fromusername, '图文消息', 1);
                }
                $return = true;
            }
        }
        return $return;
    }

    /**
     * 模板消息提醒
     * @return boolean
     */
    static function template_message($openid = '', $template_id = '', $data = array(), $url = '')
    {
        $config = model()->table('wechat')
            ->field('token, appid, appsecret')
            ->where(array('id' => 1, 'status' => 1))
            ->find();
        if (empty($config)) {
            return array('code' => 1, 'msg' => '请先配置好公众号');
        }
        if (empty($openid) || empty($template_id)) {
            return array('code' => 1, 'msg' => '缺少发送对象或消息模板');
        }
        $template = model()->table('wechat_template')->where(array('template_id' => $template_id, 'status' => 1, 'wechat_id' => 1))->count();
        if (empty($template)) {
            return array('code' => 1, 'msg' => '消息模板不可用或不存在');
        }
        $weObj = new \ectouch\wechat\Wechat($config);
        $template_data = array(
            "touser" => $openid,
            "template_id" => $template_id,
            "url" => $url,
            "topcolor" => "#FF0000",
            "data" => $data
        );
        $rs = $weObj->sendTemplateMessage($template_data);
        if (empty($rs)) {
            return array('code' => 1, 'msg' => $weObj->errMsg);
        }
        return array('code' => 0);
    }

    /**
     * 主动发送信息
     *
     * @param unknown $tousername
     * @param unknown $fromusername
     * @param unknown $keywords
     * @param unknown $weObj
     * @param unknown $return
     * @return boolean
     */
    public function send_message($fromusername, $keywords, $weObj, $return = 0)
    {
        $result = false;
        $condition = array('command' => $keywords, 'enable' => 1, 'wechat_id' => $this->wechat_id);
        $rs = $this->db->table('wechat_extend')
            ->field('name, command, config')
            ->where($condition)
            ->find();
        $file = ADDONS_PATH . 'wechat/' . $rs['command'] . '/' . $rs['command'] . '.class.php';
        if (file_exists($file)) {
            require_once($file);
            $wechat = new $rs['command']();
            $data = $wechat->show($fromusername, $rs);
            if (!empty($data)) {
                if ($return) {
                    $result = $data;
                } else {
                    $weObj->sendCustomMessage($data['content']);
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * 多客服
     *
     * @param unknown $fromusername
     * @param unknown $keywords
     */
    public function customer_service($fromusername, $keywords)
    {
        $result = false;
        // 是否处在多客服流程
        $kfsession = $this->weObj->getKFSession($fromusername);
        if (empty($kfsession) || empty($kfsession['kf_account'])) {
            $kefu = model()->table('wechat_user')->field('openid')->where(array('openid' => $fromusername))->one();
            if ($kefu && $keywords == 'kefu') {
                $rs = $this->db->table('wechat_extend')->field('config')->where(array('command' => 'kefu', 'enable' => 1, 'wechat_id' => $this->wechat_id))->one();
                if (!empty($rs)) {
                    $config = unserialize($rs);
                    $msg = array(
                        'touser' => $fromusername,
                        'msgtype' => 'text',
                        'text' => array(
                            'content' => '欢迎进入多客服系统'
                        )
                    );
                    $this->weObj->sendCustomMessage($msg);
                    //记录用户操作信息
                    $this->record_msg($fromusername, $msg['text']['content'], 1);
                    // 在线客服列表
                    $online_list = $this->weObj->getCustomServiceOnlineKFlist();
                    if ($online_list['kf_online_list']) {
                        foreach ($online_list['kf_online_list'] as $key => $val) {
                            if ($config['customer'] == $val['kf_account'] && $val['status'] > 0 && $val['accepted_case'] < $val['auto_accept']) {
                                $customer = $config['customer'];
                            } else {
                                $customer = '';
                            }
                        }
                    }
                    // 转发客服消息
                    $this->weObj->transfer_customer_service($customer)->reply();
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * 关闭多客服菜单
     */
    public function close_kf($openid, $keywords)
    {
        $openid = $this->model->table('wechat_user')->field('openid')->where(array('openid' => $openid))->one();
        if ($openid) {
            $kfsession = $this->weObj->getKFSession($openid);
            if ($keywords == 'q' && isset($kfsession['kf_account']) && !empty($kfsession['kf_account'])) {
                $rs = $this->weObj->closeKFSession($openid, $kfsession['kf_account'], '客户已主动关闭多客服');
                if ($rs) {
                    $msg = array(
                        'touser' => $openid,
                        'msgtype' => 'text',
                        'text' => array(
                            'content' => '您已退出多客服系统'
                        )
                    );
                    $this->weObj->sendCustomMessage($msg);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 记录用户操作信息
     */
    public function record_msg($fromusername, $keywords, $iswechat = 0)
    {
        $uid = model()->table('wechat_user')->field('uid')->where(array('openid' => $fromusername))->one();
        if ($uid) {
            $data['uid'] = $uid;
            $data['msg'] = $keywords;
            $data['send_time'] = time();
            //是公众号回复
            if ($iswechat) {
                $data['iswechat'] = 1;
            }
            $this->db->table('wechat_custom_message')
                ->data($data)
                ->insert();
        }
    }

    /**
     * 插件页面显示方法
     *
     * @param string $plugin
     */
    public function actionPluginShow()
    {
        if (is_wechat_browser() && ((!isset($_SESSION['openid']) || empty($_SESSION['openid'])) || empty($_SESSION['user_id']))) {
            $redirect_url = __HOST__ . $_SERVER['REQUEST_URI'];
            $this->redirect(U('oauth/index/index', array('type' => 'wechat', 'back_url' => $redirect_url)));
        }
        $plugin = I('get.name');
        $file = ADDONS_PATH . 'wechat/' . $plugin . '/' . $plugin . '.class.php';
        if (file_exists($file)) {
            include_once($file);
            $wechat = new $plugin();
            $wechat->html_show();
        }
    }

    /**
     * 插件处理方法
     *
     * @param string $plugin
     */
    public function actionPluginAction()
    {
        $plugin = I('get.name');
        $file = ADDONS_PATH . 'wechat/' . $plugin . '/' . $plugin . '.class.php';
        if (file_exists($file)) {
            include_once($file);
            $wechat = new $plugin();
            $wechat->action();
        }
    }

    /**
     * 获取公众号配置
     *
     * @param string $orgid
     * @return array
     */
    private function get_config($orgid)
    {
        $config = model()->table('wechat')
            ->field('id, token, appid, appsecret, encodingaeskey')
            ->where(array('orgid' => $orgid, 'status' => 1))
            ->find();
        if (empty($config)) {
            $config = array();
        }
        return $config;
    }

    /**
     * 获取access_token的接口
     * @return [type] [description]
     */
    public function check_auth()
    {
        $appid = I('get.appid');
        $appsecret = I('get.appsecret');
        if (empty($appid) || empty($appsecret)) {
            echo json_encode(array('errmsg' => '信息不完整，请提供完整信息', 'errcode' => 1));
            exit;
        }
        $config = $this->model->table('wechat')
            ->field('token, appid, appsecret')
            ->where(array('appid' => $appid, 'appsecret' => $appsecret, 'status' => 1))
            ->find();
        if (empty($config)) {
            echo json_encode(array('errmsg' => '信息错误，请检查提供的信息', 'errcode' => 1));
            exit;
        }
        $obj = new \ectouch\wechat\Wechat($config);
        $access_token = $obj->checkAuth();
        if ($access_token) {
            echo json_encode(array('access_token' => $access_token, 'errcode' => 0));
            exit;
        } else {
            echo json_encode(array('errmsg' => $obj->errmsg, 'errcode' => $obj->errcode));
            exit;
        }
    }
}
