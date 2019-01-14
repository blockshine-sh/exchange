<?php

namespace ExchangeCenter\Exchanges\Exmo;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'exmo';

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
            $_symbol = strtoupper($symbol);
            $symbol = explode('_', $_symbol);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = null;
            $ticker->high = $datum['high'];
            $ticker->low = $datum['low'];
            $ticker->close = $datum['last_trade'];
            $ticker->amount = $datum['vol'];
            $ticker->bid1 = $datum['buy_price'];
            $ticker->ask1 = $datum['sell_price'];
            $ticker->vol = $datum['vol_curr'];
            $ticker->timestamp = $datum['updated'];
            
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