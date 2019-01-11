<?php

namespace ExchangeCenter\Exchanges\Coinsuper;

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
    protected $exchange = 'coinsuper';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        if (empty($this->data['data'])) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data['data'] as $pair => $datum) {
            $symbol = explode('/', $pair);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = $datum['last'] * (1 - $datum['percentChange']);
            $ticker->high = $datum['high24hr'];
            $ticker->low = $datum['low24Hr'];
            $ticker->close = $datum['last'];
            $ticker->amount = $datum['baseVolume'];
            $ticker->vol = $datum['quoteVolume'];
            $ticker->timestamp = time();
            $ticker->bid1 = $datum['highestBid'];
            $ticker->ask1 = $datum['lowestAsk'];
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