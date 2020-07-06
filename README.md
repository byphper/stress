# 基于swoole多进程多协程的http压力测试工具
灵感来自于go夜读的一次分享，如何用go实现压力测试工具，代码在https://github.com/link1st/go-stress-testing。
go原生的协程可以很方便的实现并发请求，Swoole4+以后对协程做了大量的工作，包括提供了各种协程客户端，所以想着用swoole来实现一个类似的压测工具。
