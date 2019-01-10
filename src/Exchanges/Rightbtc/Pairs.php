<?php

namespace ExchangeCenter\Exchanges\Rightbtc;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\PairsModel;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:34
 */
class Pairs extends ExchangeBase
{
    protected $exchange = 'rightbtc';

    public function getData($options = [])
    {
        $this->request('GET', $this->config['pairs']);
        if (empty($this->data)) {
            Helper::fail('获取交易对失败');
        }
        $this->handleError();
        return $this->convertData();
    }

    private function convertData()
    {
        $pairs_data = [];
        if (!empty($this->data['status']['message'])) {
            foreach ($this->data['status']['message'] as $item) {
                $pair = new PairsModel();
                $pair->digital_currency = strtoupper($item['bid_asset_symbol']);
                $pair->market_currency = strtoupper($item['ask_asset_symbol']);
                $pairs_data[] = Helper::toArray($pair);
            }
        }
        return $pairs_data;
    }

    private function handleError()
    {
        if (empty($this->data)) {
            Helper::fail('请求失败');
        }
        return true;
    }
}