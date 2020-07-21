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
namespace HyperfX\Utils;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\Model;
use Hyperf\Guzzle\RingPHP\PoolHandler;
use HyperfX\Utils\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

abstract class ElasticSearch
{
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

    public function handler()
    {
        if ($this->handler instanceof PoolHandler) {
            return $this->handler;
        }

        return $this->handler = make(PoolHandler::class, [
            'option' => [
                'max_connections' => 50,
                'max_idle_time' => 1,
            ],
        ]);
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
    public function put(Model $model)
    {
        $doc = $this->document($model);
        $id = $model->getKey();

        try {
            $client = $this->client();
            $doc = [
                'index' => $this->index(),
                'type' => $this->type(),
                'id' => $id,
                'body' => [
                    'doc' => $doc,
                    'doc_as_upsert' => true,
                ],
                'refresh' => true,
                'retry_on_conflict' => 5,
            ];
            $client->update($doc);
        } catch (\Throwable $ex) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->error((string) $ex);
        }

        return true;
    }

    /**
     * 删除文档.
     * @param mixed $id
     */
    public function delete($id)
    {
        $client = $this->client();

        $doc = [
            'index' => $this->index(),
            'type' => $this->type(),
            'id' => $id,
        ];

        try {
            $client->delete($doc);
        } catch (\Throwable $ex) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->error((string) $ex);
        }

        return true;
    }

    public function search(array $body): array
    {
        $client = $this->client();

        $params = [
            'index' => $this->index(),
            'type' => $this->type(),
            'body' => $body,
        ];

        $res = $client->search($params);

        if (isset($res['hits']['hits']) && $hits = $res['hits']['hits']) {
            $ids = [];
            foreach ($hits as $item) {
                $ids[] = $item['_id'];
            }

            return [$res['hits']['total'], $ids];
        }

        return [0, []];
    }

    public function putIndex(bool $force = false): bool
    {
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
            'type' => $this->type(),
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
     * 搜索引擎类型.
     */
    abstract public function type(): string;

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
