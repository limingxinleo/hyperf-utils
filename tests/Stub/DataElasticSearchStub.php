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
use Hyperf\Guzzle\RingPHP\CoroutineHandler;

class DataElasticSearchStub extends ElasticSearch
{
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
