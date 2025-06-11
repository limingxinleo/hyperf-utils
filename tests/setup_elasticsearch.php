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
require_once 'bootstrap.php';

use HyperfTest\Stub\ContainerStub;
use HyperfTest\Stub\DataElasticSearch7Stub;
use HyperfTest\Stub\DataElasticSearchStub;

use function Hyperf\Coroutine\run;

$callback = function () {
    $container = ContainerStub::getContainer();
    $client = new DataElasticSearchStub($container);
    $client->putIndex(true);
    $client->putMapping();

    $client = new DataElasticSearch7Stub($container);
    $client->putIndex(true);
    $client->putMapping();

    Mockery::close();
};
if (extension_loaded('swoole')) {
    run($callback);
} else {
    $callback();
}
