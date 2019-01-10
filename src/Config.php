<?php
/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:25
 */

return [
    'coinsbank' => [
        'url' => 'https://coinsbank.com/sapi/',
        'ticker' => 'trade/ohlcv?pairCode=%s&interval=86400',
        'pairs' => 'head',
        'timeout' => 60,
    ],
    'oex' => [
        'url' => 'https://oex.com/api/v1/',
        'ticker' => 'tickers',
        'timeout' => 90,
    ],
    'idcm' => [
        'url' => 'https://api.idcm.io:8323/api/v1/',
        'ticker' => 'RealTimeQuote/GetRealTimeQuotes',
        'pairs' => 'RealTimeQuote/GetRealTimeQuotes',
        'timeout' => 60,
    ],
    'fatbtc' => [
        'url' => 'https://www.fatbtc.us/m/',
        'ticker' => 'allticker/1/',
        'pairs' => '',
        'timeout' => 60,
    ],
    'topbtc' => [
        'url' => 'http://www.topbtc.one/market/',
        'ticker' => 'tickerall.php',
        'pairs' => '',
        'timeout' => 60,
    ],
    'coinsuper' => [
        'url' => 'https://www.coinsuper.com/v1/api/market/',
        'ticker' => 'hour24Market',
        'pairs' => '',
        'timeout' => 60,
    ],
    'mountaintoken' => [
        'url' => 'https://www.mountaintoken.co/api/v2/',
        'ticker' => 'tickers',
        'pairs' => '',
        'timeout' => 60,
    ],
    'singbitx' => [
        'url' => 'https://manager.singbitx.io/api/v2/',
        'ticker' => 'tickers',
        'pairs' => '',
        'timeout' => 60,
    ],
    'hotbit' => [
        'url' => 'https://api.hotbit.io/api/v1/',
        'ticker' => 'allticker',
        'pairs' => '',
        'timeout' => 60,
    ],
    'cointiger' => [
        'url' => '',
        'ticker' => 'https://www.cointiger.com/exchange/api/public/market/detail',
        'pairs' => 'https://api.cointiger.com/exchange/trading/api/v2/currencys',
        'timeout' => 60,
    ],
    'huobi' => [
        'url' => 'https://api.huobipro.com',
        'ticker' => 'market/tickers',
        'pairs' => 'v1/common/symbols',
        'timeout' => 60,
    ],
    'gateio' => [
        'url' => 'https://data.gateio.io',
        'ticker' => 'api2/1/tickers',
        'timeout' => 60,
    ],
    'upbit' => [
        'url' => 'https://api.upbit.com',
        'ticker' => 'v1/ticker?markets=',
        'pairs' => 'v1/market/all',
        'timeout' => 60,
    ],
    'rightbtc' => [
        'url' => 'https://www.rightbtc.com',
        'ticker' => 'api/public/tickers',
        'pairs' => 'api/public/trading_pairs',
        'timeout' => 60,
    ],
    'bitz' => [
        'url' => 'https://apiv2.bitz.com/Market/',
        'ticker' => 'ticker',
        'pairs' => 'symbolList',
        'timeout' => 60,
    ],
    'digifinex' => [
        'url' => 'https://openapi.digifinex.com/v2/',
        'ticker' => 'ticker',
        'pairs' => '',
        'timeout' => 60,
    ],
    'crex24' => [
        'url' => 'https://api.crex24.com/v2/public/',
        'ticker' => 'tickers',
        'pairs' => '',
        'timeout' => 60,
    ],    
];