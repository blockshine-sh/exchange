<?php

namespace ExchangeCenter\Exchanges\Bitmart;

use ExchangeCenter\Exchange;
use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:34
 */
class Ticker extends ExchangeBase
{
    protected $exchange = 'bitmart';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        if (empty($this->data)) {
            return [];
        }
        return $this->convertData();
    }

    /**
     * @author liyu
     * @param $close
     * @param $fluctuation
     * @return float|int
     */
    private function getOpen($close, $fluctuation)
    {
        return $close / (1 + $fluctuation);
    }


    /**
     * @author liyu
     * @param $url
     * @return array
     */
    private function parsePairs($url)
    {
        $flag = preg_match('/symbol=([a-zA-Z0-9]+_[a-zA-Z0-9]+)/', $url, $matches);

        if ($flag === 0) {
            return [];
        }

        return explode('_', $matches[1]);
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data as $item) {

            $url = $item['url'];
            $pair = $this->parsePairs($url);

            if (empty($url)) {
                continue;
            }

            list (
                $digitalCurrency,
                $marketCurrency
                ) = $pair;

            $close = $item['current_price'];
            $fluctuation = $item['fluctuation'];

            $ticker = new TickerModel();
            $ticker->digital_currency       = $digitalCurrency;
            $ticker->market_currency        = $marketCurrency;
            $ticker->open                   = $this->getOpen($close, $fluctuation);
            $ticker->high                   = $item['highest_price'];
            $ticker->low                    = $item['lowest_price'];
            $ticker->close                  = $close;
            $ticker->amount                 = $item['volume'];
            $ticker->vol                    = $item['base_volume'];
            $ticker->timestamp              = time();
            $ticker->bid1                   = $item['bid_1'];
            $ticker->ask1                   = $item['ask_1'];
            //$ticker->price_pcnt             = $ticker->open - $ticker->close;
            $ticker_data["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }

    private function handleError()
    {
        return true;
    }
}