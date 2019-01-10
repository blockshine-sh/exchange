<?php

namespace ExchangeCenter\Exchanges\Crex24;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'crex24';

    public function getData($options = [])
    {
        $url = $this->config['url'] . $this->config['ticker'];
        $this->request('GET', $url, $options);
        
        if (empty($this->data)) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];

        foreach ($this->data as $symbol => $datum) {
            $_symbol = strtoupper($datum['instrument']);
            $datum['last'] = Helper::sctnToNumeric($datum['last'], 10);
            $datum['high'] = Helper::sctnToNumeric($datum['high'], 10);
            $datum['bid'] = Helper::sctnToNumeric($datum['bid'], 10);
            $datum['ask'] = Helper::sctnToNumeric($datum['ask'], 10);
            $datum['low'] = Helper::sctnToNumeric($datum['low'], 10);           
            $symbol = explode('-', $_symbol);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = bcmul($datum['last'], (1 - $datum['percentChange'] / 100), 10);
            $ticker->high = $datum['high'];
            $ticker->low = $datum['low'];
            $ticker->close = $datum['last'];
            $ticker->amount = $datum['baseVolume'];
            $ticker->bid1 = $datum['bid'];
            $ticker->ask1 = $datum['ask'];
            $ticker->vol = $datum['quoteVolume'];
            $ticker->timestamp = strtotime($datum['timestamp']);
            $ticker->price_pcnt = bcdiv($datum['percentChange'], 100, 4);

            $ticker_data[$_symbol] = Helper::toArray($ticker);
        }

        return $ticker_data;
    }

    private function handleError()
    {
        if (!empty($this->data['code'])) {
            Helper::fail('请求失败，返回结果：' . json_encode($this->data));
        }
        return true;
    }
}