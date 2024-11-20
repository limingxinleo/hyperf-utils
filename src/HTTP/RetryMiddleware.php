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

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function Han\Utils\app;

class RetryMiddleware extends \Hyperf\Guzzle\RetryMiddleware
{
    public function getMiddleware(?string $name = null): callable
    {
        return Middleware::retry(function ($retries, RequestInterface $request, ?ResponseInterface $response = null) use ($name) {
            if (! $this->isOk($response) && $retries < $this->retries) {
                if ($name) {
                    $formatter = new MessageFormatter(MessageFormatter::DEBUG);
                    app()->get(LoggerFactory::class)->get($name)->warning($formatter->format($request, $response));
                }

                return true;
            }
            return false;
        }, function () {
            return $this->delay;
        });
    }
}
