<?php

namespace ExchangeCenter\Exchanges\Gateio;

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
    protected $exchange = 'gateio';

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
     * @param $percentChange
     * @return float|int
     */
    private function getOpen($close, $percentChange)
    {
        return 100 * $close / (100 + $percentChange);
    }

    /**
     * @author liyu
     * @param $open
     * @param $close
     * @return mixed
     */
    private function getPricePcnt($open, $close)
    {
        return $close - $open;
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data as $pair=> $item) {

            list (
                $digitalCurrency,
                $marketCurrency
                ) = explode('_', strtoupper($pair));

            $close = $item['last'];
            $percentChange = $item['percentChange'];

            $ticker = new TickerModel();
            $ticker->digital_currency       = $digitalCurrency;
            $ticker->market_currency        = $marketCurrency;
            $ticker->open                   = $this->getOpen($close, $percentChange);
            $ticker->high                   = $item['high24hr'];
            $ticker->low                    = $item['low24hr'];
            $ticker->close                  = $close;
            $ticker->amount                 = $item['quoteVolume'];
            $ticker->vol                    = $item['baseVolume'];
            $ticker->timestamp              = time();
            $ticker->bid1                   = $item['highestBid'];
            $ticker->ask1                   = $item['lowestAsk'];
            //$ticker->price_pcnt             = $this->getPricePcnt($ticker->open, $ticker->close);
            $ticker_data["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }

    private function handleError()
    {
        return true;
    }
}