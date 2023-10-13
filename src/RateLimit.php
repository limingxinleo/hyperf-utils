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

namespace Han\Utils;

class RateLimit
{
    public function __construct(
        protected \Closure $isRateLimit
    ) {
    }

    /**
     * @param int $ms 限流时，阻塞毫秒数
     */
    public function wait(int $ms, mixed $params = null): void
    {
        if ($this->isRateLimit->__invoke($params)) {
            usleep($ms * 1000);
        }
    }
}
