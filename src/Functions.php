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

use Carbon\Carbon;
use Han\Utils\Utils\Date;
use Han\Utils\Utils\Sorter;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Optional;
use Laminas\Stdlib\SplPriorityQueue;

/**
 * @param mixed $object
 */
function optional($object): Optional
{
    return new Optional($object);
}

/**
 * @param null|int|string $date
 */
function date_load($date): ?Carbon
{
    return app()->get(Date::class)->load($date);
}

/**
 * @param array|\iterable $items
 */
function sort($items, callable $callable): SplPriorityQueue
{
    return app()->get(Sorter::class)->sort($items, $callable);
}

/**
 * Finds an entry of the container by its identifier and returns it.
 * @param null|string $id
 * @return mixed|\Psr\Container\ContainerInterface
 */
function app($id = null)
{
    $container = ApplicationContext::getContainer();
    if ($id) {
        return $container->get($id);
    }

    return $container;
}
