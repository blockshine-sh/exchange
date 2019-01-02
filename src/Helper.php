<?php

namespace ExchangeCenter;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午3:09
 */

class Helper
{
    public static function config($key)
    {
        $config = require 'Config.php';
        //var_dump($config);
        return isset($config[$key]) ? $config[$key] : [];
    }

    public static function getClass($class) {

        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }

    public static function fail($message) {
        throw new \Exception($message);
    }

    public static function toArray($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
}