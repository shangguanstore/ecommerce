<?php
namespace http\community\controllers;

use http\base\controllers\FrontendController;

class PostController extends FrontendController
{
    private $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $files = array(
            'order',
            'clips'
        );
        $this->load_helper($files);
        $this->user_id = $_SESSION['user_id'];

        if(APP_NAME== 'community' && CONTROLLER_NAME == 'post'){
            $community = 1;
            $this->assign('community', $community);
        }
    }

    private function checkLogin()
    {
        if (!$_SESSION['user_id']) {
            $url = urlencode(__HOST__ . $_SERVER['REQUEST_URI']);
            if (IS_POST) {
                $url = urlencode($_SERVER['HTTP_REFERER']);
            }
            ecs_header("Location: " . U('user/login/index', array('back_act' => $url)));
            exit;
        }
    }

    public function actionIndex()
    {
        $goods_id = I('goods_id');
        $order_id = I('order_id');
        $commentid = I('post.commentid');
        if($commentid){
            $this->assign('commentid', $commentid);
        }
        $title = I('title');
        $content = I('content');
        if ($goods_id) {
            $postgoods = get_goods_info($goods_id);
        }
        $this->assign('title', $title);
        $this->assign('content', $content);
        $this->assign('order_id', $order_id);
        $this->assign('postgoods', $postgoods);
        $this->assign('action', ACTION_NAME);
        $this->assign('page_title', '发帖');
        $this->display('write');
    }

    public function actionAddpost()
    {
        $title = I('post.title');
        $content = I('post.content');
        $commentid = I('post.commentid');
        $this->assign('title', $title);
        $this->assign('content', $content);
        $this->assign('commentid', $commentid);
        $postgoods = array();
        $sql = "SELECT order_id FROM {pre}order_info WHERE user_id=$this->user_id AND pay_status = " . PS_PAYED . " AND shipping_status = " . SS_RECEIVED . " ";
        $order_id_array = $this->db->getAll($sql);
        foreach ($order_id_array as $k => $v) {
            $goods_list = order_goods($v['order_id']);
            foreach ($goods_list as $kkk => $vvv) {
                $array = array('goods_id' => $vvv['goods_id'],'order_id' => $v['order_id']);
                array_push($postgoods, $array);
            }
        }
        $postgoods = array_unique($postgoods);
        foreach ($postgoods as $k => $v) {
            $sql = "SELECT goods_thumb FROM {pre}goods WHERE goods_id = $v[goods_id]";
            $goods_thumb = $this->db->getOne($sql);
            $postgoods[$k]['goods_thumb'] = get_image_path($goods_thumb);

        }
        $this->assign('postgoods', $postgoods);
        $this->assign('action', ACTION_NAME);
        $this->assign('page_title', '添加关联商品');
        $this->display('addpost');
    }

    public function actionChangeaddpost()
    {
        if (IS_AJAX) {
            $result = array('error' => '', 'content' => '');
            $postgoodstype = I('postgoodstype', 1, 'intval');
            if (!in_array($postgoodstype, array(1, 2, 3, 4))) {
                $result['error'] = '1';
            } else {
                $postgoods = array();
                switch ($postgoodstype) {
                    case $postgoodstype == 2: //购物车商品
                        $cart_goods = get_cart_goods('', 0);
                        if (is_array($cart_goods['goods_list'])) {
                            foreach ($cart_goods['goods_list'] as $k => $v) {
                                if (is_array($v['goods_list'])) {
                                    foreach ($v['goods_list'] as $kk => $vv) {
                                        $postgoods[] = $vv;
                                    }
                                }
                            }
                        }
                        break;
                    case $postgoodstype == 3: //收藏的商品
                        $sql = "SELECT count(rec_id) as max FROM {pre}collect_goods WHERE user_id=$this->user_id ";
                        $count = $this->db->getOne($sql);
                        $collection_goods = get_collection_goods($this->user_id, $count);
                        $postgoods = $collection_goods['goods_list'];
                        break;
                    case $postgoodstype == 4: //浏览的商品
                        $postgoods = historys();
                        break;
                    default:
                        $postgoods = array();
                        $sql = "SELECT order_id FROM {pre}order_info WHERE user_id=$this->user_id AND pay_status = " . PS_PAYED . " AND shipping_status = " . SS_RECEIVED . " ";
                        $order_id_array = $this->db->getAll($sql);
                        foreach ($order_id_array as $k => $v) {
                            $goods_list = order_goods($v['order_id']);
                            foreach ($goods_list as $kkk => $vvv) {
                                $array = array('goods_id' => $vvv['goods_id'],'order_id' => $v['order_id']);
                                array_push($postgoods, $array);
                            }
                        }
                        $postgoods = array_unique($postgoods);
                        foreach ($postgoods as $k => $v) {
                            $sql = "SELECT goods_thumb FROM {pre}goods WHERE goods_id = $v[goods_id]";
                            $goods_thumb = $this->db->getOne($sql);
                            $postgoods[$k]['goods_thumb'] = get_image_path($goods_thumb);

                        }
                        $this->assign('postgoods', $postgoods);
                        break;
                }
                $this->assign('postgoods', $postgoods);
                $result['content'] = $this->fetch('postgoods', true, true);
            }
            die(json_encode($result));
        }
    }

    /**
     * 发帖处理
    */
    public function actionAddcom()
    {
        $data = I('');
        if (empty($data['goods_id'])) {
            show_message('请选择一件关联商品');
        }
        if (empty($data['commentid'])) {
            show_message('请选择帖子主题');
        }
        if (empty($data['content'])) {
            show_message('请填写帖子内容');
        }
        if($data['commentid'] == 4 && ($data['order_id'] && $data['order_id'] != 0)){
            $rank = I('comment_rank', 5, 'intval');
            $sql = "select user_id from " . $GLOBALS['ecs']->table('goods') . " where goods_id = '$data[goods_id]'";
            $ru_id = $GLOBALS['db']->getOne($sql);
            $status = 1 - $GLOBALS['_CFG']['comment_check'];
            $return = array(
                'comment_type' => '0',
                'id_value' => $data['goods_id'],
                'email'  => $_SESSION['email'],
                'user_name' => $_SESSION['user_name'],
                'content' => $data['content'],
                'comment_rank' => $rank,
                'comment_server' => 5,
                'comment_delivery' => 5,
                'add_time' => gmtime(),
                'ip_address' => real_ip(),
                'status' => $status,
                'parent_id' => '0',
                'user_id' => $_SESSION['user_id'],
                'single_id' => '0',
                'order_id' => $data['order_id'],
                'ru_id' => $ru_id
            );
            $cid = $this->model->table('comment')->data($return)->insert();
            $pic= $_FILES;
            foreach ($pic as $key => $value) {
                $name=$value['name'];
            }
            if (!empty($name)) {
                if ($value['size'] / 1024 / 1024 > 3) {
                    show_message('图片不能大于3M');
                }
                if (($value["type"] != "image/png") && ($pic["pic"]["type"] != "image/jpeg") && ($pic["pic"]["type"] != "image/pjpeg")) {
                    show_message('图片格式需jpg/png/jpeg');
                }
                $result =$this->ectouchUpload('pic','cmt_img');
                $new_name=dirname(ROOT_PATH) . '/data/cmt_img/'.date('Ym');
                if(!file_exists($new_name)){
                    make_dir($new_name,0777);
                }
                $newname= 'data/cmt_img/'.date('Ym').'/'.$result['message']['pic']['savename'];
                $result['message']['pic']['savepath'].$result['message']['pic']['savename'];
                move_upload_file($result['message']['pic']['savepath'].$result['message']['pic']['savename'], $new_name.'/'.$result['message']['pic']['savename']);//移动图片到pc目录

                $sql = "INSERT INTO " . $GLOBALS['ecs']->table('comment_img') . "(user_id, order_id, goods_id, comment_id, comment_img, img_thumb)
                        VALUES('$_SESSION[user_id]', '$data[order_id]', '$data[goods_id]', ' $cid', '$newname', '$newname')";
                $GLOBALS['db']->query($sql);
            }
            show_message('晒单成功', '查看帖子', U('community/index/detail', array('id' => $cid,'type' => $data['commentid'])), 'success');
        }else{
            if (empty($data['title'])) {
                show_message('请填写标题');
            }
            $return = array(
                'dis_type' => $data['commentid'],
                'goods_id' => $data['goods_id'],
                'user_id'  => $_SESSION['user_id'],
                'dis_title' => $data['title'],
                'dis_text' => $data['content'],
                'user_name' => $_SESSION['user_name'],
                'add_time' => gmtime()
            );
            $dis_id = $this->model->table('discuss_circle')->data($return)->insert();
        }
        show_message('发帖成功', '查看帖子', U('community/index/detail', array('id' => $dis_id,'type' => $data['commentid'])),'success');
    }

}