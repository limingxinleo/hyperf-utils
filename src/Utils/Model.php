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
namespace HyperfX\Utils\Utils;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Utils\Collection as BaseCollection;
use Hyperf\Utils\Contracts\Arrayable;

class Model
{
    public function pagination(Builder $builder, $offset = 0, $limit = 10)
    {
        $count = $builder->count();

        $items = $builder->offset($offset)->limit($limit)->get();

        return [$count, $items];
    }

    public function query(Builder $builder, $offset = 0, $limit = 10)
    {
        return $builder->offset($offset)->limit($limit)->get();
    }

    /**
     * Returns only the columns from the collection with the specified keys.
     *
     * @param null|array|string $keys
     */
    public function columns(Collection $items, $keys): BaseCollection
    {
        if (is_null($keys)) {
            return new BaseCollection([]);
        }
        $result = [];
        $isSingleColumn = is_string($keys);
        foreach ($items as $item) {
            if ($isSingleColumn) {
                $value = $item->{$keys} ?? null;
                $result[] = $value instanceof Arrayable ? $value->toArray() : $value;
            } else {
                $result[] = value(static function () use ($item, $keys) {
                    $res = [];
                    foreach ($keys as $key) {
                        $value = $item->{$key} ?? null;
                        $res[$key] = $value instanceof Arrayable ? $value->toArray() : $value;
                    }

                    return $res;
                });
            }
        }

        return new BaseCollection($result);
    }
}
