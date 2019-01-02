<?php
/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/24
 * Time: 上午11:01
 */
require_once '../vendor/autoload.php';
use ExchangeCenter\Helper;
use ExchangeCenter\Exchange;

try {
    $obj = new Exchange();
    $obj->setExchange($argv[1]);

    switch ($argv[1]) {
        case 'idcm':
        case 'oex':
        case 'coinsbank':
            $config = [
                'proxy' => [
                    'http' => 'http://127.0.0.1:8001',
                    'https' => 'http://127.0.0.1:8001'
                ]
            ];
            $obj->setOptions($config);
            break;
        default:
            break;

    }

    $ret = $obj->getPairs();

} catch (Exception $e) {
    var_dump($e->getMessage());
    die();
}

var_dump($ret);