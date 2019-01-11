<?php

namespace ExchangeCenter\Exchanges\Upbit;

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
    protected $exchange = 'upbit';

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
        if (!empty($this->data)) {
            foreach ($this->data as $item) {
                list (
                    $marketCurrency,
                    $digitalCurrency
                    ) = explode('-', $item['market']);

                $pair = new PairsModel();
                $pair->digital_currency = $digitalCurrency;
                $pair->market_currency = $marketCurrency;
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