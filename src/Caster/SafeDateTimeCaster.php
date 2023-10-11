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

namespace Han\Utils\Caster;

use Carbon\Carbon;
use Hyperf\Contract\CastsAttributes;

use function Han\Utils\date_load;

class SafeDateTimeCaster implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        return date_load($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof Carbon) {
            return $value->toDateTimeString();
        }

        if (! empty($value)) {
            return $value;
        }

        /* @phpstan-ignore-next-line */
        return null;
    }
}
