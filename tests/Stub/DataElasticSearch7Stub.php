<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Stub;

use Han\Utils\ElasticSearch;
use Han\Utils\Exception\InvalidArgumentException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\RingPHP\CoroutineHandler;
use Psr\Container\ContainerInterface;

class DataElasticSearch7Stub extends ElasticSearch\Search7
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);

        $config = $this->config->get('elasticsearch.es7');
        if (empty($config['host'])) {
            throw new InvalidArgumentException('搜索引擎配置不存在');
        }

        $this->hosts = (array) $config['host'];
    }

    public function mapping(): array
    {
        return [
            'id' => ['type' => 'long'],
            'type' => ['type' => 'byte'],
            'message' => ['type' => 'text'],
            'keyword' => ['type' => 'keyword'],
        ];
    }

    public function index(): string
    {
        return 'test';
    }

    public function handler(): mixed
    {
        if ($this->handler instanceof CoroutineHandler) {
            return $this->handler;
        }

        return $this->handler = new CoroutineHandler();
    }
}
