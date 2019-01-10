# blockchainexorg/exchange
blockchainexorg/exchange用于集成对接各个主流交易所的开放接口

## Ticker
获取ticker数据

支持平台：

```
coinsbank、oex、idcm、fatbtc、topbtc、coinsuper、mountaintoken、singbitx、hotbit、cointiger

其中coinsbank由于接口访问频次限制，ticker接口每5秒钟访问一次，总获取时间大概90秒
```

调用示例：
```
use ExchangeCenter\Exchange;

try {
    $obj = new Exchange();
    $ticker = $obj->setExchange('coinsbank')  
        ->setOptions([
            //如果需要设置代理，则传该参数
            'proxy' => [
                'http' => 'http://127.0.0.1:8001',
                'https' => 'http://127.0.0.1:8001',
            ]
        ])
        ->getTicker();
} catch(Exception $e) {
    var_dump($e->getMessage());
}
```
方法解释：
```
setExchange() 设置平台
setOptions()  设置选项信息
```
返回数据：
```
amount 24小时交易量
vol    24小时交易额
count  成交笔数
bid1   买一价
ask1   卖一价
```
```
array(12) {
    ["digital_currency"]=>
    string(3) "CSE"
    ["market_currency"]=>
    string(3) "BTC"
    ["open"]=>
    float(0.00011)
    ["high"]=>
    string(9) "0.0001167"
    ["low"]=>
    string(8) "0.000108"
    ["close"]=>
    string(8) "0.000114"
    ["amount"]=>
    float(7852.7652384739)
    ["vol"]=>
    float(0)
    ["count"]=>
    int(0)
    ["bid1"]=>
    float(0)
    ["ask1"]=>
    float(0)
    ["timestamp"]=>
    float(1545717241)
  }


```
