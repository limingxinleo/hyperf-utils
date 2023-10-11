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

use Laminas\Stdlib\SplPriorityQueue;

class Sorter
{
    /**
     * @param array|\Traversable $items
     */
    public function sort($items, callable $callable): SplPriorityQueue
    {
        $queue = new SplPriorityQueue();
        $serial = PHP_INT_MAX;
        foreach ($items as $item) {
            $priority = (array) $callable($item);
            $priority[] = $serial--;
            /* @phpstan-ignore-next-line */
            $queue->insert($item, $priority);
        }
        return $queue;
    }
}
