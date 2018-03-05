<?php

/**
 * 模板引擎类
 */

namespace base;

class Template {

    /**
     * 模板配置
     * @var array
     */
    protected $config =array();

    /**
     * 布局模板
     * @var null
     */
    protected $label = null;

    /**
     * 模板赋值数组
     * @var array
     */
    protected $vars = array();

    /**
     * 缓存对象
     * @var null
     */
    protected $cache = null;

    /**
     * 构建函数
     * @param array $config 模板引擎配置
     */
    public function __construct($config) {
        $this->config = $config;
        $this->assign('__Template', $this);
        $this->label = array(
            /**variable label
                {$name} => <?php echo $name;?>
                {$user['name']} => <?php echo $user['name'];?>
                {$user.name}    => <?php echo $user['name'];?>
             */
            '/\$(\w+)\.(\w+)\.(\w+)\.(\w+)/is' => "\$\\1['\\2']['\\3']['\\4']",
            '/\$(\w+)\.(\w+)\.(\w+)/is' => "\$\\1['\\2']['\\3']",
            '/\$(\w+)\.(\w+)/is' => "\$\\1['\\2']",
            '/{(\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)}/i' => "<?php echo $1; ?>",

            /**constance label
            {CONSTANCE} => <?php echo CONSTANCE;?>
             */
            '/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s' => "<?php echo \\1;?>",

            /**include label
                {include file="test"}
             */
            '/{include\s*file=\"(.*)\"}/i' => "<?php \$__Template->display(\"$1\"); ?>",

            /**if label
                {if $name==1}       =>  <?php if ($name==1){ ?>
                {elseif $name==2}   =>  <?php } elseif ($name==2){ ?>
                {else}              =>  <?php } else { ?>
                {/if}               =>  <?php } ?>
             */
            '/\{if\s+(.+?)\}/' => "<?php if(\\1) { ?>",
            '/\{else\}/' => "<?php } else { ?>",
            '/\{elseif\s+(.+?)\}/' => "<?php } elseif (\\1) { ?>",
            '/\{\/if\}/' => "<?php } ?>",

            /**for label
                {for $i=0;$i<10;$i++}   =>  <?php for($i=0;$i<10;$i++) { ?>
                {/for}                  =>  <?php } ?>
             */
            '/\{for\s+(.+?)\}/' => "<?php for(\\1) { ?>",
            '/\{\/for\}/' => "<?php } ?>",

            /**foreach label
                {foreach $arr as $vo}           =>  <?php $n=1; if (is_array($arr) foreach($arr as $vo){ ?>
                {foreach $arr as $key => $vo}   =>  <?php $n=1; if (is_array($array) foreach($arr as $key => $vo){ ?>
                {/foreach}                  =>  <?php $n++;}unset($n) ?>
             */
            '/\{foreach\s+(\S+)\s+as\s+(\S+)\}/' => "<?php \$n=1;if(is_array(\\1)) foreach(\\1 as \\2) { ?>",
            '/\{foreach\s+(\S+)\s+as\s+(\S+)\s*=>\s*(\S+)\}/' => "<?php \$n=1; if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>",
            '/\{\/foreach\}/' => "<?php \$n++;}unset(\$n); ?>",

            /**function label
                {date('Y-m-d H:i:s')}   =>  <?php echo date('Y-m-d H:i:s');?>
                {$date('Y-m-d H:i:s')}  =>  <?php echo $date('Y-m-d H:i:s');?>
             */
            '/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/' => "<?php echo \\1;?>",
            '/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/' => "<?php echo \\1;?>",
        );

        $this->cache = new Cache( $this->config['TPL_CACHE'] );
    }

    /**
     * 模板赋值
     * @param  string $name  变量名
     * @param  mixed  $value 变量值
     * @return void
     */
    public function assign($name, $value = '') {
        if( is_array($name) ){
            foreach($name as $k => $v){
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * 模板输出
     * @param  string  $tpl    模板名
     * @param  boolean $return 返回模板内容
     * @param  boolean $isTpl  是否模板文件
     * @return mixed
     */
    public function display($tpl = '', $return = false, $isTpl = true) {
        if ($return) {
            ob_start();
            ob_implicit_flush(0);
        }

        extract($this->vars, EXTR_OVERWRITE);
        //$css = ectouch_global_assets('css');

        eval('?>' . $this->compile($tpl, $isTpl));

        if ($return) {
            $content = ob_get_clean();
            return $content;
        }
    }

    /**
     * 模板编译
     * @param  string  $tpl    模板名
     * @param  boolean $isTpl  是否模板文件
     * @return string
     */
    public function compile($tpl, $isTpl = true) {
        if( $isTpl ){
            $tplFile = $this->config['TPL_PATH'] . $tpl . $this->config['TPL_SUFFIX'];
            if ( !file_exists($tplFile) ) {
                throw new \Exception("Template file '{$tplFile}' not found", 500);
            }
            $tplKey = md5(realpath($tplFile));
        } else {
            $tplKey = md5($tpl);
        }

        $ret = unserialize( $this->cache->get( $tplKey ) );
        if ( empty($ret['template']) || ($isTpl&&filemtime($tplFile)>($ret['compile_time'])) ) {
            $template = $isTpl ? file_get_contents( $tplFile ) : $tpl;
            if( false === Hook::listen('templateParse', array($template), $template) ){
                foreach ($this->label as $key => $value) {
                    $template = preg_replace($key, $value, $template);
                }
            }
            $ret = array('template'=>$template, 'compile_time'=>time());
            $cache_value = serialize($ret);
            $cache_expire = isset($this->config['EXPIRE']) ? $this->config['EXPIRE'] : C('CACHE_EXPIRE');
            $this->cache->set($tplKey, $cache_value, $cache_expire);
        }
        return $ret['template'];
    }

    /**
     * 获取模板文件
     * @param string $tpl
     * @return string
     */
    private function getTpl($tpl = '')
    {
        $tpl = empty($tpl) ? strtolower(CONTROLLER_NAME) . C('TPL.TPL_DEPR') . strtolower(ACTION_NAME) : $tpl;
        $base_themes = ROOT_PATH . 'statics/';
        $base_views = ROOT_PATH . 'resources/views/';
        $base_custom = ROOT_PATH . 'app/custom/' . APP_NAME . '/views/' . $tpl . C('TPL.TPL_SUFFIX');
        $extends_tpl = 'library/' . $tpl . C('TPL.TPL_SUFFIX');

        if (file_exists($base_custom)) {
            $tpl = 'app/custom/' . APP_NAME . '/views/' . $tpl;
        } elseif (file_exists($base_themes . $extends_tpl)) {
            $tpl = 'statics/library/' . $tpl;
        } elseif (file_exists($base_views . 'base/' . $tpl . C('TPL.TPL_SUFFIX'))) {
            $tpl = 'resources/views/base/' . $tpl;
        } elseif (file_exists($base_views . APP_NAME . '/' . $tpl . C('TPL.TPL_SUFFIX'))) {
            $tpl = 'resources/views/' . APP_NAME . '/' . $tpl;
        } else {
            $tpl = 'app/http/' . APP_NAME . '/views/' . $tpl;
        }

        return $tpl;
    }
}
