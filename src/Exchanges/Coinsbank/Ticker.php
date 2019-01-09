<?php

namespace ExchangeCenter\Exchanges\Coinsbank;

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
    protected $exchange = 'coinsbank';

    public function getData($options = [])
    {
        $pairs = (new Pairs($this->exchange))->getData();
        if (empty($pairs)) return [];

        $promises = [];
        foreach ($pairs as $pair) {
            $this->request('GET', sprintf($this->config['ticker'], $pair['digital_currency'] . $pair['market_currency']), $options);
            if (!empty($this->data) && empty($this->data['code'])) {
                $promises[$pair['digital_currency'] . '_' . $pair['market_currency']] = $this->data;
            }
            //由于coinsbank接口访问频率限制 5秒钟请求一次
            sleep(5);
        }
        $this->data = $promises;
        return $this->convertData();

//        $promises = [];
//        foreach ($pairs as $pair) {
//            $promises[] = [
//                'index' => $pair['digital_currency'] . '_' . $pair['market_currency'],
//                'url' => $this->config['url'] . sprintf($this->config['ticker'], $pair['digital_currency'] . $pair['market_currency']),
//            ];
//        }
//
//        $ret = $this->multiRequest($promises);
//        if ($ret === false) {
//            Helper::fail($this->error);
//        }
//        return $this->convertData();
    }

    private function convertData()
    {
        $tickers = [];
        if (!empty($this->data)) {
            foreach ($this->data as $pair => $datum) {
                $data = current($datum);
                $symbol = explode('_', $pair);
                $ticker = new TickerModel();
                $ticker->digital_currency = $symbol[0];
                $ticker->market_currency = $symbol[1];
                $ticker->open = $data['o'];
                $ticker->high = $data['h'];
                $ticker->low = $data['l'];
                $ticker->close = $data['c'];
                $ticker->amount = $data['v'];
                $ticker->vol = $data['vq'];
                $ticker->timestamp = floor($data['date'] / 1000);
                $tickers[$pair] = Helper::toArray($ticker);
            }
        }
        return $tickers;
    }

    private function handleError()
    {
        if (!empty($this->data['code'])) {
            Helper::fail('请求失败，返回结果：' . json_encode($this->data));
        }
        return true;
    }
}