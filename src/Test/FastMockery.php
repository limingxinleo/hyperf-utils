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

namespace Han\Utils\Test;

use Hyperf\Contract\ContainerInterface;

class FastMockery
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $value
     */
    public function run(string $key, $value, callable $callable): void
    {
        $entry = $this->container->get($key);
        try {
            $this->container->set($key, $value);
            $callable();
        } finally {
            $this->container->set($key, $entry);
        }
    }
}
