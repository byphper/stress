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

    /**
     * 初始化http client
     * @param array $options
     */
    private function init(array $options)
    {
        $isHttps = $options['url']['scheme'] == 'https';
        $port = $isHttps ? 443 : $options['url']['port'];
        $client = new Client($options['url']['host'], $port, $isHttps);
        $client->set(['keep_alive' => $options['keep-alive']]);
        $client->setMethod($options['method']);
        $client->setHeaders($options['header']);
        $client->setCookies($options['cookie']);
        if (isset($options['body'])) {
            $client->setData($options['body']);
        }
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * 发起http请求
     * @return RequestModel
     */
    public function request(): RequestModel
    {
        $model = new RequestModel();
        $model->startTime = microtime(true);
        $path = empty($this->options['url']['path']) ? '/' : $this->options['url']['path'];
        $status = $this->client->execute($path);
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