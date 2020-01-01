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

use Hyperf\Database\Model\Builder;

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
}
