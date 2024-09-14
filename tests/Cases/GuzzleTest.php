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

namespace HyperfTest\Cases;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Han\Utils\HTTP\Guzzle;
use HyperfTest\Stub\ContainerStub;

/**
 * @internal
 * @coversNothing
 */
class GuzzleTest extends AbstractTestCase
{
    public function testInitRetryAndDurationMiddleware()
    {
        ContainerStub::getContainer();

        $utils = new Guzzle();
        $client = new Client([
            'base_uri' => 'https://api.github.com',
            'handler' => $utils->initRetryAndDurationMiddleware(HandlerStack::create()),
        ]);

        $res = $client->get('/');

        $this->assertSame(200, $res->getStatusCode());
    }
}
