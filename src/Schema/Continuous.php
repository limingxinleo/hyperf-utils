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

namespace Han\Utils\Schema;

class Continuous
{
    public function __construct(
        public bool $continuous,
        public null|float|int $min = null,
        public null|float|int $max = null,
        public bool $empty = false,
    ) {
    }

    /**
     * 是否连续.
     */
    public function isContinuous(): bool
    {
        return $this->continuous;
    }

    /**
     * 是否为空.
     */
    public function isEmpty(): bool
    {
        return $this->empty;
    }
}
