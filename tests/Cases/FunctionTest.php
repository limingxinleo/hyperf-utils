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

use function HyperfX\Utils\optional;

/**
 * @internal
 * @coversNothing
 */
class FunctionTest extends AbstractTestCase
{
    public function testOptional()
    {
        $this->assertNull(optional(null)->id);
    }
}
