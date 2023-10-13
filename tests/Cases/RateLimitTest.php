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

use Han\Utils\RateLimit;

/**
 * @internal
 * @coversNothing
 */
class RateLimitTest extends AbstractTestCase
{
    public function testRateLimitWait()
    {
        $limit = new RateLimit(fn (mixed $params) => $params === true);

        $now = microtime(true);
        $limit->wait(1100, true);
        $this->assertTrue(microtime(true) - $now > 1);

        $now = microtime(true);
        $limit->wait(1100, false);
        $this->assertTrue(microtime(true) - $now < 1);
    }
}
