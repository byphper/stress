<?php

namespace Actor\Stress;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
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

    /**
     * 配置命令行参数
     */
    protected function configure()
    {
        $this->addOption('concurrency', 'c', InputOption::VALUE_REQUIRED, '
Number of concurrent');
        $this->addOption('request', 'r', InputOption::VALUE_REQUIRED, '
Number of requests');
        $this->addOption('url', 'u', InputOption::VALUE_REQUIRED, '
Request Url');
        $this->addOption('method', 'm', InputOption::VALUE_OPTIONAL, '
Request method', 'GET');
        $this->addOption('keep-alive', 'k', InputOption::VALUE_OPTIONAL, '
Http keep-alive', false);
        $this->addOption('header', 'H', InputOption::VALUE_OPTIONAL, '
Http header');
        $this->addOption('cookie', 'C', InputOption::VALUE_OPTIONAL, '
Http cookie');
        $this->addOption('body', 'B', InputOption::VALUE_OPTIONAL, '
Http body');

    }

    /**
     * 执行命令
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
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

    /**
     * 检查命令参数
     * @param array $options
     */
    private function checkOptions(array &$options)
    {
        if (!isset($options['concurrency'])) {
            throw new InvalidOptionException('-c concurrency is required');
        }
        if (!isset($options['request'])) {
            throw new InvalidOptionException('-r request is required');
        }
        if (!isset($options['url'])) {
            throw new InvalidOptionException('-u url is required');
        }
        $options['full_url'] = $options['url'];
        $options['url'] = parse_url($options['url']);

        if (!isset($options['url']['scheme'])) {
            throw new InvalidOptionException('请填写完整的URL地址，包括http或https');
        }
        !isset($options['url']['port']) && $options['url']['port'] = 80;
        !isset($options['ssl']) && $options['ssl'] = false;
        !isset($options['alive']) && $options['alive'] = false;
        !isset($options['method']) && $options['method'] = 'GET';
        $options['body'] = isset($options['body']) ? json_decode($options['body'], true) : [];
        $options['cookie'] = isset($options['cookie']) ? json_decode($options['cookie'], true) : [];
        $options['header'] = isset($options['header']) ? json_decode($options['header'], true) : [
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ];;
    }
}