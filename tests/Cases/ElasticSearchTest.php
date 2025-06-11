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

namespace HyperfTest\Cases;

use Hyperf\Database\Model\Collection;
use HyperfTest\Stub\ContainerStub;
use HyperfTest\Stub\DataElasticSearch7Stub;
use HyperfTest\Stub\DataElasticSearchStub;
use HyperfTest\Stub\Model;

/**
 * @internal
 * @coversNothing
 */
class ElasticSearchTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
    }

    public function testPutAndGet()
    {
        $container = ContainerStub::getContainer();
        $client = new DataElasticSearchStub($container);

        $model = new Model($data = [
            'id' => 1,
            'type' => 1,
            'message' => 'Hello World.',
            'keyword' => 'Hyperf',
        ]);

        $this->assertIsArray($client->put($model));

        $res = $client->client()->get([
            'index' => $client->index(),
            'type' => $client->type(),
            'id' => 1,
        ]);

        $this->assertSame($data, $res['_source']);
    }

    public function testPutArrayAndGet()
    {
        $container = ContainerStub::getContainer();
        $client = new DataElasticSearchStub($container);

        $model = new Model($data = [
            'id' => 2,
            'type' => 1,
            'message' => 'Hello World.',
            'keyword' => ['Hyperf', 'Swoft'],
        ]);

        $this->assertIsArray($client->put($model));

        $res = $client->client()->get([
            'index' => $client->index(),
            'type' => $client->type(),
            'id' => 2,
        ]);

        $this->assertSame($data, $res['_source']);
    }

    public function testBulk()
    {
        $m1 = new Model(['id' => 10, 'type' => 1, 'message' => 'Hello World.', 'keyword' => ['Hyperf', 'Swoft']]);
        $m2 = new Model(['id' => 11, 'type' => 1, 'message' => 'Hello World.', 'keyword' => ['Hyperf', 'Swoft']]);
        $m3 = new Model(['id' => 12, 'type' => 1, 'message' => 'Hello World.', 'keyword' => ['Hyperf', 'Swoft']]);
        $m4 = new Model(['id' => 13, 'type' => 1, 'message' => 'Hello World.', 'keyword' => ['Hyperf', 'Swoft']]);

        $container = ContainerStub::getContainer();
        $client = new DataElasticSearch7Stub($container);
        $res = $client->bulk(new Collection([$m1, $m2, $m3]));
        $this->assertFalse($res['errors']);

        $res = $client->search(['sort' => ['id' => ['order' => 'asc']], 'query' => ['bool' => ['must' => [
            [
                'term' => ['type' => 1],
            ],
        ]]]]);

        $this->assertSame([3, ['10', '11', '12']], $res);

        $m3->type = 2;
        $m4->type = 2;

        $res = $client->bulk(new Collection([$m1, $m3, $m4]));
        $this->assertFalse($res['errors']);

        $res = $client->search(['sort' => ['id' => ['order' => 'asc']], 'query' => ['bool' => ['must' => [
            [
                'term' => ['type' => 1],
            ],
        ]]]]);

        $this->assertSame([2, ['10', '11']], $res);

        $res = $client->search(['sort' => ['id' => ['order' => 'asc']], 'query' => ['bool' => ['must' => [
            [
                'term' => ['type' => 2],
            ],
        ]]]]);

        $this->assertSame([2, ['12', '13']], $res);
    }
}
