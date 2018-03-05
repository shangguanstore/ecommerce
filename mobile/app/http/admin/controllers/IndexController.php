<?php
namespace http\admin\controllers;
use http\base\controllers\BackendController;

class IndexController extends BackendController {

	public function actionIndex() {
		$this->display();
	}


}