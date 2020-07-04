<?php


namespace Actor\Stress;


use Symfony\Component\Console\Output\OutputInterface;


class Stress
{
    private $options;

    private $type;

    public function __construct(array $options, int $type)
    {
        $this->options = $options;

        $this->type;
    }

    /**
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function start(OutputInterface $output)
    {

        $works = $cpuNum = swoole_cpu_num();
        $concurrency = $this->options['concurrency'];
        //如果并发数小于cpu核心*4 则使用单进程进行压测
        $tasks = [];
        if ($concurrency < $cpuNum * 4) {
            $works = 1;
            $tasks[0] = $concurrency;
        }
        $tasks = $this->allocateTask($concurrency, $works);
        $output->writeln($works . "个进程," . $concurrency . "个协程正在对接口发起猛烈攻击!!!");
        for ($i = 0; $i < $works; $i++) {
            $process = new Process($this->options, $tasks, $i);
            $process->start();
        }
        \Swoole\Process::wait(true);
    }

    /**
     * 根据并发数和进程数分配任务 每个进程处理多少并发
     * @param int $concurrency
     * @param int $works
     * @return array
     */
    private function allocateTask(int $concurrency, int $works): array
    {
        $task = [];
        $remains = $concurrency % $works;
        $per = floor($concurrency / $works);
        for ($i = 0; $i < $works; $i++) {
            $task[$i] = $per;
        }
        if ($remains) {
            for ($j = 0; $j < $remains; $j++) {
                $task[$j]++;
            }
        }
        return $task;
    }
}