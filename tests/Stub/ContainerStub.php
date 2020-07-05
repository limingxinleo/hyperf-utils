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
namespace HyperfTest\Stub;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\Utils\ApplicationContext;
use Mockery;
use Psr\Container\ContainerInterface;

class ContainerStub
{
    public static function getContainer()
    {
        $container = Mockery::mock(ContainerInterface::class);
        ApplicationContext::setContainer($container);

        $config = new Config([
            'elasticsearch' => [
                'default' => [
                    'host' => ['127.0.0.1:9200'],
                ],
            ],
        ]);
        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(StdoutLoggerInterface::class)->andReturn(new StdoutLogger($config));

        return $container;
    }
}
