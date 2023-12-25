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

namespace Han\Utils\Utils;

use Hyperf\Collection\Collection as BaseCollection;
use Hyperf\Contract\Arrayable;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model as BaseModel;

use function Hyperf\Support\value;
use function Hyperf\Tappable\tap;

class Model
{
    public function pagination(Builder $builder, $offset = 0, $limit = 10, $columns = ['*'])
    {
        $count = $builder->count();
        if ($limit > 0) {
            $items = $builder->offset($offset)->limit($limit)->get($columns);
        } else {
            $items = new Collection([]);
        }

        return [$count, $items];
    }

    public function query(Builder $builder, $offset = 0, $limit = 10, $columns = ['*'])
    {
        return $builder->offset($offset)->limit($limit)->get($columns);
    }

    public function loadCache(BaseModel $model, array $relations = []): BaseModel
    {
        tap(new Collection([$model]), static function (Collection $col) use ($relations) {
            /* @phpstan-ignore-next-line */
            $col->loadCache($relations);
        });

        return $model;
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
