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

use Hyperf\Support\Traits\StaticInstance;

abstract class ContextInstance
{
    use StaticInstance;

    protected array $ids = [];

    protected ?string $key = null;

    protected array $models = [];

    /**
     * @return $this
     */
    public function init(array $ids)
    {
        $ids = array_unique($ids);
        $diff = array_diff($ids, $this->ids);

        if (empty($diff)) {
            return $this;
        }

        $this->ids = array_merge($this->ids, $diff);

        $this->mergeModels($this->initModels($diff));

        return $this;
    }

    public function first($id, bool $init = false)
    {
        if ($init && ! isset($this->models[$id])) {
            $this->init([$id]);
        }
        return $this->models[$id] ?? null;
    }

    public function find(array $ids): array
    {
        $result = [];
        foreach ($ids as $id) {
            $result[$id] = $this->models[$id] ?? null;
        }
        return $result;
    }

    public function all(): array
    {
        return $this->models;
    }

    public function exist($id): bool
    {
        return isset($this->models[$id]);
    }

    public function getCount(): int
    {
        return count($this->models);
    }

    public function mergeModels($models): void
    {
        foreach ($models as $key => $model) {
            if ($this->key) {
                $this->models[$model->{$this->key}] = $model;
            } else {
                $this->models[$key] = $model;
            }
        }
    }

    abstract protected function initModels(array $ids): array;
}
