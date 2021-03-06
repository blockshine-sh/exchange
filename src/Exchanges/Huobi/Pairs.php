<?php

namespace ExchangeCenter\Exchanges\Huobi;

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
    protected $exchange = 'huobi';

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
        if (!empty($this->data['data'])) {
            foreach ($this->data['data'] as $datum) {
                $pair = new PairsModel();
                $pair->digital_currency = strtoupper($datum['base-currency']);
                $pair->market_currency = strtoupper($datum['quote-currency']);
                $pairs_data[] = Helper::toArray($pair);
            }
        }
        return $pairs_data;
    }

    private function handleError()
    {
        if (!empty($this->data['code'])) {
            Helper::fail('请求失败，返回结果：' . json_encode($this->data));
        }
        return true;
    }
}