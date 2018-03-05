<?php
use base\Config;
use base\Route;

//$_GET       && safe_filter($_GET);
//$_POST      && safe_filter($_POST);
//$_COOKIE    && safe_filter($_COOKIE);
//$_REQUEST   && safe_filter($_REQUEST);

/**
 * 防注入和XSS攻击通用过滤.
 */
function safe_filter(&$arr)
{
    is_array($arr) && array_walk_recursive($arr, 'think_filter');
    if (is_array($arr)) {
        foreach ($arr as $key => $value) {
            if (!is_array($value)) {
                if (!get_magic_quotes_gpc()) { //不对magic_quotes_gpc转义过的字符使用addslashes(),避免双重转义。
                    $value = addslashes($value); //给单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）加上反斜线转义
                }
                $arr[$key] = htmlspecialchars($value, ENT_QUOTES); //&,",',> ,< 转为html实体 &amp;,&quot;&#039;,&gt;,&lt;
            } else {
                safe_filter($arr[$key]);
            }
        }
    }
}

function think_filter(&$value)
{
    // TODO 其他安全过滤

    // 过滤查询特殊字符
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}






/**
 * 获取设置配置
 * @param  string $key   配置项
 * @param  mixed  $value 配置值
 * @return array
 */
function C($key = NULL, $value = NULL){
    if( func_num_args() <= 1 ){
        return Config::get($key);
    }else{
        return Config::set($key, $value);
    }
}

/**
 * URL生成
 * @param  string $route  地址
 * @param  array  $params 参数
 * @return string
 */
function U($route = null, $params = array(), $domain = false){
    $url = Route::url($route, $params);
    if (true === $domain) {
        $domain = $_SERVER['HTTP_HOST'];
        $url = (is_ssl() ? 'https://' : 'http://') . $domain . $url;
    }
    return $url;
}

/**
 * 对象调用函数
 * @param  string $class 模块名/类名
 * @param  string $layer 模块层
 * @return object
 */
function A($class, $layer = 'models'){
    static $objArr = array();
    $param = explode('/', $class, 2);
    $paramCount = count($param);
    switch ($paramCount) {
    case 1:
        $app = APP_NAME;
        $module = $param[0];
        break;
    case 2:
        $app = $param[0];
        $module = $param[1];
        break;
    }
    $app = strtolower($app);
    $class = "\\http\\{$app}\\{$layer}\\{$module}".ucfirst(rtrim($layer, 's'));
    if(!class_exists($class)){
        $class = "\\http\\base\\{$layer}\\{$module}".ucfirst(rtrim($layer, 's'));
    }
    if(isset($objArr[$class])){
        return $objArr[$class];
    }
    if(!class_exists($class)){
        throw new \Exception("Class '{$class}' not found'", 500);
    }
    $obj = new $class();
    $objArr[$class] = $obj;
    return $obj;
}

/**
 * 检查是否是微信浏览器访问
 */
function is_wechat_browser()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($user_agent, 'micromessenger') === false) {
        return false;
    } else {
        return true;
    }
}

   /**
     * 过滤微信昵称中特殊字符
     */ 
 function removeEmoji($nickname) 
 {
    $clean_text = "";
    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);
    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);
    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);
    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);
    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);
    return $clean_text;
}

/**
 * 获得 ECSHOP 当前环境的 HTTP 协议方式
 *
 * @access  public
 *
 * @return  void
 */
function http()
{
    return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}

/**
 * 取得当前的域名
 *
 * @access  public
 *
 * @return  string      当前的域名
 */
function get_domain()
{
    /* 协议 */
    $protocol = http();

    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
    {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    }
    elseif (isset($_SERVER['HTTP_HOST']))
    {
        $host = $_SERVER['HTTP_HOST'];
    }
    else
    {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT']))
        {
            $port = ':' . $_SERVER['SERVER_PORT'];

            if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
            {
                $port = '';
            }
        }
        else
        {
            $port = '';
        }

        if (isset($_SERVER['SERVER_NAME']))
        {
            $host = $_SERVER['SERVER_NAME'] . $port;
        }
        elseif (isset($_SERVER['SERVER_ADDR']))
        {
            $host = $_SERVER['SERVER_ADDR'] . $port; //IP
        }
    }

    return $protocol . $host;
}

/**
 *  * 获取顶级域名
 *   * @param unknown $url
 *    * @return unknown
 *     */
function get_top_domain($url = '')
{
    $url = empty($url) ? get_domain() : $url;
    $host = strtolower($url);
    if (strpos($host, '/') !== false) {
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb = array(
        'com',
        'edu',
        'gov',
        'int',
        'mil',
        'net',
        'org',
        'biz',
        'info',
        'pro',
        'name',
        'museum',
        'coop',
        'aero',
        'xxx',
        'idv',
        'mobi',
        'cc',
        'me'
    );
    $str = '';
    foreach ($topleveldomaindb as $v) {
        $str .= ($str ? '|' : '') . $v;
    }

    $matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
    if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
        $domain = $matchs['0'];
    } else {
        $domain = $host;
    }
    return $domain;
}


/**
 * 自动注册类
 */
spl_autoload_register(function($class){
    static $fileList = array();
    $prefixes =array(
        'base' => BASE_PATH,
        'libraries' => BASE_PATH,
        'classes' => BASE_PATH,
        'vendor' => BASE_PATH,
        'apps' => BASE_PATH,
        '*' => BASE_PATH,
    );

    $class = ltrim($class, '\\');
    if (false !== ($pos = strrpos($class, '\\')) ){
        $namespace = substr($class, 0, $pos);
        $className = substr($class, $pos + 1);

        foreach ($prefixes as $prefix => $baseDir){
            if ( '*'!==$prefix && 0!==strpos($namespace, $prefix) ) continue;

            //file path case-insensitive
            $fileDIR = $baseDir.str_replace('\\', '/', $namespace).'/';
            if( !isset($fileList[$fileDIR]) ){
                $fileList[$fileDIR] = array();
                $phpFile = glob($fileDIR.'*.php');
                if($phpFile){
                    foreach($phpFile as $file){
                        $fileList[$fileDIR][] = $file;
                    }
                }
            }

            $fileBase = $baseDir.str_replace('\\', '/', $namespace).'/'.$className;
            foreach($fileList[$fileDIR] as $file){
                if( false!==stripos($file, $fileBase) ){
                    require $file;
                    return true;
                }
            }
        }
    }
    return false;
});
