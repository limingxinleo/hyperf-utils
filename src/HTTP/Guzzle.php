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

namespace Han\Utils\HTTP;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\MessageFormatterInterface;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\PromiseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use function Han\Utils\app;

class Guzzle
{
    public function logMiddleware(string $name, string $formatter = MessageFormatter::DEBUG): callable
    {
        $formatter = new MessageFormatter($formatter);

        return Middleware::log(app()->get(LoggerFactory::class)->get($name), $formatter);
    }

    public function retryMiddleware(?string $name = null): callable
    {
        return app()->get(RetryMiddleware::class)->getMiddleware($name);
    }

    public function initMiddlewares(HandlerStack $stack): HandlerStack
    {
        $stack->push($this->retryMiddleware('http'), 'retry');
        $stack->push($this->logMiddleware('http'), 'log');

        return $stack;
    }

    public function initRetryAndDurationMiddleware(HandlerStack $stack): HandlerStack
    {
        $stack->push($this->retryMiddleware('name'), 'retry');

        $formatter = new MessageFormatter(MessageFormatter::DEBUG);
        $stack->push(static::log(app()->get(LoggerFactory::class)->get('http'), $formatter));

        return $stack;
    }

    /**
     * Middleware that logs requests, responses, and errors using a message
     * formatter.
     *
     * @phpstan-param \Psr\Log\LogLevel::* $logLevel Level at which to log requests.
     *
     * @param LoggerInterface $logger logs messages
     * @param MessageFormatterInterface $formatter formatter used to create message strings
     * @param string $logLevel level at which to log requests
     *
     * @return callable returns a function that accepts the next handler
     */
    public static function log(LoggerInterface $logger, MessageFormatterInterface $formatter, string $logLevel = 'info'): callable
    {
        return static function (callable $handler) use ($logger, $formatter, $logLevel): callable {
            return static function (RequestInterface $request, array $options = []) use ($handler, $logger, $formatter, $logLevel) {
                $ms = microtime(true);
                return $handler($request, $options)->then(
                    static function ($response) use ($logger, $request, $formatter, $logLevel, $ms): ResponseInterface {
                        $message = $formatter->format($request, $response);
                        $logger->log($logLevel, $message, [
                            'duration' => microtime(true) - $ms,
                        ]);

                        return $response;
                    },
                    static function ($reason) use ($logger, $request, $formatter): PromiseInterface {
                        $response = $reason instanceof RequestException ? $reason->getResponse() : null;
                        $message = $formatter->format($request, $response, P\Create::exceptionFor($reason));
                        $logger->error($message);

                        return P\Create::rejectionFor($reason);
                    }
                );
            };
        };
    }
}
