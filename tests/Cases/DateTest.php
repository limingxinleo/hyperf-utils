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

use Han\Utils\Utils\Date;
use HyperfTest\Stub\ContainerStub;
use function Han\Utils\date_load;

/**
 * @internal
 * @coversNothing
 */
class DateTest extends AbstractTestCase
{
    public function testDateLoad()
    {
        ContainerStub::getContainer();

        $util = new Date();
        $carbon = $util->load('2020-01-01 08:00:00');
        $this->assertSame(1577836800, $carbon->getTimestamp());

        $carbon = date_load('2020-01-01 08:00:00');
        $this->assertSame(1577836800, $carbon->getTimestamp());

        $carbon = $util->load(0);
        $this->assertSame('1970-01-01 08:00:00', $carbon->toDateTimeString());

        $carbon = $util->load('');
        $this->assertNull($carbon);

        $carbon = $util->load(null);
        $this->assertNull($carbon);

        $carbon = $util->load(1577836800);
        $this->assertSame(1577836800, $carbon->getTimestamp());

        $carbon = $util->load('1577836800');
        $this->assertSame(1577836800, $carbon->getTimestamp());
    }
}
