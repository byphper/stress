<?php

namespace Actor\Stress;

use Swoole\Coroutine\Http\Client;
use Swoole\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Swoole\Coroutine\run;

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
        $this->addOption('method', 'm', InputOption::VALUE_OPTIONAL, '
Request method', 'GET');
        $this->addOption('keep-alive', 'k', InputOption::VALUE_OPTIONAL, '
Http keep-alive', false);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $this->checkOptions($options);
        $stress = new Stress($options, 1);
        try {
            $stress->start($output);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
        return Command::SUCCESS;
    }

    private function checkOptions(array &$options)
    {
        $options['full_url'] = $options['url'];
        $options['url'] = parse_url($options['url']);
        !isset($options['url']['port']) && $options['url']['port'] = 80;
        !isset($options['ssl']) && $options['ssl'] = false;
        !isset($options['alive']) && $options['alive'] = false;
        !isset($options['method']) && $options['method'] = 'GET';
        !isset($options['cookie']) && $options['cookie'] = [];
        !isset($options['header']) && $options['header'] = [
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ];
    }
}