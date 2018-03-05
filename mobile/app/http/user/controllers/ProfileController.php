<?php
namespace http\user\controllers;

use http\base\controllers\FrontendController;

class ProfileController extends FrontendController
{

    public $user_id;

    public $email;

    public $mobile;

    public $sex;

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        L(require (LANG_PATH  . C('shop.lang') . '/user.php'));
        $file = array(
            'passport',
            'clips'
        );
        $this->load_helper($file);
        // 属性赋值
        $this->user_id = $_SESSION['user_id'];
        // 验证登录
        $this->actionchecklogin();
        $this->assign('lang', L());
    }

    /**
     * 频道页
     */
    public function actionIndex()
    {
        $this->parameter();
        $sql = "SELECT user_id,user_name,sex FROM {pre}users WHERE user_id = " . $this->user_id;
        $user_info = $this->db->getRow($sql);
        $this->assign("user_sex", $user_info['sex']);
        $this->display('user_detail');
    }
    // 修改个人资料
    public function actionEditProfile()
    {
        $this->parameter();
        if (IS_POST) {
            if (! empty($this->sex)) {
                $update = " sex = '" . $this->sex . "'";
            }
            $where = " WHERE user_id = '" . $this->user_id . "'";
            if (isset($update) && isset($where)) {
                $sql = "UPDATE {pre}users SET $update $where";
                $this->db->query($sql);
            }
            $info = get_user_default($this->user_id);
            echo json_encode($info);
            exit();
        }
    }

    /**
     * 修改手机
     */
    public function actionUserEditMobile()
    {
        $this->parameter();
        $sql = "SELECT user_id,user_name,mobile_phone FROM {pre}users WHERE user_id = " . $this->user_id;
        $user_info = $this->db->getRow($sql);
        if (IS_POST && I('sms_signin') == 1) {
            $sms_code = I('sms_code');
            if ($sms_code !== $_SESSION['sms_code']) {
                show_message(L('msg_auth_code_error'));
                exit();
            }
            if (empty($this->mobile)) {
                show_message(L('msg_input_mobile'));
                exit();
            }
            if (!empty($user_info)) {
                $sql = "UPDATE {pre}users SET mobile_phone = '" . $this->mobile . "' WHERE user_id = '" . $this->user_id . "'";
                $this->db->query($sql);
            }
        }
        if(IS_POST && I('sms_signin') == 0){
            $sql="SELECT user_id FROM {pre}users WHERE mobile_phone='" . $this->mobile . "'AND user_id!=".$_SESSION['user_id'];
            $mobile_phone=$this->db->getOne($sql);
            if(!empty($mobile_phone)){
                show_message(L('msg_mobile_exist'));
                exit();
            }
            $sql="SELECT user_id FROM {pre}users WHERE user_name='" . $this->mobile . "'AND user_id!=".$_SESSION['user_id'];
            $user_name=$this->db->getOne($sql);
            if(!empty($user_name)){
                show_message(L('msg_mobile_exist'));
                exit();
            }
            if(!empty($this->mobile)){
                $sql = "UPDATE {pre}users SET mobile_phone = '" . $this->mobile . "' WHERE user_id = '" . $this->user_id . "'";
                $up = $this->db->query($sql);
                ecs_header("Location: " . U('user/profile/index'));
            }            
        }
        $_SESSION['sms_code'] = $sms_code = md5(mt_rand(1000, 9999));
        $this->assign("sms_code", $sms_code);
        $this->assign("mobile", $user_info['mobile_phone']);
        $this->assign("sms_signin", C('shop.sms_signin'));
        $this->assign("page_title",L('edit_mobile') );
        $this->display('user_edit_mobile');
    }

    /**
     * 修改邮箱
     */
    public function actionUserEditEmail()
    {
        $this->parameter();
        $sql = "SELECT user_id,email FROM {pre}users WHERE user_id = " . $this->user_id;
        $user_info = $this->db->getRow($sql);
        if (IS_POST) {
            $sql="SELECT user_id FROM {pre}users WHERE email='" .$this->email ."'AND user_id!=".$_SESSION['user_id'];
            $email=$this->db->getOne($sql);
            if(!empty($email)){
//                show_message("该邮箱已存在");
                show_message(L('msg_email_registered'));
                exit();
            }
            $sql="SELECT user_id FROM {pre}users WHERE user_name='" .$this->email ."'AND user_id!=".$_SESSION['user_id'];
            $user_email=$this->db->getOne($sql);
            if(!empty($user_email)){
                show_message(L('msg_email_registered'));
                exit();
            }
            if (! empty($this->email)) {
                $sql = "UPDATE {pre}users SET email = '" . $this->email . "' WHERE user_id = '" . $this->user_id . "'";
                $this->db->query($sql);
            }
            ecs_header("Location: " . U('user/profile/index'));
        }
        $this->assign('emails', $user_info['email']);
        $this->assign("page_title",L('edit_email') );
        $this->display('user_edit_email');
    }

    private function parameter()
    {
        $this->user_id = $_SESSION['user_id'];
        if (empty($this->user_id)) {
            ecs_header("Location: ./\n");
        }
        $this->mobile = I('mobile');
        $this->sex = I('sex');
        $this->email = I('email');
        $this->postbox = I('postbox');
        $this->assign('info', get_user_default($this->user_id)); // 自定义导航栏
    }

    /**
     * 验证是否登录
     */
    public function actionchecklogin(){
        if(!$this->user_id){
            $url = urlencode(__HOST__ . $_SERVER['REQUEST_URI']);
            if(IS_POST) {
                $url = urlencode($_SERVER['HTTP_REFERER']);
            }
            ecs_header("Location: ".U('user/login/index',array('back_act'=>$url)));
            exit;
        }
    }
    /**
     * 实名认证页面
     */
    public function actionRealname(){
        $user_id = $this->user_id;

        $type = $_REQUEST['type'];
        $real_user = $this->db->getRow("SELECT * FROM {pre}users_real WHERE user_id = '$user_id' ");
        $count_user = $this->db->getOne("SELECT COUNT(*) FROM {pre}users_real WHERE user_id = '$user_id' ");

        if($type == 'edit'){
            $real_user['validate_time'] = date('Y-m-d H:i:s', $real_user['add_time']);
            $this->assign('real_user', $real_user);
        }
        if($count_user > 0 && $type != 'edit'){
            ecs_header("Location: ".U('user/Profile/RealnameOk'));
        }


        if(IS_POST){
            // 获取实名信息

            $real_user['user_id'] = $user_id;
            $real_user['real_name'] = trim($_POST['real_name']);
            $real_user['self_num'] = trim($_POST['self_num']); // 身份证号
            $real_user['bank_mobile'] = trim($_POST['mobile_phone']);
            $real_user['bank_name'] = trim($_POST['bank_name']);
            $real_user['bank_card'] = trim($_POST['bank_card']);
            $real_user['bank_mobile'] = trim($_POST['mobile_phone']);
            $real_user['add_time'] = gmtime();
            if (empty($real_user['real_name']))
            {
                show_message('真实姓名不可为空', '返回重新填写', '', 'error');
            }
            if (empty($real_user['self_num']))
            {
                show_message('身份证号不可为空', '返回重新填写', '', 'error');
            }
            if (empty($real_user['bank_name']))
            {
                show_message('银行不可为空', '返回重新填写', '', 'error');
            }
            if (empty($real_user['bank_card']))
            {
                show_message('银行卡号不可为空', '返回重新填写', '', 'error');
            }
            if (empty($real_user['bank_mobile']))
            {
                show_message('手机号不可为空', '返回重新填写', '', 'error');
            }

            // 验证手机号是否为本人
            // ..........................

            //手机号码 验证短信
            if(!empty($_POST['mobile_phone']))
            {
                if (empty($_POST['mobile_code']))
                {
                    show_message('手机验证码不可为空', '返回重新输入', '', 'error');
                }

                if(!empty($_POST['mobile_code']))
                {
                    if($_POST['mobile_phone'] != $_SESSION['sms_mobile'] || $_POST['mobile_code'] != $_SESSION['sms_mobile_code'])
                    {
                        show_message('手机或手机验证码错误', '返回重新验证', '', 'error');
                    }
                }
            }
            else
            {
                show_message('手机号为空，请先认证手机号', '返回重新验证', '', 'error');
            }

            if ($count_user)
            {
                if ($this->db->autoExecute("{pre}users_real", $real_user, 'UPDATE', 'user_id='.$user_id))
                {
                    ecs_header("Location: ".U('user/Profile/RealnameOk'));
                }
            }
            else
            {
                if ($this->db->autoExecute("{pre}users_real", $real_user, 'INSERT'))
                {
                    ecs_header("Location: ".U('user/Profile/RealnameOk'));
                }
            }
        }
        $this->assign('page_title', '实名认证');
        $this->display('user_realname');
    }

    /**
     * 实名短信
     */
    public function actionRealnameSend(){
        $_SESSION['sms_mobile'] = I('mobile');
        $_SESSION['sms_mobile_code'] = rand(1000, 9999);
        // 验证手机号码格式
        $preg = '/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/';
        if (!preg_match($preg, $_SESSION['sms_mobile'])) {
            $result['error'] = 1;
            $result['content'] = '手机号码格式不正确';
            exit(json_encode($result));
        }
        // 组装数据
        $message = array(
            'code' => $_SESSION['sms_mobile_code']
        );
        $send_result = send_sms($_SESSION['sms_mobile'], 'sms_code', $message);
        if($send_result === true){
            $result['error'] = 0;
            $result['content'] = '发送短信成功';
        }else{
            $result['error'] = 1;
            $result['content'] = '发送短信失败';
        }
        exit(json_encode($result));
    }

    /**
     * 实名认证成功页面
     */
    public function actionRealnameOk(){
        $user_id = $this->user_id;
        $real_user = $this->db->getRow("SELECT * FROM {pre}users_real WHERE user_id = '$user_id' ");

        if (!$real_user)
        {
            ecs_header("Location: " . U('user/Profile/Realname'));
        }

        $real_user['validate_time'] = date('Y-m-d H:i:s', $real_user['add_time']);
        $this->assign('real_user', $real_user);
        $this->assign('page_title', '实名认证');
        $mobile = $this->db->getOne("SELECT  mobile_phone FROM {pre}users WHERE user_id = '$user_id' ");
        $this->assign('mobile_phone', $mobile);
        $this->display('user_realname_ok');
    }

}