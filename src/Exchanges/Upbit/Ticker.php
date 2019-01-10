<?php

namespace ExchangeCenter\Exchanges\Upbit;

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
    protected $exchange = 'upbit';

    public function getData($options = [])
    {
        $pairs = (new Pairs($this->exchange))->getData();
        if (empty($pairs)) return [];

        $promises = [];
        foreach ($pairs as $pair) {
            $promises[] = [
                'index' => $pair['digital_currency'] . '_' . $pair['market_currency'],
                'url' => sprintf('%s/%s%s-%s', $this->config['url'], $this->config['ticker'], $pair['market_currency'], $pair['digital_currency']),
            ];
        }

        $ret = $this->multiRequest($promises);var_dump($ret);die;
        if ($ret === false) {
            Helper::fail($this->error);
        }
        return $this->convertData();
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
}