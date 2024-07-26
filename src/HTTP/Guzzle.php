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

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Hyperf\Guzzle\RetryMiddleware;
use Hyperf\Logger\LoggerFactory;

use function Han\Utils\app;

class Guzzle
{
    public function logMiddleware(string $name, string $formatter = MessageFormatter::DEBUG): callable
    {
        $formatter = new MessageFormatter($formatter);

        return Middleware::log(app()->get(LoggerFactory::class)->get($name), $formatter);
    }

    public function retryMiddleware(): callable
    {
        return app()->get(RetryMiddleware::class)->getMiddleware();
    }

    public function initMiddlewares(HandlerStack $stack): HandlerStack
    {
        $stack->push($this->retryMiddleware(), 'retry');
        $stack->push($this->logMiddleware('http'), 'log');

        return $stack;
    }
}
