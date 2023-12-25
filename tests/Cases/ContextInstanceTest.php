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
use HyperfTest\Stub\FooContext;

use function Hyperf\Coroutine\wait;

/**
 * @internal
 * @coversNothing
 */
class ContextInstanceTest extends AbstractTestCase
{
    public function testContextFirst()
    {
        ContainerStub::getContainer();

        wait(function () {
            $foo = FooContext::instance();
            $foo->init([1, 2, 3]);

            $this->assertSame(1, $foo->first(1)->id);
            $this->assertSame('foo', $foo->first(1)->type);
        });
    }
}
