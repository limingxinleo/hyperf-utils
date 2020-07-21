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

use HyperfTest\Stub\ContainerStub;
use HyperfTest\Stub\DataElasticSearchStub;
use HyperfTest\Stub\Model;

/**
 * @internal
 * @coversNothing
 */
class ElasticSearchTest extends AbstractTestCase
{
    protected function tearDown()
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

        $this->assertTrue($client->put($model));

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

        $this->assertTrue($client->put($model));

        $res = $client->client()->get([
            'index' => $client->index(),
            'type' => $client->type(),
            'id' => 2,
        ]);

        $this->assertSame($data, $res['_source']);
    }
}
