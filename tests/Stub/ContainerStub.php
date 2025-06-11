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

use Han\Utils\Utils\Date;
use Han\Utils\Utils\Model;
use Han\Utils\Utils\Sorter;
use Hyperf\Config\Config;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Coroutine\Waiter;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\Guzzle\RetryMiddleware;
use Hyperf\Logger\LoggerFactory;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Psr\Container\ContainerInterface;

class ContainerStub
{
    public static function getContainer()
    {
        $container = \Mockery::mock(ContainerInterface::class);
        ApplicationContext::setContainer($container);

        $config = new Config([
            'elasticsearch' => [
                'default' => [
                    'host' => ['127.0.0.1:9200'],
                ],
                'es7' => [
                    'host' => ['127.0.0.1:9200'],
                ],
            ],
            'logger' => [
                'default' => [
                    'handler' => [
                        'class' => StreamHandler::class,
                        'constructor' => [
                            'stream' => __DIR__ . '/../../utils.log',
                            // 'stream' => 'php://output',
                            'level' => Level::Info,
                        ],
                    ],
                    'formatter' => [
                        'class' => LineFormatter::class,
                        'constructor' => [
                            'format' => null,
                            'dateFormat' => 'Y-m-d H:i:s',
                            'allowInlineLineBreaks' => true,
                        ],
                    ],
                ],
            ],
        ]);
        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(StdoutLoggerInterface::class)->andReturn(new StdoutLogger($config));
        $container->shouldReceive('get')->with(Date::class)->andReturn(new Date());
        $container->shouldReceive('get')->with(Model::class)->andReturn(new Model());
        $container->shouldReceive('get')->with(Sorter::class)->andReturn(new Sorter());
        $container->shouldReceive('get')->with(Waiter::class)->andReturn(new Waiter());
        $container->shouldReceive('get')->with(RetryMiddleware::class)->andReturn(new RetryMiddleware());
        $container->shouldReceive('get')->with(LoggerFactory::class)->andReturn(new LoggerFactory($container, $config));

        return $container;
    }
}
