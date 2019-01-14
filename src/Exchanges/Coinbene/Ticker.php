<?php

namespace ExchangeCenter\Exchanges\Coinbene;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'coinbene';
    protected $dataPairs = [];

    public function getData($options = [])
    {
        $pairs = (new Pairs($this->exchange))->getData();
        
        if (empty($pairs)) {
            return [];
        }

        foreach ($pairs as $pv) {
            $this->dataPairs[$pv['digital_currency'] . $pv['market_currency']] = $pv;
        }
        
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
 
        foreach ($this->data['ticker'] as $datum) {
            if (!isset($this->dataPairs[$datum['symbol']])){
                continue;
            }
            
            $ticker = new TickerModel();
            $ticker->digital_currency = $this->dataPairs[$datum['symbol']]['digital_currency'];
            $ticker->market_currency = $this->dataPairs[$datum['symbol']]['market_currency'];
            $ticker->open = null;
            $ticker->high = $datum['24hrHigh'];
            $ticker->low = $datum['24hrLow'];
            $ticker->close = $datum['last'];
            $ticker->amount = $datum['24hrVol'];
            $ticker->bid1 = $datum['bid'];
            $ticker->ask1 = $datum['ask'];
            $ticker->vol = $datum['24hrAmt'];
            $ticker->timestamp = floor($this->data['timestamp'] / 1000);
            
            $ticker_data[$this->dataPairs[$datum['symbol']]['digital_currency'] . '_' . $this->dataPairs[$datum['symbol']]['market_currency']] = Helper::toArray($ticker);
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