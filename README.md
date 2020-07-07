# 基于swoole多进程多协程的http压力测试工具
灵感来自于go夜读的一次分享，如何用go实现压力测试工具，代码在https://github.com/link1st/go-stress-testing。
go原生的协程可以很方便的实现并发请求，Swoole4+以后对协程做了大量的工作，包括提供了各种协程客户端，所以试着用swoole来实现一个类似的压测工具。得力于swoole对底层进程，协程，以及它们之间通信的高度封装，我们实现这个小工具只需要很少的代码。

### 安装
composer require byphper/stress

### 使用

目前只支持HTTP压测

```

composer stress http -c 10 -r 100 -u http://www.xxx.com/

```
#### 参数说明
- -c  concurrency 并发数  
- -r  request 请求数
- -u  url 请求地址  一定要是完整的URL格式 不能省略http://
- -m  method 请求方法 默认GET
- -k  keep-alive 是否开启http keep-alive 默认false不开启
- -H  http-header 设置请求头  以json字符串格式 eg：-H '{"access_token":"xxxxxx"}'
- -C  http-cookie 设置cookie  以json字符串格式 eg：-C '{"seesion_id":"xxxxxx"}'
- -B  http-body 设置请求body  以json字符串格式 eg：-B '{"name":"xxxxxx"}'

#### 输出示例
```
本次派出2个进程,共2个协程对接口进行疯狂轰炸!!!

请求地址：http://127.0.0.1:9501/

并发数：2

请求数：10

请求cookie：{"seesion_id":"abcdefg"}

请求body：{"name":"andy"}

+-------+--------+--------+--------+-------+----------+
| 耗时  | 并发数 | 成功数 | 失败数 | QPS   | 平均耗时 |
+-------+--------+--------+--------+-------+----------+
+-------+-------+-------+-------+-----------+----------+
| 0     | 2     | 20    | 0     | 740.74/秒 | 0.0013秒 |
+-------+-------+-------+-------+-----------+----------+

战况：共派出2个协程，发起总请求数：20，成功请求数：20,失败请求：0，QPS：740.74/秒，平均耗时：0.0013秒

```