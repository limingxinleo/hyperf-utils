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
namespace HyperfTest\Stub;

use Han\Utils\ContextInstance;

class FooContext extends ContextInstance
{
    protected function initModels(array $ids): array
    {
        $result = [];
        foreach ($ids as $id) {
            $result[$id] = (object) [
                'id' => $id,
                'name' => uniqid(),
                'type' => 'foo',
            ];
        }

        return $result;
    }
}
