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

use Hyperf\Contract\CastsAttributes;

class StringItemsCaster implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): StringItems
    {
        return StringItems::makeFromString((string) $value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return (string) $value;
    }
}
