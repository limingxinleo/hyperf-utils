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

use Hyperf\Utils\Contracts\Arrayable;

class StringItems implements Arrayable, \Countable
{
    protected array $items;

    public function __construct(array $items)
    {
        // 将会过滤空字符串和0
        $this->items = array_values(array_unique(array_filter($items)));
    }

    public function __toString(): string
    {
        if ($string = implode(',', $this->toArray())) {
            return ',' . $string . ',';
        }
        return '';
    }

    public static function makeFromString(string $tagString)
    {
        $tags = explode(',', $tagString);
        return new StringItems($tags);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function add($data)
    {
        if (! in_array($data, $this->items)) {
            $this->items[] = $data;
        }
    }

    public function count(): int
    {
        return count($this->items);
    }
}
