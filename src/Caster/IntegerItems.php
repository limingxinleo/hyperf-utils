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

class IntegerItems implements \Countable
{
    /**
     * @var int[]
     */
    protected array $items;

    public function __construct(array $items)
    {
        // 将会过滤空字符串和0
        $integers = [];
        foreach ($items as $item) {
            if ($item === '') {
                continue;
            }
            $integers[] = (int) $item;
        }

        $this->items = array_values(array_unique($integers));
    }

    public function __toString(): string
    {
        if ($string = implode(',', $this->toArray())) {
            return ',' . $string . ',';
        }

        return '';
    }

    public static function makeFromString(string $string)
    {
        return new IntegerItems(explode(',', $string));
    }

    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return $this
     */
    public function remove(int $id)
    {
        $index = array_search($id, $this->items);
        if (is_int($index)) {
            unset($this->items[$index]);
            $this->items = array_values(array_unique($this->items));
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function insert(int $id)
    {
        $this->items[] = $id;
        $this->items = array_values(array_unique($this->items));
        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
