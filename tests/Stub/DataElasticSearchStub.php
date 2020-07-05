<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Stub;

use Hyperf\Guzzle\RingPHP\CoroutineHandler;
use HyperfX\Utils\ElasticSearch;

class DataElasticSearchStub extends ElasticSearch
{
    public function mapping(): array
    {
        return [
            'id' => 'long',
            'type' => 'byte',
            'message' => 'text',
            'keyword' => 'keyword',
        ];
    }

    public function index(): string
    {
        return 'test';
    }

    public function type(): string
    {
        return 'data';
    }

    public function handler()
    {
        if ($this->handler instanceof CoroutineHandler) {
            return $this->handler;
        }

        return $this->handler = new CoroutineHandler();
    }
}
