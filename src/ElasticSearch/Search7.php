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

namespace Han\Utils\ElasticSearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Han\Utils\Exception\InvalidArgumentException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\Guzzle\RingPHP\PoolHandler;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerInterface;

abstract class Search7
{
    public const VERSION = 7;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var array
     */
    protected $hosts;

    /**
     * @var null|PoolHandler
     */
    protected $handler;

    /**
     * @var null|Client
     */
    protected $client;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);

        $config = $this->config->get('elasticsearch.default');
        if (empty($config['host'])) {
            throw new InvalidArgumentException('搜索引擎配置不存在');
        }

        $this->hosts = (array) $config['host'];
    }

    public function client(): Client
    {
        if (! $this->client instanceof Client) {
            $this->client = ClientBuilder::create()
                ->setHandler($this->handler())
                ->setHosts($this->hosts)
                ->build();
        }

        return $this->client;
    }

    public function handler(): mixed
    {
        return null;
    }

    /**
     * 判断当前修改过得文档，是否在mapping中存在.
     * @param mixed $attributes
     */
    public function isModified($attributes = []): bool
    {
        if (is_array($attributes) && count($attributes) > 0) {
            $mapping = $this->mapping();
            foreach ($mapping as $key => $item) {
                if (isset($attributes[$key])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 保存文档.
     */
    public function put(Model $model): ?array
    {
        $doc = $this->document($model);
        $id = $model->getKey();
        $result = null;

        try {
            $client = $this->client();
            $doc = [
                'index' => $this->index(),
                'id' => $id,
                'body' => [
                    'doc' => $doc,
                    'doc_as_upsert' => true,
                ],
                'refresh' => true,
                'retry_on_conflict' => 5,
            ];

            $result = $client->update($doc);
        } catch (\Throwable $ex) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->error((string) $ex);
        }

        return $result;
    }

    /**
     * @param Collection<int, Model> $models
     */
    public function bulk(Collection $models): ?array
    {
        $result = null;

        try {
            $client = $this->client();

            $params = [
                'body' => [],
                'refresh' => 'wait_for',
            ];

            foreach ($models as $model) {
                $params['body'][] = [
                    'update' => [
                        '_index' => $this->index(),
                        '_id' => $model->getKey(),
                    ],
                ];

                $params['body'][] = [
                    'doc' => $this->document($model),
                    'doc_as_upsert' => true,
                ];
            }

            $result = $client->bulk($params);
        } catch (\Throwable $exception) {
            $this->container->get(StdoutLoggerInterface::class)->error((string) $exception);
        }

        return $result;
    }

    /**
     * 删除文档.
     * @param mixed $id
     */
    public function delete($id): ?array
    {
        $client = $this->client();
        $result = null;
        $doc = [
            'index' => $this->index(),
            'id' => $id,
        ];

        try {
            $result = $client->delete($doc);
        } catch (\Throwable $ex) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->error((string) $ex);
        }

        return $result;
    }

    /**
     * @param array $extra don't remove from arguments, it can easily cut by aop
     */
    public function rawSearch(array $params, array $extra = []): array
    {
        return $this->client()->search($params);
    }

    public function search(array $body, array $extra = []): array
    {
        $res = $this->rawSearch(
            [
                'index' => $this->index(),
                'body' => $body,
            ],
            $extra
        );

        if (isset($res['hits']['hits']) && $hits = $res['hits']['hits']) {
            $ids = [];
            foreach ($hits as $item) {
                $ids[] = $item['_id'];
            }

            return [$res['hits']['total']['value'] ?? 0, $ids];
        }

        return [0, []];
    }

    public function putIndex(
        bool $force = false,
        #[ArrayShape(['settings' => ['number_of_replicas' => 'int', 'number_of_shards' => 'int']])]
        array $body = []
    ): bool {
        $client = $this->client();
        $indices = $client->indices();

        $params = [
            'index' => $this->index(),
        ];
        $exist = $indices->exists($params);
        if ($exist && $force !== false) {
            $indices->delete($params);
            $exist = false;
        }

        if (! $exist) {
            if ($body) {
                $params['body'] = $body;
            }
            $indices->create($params);
            return true;
        }

        return false;
    }

    public function putMapping(): bool
    {
        $mapping = $this->mapping();
        $params = [
            'index' => $this->index(),
            'body' => [
                'properties' => $mapping,
            ],
        ];

        $indices = $this->client()->indices();
        $res = $indices->putMapping($params);
        if ($res['acknowledged']) {
            return true;
        }

        return false;
    }

    /**
     * 返回搜索引擎实体结构.
     */
    abstract public function mapping(): array;

    /**
     * 搜索引擎索引.
     */
    abstract public function index(): string;

    /**
     * 根据模型获取对应document，如果数据字段不一致，请重写此方法.
     */
    protected function document(Model $model): array
    {
        $map = $this->mapping();
        $data = [];
        foreach ($map as $key => $item) {
            $data[$key] = $model->{$key};
        }

        if (! $this->check($data)) {
            throw new InvalidArgumentException('数据参数与定义不一致');
        }

        return $data;
    }

    protected function check($data): bool
    {
        $map = $this->mapping();
        foreach ($map as $key => $item) {
            if (! isset($data[$key])) {
                $logger = $this->container->get(StdoutLoggerInterface::class);
                $logger->error(sprintf('[%s] Mapping invalid! Not has [%s]', get_called_class(), $key));
                return false;
            }
        }

        return true;
    }
}
