<?php

namespace ExchangeCenter\Exchanges\B2bx;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\PairsModel;

class Pairs extends ExchangeBase
{
    protected $exchange = 'b2bx';

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
            
        foreach ($this->data as $datum) {
            if (preg_match('/([A-Z]+)(BTC|USDT|ETH|B2BX)/', $datum['Instrument'], $pairArr)) {                
                $pair->digital_currency = $pairArr['1'];
                $pair->market_currency = $pairArr['2'];
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