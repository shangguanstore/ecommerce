<?php
namespace custom\site\controllers;

use http\site\controllers\IndexController;

class MY_IndexController extends IndexController
{
    /**
     * URL路由访问地址: mobile/index.php?r=site/index/about
     */
    public function MY_About()
    {
        echo '这是新的About页面。';
    }
}