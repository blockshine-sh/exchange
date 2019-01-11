<?php

namespace ExchangeCenter\Exchanges\Idcm;

use ExchangeCenter\Exchange;
use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:34
 */
class Ticker extends ExchangeBase
{
    protected $exchange = 'idcm';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);
        if (empty($this->data['Data'])) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data['Data'] as $datum) {
            $symbol = explode('/', $datum['TradePairCode']);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = $datum['Open'];
            $ticker->high = $datum['High'];
            $ticker->low = $datum['Low'];
            $ticker->close = $datum['Close'];
            $ticker->amount = $datum['Volume'];
            $ticker->vol = $datum['Turnover'];
            $ticker->timestamp = time();
            $ticker_data[$symbol[0] . '_' . $symbol[1]] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }

    private function handleError()
    {
        return true;
    }

//    private function getSign($secret)
//    {
//        $uri = $this->config['url'] . $this->config['ticker'];
//        $str = hash_hmac("sha384", $uri, $secret);
//        return $this->base64UrlEncode($str);
//    }
//
//    private function base64UrlEncode($str)
//    {
//        $find = array('+', '/');
//        $replace = array('-', '_');
//        return str_replace($find, $replace, base64_encode($str));
//    }
}