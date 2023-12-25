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
use Han\Utils\Exception\RuntimeException;
use Han\Utils\Schema\Continuous;
use Han\Utils\Utils\Date;
use Han\Utils\Utils\Sorter;
use Hyperf\Collection\Arr;
use Hyperf\Context\ApplicationContext;
use Hyperf\Database\Model\Model;
use Hyperf\HttpMessage\Uri\Uri;
use Hyperf\Support\Optional;
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
 * @param array|\Traversable $items
 */
function sort($items, callable $callable): SplPriorityQueue
{
    return app()->get(Sorter::class)->sort($items, $callable);
}

/**
 * @param array|\Traversable $items
 */
function spl_sort($items, callable $callable): SplPriorityQueue
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

function csv_open(string $path)
{
    $dirname = dirname($path);
    if (! is_dir($dirname)) {
        @mkdir($dirname, 0775, true);
    }

    $fp = fopen($path, 'w+');
    if (! $fp) {
        throw new RuntimeException('Csv init failed.');
    }

    fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

    return $fp;
}

function safe_path(string $path): string
{
    $dirname = dirname($path);
    if (! is_dir($dirname)) {
        @mkdir($dirname, 0775, true);
    }

    return $path;
}

/**
 * 判断模型是否被修改过.
 * @param array $expect 被排除检测的 key 列表
 */
function model_is_dirty(Model $model, array $expect = []): bool
{
    $dirty = $model->getDirty();

    Arr::forget($dirty, $expect);

    return ! empty($dirty);
}

/**
 * WARN: 注意，参数会被 urldecode
 * 根据 http query 解析成数组.
 */
function http_parse_query(string $query): array
{
    parse_str($query, $result);
    return $result;
}

/**
 * 移除 Uri 中某个参数.
 */
function unset_uri_param(Uri $uri, string $key): Uri
{
    $query = $uri->getQuery();
    $params = http_parse_query($query);
    unset($params[$key]);

    return $uri->withQuery(http_build_query($params));
}

/**
 * 判断数组是否前后连续.
 * @param array $array = [[1,5], [5,6], [6,10]]
 */
function is_continuous(array $array): Continuous
{
    $queue = new SplPriorityQueue();
    foreach ($array as $item) {
        $queue->insert($item, $item[1]);
    }

    $min = $max = null;
    foreach ($queue as $item) {
        if ($max === null) {
            $min = $item[0];
            $max = $item[1];
            continue;
        }

        if ($min > $item[1]) {
            return new Continuous(false);
        }

        $min = min($min, $item[0]);
    }

    return new Continuous(true, $min, $max);
}
