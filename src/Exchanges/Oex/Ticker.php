<?php

namespace ExchangeCenter\Exchanges\Oex;

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
    protected $exchange = 'oex';

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
        $timestamp = floor($this->data['time'] / 1000);
        foreach ($this->data['data'] as $datum) {
            $_symbol = strtoupper($datum['name']);
            $symbol = explode('_', $_symbol);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = $datum['buy'] ?? $datum['latest'];
            $ticker->high = $datum['high'] ?? $datum['latest'];
            $ticker->low = $datum['low'] ?? $datum['latest'];
            $ticker->close = $datum['latest'];
            $ticker->amount = $datum['24h_vol'];
            //$ticker->vol = $datum['vq'];
            $ticker->timestamp = $timestamp;

            $ticker_data[$_symbol] = Helper::toArray($ticker);
        }

        return $ticker_data;
    }

}