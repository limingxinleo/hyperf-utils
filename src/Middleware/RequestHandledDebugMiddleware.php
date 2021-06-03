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
namespace Han\Utils\Middleware;

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandledDebugMiddleware implements MiddlewareInterface
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
            $debug = $request->getMethod() . ' ' . (string)$request->getUri() . PHP_EOL;
            $debug .= 'TIME: ' . $time . PHP_EOL;
            $debug .= $this->getRequestString($request) . PHP_EOL;
            if (isset($response)) {
                $debug .= 'RESPONSE: ' . $this->getResponseString($response) . PHP_EOL;
            }
            if (isset($exception) && $exception instanceof \Throwable) {
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

    protected function getResponseString(ResponseInterface $response): string
    {
        return (string)$response->getBody();
    }

    protected function getRequestString(ServerRequestInterface $request): string
    {
        $result = '';
        foreach ($request->getHeaders() as $header => $values) {
            foreach ((array)$values as $value) {
                $result .= $header . ': ' . $value . PHP_EOL;
            }
        }

        $result .= (string)$request->getBody();
        return $result;
    }

    protected function getCustomData(): string
    {
        return '';
    }
}
