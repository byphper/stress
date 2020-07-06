<?php


namespace Actor\Stress;


use Swoole\Coroutine;

class Process
{

    public static $processMap = [];

    private $process;

    private $processId;

    private $options;

    private $concurrency;

    private $id;

    public function __construct(array $options, array $task, int $id)
    {
        $this->options = $options;
        $this->concurrency = $task[$id];
        $this->id = $id;
        $this->process = new \Swoole\Process([$this, 'run'], false, 2, true);
    }

    public function run(\Swoole\Process $proc)
    {
        $socket = $proc->exportSocket();
        $requests = $this->options['request'];
        $channelSize = $this->concurrency * $requests;
        $modelChannel = new Coroutine\Channel($channelSize);
        for ($i = 0; $i < $this->concurrency; $i++) {
            Coroutine::create(function () use ($requests, $socket, $modelChannel) {
                $client = new HttpClient($this->options);
                for ($j = 0; $j < $requests; $j++) {
                    $requestModel = $client->request();
                    $modelChannel->push($requestModel);
                }
            });
        }
        for ($i = 0; $i < $channelSize; $i++) {
            $requestModel = $modelChannel->pop();
            $socket->send(json_encode($requestModel));
        }
        $socket->send("over");
    }

    /**
     * @throws ProcessException
     */
    public function start()
    {
        $this->processId = $this->process->start();
        if ($this->processId === false) {
            throw new ProcessException('进程创建失败，code:' . swoole_errno() . '，error:' . swoole_strerror(swoole_errno()));
        }
        static::$processMap[$this->processId] = $this->process;
    }
}