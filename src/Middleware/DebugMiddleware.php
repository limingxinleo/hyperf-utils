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
namespace HyperfX\Utils\Middleware;

use Hyperf\HttpServer\Request;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Codec\Json;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DebugMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $time = microtime(true);
        try {
            $response = $handler->handle($request);
        } catch (\Throwable $exception) {
            throw $exception;
        } finally {
            $logger = $this->container->get(LoggerFactory::class)->get('request');

            // 日志
            $time = microtime(true) - $time;
            $debug = 'URI: ' . $request->getUri()->getPath() . PHP_EOL;
            $debug .= 'TIME: ' . $time . PHP_EOL;
            $debug .= 'REQUEST: ' . $this->getRequestString() . PHP_EOL;
            if (isset($response)) {
                $debug .= 'RESPONSE: ' . $response->getBody()->getContents() . PHP_EOL;
            } else {
                $debug .= 'EXCEPTION: ' . $exception->getMessage() . PHP_EOL;
            }

            if ($time > 1) {
                $logger->error($debug);
            } else {
                $logger->info($debug);
            }
        }

        return $response;
    }

    protected function getRequestString(): string
    {
        $data = $this->container->get(Request::class)->all();

        return Json::encode($data);
    }
}
