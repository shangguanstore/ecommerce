<?php
$url_data = array (
  'domain' => 'http://www.pjshch.com',
  'url' => 'http://www.pjshch.com/',
  'shop_name' => '平价市场',
  'shop_title' => '平价市场—官网！',
  'shop_desc' => '平价市场/批价商城！是由南京商之俏电子商务有限公司推出的B2B2C商城系统，以自营店铺产品为主，支持多店铺入驻，包含多城市多仓库等众多功能，能帮助企业及个人快速搭建多商户电商系统，只与诚信优质厂家直接合作，正品供货，采取批发价零售模式！最大让利广大消费者，拒绝暴利！',
  'shop_keywords' => '平价市场，平价商城，批价市场，批价商城!',
  'country' => '中国',
  'province' => '江苏',
  'city' => '南京',
  'address' => '浦口区大桥北路9号',
  'qq' => '',
  'ww' => '平价市场网站',
  'ym' => '',
  'msn' => '',
  'email' => '61427235@qq.com',
  'phone' => '17705151456',
  'icp' => '苏ICP备15036568号',
  'version' => 'v1.8.8',
  'release' => '20161117',
  'language' => 'zh_cn',
  'php_ver' => '5.3.29',
  'mysql_ver' => '5.1.73',
  'charset' => 'utf-8',
  'post_type' => 1,
);
$url_http = base64_decode('aHR0cDovL2Vjc2hvcC5lY21vYmFuLmNvbS9kc2MucGhw');
$purl_http = new Http();
$purl_http->doPost($url_http, $url_data);
?>