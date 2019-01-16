<?php

namespace ExchangeCenter\Exchanges\Bitz;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'bitz';

    public function getData($options = [])
    {
        $pairs = (new Pairs($this->exchange))->getData();
        if (empty($pairs)) return [];

        $promises = [];
        foreach ($pairs as $pair) {
            $index = strtolower($pair['digital_currency'] . '_' . $pair['market_currency']); 
            $promises[] = [
                'index' => $index,
                'url' => $this->config['url'] . sprintf('%s?symbol=%s', $this->config['ticker'], $index),
            ];
        }

        $ret = $this->multiRequest($promises);
        
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
                $data = $datum['data'];
                $symbol = explode('_', $pair);
                $ticker = new TickerModel();
                $ticker->digital_currency = $symbol[0];
                $ticker->market_currency = $symbol[1];
                $ticker->open = $data['open'];
                $ticker->high = $data['high'];
                $ticker->low = $data['low'];
                $ticker->close = $data['now'];
                $ticker->amount = $data['volume'];
                $ticker->vol = $data['quoteVolume'];
                $ticker->timestamp = $datum['time'];
                $ticker->ask1 = $data['askPrice'];
                $ticker->bid1 = $data['bidPrice'];
                $tickers[$pair] = Helper::toArray($ticker);
                $tickers[$pair]['price_pcnt'] = bcdiv($data['priceChange24h'], 100, 4);
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