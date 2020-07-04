<?php


namespace Actor\Stress;


class Process
{

    public static $processMap = [];

    private $process;

    private $processId;

    private $options;

    private $task;

    public function __construct(array $options, array $task, int $id)
    {
        $this->options = $options;
        $this->task = $task;

        $this->process = new \Swoole\Process([$this, 'run']);
    }

    public function run()
    {

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