<?php
namespace http\article\controllers;

use http\base\controllers\FrontendController;

class IndexController extends FrontendController
{
    private $parameter = array();
    public function __construct()
    {
        parent::__construct();
        L(require(LANG_PATH  . C('shop.lang') . '/user.php'));
    }
    /**
     * 文章列表
     */
    public function actionIndex()
    {
        $size = intval(C('page_size')) > 0 ? intval(C('page_size')) : 10;
        $page = I('post.page', 1, 'intval');
        $article_nav = $this->db->query(
            'select cat_id, cat_name from {pre}article_cat where parent_id = 0 AND cat_type = 1 order by sort_order,cat_id asc');
        foreach ($article_nav as $key => $vo) {
            $article_nav[$key]['url'] = build_uri('article_cat', array('acid' => $vo['cat_id']));
            $article_nav[$key]['list'] = get_cat_articles($vo['cat_id'], 1, 10);
        }
        $this->assign('data', $article_nav);
        if (IS_AJAX) {
            $count = $this->get_cat_article('', '', I('keyword'), I('cat_id'));
            $max_page = ($count > 0) ? ceil($count / $size) : 1;
            if ($page > $max_page) $page = $max_page;
            $article_index_new = $this->get_cat_article($size, $page, I('keyword'), I('cat_id'));
            foreach ($article_index_new as $key => $val) {
                $article_index_new[$key]['add_time'] = local_date("Y-m-d H:i:s", $val['add_time']);
                $article_index_new[$key]['url'] = build_uri('article', array('aid' => $val['article_id']));
                preg_match_all("/<img.*>/isU", $val['content'], $match);
                $match = $match[0];
                if ($match) {
                    $pic = $this->get_first_img($match);
                    if($pic){
                        if (strpos(get_image_path($pic), 'no_image')) {
                            $article_index_new[$key]['first_img'] = '';
                        } else {
                            $article_index_new[$key]['first_img'] = get_image_path($pic);
                        }
                    }
                }
                if (empty($val['author']) || $val['author'] == '_SHOPHELP') {
                    $article_index_new[$key]['author'] = $GLOBALS['_CFG']['shop_name'];
                }
                $article_index_new[$key]['comment'] = $this->db->query(
                    'select content from {pre}comment where id_value = ' . $val['article_id'] . ' and comment_type = 1 AND status = 1');
                $article_index_new[$key]['comment_count'] = count($article_index_new[$key]['comment']);
                $res = $this->db->query(
                    'SELECT likenum,click FROM {pre}article_extend  WHERE article_id = ' . $val['article_id'] . ' ');

                if (!$res) {
                    $data['article_id'] = $val['article_id'];
                    $this->model->table('article_extend')->data($data)->insert();
                }
                foreach ($res as $ky => $vv) {
                    $vv['likenum'] = $vv['likenum'] ? $vv['likenum'] : 0;
                    $vv['click'] = $vv['click'] ? $vv['click'] : 0;
                    $article_index_new[$key]['like'] = $vv['likenum'];
                    $article_index_new[$key]['views'] = $vv['click'];
                }

                if ($_COOKIE[$val['article_id'] . 'islike'] == '1') {
                    $article_index_new[$key]['islike'] = '1';
                } else {
                    $article_index_new[$key]['islike'] = '0';
                }
            }
            die(json_encode(array('article_index_new' => $article_index_new, 'totalPage' => ceil($count / $size))));
        }
//        $this->assign('page_title', '文章频道');
        $this->assign('page_title', L('article_channel'));
        $this->display('article');
    }

    /*
     * 按条件获取文章列表
     * */
    private function get_cat_article($size, $page, $keyword = '', $cat_id = '')
    {
        $where = '';
        if ($keyword) {
            $where .= " AND title LIKE '%" . $keyword . "%'";
        } else {
            //通过一级文章分类向下寻找的二级和三级的分类ID，再通过ID获取对应文章 start
            if ($cat_id) {
                $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE parent_id = '$cat_id' ";
                if ($GLOBALS['db']->getOne($sql)) {

                    $sql = 'SELECT a.cat_id ' .
                        'FROM ' . $GLOBALS['ecs']->table('article_cat') . ' AS a ' .
                        'LEFT JOIN ' . $GLOBALS['ecs']->table('article_cat') . ' AS b ON b.parent_id = a.cat_id ' .
                        "WHERE a.parent_id = '$cat_id' ";
                    $res = $GLOBALS['db']->getAll($sql);
                    foreach ($res as $ke => $vv) {
                        $res[$ke] = $vv['cat_id'];
                    }
                    $res = array_unique($res);
                    foreach ($res as $key => $val) {
                        $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE parent_id = '$val' ";
                        if ($GLOBALS['db']->getOne($sql)) {
                            $sql = 'SELECT b.cat_id ' .
                                'FROM ' . $GLOBALS['ecs']->table('article_cat') . ' AS a ' .
                                'LEFT JOIN ' . $GLOBALS['ecs']->table('article_cat') . ' AS b ON b.parent_id = a.cat_id ' .
                                "WHERE a.parent_id = '$cat_id' ";
                            $row = $GLOBALS['db']->getAll($sql);
                            foreach ($row as $value) {
                                $array[] = $value['cat_id'];
                            }
                        } else {
                            $array[] = $val;
                        }
                    }
                    $where .= " AND cat_id " . db_create_in($array) . "";
                } else {
                    $where .= " AND cat_id = " . $cat_id . "";
                }
            }
            //通过一级文章分类向下寻找二级和三级的分类ID，再通过ID获取对应文章 end
        }
        if (empty($size) && empty($page)) {
            $sql = "select article_id from {pre}article where is_open = 1 $where ";
            return count($GLOBALS['db']->getAll($sql));
        } else {
            $sql = "select article_id,cat_id,title,author,content,add_time,link from {pre}article where is_open = 1 $where order by add_time desc";
            return $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
        }
    }

    /*
     *获取文章中第一个图片
     * */
    private function get_first_img($match)
    {
        foreach ($match as $k => $v) {
            $first_src = strpos($v, "src=\"");
            $match[$k] = substr($v, ($first_src + 5));
            $match[$k] = strchr($match[$k], "\"", true);
            $match[$k] = substr($match[$k], 1);
            $match[$k] = strchr($match[$k], "/");
            if (file_exists(ROOT_PATH . get_image_path($match[$k]))) {
                $arr = getimagesize(ROOT_PATH . get_image_path($match[$k]));
                if ($arr[0] > 300 && $arr[1] > 200) {
                    $pic = $match[$k];
                    break;
                }
            }
        }
        return $pic;
    }


    /**
     * 点赞
     */
    public function actionLike()
    {
        $article_id = I('article_id', 0 ,'intval');
        if (IS_AJAX) {
            if ($_COOKIE[$article_id . 'islike'] == '1') {
                $sql = "UPDATE {pre}article_extend SET likenum=likenum-1 WHERE article_id ='$article_id'";
                $this->db->query($sql);
                $like_num = $this->db->getOne(
                    'SELECT likenum FROM {pre}article_extend  WHERE article_id = ' . $article_id . ' ');
                if (!$like_num) {
                    $like_num = 0;
                }
                setcookie($article_id . 'islike', '0', gmtime() - 86400);
                echo json_encode(array('like_num' => $like_num, 'is_like' => 0, 'article_id' => $article_id));
            } else {
                $sql = "UPDATE {pre}article_extend SET likenum=likenum+1 WHERE article_id ='$article_id'";
                $res = $this->db->query($sql);
                if ($res == true) {
                    $like_num = $this->db->getOne(
                        'SELECT likenum FROM {pre}article_extend  WHERE article_id = ' . $article_id . ' ');
                }
                setcookie($article_id . 'islike', '1', gmtime() + 86400);

                echo json_encode(array('like_num' => $like_num, 'is_like' => 1, 'article_id' => $article_id));
            }
        }
    }


    /**
     * 文章详情
     */
    public function actionDetail()
    {
        $article_id = I('id', 0 ,'intval');
        if (isset($_REQUEST['cat_id']) && $_REQUEST['cat_id'] < 0) {
            $article_id = $this->db->getOne("SELECT article_id FROM " . $this->ecs->table('article') . " WHERE cat_id = '" . intval($_REQUEST['cat_id']) . "' ");
        }
        /* 文章详情 */
        $article = get_article_info($article_id);
        if (empty($article)) {
            ecs_header("Location: ./\n");
            exit;
        }
        $res = $this->db->query('SELECT likenum,click FROM {pre}article_extend  WHERE article_id = ' . $article_id . ' ');
        if (!$res) {
            $data['article_id'] = $article_id;
            $this->model->table('article_extend')->data($data)->insert();
            $like_num = 0;
        } else {
            $like_num = $res[0]['likenum'];
        }
        if (empty($_COOKIE[$article_id . 'isclick'])) {
            $sql = "UPDATE {pre}article_extend SET click=click+1 WHERE article_id ='$article_id'";
            $this->db->query($sql);
        }
        setcookie($article_id . 'isclick', '1', gmtime() + 86400);
        $this->assign('like_num', $like_num);
        $islike = $_COOKIE[$article_id . 'islike'] ? $_COOKIE[$article_id . 'islike'] : '0';
        $this->assign('islike', $islike);
        if (!empty($article['link']) && $article['link'] != 'http://' && $article['link'] != 'https://') {
            ecs_header("location:$article[link]\n");
            exit;
        }
        $comment = $this->db->getAll("SELECT user_id,user_name,content,comment_id,add_time FROM " . $this->ecs->table('comment') . " WHERE id_value = '" . $article['article_id'] . "'
        and parent_id = 0 and comment_type = 1 AND status = 1 order by add_time desc");

        foreach ($comment as $ke => $val) {
            $parent_id = $comment[$ke]['comment_id'];
            $comment[$ke]['second'] = $this->db->getAll("SELECT user_id,user_name,content,add_time FROM " . $this->ecs->table('comment') . " WHERE id_value = '" . $article['article_id'] . "'
        and parent_id =$parent_id  and comment_type = 1 AND status = 1 order by add_time desc");
            $comment[$ke]['user_picture'] = get_image_path($this->db->getOne("SELECT user_picture FROM " . $this->ecs->table('users') . " WHERE user_id = '" . $val['user_id'] . "' "));
            $comment[$ke]['count'] = count($comment[$ke]['second']);
            $comment[$ke]['comment_time'] = mdate($val['add_time']);
        }

        $this->assign('commentfirst', $comment);
        $this->assign('article_categories', article_categories_tree($article_id)); //文章分类树
        $this->assign('id', $article_id);
        $this->assign('username', $_SESSION['user_name']);
        $this->assign('email', $_SESSION['email']);
        $this->assign('type', '1');
        $this->assign('promotion_info', get_promotion_info());
        $article['content_fx'] = article_content_html_out($article['content']);
        $this->assign('article', $article);

        $this->assign('keywords', htmlspecialchars($article['keywords']));
        $this->assign('description', htmlspecialchars($article['description']));

        $catlist = array();
        foreach (get_article_parent_cats($article['cat_id']) as $k => $v) {
            $catlist[] = $v['cat_id'];
        }
        $position = assign_ur_here($article['cat_id'], $article['title']);
        $this->assign('page_title', $position['title']);    // 页面标题
        $this->assign('ur_here', $position['ur_here']);  // 当前位置
        $this->assign('comment_type', 1);

        /* 相关商品 */
        $sql = "SELECT a.goods_id, g.goods_name " .
            "FROM " . $this->ecs->table('goods_article') . " AS a, " . $this->ecs->table('goods') . " AS g " .
            "WHERE a.goods_id = g.goods_id " .
            "AND a.article_id = $article_id ";
        $acticle_goods = $this->db->getAll($sql);
        if ($acticle_goods) {
            $count = count($acticle_goods);
            $rand = rand(0, ($count - 1));
            $acticle_goods = get_goods_info($acticle_goods[$rand]['goods_id']);
            $this->assign('acticle_goods', $acticle_goods);
        } else {
            $acticle_goods = get_recommend_goods('new');
            if($acticle_goods){
                $this->assign('recommend_goods', $acticle_goods[0]);
            }else{
                $acticle_goods = get_recommend_goods('best');
                if($acticle_goods){
                    $this->assign('recommend_goods', $acticle_goods[0]);
                }else{
                    $acticle_goods = get_recommend_goods('hot');
                    $this->assign('recommend_goods', $acticle_goods[0]);
                }
            }
        }

        assign_dynamic('article');
        $this->display('article_detail');
    }

    /**
     * 提交评论
     */
    public function actionCommnet()
    {

        if (I('content')) {
            $user_id = $_SESSION['user_id'];
            $user_name = $_SESSION['user_name'];
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $parent_id = I('cid') ? I('cid') : 0;
            if (!empty($user_id)) {
                $article_id = I('article_id');
                if (IS_POST) {
                    $data['content'] = I('content');
                    $data['user_id'] = $user_id;
                    $data['user_name'] = $user_name;
                    $data['id_value'] = $article_id;
                    $data['comment_type'] = '1';
                    $data['parent_id'] = $parent_id;
                    $data['status'] = '1';
                    $data['add_time'] = gmtime();
                    $data['ip_address'] = $user_ip;
                    if (!empty($data['content'])) {
                        $res = $this->model->table('comment')->data($data)->insert();
                        if ($res == true) {
                            echo json_encode(U('article/index/detail', array('id' => $article_id)));
                        }
                    }
                }
            } else {
                echo json_encode(U('user/login/index'));
            }
        }
    }

    /**
     * 微信图文详情
     */
    public function actionWechatNewsInfo()
    {
        /* 文章详情 */
        $news_id = I('get.id', 0 ,'intval');
        $data = $this->db->table('wechat_media')->field('title,author,add_time, content')->where(array('id' => $news_id))->find();
        if (empty($data)) {
            $this->redirect(U('site/index/index'));
        }
        $data['add_time'] = local_date('Y-m-d',$data['add_time']);
        $data['content'] = article_content_html_out($data['content']);

        $this->assign('page_title', $data['title']);
        $this->assign('article', $data);
        $this->display('wechat_article');
    }
    /**
     * 列表页面
     */
    public function actionCategory()
    {
        /* 获得指定的分类ID */
        if (!empty($_GET['id'])) {
            $cat_id = intval($_GET['id']);
        } elseif (!empty($_GET['category'])) {
            $cat_id = intval($_GET['category']);
        } else {
            ecs_header("Location: ./\n");
            exit;
        }
        $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;

        assign_template('a', array($cat_id));
        $position = assign_ur_here($cat_id);

        $condition['cat_id'] = $cat_id;
        $cat_info = $this->db->table('article_cat')->where($condition)->find();
        $this->assign('page_title', $cat_info['cat_name']);     // 页面标题
        $this->assign('ur_here', $position['ur_here']);   // 当前位置
        $this->assign('cat_info', $cat_info);

        $this->assign('categories', get_categories_tree(0)); // 分类树
        $this->assign('article_categories', article_categories_tree($cat_id)); //文章分类树
        $this->assign('helps', get_shop_help());        // 网店帮助
        $this->assign('top_goods', get_top10());            // 销售排行

        $this->assign('best_goods', get_recommend_goods('best'));
        $this->assign('new_goods', get_recommend_goods('new'));
        $this->assign('hot_goods', get_recommend_goods('hot'));
        $this->assign('promotion_goods', get_promote_goods());
        $this->assign('promotion_info', get_promotion_info());

        /* Meta */
        $meta = $this->db->getRow("SELECT keywords, cat_desc FROM " . $this->ecs->table('article_cat') . " WHERE cat_id = '$cat_id'");

        if ($meta === false || empty($meta)) {
            /* 如果没有找到任何记录则返回首页 */
            ecs_header("Location: ./\n");
            exit;
        }

        $this->assign('keywords', htmlspecialchars($meta['keywords']));
        $this->assign('description', htmlspecialchars($meta['cat_desc']));


        //异步输出
        if (IS_AJAX) {
            /* 获得文章总数 */
            $size = isset($_CFG['article_page_size']) && intval($_CFG['article_page_size']) > 0 ? intval($_CFG['article_page_size']) : 20;
            $count = get_article_count($cat_id);
            $pages = ($count > 0) ? ceil($count / $size) : 1;

            if ($page > $pages) {
                $page = $pages;
            }
            $pager['search']['id'] = $cat_id;
            $keywords = '';
            $goon_keywords = ''; //继续传递的搜索关键词

            /* 获得文章列表 */
            if (isset($_REQUEST['keywords'])) {
                $keywords = addslashes(htmlspecialchars(urldecode(trim($_REQUEST['keywords']))));
                $pager['search']['keywords'] = $keywords;
                $search_url = substr(strrchr($_POST['cur_url'], '/'), 1);

                $this->assign('search_value', stripslashes(stripslashes($keywords)));
                $this->assign('search_url', $search_url);
                $count = get_article_count($cat_id, $keywords);
                $pages = ($count > 0) ? ceil($count / $size) : 1;
                if ($page > $pages) {
                    $page = $pages;
                }

                $goon_keywords = urlencode($_REQUEST['keywords']);
            }

            $last = intval($_POST['last']);
            $size = intval($_POST['amount']);
            $page = $last <= 0 ? 1 : ($last + 1) / $size;
            $artciles_list = get_cat_articles($cat_id, $page, $size, $keywords);
            $this->response($artciles_list);
        }
        $this->assign('cat_id', $cat_id);
        $this->display('category');
    }

    /**
     * 处理参数便于搜索商品信息
     */
    private function parameter()
    {
        $page_size = C('shop.article_number');
        $this->parameter['size'] = $page_size > 0 ? $page_size : 10;
        $this->parameter['page'] = I('page') ? I('page', 'intval') : 1;
        $this->parameter['cat_id'] = I('id', 'intval');
        $this->parameter['keywords'] = I('keywords');
    }
}
