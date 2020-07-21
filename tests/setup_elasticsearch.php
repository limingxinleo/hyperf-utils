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
use HyperfTest\Stub\DataElasticSearchStub;

run(function () {
    $container = ContainerStub::getContainer();
    $client = new DataElasticSearchStub($container);
    $client->putIndex(true);
    $client->putMapping();

    Mockery::close();
});
