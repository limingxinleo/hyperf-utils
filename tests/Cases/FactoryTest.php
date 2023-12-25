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

use Han\Utils\Exception\NotFoundException;
use Han\Utils\Exception\RuntimeException;
use Han\Utils\Factory;
use Han\Utils\Utils\Date;
use Han\Utils\Utils\Model;
use Han\Utils\Utils\Sorter;
use Hyperf\Collection\Collection as BaseCollection;
use Hyperf\Database\Model\Collection;
use HyperfTest\Stub\ContainerStub;
use HyperfTest\Stub\Model as ModelStub;

/**
 * @internal
 * @coversNothing
 */
class FactoryTest extends AbstractTestCase
{
    public function testFactoryGet()
    {
        $container = ContainerStub::getContainer();

        $facotry = new Factory($container);

        $this->assertInstanceOf(Date::class, $facotry->date);
        $this->assertInstanceOf(Model::class, $facotry->model);

        $this->expectException(RuntimeException::class);
        $facotry->date = new Date();

        $this->expectException(NotFoundException::class);
        $facotry->xxx;
    }

    public function testModelColumn()
    {
        $col = new Collection([
            new ModelStub(['message' => $id = uniqid()]),
            new ModelStub(['message' => $id2 = uniqid()]),
        ]);

        $result = (new Model())->columns($col, 'message');
        $this->assertInstanceOf(BaseCollection::class, $result);
        $this->assertSame([$id, $id2], $result->toArray());

        $result = (new Model())->columns($col, ['message']);
        $this->assertSame([['message' => $id], ['message' => $id2]], $result->toArray());
    }

    public function testSorter()
    {
        $col = new Collection([
            new ModelStub(['id' => 1, 'message' => $id = uniqid()]),
            new ModelStub(['id' => 2, 'message' => $id2 = uniqid()]),
        ]);

        $sorter = new Sorter();
        $res = $sorter->sort($col, static function (ModelStub $model) {
            return $model->id;
        });

        $data = $res->toArray();
        $this->assertSame(2, $data[0]->id);
        $this->assertSame(1, $data[1]->id);
        $this->assertSame($id2, $data[0]->message);
        $this->assertSame($id, $data[1]->message);

        $res = \Han\Utils\sort($col, static function (ModelStub $model) {
            return $model->id;
        });

        $data = $res->toArray();
        $this->assertSame(2, $data[0]->id);
        $this->assertSame(1, $data[1]->id);
        $this->assertSame($id2, $data[0]->message);
        $this->assertSame($id, $data[1]->message);
    }
}
