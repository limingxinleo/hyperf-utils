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
namespace Han\Utils\Reflection;

use Hyperf\Support\Reflection\ClassInvoker;

function invoke($instance): ClassInvoker
{
    return new ClassInvoker($instance);
}
