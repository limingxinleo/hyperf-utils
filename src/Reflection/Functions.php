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
namespace HyperfX\Utils\Reflection;

use Hyperf\Utils\Reflection\ClassInvoker;

function invoke($instance): ClassInvoker
{
    return new ClassInvoker($instance);
}
