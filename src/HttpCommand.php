<?php

namespace Actor\Stress;

use Swoole\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * http压测命令
 * Class HttpCommand
 * @package Actor\Stress
 */
class HttpCommand extends Command
{
    protected static $defaultName = 'http:stress';

    protected function configure()
    {
        $this->addOption('concurrency', 'c', InputOption::VALUE_REQUIRED, '
Number of concurrent', 1);
        $this->addOption('request', 'r', InputOption::VALUE_REQUIRED, '
Number of requests', 1);
        $this->addOption('url', 'u', InputOption::VALUE_REQUIRED, '
Request Url');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $this->checkOptions($options);
        $stress = new Stress($options, 1);
        $stress->start($output);
        return Command::SUCCESS;
    }

    private function checkOptions(array $options)
    {

    }
}