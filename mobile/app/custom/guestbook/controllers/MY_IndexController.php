<?php
namespace custom\guestbook\controllers;

use http\base\controllers\FrontendController;

class MY_IndexController extends FrontendController
{

    public function MY_Index()
    {
        echo 'this guestbook list. ';
        echo '<a href="' . U('add') . '">Goto Add</a>';
    }

    public function MY_Add()
    {
        $this->display();
    }

    public function MY_Save()
    {
        $post = array(
            'title' => I('title'),
            'content' => I('content')
        );

        // 验证数据
        // todo

        // 保存数据        
        // $this->model->table('guestbook')->data($post)->insert();

        // 页面跳转
        $this->redirect(U('index'));
    }
}