<?php
namespace classes;

class app
{

    public static function __callStatic($func, $arguments = '')
    {
        static $_container_instance = array();
        $name = md5($func);
        if (isset($_container_instance[$name])) {
            return $_container_instance[$name];
        }
        $_container_instance[$name] = $arguments[0];
    }

}
