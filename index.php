<?php

include "vendor/autoload.php";

$application = new \Symfony\Component\Console\Application();
$application->setName('基于swoole的多进程+协程测试工具');
$application->setVersion('1.0.0'); //
$application->add(new \Actor\Stress\HttpCommand());
$application->run();

