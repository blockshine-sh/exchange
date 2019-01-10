<?php

namespace ExchangeCenter\Exchanges\Fatbtc;

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
    protected $exchange = 'fatbtc';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        if(empty($this->data['data'])) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data['data'] as $datum) {
            $symbol = explode('/', $datum['dspName']);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = $datum['open'];
            $ticker->high = $datum['high'];
            $ticker->low = $datum['low'];
            $ticker->close = $datum['close'];
            $ticker->amount = $datum['volume'];
            $ticker->vol = $datum['amount'];
            $ticker->timestamp = floor($datum['timestamp']/1000);
            $ticker->price_pcnt = bcdiv($ticker->close - $ticker->open, $ticker->open, 4);
            if(!empty($datum['bis1'])) $ticker->bid1 = $datum['bis1'][0];
            if(!empty($datum['ask1'])) $ticker->ask1 = $datum['ask1'][0];
            $ticker_data[$symbol[0].'_'.$symbol[1]] = Helper::toArray($ticker);
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