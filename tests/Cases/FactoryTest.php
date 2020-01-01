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

namespace HyperfTest\Cases;

use HyperfX\Utils\Exception\NotFoundException;
use HyperfX\Utils\Exception\RuntimeException;
use HyperfX\Utils\Factory;
use HyperfX\Utils\Utils\Date;
use HyperfX\Utils\Utils\Model;
use Mockery;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class FactoryTest extends AbstractTestCase
{
    public function testFactoryGet()
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->with(Date::class)->andReturn(new Date());
        $container->shouldReceive('get')->with(Model::class)->andReturn(new Model());

        $facotry = new Factory($container);

        $this->assertInstanceOf(Date::class, $facotry->date);
        $this->assertInstanceOf(Model::class, $facotry->model);

        $this->expectException(RuntimeException::class);
        $facotry->date = new Date();

        $this->expectException(NotFoundException::class);
        $facotry->xxx;
    }
}
