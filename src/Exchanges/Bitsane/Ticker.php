<?php

namespace ExchangeCenter\Exchanges\Bitsane;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'bitsane';

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
        $ticker = new TickerModel();

        foreach ($this->data as $symbol => $datum) {
            $pairArr = explode('_', strtoupper($symbol));
            $ticker->digital_currency = $pairArr['0'];
            $ticker->market_currency = $pairArr['1'];
            $ticker->open = bcmul($datum['last'], (1 - $datum['percentChange'] / 100), 10);
            $ticker->high = $datum['high24hr'];
            $ticker->low = $datum['low24hr'];
            $ticker->close = $datum['last'];
            $ticker->amount = $datum['baseVolume'];
            $ticker->bid1 = $datum['highestBid'];
            $ticker->ask1 = $datum['lowestAsk'];
            $ticker->vol = $datum['quoteVolume'];
            $ticker->timestamp = time();

            $ticker_data[$symbol] = Helper::toArray($ticker);
            $ticker_data[$symbol]['price_pcnt'] = bcdiv($datum['percentChange'], 100, 4);
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