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

use function Han\Utils\csv_open;
use function Han\Utils\http_parse_query;
use function Han\Utils\optional;

/**
 * @internal
 * @coversNothing
 */
class FunctionTest extends AbstractTestCase
{
    public function testOptional()
    {
        $this->assertNull(optional(null)->id);
    }

    public function testCsvOpen()
    {
        $fp = csv_open(__DIR__ . '/../../runtime/' . uniqid() . '.csv');

        fputcsv($fp, ['ID', 'Name']);
        fputcsv($fp, ['1', 'hyperf']);

        fclose($fp);

        $this->assertTrue(true);
    }

    public function testHttpParseQuery()
    {
        $res = http_parse_query('id=1&name=limx');
        $this->assertSame(['id' => '1', 'name' => 'limx'], $res);
    }
}
