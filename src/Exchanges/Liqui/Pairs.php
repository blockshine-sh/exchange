<?php

namespace ExchangeCenter\Exchanges\Liqui;

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
    protected $exchange = 'liqui';

    public function getData($options = [])
    {
        $this->request('GET', $this->config['pairs']);
        $this->handleError();
        return $this->convertData();
    }

    private function convertData()
    {
        $pairs_data = [];
        if (!empty($this->data['pairs'])) {
            foreach ($this->data['pairs'] as $pair=> $item) {

                list (
                    $digitalCurrency,
                    $marketCurrency
                    ) = explode('_', strtoupper($pair));

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