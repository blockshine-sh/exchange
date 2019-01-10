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
    
  
    /**
     * 科学计数转换成正常数字
     * @param string $sct 科学计数字符，1.117E-5
     * @param numeric $scale 保留小数位
     */
    public static function sctnToNumeric($sct, $scale = 0){
        if (false !== stripos($sct, 'e')) {
            $num = explode('e', strtolower($sct));
            return bcmul($num[0], bcpow(10, intval($num[1]), $scale), $scale);
        } else {
            return $sct;
        }      
    }
}