<?php


namespace Actor\Stress;


use Swoole\Coroutine\Http\Client;

class HttpClient
{
    /**
     * @var Client
     */
    private $client;

    private $options;

    public function __construct(array $options)
    {
        $this->init($options);
    }

    private function init(array $options)
    {
        $client = new Client($options['url']['host'], $options['url']['port'], $options['ssl']);
        $client->set(['keep_alive' => $options['alive']]);
        $client->setMethod($options['method']);
        $client->setHeaders($options['header']);
        $client->setCookies($options['cookie']);
        if (isset($options['body'])) {
            $client->setData($options['body']);
        }
        $this->client = $client;
        $this->options = $options;
    }

    public function request(): RequestModel
    {
        $model = new RequestModel();
        $model->startTime = microtime(true);
        $status = $this->client->execute($this->options['url']['path']);
        $model->endTime = microtime(true);
        if (!$status) {
            $model->success = false;
            return $model;
        }
        $model->success = $this->client->statusCode == 200;
        $model->statusCode = $this->client->statusCode;
        $model->transferSize = strlen($this->client->body);
        $model->spendTime = bcsub($model->endTime, $model->startTime, 4);
        return $model;
    }

}