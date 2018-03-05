<?php

/**
 * 应用启动
 */

namespace base;

class App {

	/**
	 * 初始化配置
	 */
	static protected function init() {
		Config::init( ROOT_PATH );
		Config::loadConfig( CONF_PATH . 'global.php' );
		Config::loadConfig( CONF_PATH . Config::get('ENV') . '.php' );
		date_default_timezone_set( Config::get('TIMEZONE') );

		//error display
		if ( Config::get('DEBUG') ) {
			ini_set("display_errors", 1);
			error_reporting( E_ALL ^ E_NOTICE );
		} else {
			ini_set("display_errors", 0);
			error_reporting(0);
		}

		// 加载系统基础函数库
		require dirname(__FILE__).'/helpers/function.php';
	}

	/**
	 * 运行框架
	 */
	static public function run() {
		try{
			self::init();

			Hook::init(BASE_PATH);
			Hook::listen('appBegin');

			Hook::listen('routeParseUrl', array( Config::get('REWRITE_RULE'), Config::get('REWRITE_ON')));

			//default route
			if( !defined('APP_NAME') || !defined('CONTROLLER_NAME') || !defined('ACTION_NAME')){
				Route::parseUrl( Config::get('REWRITE_RULE'), Config::get('REWRITE_ON') );
			}
			
			//execute action
			$controller = '\http\\'. APP_NAME .'\controllers\\'. ucfirst(CONTROLLER_NAME) .'Controller';
			$action = Config::get('ACTION_PREFIX') . ucfirst(ACTION_NAME);

			$MY_controller = '\custom\\'. APP_NAME .'\controllers\\MY_'. ucfirst(CONTROLLER_NAME) .'Controller';
			$MY_action = 'MY_'. ucfirst(ACTION_NAME);
			$controller = class_exists($MY_controller) ? $MY_controller : $controller;

			if( !class_exists($controller) ) {
				throw new \Exception("Controller '{$controller}' not found", 404);
			}

			$obj = new $controller();
			$action = method_exists($obj, $MY_action) ? $MY_action : $action;
			if( !method_exists($obj, $action) ){
				throw new \Exception("Action '{$controller}::{$action}()' not found", 404);
			}

			Hook::listen('actionBefore', array($obj, $action));
			$obj->$action();
			Hook::listen('actionAfter', array($obj, $action));
			
		} catch(\Exception $e){
			Hook::listen('appError', array($e));
		}
		
		Hook::listen('appEnd');
	}
}