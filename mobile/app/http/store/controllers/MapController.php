<?php
namespace http\store\controllers;

use http\base\controllers\FrontendController;
use libraries\Http;

/**
 * 经度：longitude
 * 纬度：latitude
 */
class MapController extends FrontendController
{

    public function __construct()
    {
        parent::__construct();
        //初始化位置信息
        //$this->init_params();
        L(require(LANG_PATH  . C('shop.lang') . '/other.php'));
    }


    public function actionIndex()
    {
        if(IS_POST){
            $lng = I('post.lng', 0);
            $lat = I('post.lat', 0);
            $sql = 'SELECT b.shop_name, b.province, b.city, b.district, b.shop_address, b.longitude, b.latitude, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( b.latitude ) ) * cos( radians( b.longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( b.latitude ) ) ) ) AS distance FROM {pre}seller_shopinfo as b LEFT JOIN {pre}merchants_shop_information as a ON a.user_id=b.ru_id  WHERE a.is_street = 1 and a.merchants_audit = 1 HAVING distance < 100 ORDER BY distance LIMIT 10';
            $seller_shopinfo = $this->model->query($sql);
            $list = array();
            $store = '';
            foreach ($seller_shopinfo as $key=>$vo) {
                $province = get_region_name($vo['province']);
                $city = get_region_name($vo['city']);
                $district = get_region_name($vo['district']);
                $address = $province['region_name'] . $city['region_name'] . $district['region_name'] . $vo['shop_address'];

                $info = array(
                    'coord' => $vo['latitude'] . ',' .$vo['longitude'],
                    'title' => $vo['shop_name'],
                    'addr' => $address
                );
                $list[] = urldecode(str_replace('=', ':', http_build_query($info, '', ';')));
            }
            $store = implode('|', $list);
            if(empty($store)){
                exit(json_encode(array('error'=>1, 'message'=> '您的附近暂无商家哦')));
            }
            $url = 'http://apis.map.qq.com/tools/poimarker?type=0&marker='.$store.'&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77&referer=ectouch';
            exit(json_encode(array('error'=> 0, 'url'=> $url)));
        }
        $this->assign('page_title', L('nearby_shop'));
        $this->display();
    }

    // 根据地址获取地址坐标
    public function actionTest()
    {
        $seller_shopinfo = $this->model->table('seller_shopinfo')->select();
        foreach ($seller_shopinfo as $key=>$vo) {
            $province = get_region_name($vo['province']);
            $city = get_region_name($vo['city']);
            $district = get_region_name($vo['district']);
            $address = $province['region_name'] . $city['region_name'] . $district['region_name'] . $vo['shop_address'];

            $result = Http::doGet('http://apis.map.qq.com/ws/geocoder/v1/?key=QOVBZ-KIICG-BKNQG-IUWJE-LMGUZ-R6BSD&address=' . $address);
            $data = json_decode($result, 1);
            if (!$data['status']) {
                $location = $data['result']['location'];
                $locat['longitude']=$location['lng'];
                $locat['latitude']=$location['lat'];
                $condition['id'] = $vo['id'];
                $this->model->table('seller_shopinfo')->data($locat)->where($condition)->update();
            }
            if(($key + 1) % 5 == 0) sleep(1);
        }
    }
}

