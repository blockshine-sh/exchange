<?php

namespace ExchangeCenter\Exchanges\Coinbene;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\PairsModel;

class Pairs extends ExchangeBase
{
    protected $exchange = 'coinbene';

    public function getData($options = [])
    {
        $this->request('GET', $this->config['url'] . $this->config['pairs']);
        if (empty($this->data)) {
            Helper::fail('获取交易对失败');
        }
        $this->handleError();
        return $this->convertData();
    }

    private function convertData()
    {
        $pairs_data = [];

        if (!empty($this->data['symbol'])) {            
            foreach ($this->data['symbol'] as $datum) {
                $pair = new PairsModel();
                $pair->digital_currency = strtoupper($datum['baseAsset']);
                $pair->market_currency = strtoupper($datum['quoteAsset']);
                $pairs_data[] = Helper::toArray($pair);
            }
        }
        
        return $pairs_data;
    }

    private function handleError()
    {
        if ($this->data['status'] != 'ok') {
            Helper::fail('请求失败，返回结果：' . json_encode($this->data));
        }
        return true;
    }
}