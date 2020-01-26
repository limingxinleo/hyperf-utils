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

namespace HyperfX\Utils\Utils;

use Carbon\Carbon;

class Date
{
    /**
     * @param int|string $date
     */
    public function load($date): Carbon
    {
        if (is_int($date)) {
            return Carbon::createFromTimestamp($date);
        }

        return Carbon::make($date);
    }
}
