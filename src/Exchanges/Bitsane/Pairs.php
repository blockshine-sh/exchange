<?php

namespace ExchangeCenter\Exchanges\Bitsane;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\PairsModel;

class Pairs extends ExchangeBase
{
    protected $exchange = 'bitsane';

    public function getData($options = [])
    {
        $this->request('GET', $this->config['url'] . $this->config['pairs']);
        if (empty($this->data)) {
            Helper::fail('获取交易对失败');
        }

        return $this->convertData();
    }

    private function convertData()
    {
        $pairs_data = [];
        $pair = new PairsModel();
            
        foreach ($this->data as $symbol => $datum) {
            $pairArr = explode('_', strtoupper($symbol));
            $pair->digital_currency = $pairArr['0'];
            $pair->market_currency = $pairArr['1'];
            $pairs_data[] = Helper::toArray($pair);
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