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

use HyperfTest\Stub\Model;
use function Han\Utils\Reflection\invoke;

/**
 * @internal
 * @coversNothing
 */
class ReflectionTest extends AbstractTestCase
{
    public function testInvoke()
    {
        $fillable = invoke(new Model())->fillable;

        $this->assertSame(['id', 'type', 'message', 'keyword'], $fillable);
    }
}
