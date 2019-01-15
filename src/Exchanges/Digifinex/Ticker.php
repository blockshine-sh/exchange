<?php

namespace ExchangeCenter\Exchanges\Digifinex;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'digifinex';
    protected $apiKey = '5b7fbd0fe9026';

    public function getData($options = [])
    {
        $url = $this->config['url'] . $this->config['ticker'] . sprintf('?apiKey=%s', $this->apiKey);
        $this->request('GET', $url, $options);
        
        if (empty($this->data['ticker'])) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        
        foreach ($this->data['ticker'] as $symbol => $datum) {
            $_symbol = strtoupper($symbol);
            $symbol = explode('_', $_symbol);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[1];
            $ticker->market_currency = $symbol[0];
            $ticker->open = bcmul($datum['last'], (1 - $datum['change'] / 100), 8);
            $ticker->high = $datum['high'];
            $ticker->low = $datum['low'];
            $ticker->close = $datum['last'];
            $ticker->amount = $datum['vol'];
            $ticker->bid1 = $datum['buy'];
            $ticker->ask1 = $datum['sell'];
            //$ticker->vol = $datum['vq'];
            $ticker->timestamp = $this->data['date'];
            
            $ticker_data[$_symbol] = Helper::toArray($ticker);
            $ticker_data[$_symbol]['price_pcnt'] = bcdiv($datum['change'], 100, 4);
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